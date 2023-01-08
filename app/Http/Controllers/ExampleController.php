<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ExampleController extends Controller
{
    public function homepage(){
        return '<h1>Homepage!!</h1><a href="/about">Go to About page</a>';
    }

    public function aboutPage(){
        return '<h1>My About Page</h1><a href="/">Back to homepage</a>';
    }
}
