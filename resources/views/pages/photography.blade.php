@extends('layouts.app')
@section('title', 'Photography')

@section('content')
    <div class="flex container mx-auto mb-6">
        <div class="hidden lg:visible lg:block mr-10">
            <img src="https://42f2671d685f51e10fc6-b9fcecea3e50b3b59bdc28dead054ebc.ssl.cf5.rackcdn.com/illustrations/camera_mg5h.svg" >
        </div>
        <div class="col-9">
            <h1 class="font-light text-5xl">My Photos.</h1>
            <p>
                Once in a blue moon I enjoy going out and taking photos, here's a few of them from my <a target="_blank" href="https://www.flickr.com/photos/zack6849/">flickr page</a>
            </p>
        </div>
    </div>
    <hr>
    <div class="container-fluid  mt-5 pb-5" id="technasdologies">
        <div class="container mx-auto">
            <photo-gallery
                api_key="{{config('services.flickr.api_key')}}"
                user_id="{{config('services.flickr.gallery_uid')}}"
            />
        </div>
    </div>
@endsection

