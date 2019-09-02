<template>
    <div class="photo-gallery-container">
        <div class="photo-gallery flex flex-wrap justify-between">
            <photo v-if="photo.width_l >= 1024" v-for="photo in photos" v-bind:info="photo" />
        </div>
    </div>
</template>

<script>
    export default {
        props: ['api_key', 'user_id'],
        mounted(){
            console.log("Loading Photos for gallery");
            this.loadPhotos();
        },
        data(){
            return {
                photos: [],
            };
        },
        methods: {
            loadPhotos(){
                var flickr_api_url = 'https://www.flickr.com/services/rest/?method=flickr.people.getPhotos&content_type=1&extras=url_l,url_m,tags&format=json&nojsoncallback=1';
                flickr_api_url += '&user_id=' + encodeURIComponent(this.user_id);
                flickr_api_url += '&api_key=' + encodeURIComponent(this.api_key);
                flickr_axios.get(flickr_api_url).then(response => {
                    //we should have photos.
                    if(response.status === 200 && response.data.stat === "ok"){
                        this.photos = response.data.photos.photo;
                    }
                });
            }
        }
    }
</script>
