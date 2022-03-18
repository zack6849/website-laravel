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
        debug("Valid Signature?:", $request->hasValidSignature());
        return view('home');
    }

    public function photos(Request $request){
        return view('photography');
    }
}
