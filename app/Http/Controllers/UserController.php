<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use App\Models\Follow;
use Illuminate\Http\Request;
use App\Events\OurExampleEvent;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{

    public function storeAvatar(Request $request) {
        // $request->file('avatar')->store('public/avatars');
        $request->validate([
            'avatar' => 'required|image|max:3000'
        ]);

        $user = auth()->user();
        
        $filename = $user->id . '-' . uniqid() . '.jpg';

        $imgData = Image::make($request->file('avatar'))->fit(120)->encode('jpg'); 
        Storage::put('public/avatars/' . $filename, $imgData);

        $oldAvatar = $user->avatar;

        $user->avatar = $filename;
        $user->save();

        if ($oldAvatar != "/fallback-avatar.jpg") {
            // 1st arg = what you're trying to replace 2nd arg = what you want to replace it with and 3rd arg = string of text to perform replacement on
            Storage::delete(str_replace("/storage/", "public/", $oldAvatar));
        }

        return back()->with('success', 'Avatar successfully updated');
    }


    public function showAvatarForm(){
        return view('avatar-form');
    }

    private function getSharedData($user){
        $currentlyFollowing = 0; // false by default

        if (auth()->check()) {
            $currentlyFollowing = Follow::where([['user_id', '=', auth()->user()->id], ['followeduser', '=', $user->id]])->count();
        }

        View::share('sharedData', ['currentlyFollowing' => $currentlyFollowing, 'avatar' => $user->avatar, 'username' => $user->username, 'postCount' => $user->posts()->count(), 'followersCount' => $user->followers()->count(), 'followingCount' => $user->followingUsers()->count()]);
    }

    public function profile(User $user){
        $this->getSharedData($user);
        //need set out relationship between user and post
        return view('profile-posts', ['posts' => $user->posts()->latest()->get()]);
    }

    public function profileFollowers(User $user){
        $this->getSharedData($user);
        return view('profile-followers', ['followers' => $user->followers()->latest()->get()]);
    }

    public function profileFollowing(User $user){
        $this->getSharedData($user);
        return view('profile-following', ['following' => $user->followingUsers()->latest()->get()]);
    }

    public function logout(){
        event(new OurExampleEvent(['username' => auth()->user()->username, 'action' => 'logout']));
        auth()->logout();
        return redirect('/')->with('success', 'You are now logged out');
    }

    public function showCorrectHomepage(){
        if (auth()->check()) {
            return view('homepage-feed', ['posts' => auth()->user()->feedPosts()->latest()->paginate(3)]);
        } else {
            // 1st = key/lavel in cache 2nd how many seconds to remember 3rd function if data not in cache
            $postCount = Cache::remember('postCount', 20, function(){
                return Post::count();
            });
            return view('homepage', ['postCount' => $postCount]);
        }
    }

    public function register(Request $request){
        $incomingFields = $request->validate([
            'username' => ['required', 'min:3', 'max:20', Rule::unique('users', 'username')],
            'email' => ['required', 'email', Rule::unique('users', 'email')],
            'password' => ['required', 'min:8', 'confirmed']
        ]);
        // hashing password - must do this
        $incomingFields['password'] = bcrypt($incomingFields['password']);

        $newUser = User::create($incomingFields);
        auth()->login($newUser);
        return redirect('/')->with('success', 'Your account has been successfully created');
    }

    public function loginApi(Request $request){
        $incomingFields = $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        if (auth()->attempt($incomingFields)) {
            // if yes return personal access tokens
            $user = User::where('username', $incomingFields['username'])->first();
            $token = $user->createToken('apptoken')->plainTextToken;
            return $token;
        }

        return '';
    }

    public function login(Request $request){
        $incomingFields = $request->validate([
            'loginusername' => 'required',
            'loginpassword' => 'required'
        ]);

        if (auth()->attempt(['username' => $incomingFields['loginusername'], 'password' => $incomingFields['loginpassword']])) {
            $request->session()->regenerate();
            event(new OurExampleEvent(['username' => auth()->user()->username, 'action' => 'login']));
            return redirect('/')->with('success', 'You have been successfully logged in');
        } else {
            return redirect('/')->with('error', 'You failed to log in');
        }
        
    }
}
