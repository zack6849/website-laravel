<template>
    <div class="flex flex-col border-2 border-gray-300 m-2 rounded bg-white w-full sm:w-2/5 md:w-2/6 lg:w-1/4 xl:w-1/4">
        <b class="p-2" v-html="title"></b>
        <hr>
        <div class="flex-grow p-2">
            <div class="flex flex-col h-full">
                <div class="project-card-img">
                    <img v-if="image.length > 0" v-bind:src="image" height="150px" class="h-64" :alt="title">
                </div>
                <div class="project-card-description flex-grow">
                    <slot> </slot>
                </div>
            </div>
        </div>
        <div class="bg-gray-100 p-2">
            <div class="project-footer flex flex-row-reverse">
                <a v-if="github" :href="github" class="text-blue-500" target="_blank"><i class="fab fa-github"></i></a>
                <a v-if="packageLink" :href="packageLink" class="text-blue-500" target="_blank"><i class="fa fa-box"></i></a>
            </div>
        </div>
    </div>
</template>
<style scoped>
.project-footer{
    a {
        padding-left: 1rem;
    }
}
</style>
<script>
export default {
    name: 'ProjectCardComponent',
    props: {
        title: {
            type: String,
            required: true
        },
        image: {
            type: String,
            default: ''
        },
        github: {
            type: String,
            default: null,
        },
        packageLink: {
            type: String,
            default: null,
        }
    }
}
</script>
