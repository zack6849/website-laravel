<template>
    <div class="showcase mb-6 rounded-xl border border-gray-200 bg-white p-4 shadow-md md:p-6">
        <div class="mb-8">
            <div class="mb-4 flex flex-col-reverse gap-3 md:flex-row md:items-center md:justify-between">
                <h3><span class="text-2xl">&#129520;</span> Tech I Work With</h3>
                <div class="w-full md:w-auto">
                    <div class="grid w-full grid-cols-3 rounded-full border border-gray-200 p-0.5 text-xs md:inline-flex md:w-auto">
                        <button
                            v-for="option in options"
                            :key="option.value"
                            type="button"
                            @click="activeContext = option.value"
                            class="rounded-full px-3 py-1 text-center"
                            :class="activeContext === option.value ? 'bg-brand-600 text-white glow' : 'text-gray-500 hover:text-gray-700'"
                        >
                            {{ option.label }}
                        </button>
                    </div>
                </div>
            </div>

            <p class="mb-3 flex items-center gap-3 text-xs text-gray-400">
                <span><i class="fas fa-arrow-trend-up text-amber-500"></i> growing interest</span>
                <span><i class="fas fa-arrow-trend-down text-gray-400"></i> fading from use</span>
            </p>

            <div class="divide-y divide-gray-100">
                <div
                    v-for="(items, category) in filteredCategories"
                    :key="category"
                    class="flex flex-wrap items-start gap-x-4 gap-y-2 py-3"
                >
                    <h4 class="w-28 shrink-0 pt-1 text-xs font-semibold uppercase tracking-wide text-gray-400">{{ category }}</h4>
                    <div class="flex flex-1 flex-wrap items-center gap-2">
                        <span
                            v-for="tech in items"
                            :key="tech.name"
                            class="inline-flex items-center gap-1.5 rounded-full"
                            :class="pillClasses(tech)"
                        >
                            <technology :name="tech.name" :image="tech.image"></technology>
                            <i v-if="tech.trend === 'up'" class="fas fa-arrow-trend-up text-amber-500 text-xs" aria-label="trending up"></i>
                            <i v-else-if="tech.trend === 'down'" class="fas fa-arrow-trend-down text-gray-400 text-xs" aria-label="trending down"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div>
            <div class="my-4">
                <h3><span class="text-2xl">&#128187;</span> Things I've built</h3>
                <p class="mt-1 text-sm text-gray-500">A few projects from my personal and professional work.</p>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:items-start" :class="activeContext === 'all' ? 'lg:grid-cols-2' : 'lg:grid-cols-1'">
                <div v-if="activeContext !== 'personal'" class="order-1 space-y-4">
                    <project-card
                        v-for="(project, index) in professionalFeatured"
                        :key="`professional-featured-${project.name}-${index}`"
                        :project="project"
                    />
                    <template v-if="professionalRest.length">
                        <div v-show="showAllProfessional" class="space-y-4">
                            <project-card
                                v-for="(project, index) in professionalRest"
                                :key="`professional-rest-${project.name}-${index}`"
                                :project="project"
                            />
                        </div>
                        <button
                            v-if="!showAllProfessional"
                            type="button"
                            @click="showAllProfessional = true"
                            class="w-full rounded border border-dashed border-gray-300 py-2 text-sm text-gray-500 hover:border-gray-400 hover:text-gray-700"
                        >
                            Show {{ professionalRest.length }} more professional project{{ professionalRest.length === 1 ? '' : 's' }} <i class="fas fa-chevron-down text-xs"></i>
                        </button>
                    </template>
                </div>
                <div v-if="activeContext !== 'professional'" class="order-2 space-y-4">
                    <project-card
                        v-for="(project, index) in personalFeatured"
                        :key="`personal-featured-${project.name}-${index}`"
                        :project="project"
                    />
                    <template v-if="personalRest.length">
                        <div v-show="showAllPersonal" class="space-y-4">
                            <project-card
                                v-for="(project, index) in personalRest"
                                :key="`personal-rest-${project.name}-${index}`"
                                :project="project"
                            />
                        </div>
                        <button
                            v-if="!showAllPersonal"
                            type="button"
                            @click="showAllPersonal = true"
                            class="w-full rounded border border-dashed border-gray-300 py-2 text-sm text-gray-500 hover:border-gray-400 hover:text-gray-700"
                        >
                            Show {{ personalRest.length }} more personal project{{ personalRest.length === 1 ? '' : 's' }} <i class="fas fa-chevron-down text-xs"></i>
                        </button>
                    </template>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'Showcase',
    props: {
        categories: {
            type: Object,
            required: true,
        },
        projects: {
            type: [Array, Object],
            required: true,
        },
    },
    data() {
        return {
            activeContext: 'all',
            showAllProfessional: false,
            showAllPersonal: false,
            options: [
                { value: 'all', label: 'All' },
                { value: 'personal', label: 'Personal' },
                { value: 'professional', label: 'Professional' },
            ],
        };
    },
    computed: {
        professionalFeatured() {
            return this.filteredProjects.professional.filter((project) => project.featured);
        },
        professionalRest() {
            return this.filteredProjects.professional.filter((project) => !project.featured);
        },
        personalFeatured() {
            return this.filteredProjects.personal.filter((project) => project.featured);
        },
        personalRest() {
            return this.filteredProjects.personal.filter((project) => !project.featured);
        },
        categorizedProjects() {
            if (Array.isArray(this.projects)) {
                return {
                    professional: this.projects.filter((project) => project.context === 'professional'),
                    personal: this.projects.filter((project) => project.context !== 'professional'),
                };
            }

            const categories = this.projects?.categories ?? {};

            return {
                professional: Array.isArray(categories.professional) ? categories.professional : [],
                personal: Array.isArray(categories.personal) ? categories.personal : [],
            };
        },
        filteredProjects() {
            if (this.activeContext === 'all') {
                return this.categorizedProjects;
            }

            if (this.activeContext === 'professional') {
                return {
                    professional: this.categorizedProjects.professional,
                    personal: [],
                };
            }

            return {
                professional: [],
                personal: this.categorizedProjects.personal,
            };
        },
        visibleTechNames() {
            if (this.activeContext === 'all') {
                return null;
            }

            const projects = this.categorizedProjects[this.activeContext] ?? [];
            return new Set(
                projects
                    .flatMap((project) => project.tech ?? [])
                    .map((name) => name.toLowerCase())
            );
        },
        filteredCategories() {
            if (!this.visibleTechNames) {
                return this.categories;
            }

            return Object.entries(this.categories).reduce((acc, [category, items]) => {
                const filteredItems = items.filter((tech) => this.visibleTechNames.has(tech.name.toLowerCase()));

                if (filteredItems.length > 0) {
                    acc[category] = filteredItems;
                }

                return acc;
            }, {});
        },
    },
    methods: {
        pillClasses(tech) {
            if (tech.tier === 1) {
                return 'text-sm font-medium text-brand-900 bg-brand-50 border border-brand-200 pl-1.5 pr-3 py-1';
            }
            if (tech.tier === 2) {
                return 'text-sm text-gray-600 bg-gray-100 px-3 py-1';
            }
            return 'text-xs text-gray-400 px-1';
        },
    },
};
</script>
