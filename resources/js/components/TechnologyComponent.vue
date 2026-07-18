<template>
    <img v-if="image" :alt="name" v-bind:src="image" class="w-4 h-4">
    <span v-text="name"></span>
</template>

<script>
    export default {
        name: 'TechnologyComponent',
        props: {
            name: {
                type: String,
                required: true,
            },
            image: {
                type: String,
                default: null,
            },
        },
    }
</script>
