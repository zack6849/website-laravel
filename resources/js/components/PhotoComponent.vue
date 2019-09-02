<template>
    <div class="photo-container m-4">
        <div class="max-w-sm rounded overflow-hidden shadow-lg">
            <img @click="openFlickr" class="w-full cursor-pointer" v-bind:src="info.url_m" v-bind:alt="info.title">
            <div class="px-6 py-4">
                <div class="font-bold text-xl mb-2" v-text="info.title"></div>
            </div>
            <div class="px-6 py-4">
                <span class="inline-block bg-gray-200 rounded-full px-3 py-1 mb-1 text-sm font-semibold text-gray-700" v-if="info.tags.length > 0" v-for="tag in info.tags.split(' ')" v-text="tag"></span>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['info'],
        methods: {
          openFlickr(){
              var url = "https://www.flickr.com/photos/" + this.info.owner + "/" + this.info.id;
              window.open(url);
          }
        }
    }
</script>
