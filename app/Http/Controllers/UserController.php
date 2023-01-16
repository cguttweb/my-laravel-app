<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{

    public function storeAvatar(Request $request){
        // file references the file field name in avatar form
        // $request->file('avatar')->store('public/images');
        $request->validate([
            'avatar' => 'required|image|max:3000'
        ]);

        $request->file('avatar')->store('public/images');
    }

    public function showAvatarForm(){
        return view('avatar-form');
    }

    public function profile(User $user){
        //need set out relationship between user and post
        return view('profile-posts', ['username' => $user->username, 'posts' => $user->posts()->latest()->get(), 'postCount' => $user->posts()->count()]);
    }

    public function logout(){
        auth()->logout();
        return redirect('/')->with('success', 'You are now logged out');
    }

    public function showCorrectHomepage(){
        if (auth()->check()) {
            return view('homepage-feed');
        } else {
            return view('homepage');
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

    public function login(Request $request){
        $incomingFields = $request->validate([
            'loginusername' => 'required',
            'loginpassword' => 'required'
        ]);

        if (auth()->attempt(['username' => $incomingFields['loginusername'], 'password' => $incomingFields['loginpassword']])) {
            $request->session()->regenerate();
            return redirect('/')->with('success', 'You have been successfully logged in');
        } else {
            return redirect('/')->with('error', 'You failed to log in');
        }
        
    }
}
