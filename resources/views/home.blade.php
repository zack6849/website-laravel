@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{asset('css/index.css')}}">
@endpush

@section('title', 'Index')

@section('content')
    <div class="jumbotron">
        <div class="container">
            <div class="row">
                <div class="col-3 d-none d-lg-block d-xl-block">
                    <img src="{{asset('img/profile.jpg')}}" class="rounded-circle">
                </div>
                <div class="col-9">
                    <h1 class="display-3">Hello There.</h1>
                    <p>
                        I'm Zack, I'm a 21 year old Web Developer, Java Developer and Hobbyist Photographer from the Tampa
                        Bay Area<br>
                        I like to solve problems and take pictures. This is my website / portfolio / dumping ground<br>
                        Nice to meet you.
                    </p>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid" style="background-image: url('//c1.staticflickr.com/2/1487/24940101593_36aae30400_k.jpg'); padding-top: 2em; padding-bottom: 2em;  ">
        <div class="container" id="photosContainer">
            <h2 style="color: whitesmoke">My Photos<sup><small><i class="fas fa-images"></i></small></sup></h2>
            <div class="row">
                <div class="col col-sm-12 col-md-4">
                    <img  class="img-thumbnail" src="http://c1.staticflickr.com/5/4601/24573472887_35570efd85_k.jpg">
                </div>
                <div class="col col-sm-12 col-md-4">
                    <img  class="img-thumbnail" src="http://c1.staticflickr.com/5/4540/24041726457_90a44d8ede_k.jpg">
                </div>
                <div class="col col-sm-12 col-md-4">
                    <img  class="img-thumbnail" src="http://c1.staticflickr.com/5/4556/24041729157_1a0352d35d_k.jpg">
                </div>
            </div>
            <div class="d-flex justify-content-end">
                <div class="p-2">
                    <a href="https://www.flickr.com/photos/zack6849/" target="_blank" rel="noopener"><button class="btn btn-primary">See More</button></a>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid" style="background-color: white; padding-top: 2em; padding-bottom: 2em;">
        <div class="container" id="projectsContainer">
            <h2>My Projects<sup><small>
            <span class="fa-layers fa-fw">
                <span class="fas fa-male" style="" data-fa-transform="grow-10"></span>
                <i class="fas fa-briefcase" style="color:  rgb(0, 123, 255);" data-fa-transform="shrink-6 down-2 right-2 rotate-15 flip-h"></i>
            </span></small></sup></h2>
            <p>Most of the things I make are work projects nowadays and not public, but here's a small collection of things I maintain or created at one point in time, in no particular order.</p>
            <div class="row">
                <div class="row">
                    <div class="col col-sm-12 col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">AlphabotV2</h5>
                                <p class="card-text">A Highly extensible IRC bot with a node-based permissions system, meant for basic channel administration and utilities. </p>
                            </div>
                            <div class="card-footer text-muted">
                                <a href="https://github.com/zack6849/AlphabotV2" title="Github" target="_blank" rel="noopener" class="card-link align-bottom"><i class="fab fa-github"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="col col-sm-12 col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">ShipStation PHP Wrapper</h5>
                                <p class="card-text">A maintained fork of <a href="https://github.com/michaelbonds/ship-station">https://github.com/michaelbonds/ship-station</a><br>
                                    Designed to programmatically access the ShipStation REST API via PHP with Guzzle</p>
                            </div>
                            <div class="card-footer text-muted">
                                <a href="https://github.com/zack6849/ship-station" title="GitHub" target="_blank" rel="noopener" class="align-bottom"><i class="fab fa-github"></i></a>
                                <a href="https://packagist.org/packages/zack6849/ship-station" title="Packagist" target="_blank" rel="noopener" class="align-bottom"><i class="fas fa-archive"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="col col-sm-12 col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">SuperLogger</h5>
                                <p class="card-text">A Highly configurable logging system for the Bukkit API, largely unnecessary now that log4j is bundled with spigot and bukkit, but it was pretty useful back in the day :)</p>
                            </div>
                            <div class="card-footer text-muted">
                                <a href="https://github.com/zack6849/SuperLogger" title="GitHub" target="_blank" rel="noopener" class="card-link align-bottom"><i class="fab fa-github"></i> </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
