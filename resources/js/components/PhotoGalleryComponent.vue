<template>
    <div class="photo-gallery-container">
        <div class="photo-gallery flex flex-wrap justify-between">
            <template v-for="photo in photos">
                <photo-component v-if="photo.width_l >= 1024" v-bind:key="photo.id" v-bind:info="photo"/>
            </template>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import PhotoComponent from "./PhotoComponent.vue";

export default {
    name: 'PhotoGalleryComponent',
    components: {
        PhotoComponent,
    },
    props: ['api_key', 'user_id'],
    mounted() {
        this.loadPhotos();
    },
    data() {
        return {
            photos: [],
        };
    },
    methods: {
        loadPhotos() {
            var flickr_api_url = 'https://www.flickr.com/services/rest/?method=flickr.people.getPhotos&content_type=1&extras=url_l,url_m,tags&format=json&nojsoncallback=1';
            flickr_api_url += '&user_id=' + encodeURIComponent(this.user_id);
            flickr_api_url += '&api_key=' + encodeURIComponent(this.api_key);
            axios.get(flickr_api_url, {
                transformRequest: [(data, headers) => {
                    delete headers['X-CSRF-TOKEN'];
                    delete headers['X-Requested-With'];
                    return data;
                }]
            }).then(response => {
                //we should have photos.
                if (response.status === 200 && response.data.stat === "ok") {
                    this.photos = response.data.photos.photo;
                }
            });
        }
    }
}
</script>
