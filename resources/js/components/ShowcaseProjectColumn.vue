<template>
    <div class="space-y-4">
        <project-card
            v-for="(project, index) in featured"
            :key="`${category}-featured-${project.name}-${index}`"
            :project="project"
        />
        <template v-if="rest.length">
            <div v-show="showAll" class="space-y-4">
                <project-card
                    v-for="(project, index) in rest"
                    :key="`${category}-rest-${project.name}-${index}`"
                    :project="project"
                />
            </div>
            <button
                type="button"
                @click="showAll = !showAll"
                class="w-full rounded border border-dashed border-gray-300 py-2 text-sm text-gray-500 hover:border-gray-400 hover:text-gray-700"
            >
                <template v-if="showAll">
                    Show fewer {{ category }} projects <i class="fas fa-chevron-up text-xs"></i>
                </template>
                <template v-else>
                    Show {{ rest.length }} more {{ category }} project{{ rest.length === 1 ? '' : 's' }} <i class="fas fa-chevron-down text-xs"></i>
                </template>
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
    },
    data() {
        return {
            showAll: false,
        };
    },
    computed: {
        featured() {
            return this.projects.filter((project) => project.featured);
        },
        rest() {
            return this.projects.filter((project) => !project.featured);
        },
    },
};
</script>
