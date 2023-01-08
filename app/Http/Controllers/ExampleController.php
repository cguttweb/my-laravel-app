<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ExampleController extends Controller
{
    public function homepage(){
        // any complexity should be added here in the controller nopt the view which is for outputting html
        $myName = 'Chloe';
        $pets = ['Darcy', 'Hector', 'Monty'];
        // 1st arg is name of view file and 2nd arg - array and can pass any data - associative array which can have multiple values defined
        return view('homepage', ['allAnimals' => $pets, 'name' => $myName]);
    }

    public function aboutPage(){
        return view('single-post');
    }
}
