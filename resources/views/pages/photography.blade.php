<x-base-page vue="true" title="Photos" noheader="true">
    <div class="mb-6 flex flex-col items-start gap-4 lg:flex-row lg:items-center">
        <div class="order-2 w-full lg:order-1 lg:mr-10 lg:block lg:w-auto">
            <img src="https://42f2671d685f51e10fc6-b9fcecea3e50b3b59bdc28dead054ebc.ssl.cf5.rackcdn.com/illustrations/camera_mg5h.svg" alt="person holding camera graphic">
        </div>
        <div class="order-1 lg:order-2">
            <h1 class="font-light text-4xl sm:text-5xl">My Photos.</h1>
            <p>
                Once in a blue moon I enjoy going out and taking photos, here's a few of them from my <a target="_blank" href="https://www.flickr.com/photos/zack6849/">flickr page</a>
            </p>
        </div>
    </div>
    <hr>
    <div class="mt-5 pb-5">
        <photo-gallery
            api_key="{{config('services.flickr.api_key')}}"
            user_id="{{config('services.flickr.gallery_uid')}}"
        />
    </div>
</x-base-page>
