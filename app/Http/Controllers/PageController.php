<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function home(Request $request)
    {
        return view('home');
    }

    public function photos(Request $request){
        return view('photography');
    }
}
