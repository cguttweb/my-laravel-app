<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function createPost(){
        return view('create-post');
    }

    public function storeNewPost(Request $request){
        $incomingFields = $request->validate([
            'title' => 'required',
            'body' => 'required'
        ]);

        $incomingFields['title'] = strip_tags($incomingFields['title']);
        $incomingFields['body'] = strip_tags($incomingFields['body']);
        $incomingFields['user_id'] = auth()->id();

        $newPost = Post::create($incomingFields);

        return redirect("/post/{$newPost->id}")->with('success', 'New post created successfully');
    }

    public function showSinglePost(Post $post){
        // $ourHTML = Str::markdown($post->body);
        // set to only allow certain tags e.g. no links
        $post['body'] = strip_tags(Str::markdown($post->body), '<p><ul><ol><li><h1><h2><h3><h4><em><strong>');
        return view('single-post', ['post' => $post]);
    }
}
