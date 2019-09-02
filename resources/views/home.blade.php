@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{asset('css/index.css')}}">
@endpush

@section('title', 'Index')

@section('content')
<div class="flex container mx-auto mb-6">
    <div class="hidden lg:visible lg:block mr-10">
        <img src="{{asset('img/profile.jpg')}}" class="rounded-full w-48">
    </div>
    <div class="col-9">
        <h1 class="font-light text-5xl">Hello There</h1>
        <p>
            I'm Zack, I'm a 21 year old Web Developer, Java Developer and Hobbyist Photographer from the Tampa Bay Area.<br>
            I like to solve problems and take pictures. This is my website / portfolio / dumping ground.<br>
            Nice to meet you.
        </p>
    </div>
</div>
<hr>
<div class="container-fluid  mt-5 pb-5" id="technasdologies">
    <div class="container mx-auto">
        <h3 class="text-3xl font-light text-black">Technologies</h3>
        <p class="text-sm">I've worked with a number of things, but these are the ones that are worth mentioning or like to work with</p>
        <div class="flex justify-between mt-3 flex-wrap">
           <project-card title="<i class='fab fa-laravel text-red-500'></i> Laravel" image="https://42f2671d685f51e10fc6-b9fcecea3e50b3b59bdc28dead054ebc.ssl.cf5.rackcdn.com/illustrations/laravel_and_vue_59tp.svg">
                I've built a few internal projects in laravel, one of which is responsible for running the entire operation of a nation-wide utility billing company
            </project-card>
            <project-card title="<i class='fab fa-magento text-orange-500'></i> Magento 2" image="https://42f2671d685f51e10fc6-b9fcecea3e50b3b59bdc28dead054ebc.ssl.cf5.rackcdn.com/illustrations/online_shopping_ga73.svg">
                I have extensive experience working with Magento 2, both Community and Enterprise Editions, and have created internal use modules for companies, enforcing business requirements and generating helpful marketing data
            </project-card>
            <project-card title="<i class='fab fa-wordpress text-blue-500'></i> WordPress" image="https://42f2671d685f51e10fc6-b9fcecea3e50b3b59bdc28dead054ebc.ssl.cf5.rackcdn.com/illustrations/wordpress_utxt.svg">
                I spent bit of time engineering a wordpress based SaaS product for the restaurant and food service industry featuring online ordering and delivery quotes through postmates
            </project-card>
        </div>
        <div class="flex justify-between mt-3 flex-wrap">
            <project-card title="<i class='fab fa-linux'></i> Linux" image="https://42f2671d685f51e10fc6-b9fcecea3e50b3b59bdc28dead054ebc.ssl.cf5.rackcdn.com/illustrations/maintenance_cn7j.svg">
                I've been using Linux for years, both from a server administration and end user perspective, with a focus on security
            </project-card>
            <project-card title="<i class='fas fa-database text-blue-300'></i> MySQL" image="https://42f2671d685f51e10fc6-b9fcecea3e50b3b59bdc28dead054ebc.ssl.cf5.rackcdn.com/illustrations/server_status_5pbv.svg">
                I've been using LAMP/LEMP as my primary development work for most of my career
            </project-card>
            <project-card title="<i class='fab fa-docker text-blue-800'></i> Docker" image="https://42f2671d685f51e10fc6-b9fcecea3e50b3b59bdc28dead054ebc.ssl.cf5.rackcdn.com/illustrations/Container_ship_ok1c.svg">
                Containers are the future, woo, I should insert some important sounding text here or something, this isn't even important but I didn't want it to not be 3 items per row
            </project-card>
        </div>
    </div>
</div>
<div class="container mx-auto my-5">
    <h3 class="text-3xl font-light">Open Source Projects</h3>
    <p class=" text-sm">Most of the things I make are work projects nowadays and not public, but here's a small collection of things I maintain or created at one point in time, in no particular order.</p>
    <div class="flex justify-between mt-3">
        <project-card title="ShipStation PHP Wrapper" image="https://42f2671d685f51e10fc6-b9fcecea3e50b3b59bdc28dead054ebc.ssl.cf5.rackcdn.com/illustrations/logistics_x4dc.svg">
            A maintained fork of <a href="https://github.com/michaelbonds/ship-station">michaelbonds/ship-station</a><br>
            Designed to programmatically access the ShipStation REST API via PHP with Guzzle
            <template slot="footer">
                <a href="https://github.com/zack6849/ship-station"><i class="fab fa-github"></i></a>
                <a href="https://packagist.org/packages/zack6849/ship-station"><i class="fa fa-box"></i></a>
            </template>
        </project-card>
        <project-card title="SuperLogger" image="https://42f2671d685f51e10fc6-b9fcecea3e50b3b59bdc28dead054ebc.ssl.cf5.rackcdn.com/illustrations/progress_tracking_7hvk.svg">
            A gighly configurable logging system for the Bukkit API, largely unnecessary now that log4j is bundled with spigot and bukkit, but it was pretty useful back in the day :)
            <template slot="footer">
                <a href="https://github.com/zack6849/AlphabotV2"><i class="fab fa-github"></i></a>
            </template>
        </project-card>
        <project-card title="AlphabotV2" image="https://42f2671d685f51e10fc6-b9fcecea3e50b3b59bdc28dead054ebc.ssl.cf5.rackcdn.com/illustrations/Artificial_intelligence_oyxx.svg">
            An extensible IRC bot with a node-based permissions system, meant for basic channel administration and utilities.
            <template slot="footer">
                <a href="https://github.com/zack6849/AlphabotV2"><i class="fab fa-github"></i></a>
            </template>
        </project-card>
    </div>
</div>


@endsection
