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
                <!--
                    On mobile this wrapper is `display: contents`, so its children (featured/personal/archive)
                    become direct items of the outer grid and can interleave with the professional column via
                    `order`. On desktop it becomes a real flex column instead, so it's a single grid item again
                    (independently sized from the professional column) — otherwise the outer grid shares row
                    heights across both columns and expanding one column's fold blows out empty space in the
                    other.
                -->
                <div v-if="activeContext !== 'professional'" class="contents lg:order-1 lg:col-start-1 lg:flex lg:flex-col lg:gap-6">
                    <div v-if="personalFeatured" class="order-1">
                        <project-card :project="personalFeatured" />
                    </div>

                    <showcase-project-column
                        v-if="personalNonArchive.length"
                        class="order-3"
                        category="personal"
                        :projects="personalNonArchive"
                    />

                    <showcase-project-column
                        v-if="personalArchive.length"
                        class="order-4"
                        category="archived"
                        :projects="personalArchive"
                    />
                </div>

                <showcase-project-column
                    v-if="activeContext !== 'personal'"
                    class="order-2 lg:order-2"
                    category="professional"
                    :projects="categorizedProjects.professional"
                />
            </div>
        </div>
    </div>
</template>

<script>
import ShowcaseProjectColumn from './ShowcaseProjectColumn.vue';

export default {
    name: 'Showcase',
    components: {
        ShowcaseProjectColumn,
    },
    props: {
        categories: {
            type: Object,
            required: true,
        },
        projects: {
            type: Object,
            required: true,
        },
    },
    data() {
        return {
            activeContext: 'all',
            options: [
                { value: 'all', label: 'All' },
                { value: 'personal', label: 'Personal' },
                { value: 'professional', label: 'Professional' },
            ],
        };
    },
    computed: {
        categorizedProjects() {
            const categories = this.projects?.categories ?? {};

            return {
                professional: Array.isArray(categories.professional) ? categories.professional : [],
                personal: Array.isArray(categories.personal) ? categories.personal : [],
            };
        },
        personalFeatured() {
            return this.categorizedProjects.personal.find((project) => project.featured) ?? null;
        },
        personalNonArchive() {
            return this.categorizedProjects.personal.filter((project) => !project.featured && project.status !== 'Retired');
        },
        personalArchive() {
            return this.categorizedProjects.personal.filter((project) => !project.featured && project.status === 'Retired');
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
