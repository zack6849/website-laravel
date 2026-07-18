<template>
    <article
        class="flex flex-col rounded border bg-white p-4"
        :class="[project.featured ? 'border-brand-300 shadow-sm md:p-5' : 'border-gray-200', {hilight: project.glow === true}]"
    >
        <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
            <h3 class="break-words font-medium text-gray-900" :class="project.featured ? 'text-2xl' : 'text-xl'">{{ project.name }}</h3>
            <div class="flex shrink-0 items-center gap-2">
                <span v-if="project.featured" class="self-start rounded-full bg-brand-100 px-2 py-0.5 text-xs font-medium text-brand-700">
                    <i class="fas fa-star text-brand-500" aria-hidden="true"></i> Featured
                </span>
                <span v-if="project.status" class="self-start rounded-full bg-gray-100 px-2 py-0.5 text-xs text-gray-500">
                    {{ project.status }}
                </span>
            </div>
        </div>

        <p class="mt-3 text-sm text-gray-600">{{ project.description }}</p>

        <div v-if="project.tech?.length" class="mt-3 flex flex-wrap gap-1.5">
            <span
                v-for="tag in project.tech"
                :key="tag"
                class="rounded-full bg-gray-100 px-2 py-0.5 text-xs text-gray-600"
            >
                {{ tag }}
            </span>
        </div>

        <div v-if="project.links?.length" class="mt-4 flex flex-wrap gap-x-4 gap-y-1">
            <a
                v-for="link in project.links"
                :key="`${project.name}-${link.url}`"
                :href="link.url"
                :target="isExternal(link.url) ? '_blank' : null"
                :rel="isExternal(link.url) ? 'noopener noreferrer' : null"
                class="text-sm text-brand-700 hover:text-brand-900"
            >
                <i v-if="link.icon" :class="link.icon" class="mr-1" aria-hidden="true"></i>{{ link.label || 'View' }} &rarr;
            </a>
        </div>
    </article>
</template>
<script>
export default {
    name: 'ProjectCardComponent',
    props: {
        project: {
            type: Object,
            required: true,
        },
    },
    methods: {
        isExternal(url) {
            return /^https?:\/\//.test(url);
        },
    },
}
</script>
