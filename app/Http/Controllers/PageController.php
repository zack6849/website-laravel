<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;

class PageController extends Controller
{
    public function home(): Renderable
    {
        return view('pages.home');
    }

    public function photos(): Renderable
    {
        return view('pages.photography');
    }

    public function qsos(): Renderable
    {
        return view('pages.qsos');
    }
}
