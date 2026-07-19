<template>
    <div class="space-y-4">
        <h4 class="text-sm font-semibold uppercase tracking-wide text-gray-500">{{ title }}</h4>
        <project-card
            v-for="(project, index) in featured"
            :key="`${category}-featured-${project.name}-${index}`"
            :project="project"
        />
        <template v-if="rest.length">
            <div :id="restId" v-show="showAll" class="space-y-4">
                <project-card
                    v-for="(project, index) in rest"
                    :key="`${category}-rest-${project.name}-${index}`"
                    :project="project"
                />
            </div>
            <button
                type="button"
                @click="showAll = !showAll"
                class="inline-flex min-h-11 items-center gap-2 rounded-sm px-1 text-sm font-medium text-brand-700 hover:text-brand-900 focus:outline-none focus:ring-2 focus:ring-brand-200"
                :aria-expanded="showAll.toString()"
                :aria-controls="restId"
            >
                <span>{{ disclosureLabel }}</span>
                <i class="fas fa-chevron-down text-xs transition-transform" :class="{ 'rotate-180': showAll }"></i>
            </button>
        </template>
    </div>
</template>

<script>
export default {
    name: 'ShowcaseProjectColumn',
    props: {
        category: {
            type: String,
            required: true,
        },
        projects: {
            type: Array,
            required: true,
        },
        title: {
            type: String,
            required: true,
        },
        moreLabel: {
            type: String,
            default: null,
        },
    },
    data() {
        return {
            showAll: false,
        };
    },
    computed: {
        featured() {
            return this.projects.filter((project) => project.featured).slice(0, 2);
        },
        rest() {
            return this.projects.filter((project) => !this.featured.includes(project));
        },
        disclosureLabel() {
            if (this.showAll) {
                return 'Show less';
            }

            return `${this.rest.length} more ${this.disclosureNoun}`;
        },
        disclosureNoun() {
            const noun = this.moreLabel ?? `${this.category} project`;

            return this.rest.length === 1 ? noun : `${noun}s`;
        },
        restId() {
            return `${this.category.replace(/[^a-z0-9]+/gi, '-').toLowerCase()}-more-projects`;
        },
    },
};
</script>
