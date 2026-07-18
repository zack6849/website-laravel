<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;

class PageController extends Controller
{
    public function home(): Renderable
    {
        $techCategories = collect(config('techstack.categories'))
            ->map(fn (array $items) => collect($items)->map(function (array $tech) {
                if (isset($tech['image'])) {
                    $tech['image'] = asset($tech['image']);
                }

                return $tech;
            })->all())
            ->all();

        return view('pages.home', [
            'techCategories' => $techCategories,
            'projects' => config('projects'),
        ]);
    }

    public function photos(): Renderable
    {
        return view('pages.photography');
    }

    public function radio(): Renderable
    {
        return view('pages.radio-contact-map');
    }
}
