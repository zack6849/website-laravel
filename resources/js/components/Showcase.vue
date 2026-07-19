<template>
    <div class="showcase space-y-12 md:space-y-16">
        <section>
            <div class="mb-4">
                <h3><span class="text-2xl">&#129520;</span> Technology Stack</h3>
            </div>

            <div class="mb-3 flex flex-wrap items-center gap-3 text-xs text-gray-500">
                <span
                    v-for="tier in tierLegend"
                    :key="tier.value"
                    class="inline-flex items-center gap-1.5"
                >
                    <span class="h-2.5 w-2.5 rounded-full border" :class="legendSwatchClasses(tier.value)"></span>
                    {{ tier.label }}
                </span>
            </div>

            <div class="divide-y divide-gray-100">
                <div
                    v-for="(items, category) in categories"
                    :key="category"
                    class="flex flex-wrap items-start gap-x-4 gap-y-2 py-3"
                >
                    <h4 class="w-full shrink-0 pt-1 text-xs font-semibold uppercase tracking-wide text-gray-400 sm:w-44">{{ category }}</h4>
                    <div class="flex flex-1 flex-wrap items-center gap-2">
                        <span
                            v-for="tech in items"
                            :key="tech.name"
                            class="inline-flex items-center gap-1.5 rounded-full"
                            :class="pillClasses(tech)"
                        >
                            <technology :name="tech.name" :image="tech.tier === 1 ? tech.image : null"></technology>
                        </span>
                    </div>
                </div>
            </div>

            <div v-if="exploring.length" class="mt-4 flex flex-wrap items-center gap-2 text-sm text-gray-500">
                <span class="font-medium text-gray-600">Currently exploring:</span>
                <span
                    v-for="tech in exploring"
                    :key="tech.name"
                    class="inline-flex items-center rounded border border-gray-200 bg-white px-2 py-0.5 text-gray-600"
                >
                    {{ tech.name }}
                </span>
            </div>
        </section>

        <section class="border-t border-gray-100 pt-12 md:pt-16">
            <div class="mb-4 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                <h3><span class="text-2xl">&#128187;</span> Selected Work</h3>
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
            <p class="mb-6 text-sm text-gray-500">Selected personal and professional work.</p>

            <div class="grid grid-cols-1 gap-6 lg:items-start" :class="activeContext === 'all' ? 'lg:grid-cols-2' : 'lg:grid-cols-1'">
                <showcase-project-column
                    v-if="activeContext !== 'personal'"
                    class="order-1"
                    category="professional"
                    title="Professional work"
                    more-label="professional project"
                    :projects="categorizedProjects.professional"
                />

                <showcase-project-column
                    v-if="activeContext !== 'professional'"
                    class="order-2"
                    category="personal"
                    title="Personal projects"
                    more-label="personal project"
                    :projects="categorizedProjects.personal"
                />
            </div>
        </section>
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
        exploring: {
            type: Array,
            default: () => [],
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
            tierLegend: [
                { value: 1, label: 'Primary' },
                { value: 2, label: 'Other production experience' },
                { value: 3, label: 'Earlier experience' },
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
    },
    methods: {
        pillClasses(tech) {
            if (tech.tier === 1) {
                return 'text-sm font-medium text-brand-900 bg-brand-50 border border-brand-200 pl-1.5 pr-3 py-1';
            }
            if (tech.tier === 2) {
                return 'text-sm text-gray-600 bg-gray-100 border border-gray-200 px-3 py-1';
            }
            return 'text-sm text-gray-500 bg-white border border-gray-300 px-3 py-1';
        },
        legendSwatchClasses(tier) {
            if (tier === 1) {
                return 'border-brand-200 bg-brand-100';
            }
            if (tier === 2) {
                return 'border-gray-200 bg-gray-200';
            }
            return 'border-gray-200 bg-white';
        },
    },
};
</script>
