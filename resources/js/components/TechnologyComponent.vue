<template>
    <div class="technology py-3 px-2 text-white shadow mr-1 mt-2 bg-white flex">
        <img v-bind:src="image" class="pr-2 technology-logo" style="max-width: 40px; max-height: 20px;">
        <span v-text="name" class="text-black technology-text"></span>
    </div>
</template>

<script>
    export default {
        props: ['name', 'image']
    }
</script>
