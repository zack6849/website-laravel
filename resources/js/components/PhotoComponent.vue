<!-- resources/js/components/PhotoComponent.vue -->
<template>
    <div class="photo-container m-4 shadow-lg">
        <div class="max-w-sm rounded overflow-hidden">
            <img @click="openFlickr" class="w-full cursor-pointer" :src="info.url_m" :alt="info.title">
            <div class="px-6 py-4">
                <div class="font-bold text-xl mb-2">{{ info.title }}</div>
            </div>
            <div class="px-6 py-4">
                <span
                    v-if="info.tags.length > 0"
                    v-for="tag in info.tags.split(' ')"
                    :key="tag"
                    v-text="tag"
                    class="inline-block bg-gray-200 rounded-full px-3 py-1 mb-1 text-sm font-semibold text-gray-700"
                />
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'PhotoComponent',
    props: {
        info: {
            type: Object,
            required: true
        }
    },
    methods: {
        openFlickr() {
            const url = `https://www.flickr.com/photos/${this.info.owner}/${this.info.id}`;
            window.open(url);
        }
    }
}
</script>
