<template>
    <div class="space-y-4">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:gap-4">
            <div class="grow text-gray-500">
                <p>Searchable amateur radio contacts from my logbook.</p>
                <p class="text-sm" v-text="lastImportSummary" />
            </div>
            <a @click.prevent="toggleHelp" class="btn-primary inline-block shrink-0 self-start sm:self-auto">
                What's This?
            </a>
        </div>

        <p v-if="showHelp" class="p-2">
            One of my hobbies is <a target="_blank" rel="noopener noreferrer" href="https://en.wikipedia.org/wiki/Amateur_radio" class="text-brand-700 underline hover:text-brand-900">Ham Radio</a>. This logbook shows stations and parks I have contacted, with a map view for each contact.
        </p>

        <div class="border border-gray-200 bg-white p-3">
            <div class="grid gap-3 lg:grid-cols-[minmax(16rem,1fr)_10rem_10rem_14rem_auto] lg:items-end">
                <label class="block">
                    <span class="block text-sm font-semibold text-gray-700">Search logbook</span>
                    <input
                        v-model.trim="searchTerm"
                        type="search"
                        class="form-control mt-1 w-full"
                        placeholder="Callsign, grid, country, comments"
                    />
                </label>

                <label class="block">
                    <span class="block text-sm font-semibold text-gray-700">Band</span>
                    <select class="form-control mt-1 w-full" v-model="currentBand">
                        <option v-for="band in bandOptions" :value="band" :key="band" v-text="band" />
                    </select>
                </label>

                <label class="block">
                    <span class="block text-sm font-semibold text-gray-700">Mode</span>
                    <select class="form-control mt-1 w-full" v-model="currentMode">
                        <option v-for="mode in modeOptions" :value="mode" :key="mode" v-text="mode" />
                    </select>
                </label>

                <label class="block">
                    <span class="block text-sm font-semibold text-gray-700">Sort</span>
                    <select class="form-control mt-1 w-full" v-model="currentSort">
                        <option
                            v-for="option in sortOptions"
                            :value="option.value"
                            :key="option.value"
                            v-text="option.label"
                        />
                    </select>
                </label>

                <div class="text-sm lg:text-right">
                    <button
                        v-if="hasActiveFilters"
                        type="button"
                        class="text-brand-700 underline hover:text-brand-900"
                        @click="clearFilters"
                    >
                        Reset view
                    </button>
                </div>
            </div>
        </div>

        <div v-show="!loaded" class="text-gray-500">
            Please wait, map loading...
        </div>

        <div class="space-y-4">
            <section id="qso-map-shell" class="min-w-0 border border-gray-200 bg-white">
                <div class="flex items-center justify-between border-b border-gray-200 px-3 py-2">
                    <h2 class="text-base font-semibold text-gray-900">Map View</h2>
                    <span class="text-sm text-gray-500">Newer contacts are brighter</span>
                </div>
                <div id="map" />
            </section>

            <section class="min-w-0 border border-gray-200 bg-white">
                <div class="flex items-center justify-between border-b border-gray-200 px-3 py-2">
                    <h2 class="text-base font-semibold text-gray-900">Logbook Contacts</h2>
                    <span v-if="loadingQsos" class="text-sm text-gray-500">Loading...</span>
                </div>

                <div class="qso-table-scroll overflow-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
                        <thead class="sticky top-0 bg-gray-50 text-xs uppercase text-gray-500">
                            <tr>
                                <th scope="col" class="px-3 py-2 font-semibold">Date</th>
                                <th scope="col" class="px-3 py-2 font-semibold">Station</th>
                                <th scope="col" class="px-3 py-2 font-semibold">Band</th>
                                <th scope="col" class="px-3 py-2 font-semibold">Mode</th>
                                <th scope="col" class="px-3 py-2 font-semibold">Grid</th>
                                <th scope="col" class="px-3 py-2 font-semibold">Distance</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            <tr
                                v-for="(contact, index) in tableContacts"
                                :id="contactRowId(contact)"
                                :key="contactKey(contact, index)"
                                :class="rowClass(contact)"
                                class="cursor-pointer transition-colors hover:bg-brand-50"
                                @click="selectContactFromTable(contact)"
                                @mouseenter="highlightFeature(contact)"
                                @mouseleave="clearHighlight"
                            >
                                <td class="whitespace-nowrap px-3 py-3">
                                    <div class="text-gray-700" v-text="formatDate(contact.properties.qso_date)" />
                                    <div class="text-xs text-gray-500" v-text="formatRelativeAge(contact.properties.age_days)" />
                                </td>
                                <td class="px-3 py-3">
                                    <div class="font-semibold text-gray-900" v-text="contact.properties.to_callsign" />
                                    <div class="text-xs text-gray-500" v-text="contact.properties.display_location || contact.properties.to_country" />
                                    <div v-if="contact.properties.category === 'POTA'" class="mt-1 inline-block bg-emerald-100 px-2 py-0.5 text-xs font-semibold text-emerald-800">
                                        POTA
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-3 py-3 text-gray-700" v-text="contact.properties.band" />
                                <td class="whitespace-nowrap px-3 py-3 text-gray-700" v-text="contact.properties.mode" />
                                <td class="whitespace-nowrap px-3 py-3 text-gray-700" v-text="contact.properties.to_grid || '-'" />
                                <td class="whitespace-nowrap px-3 py-3 text-gray-700">
                                    <div class="flex items-center gap-2">
                                        <span
                                            v-if="hasBearing(contact.properties)"
                                            class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-gray-100 text-xs text-gray-700"
                                            :title="formatBearingTitle(contact.properties)"
                                        >
                                            <span
                                                class="direction-arrow"
                                                :style="directionArrowStyle(contact.properties.bearing_degrees)"
                                                aria-hidden="true"
                                            >↑</span>
                                            <span class="sr-only" v-text="formatBearingTitle(contact.properties)" />
                                        </span>
                                        <span
                                            v-text="formatDistance(
                                                contact.properties.distance,
                                                contact.properties.bearing_cardinal,
                                                contact.properties.distance_estimated
                                            )"
                                        />
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="tableContacts.length === 0">
                                <td colspan="6" class="px-3 py-6 text-center text-gray-500">
                                    No contacts found.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-gray-200 px-3 py-2 text-right text-sm text-gray-600">
                    <div class="font-semibold text-gray-900" v-text="resultSummary" />
                    <div v-if="isResultLimited" class="text-xs text-gray-500">
                        First <span v-text="resultMeta.limit" /> by <span v-text="currentSortLabel" />
                    </div>
                </div>
            </section>
        </div>
    </div>
</template>

<script>
import {Map, Popup} from 'maplibre-gl';
import {isMapboxURL, transformMapboxUrl} from 'maplibregl-mapbox-request-transformer'
import {escape} from 'lodash';
import RelativeUTCTime from '../support/RelativeUTCTime';

const EMPTY_FEATURE_COLLECTION = {
    type: 'FeatureCollection',
    features: [],
};

const EMPTY_HIGHLIGHT_FILTER = ['==', ['to-string', ['get', 'id']], '__none__'];
const DEFAULT_BAND = '20M';
const DEFAULT_MODE = 'SSB';
const DEFAULT_SORT = 'newest';
const CONTACT_LIMIT = 200;
const DISTANCE_FORMATTER = new Intl.NumberFormat(undefined, {
    maximumFractionDigits: 0,
});
const SORT_OPTIONS = [
    {value: 'newest', label: 'Newest'},
    {value: 'oldest', label: 'Oldest'},
    {value: 'distance_desc', label: 'Longest distance (DX)'},
    {value: 'distance_asc', label: 'Shortest distance'},
];

export default {
    name: 'QSOMapComponent',
    props: ['mapboxKey', 'config'],
    data() {
        return {
            loaded: false,
            loadingQsos: false,
            contacts: EMPTY_FEATURE_COLLECTION,
            bands: [],
            modes: [],
            mapObject: null,
            activePopup: null,
            currentMode: DEFAULT_MODE,
            currentBand: DEFAULT_BAND,
            currentSort: DEFAULT_SORT,
            sortOptions: SORT_OPTIONS,
            searchTerm: '',
            resultMeta: {
                total: 0,
                returned: 0,
                limit: CONTACT_LIMIT,
                last_imported_at: null,
            },
            selectedContactId: null,
            hoveredContactId: null,
            showHelp: false,
            searchTimeout: null,
            loadRequestId: 0,
        }
    },
    mounted() {
        this.initMap();
        this.fetchBands();
        this.fetchModes();
    },
    beforeUnmount() {
        clearTimeout(this.searchTimeout);
        this.activePopup?.remove();
        this.mapObject?.remove();
    },
    methods: {
        initMap() {
            const transformRequest = (url, resourceType) => {
                if (isMapboxURL(url)) {
                    return transformMapboxUrl(url, resourceType, this.mapboxKey)
                }

                return {url}
            }

            this.mapObject = new Map({
                container: 'map',
                style: `mapbox://styles/mapbox/outdoors-v12`,
                center: [this.config.lng, this.config.lat],
                zoom: this.config.zoom,
                transformRequest,
            });
            this.mapObject.on('load', () => {
                this.loaded = true;
                this.onMapLoad(this.mapObject)
            });
        },
        loadQsos() {
            const requestId = ++this.loadRequestId;
            this.loadingQsos = true;

            axios.get(this.apiUrl).then(response => {
                if (requestId !== this.loadRequestId) {
                    return;
                }

                if (response.status === 200) {
                    this.contacts = response.data ?? EMPTY_FEATURE_COLLECTION;
                    this.resultMeta = response.data?.meta ?? {
                        total: this.contacts?.features?.length ?? 0,
                        returned: this.contacts?.features?.length ?? 0,
                        limit: CONTACT_LIMIT,
                        last_imported_at: null,
                    };
                    this.updateMap();
                    this.clearMissingSelection();
                }
            }).catch(error => {
                console.error(error);
            }).finally(() => {
                if (requestId === this.loadRequestId) {
                    this.loadingQsos = false;
                }
            });
        },
        queueLoadQsos() {
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => this.loadQsos(), 250);
        },
        fetchBands() {
            axios.get('/api/radio/bands').then(response => {
                if (response.status === 200) {
                    this.bands = response.data.filter(Boolean);
                }
            });
        },
        fetchModes() {
            axios.get('/api/radio/modes').then(response => {
                if (response.status === 200) {
                    this.modes = response.data.filter(Boolean);
                }
            });
        },
        updateMap() {
            const source = this.mapObject?.getSource?.('qsos');

            if (source) {
                source.setData(this.contacts);
            }

            this.updateMapHighlight();
        },
        onMapLoad(map) {
            map.loadImage('/img/map-pin.png', (error, image) => {
                if (error) {
                    console.error(error);
                    return;
                }

                if (! map.hasImage('pin')) {
                    map.addImage('pin', image)
                }
            });
            map.loadImage('/img/pota-logo.png', (error, image) => {
                if (error) {
                    console.error(error);
                    return;
                }

                if (! map.hasImage('tree')) {
                    map.addImage('tree', image)
                }
            });
            map.addSource('qsos', {
                type: 'geojson',
                data: EMPTY_FEATURE_COLLECTION,
            });

            const recencyScore = () => ['to-number', ['get', 'recency_score'], 0.35];
            map.addLayer({
                'id': 'qso-recency',
                'type': 'circle',
                'source': 'qsos',
                'paint': {
                    'circle-radius': [
                        'interpolate',
                        ['linear'],
                        recencyScore(),
                        0, 4,
                        0.5, 9,
                        1, 18,
                    ],
                    'circle-color': [
                        'interpolate',
                        ['linear'],
                        recencyScore(),
                        0, '#64748b',
                        0.55, '#fbbf24',
                        1, '#f97316',
                    ],
                    'circle-opacity': [
                        'interpolate',
                        ['linear'],
                        recencyScore(),
                        0, 0.08,
                        0.5, 0.18,
                        1, 0.4,
                    ],
                    'circle-blur': [
                        'interpolate',
                        ['linear'],
                        recencyScore(),
                        0, 0.9,
                        1, 1.35,
                    ],
                    'circle-stroke-color': '#ffffff',
                    'circle-stroke-opacity': [
                        'interpolate',
                        ['linear'],
                        recencyScore(),
                        0, 0,
                        1, 0.55,
                    ],
                    'circle-stroke-width': [
                        'interpolate',
                        ['linear'],
                        recencyScore(),
                        0, 0,
                        1, 1.5,
                    ],
                },
            });
            map.addLayer({
                'id': 'qso-highlight',
                'type': 'circle',
                'source': 'qsos',
                'filter': EMPTY_HIGHLIGHT_FILTER,
                'paint': {
                    'circle-radius': 24,
                    'circle-color': '#0f766e',
                    'circle-opacity': 0.18,
                    'circle-stroke-color': '#0f766e',
                    'circle-stroke-opacity': 0.65,
                    'circle-stroke-width': 2,
                },
            });
            map.addLayer({
                'id': 'qsos',
                'type': 'symbol',
                'source': 'qsos',
                'layout': {
                    'icon-image': '{icon}',
                    'icon-size': [
                        '*',
                        ['to-number', ['get', 'icon_size'], 0.025],
                        [
                            'interpolate',
                            ['linear'],
                            recencyScore(),
                            0, 0.85,
                            0.6, 1,
                            1, 1.15,
                        ],
                    ],
                    'icon-allow-overlap': true,
                },
                'paint': {
                    'icon-opacity': [
                        'interpolate',
                        ['linear'],
                        recencyScore(),
                        0, 0.35,
                        0.5, 0.7,
                        1, 1,
                    ],
                }
            });

            const qsoLayers = ['qsos', 'qso-highlight', 'qso-recency'];
            map.on('click', (event) => {
                const features = map.queryRenderedFeatures(event.point, {layers: qsoLayers});
                if (features.length === 0) {
                    return;
                }

                const feature = features.find((feature) => feature.layer.id === 'qsos') ?? features[0];
                this.selectContact(feature, {
                    scrollRow: true,
                    popupLngLat: event.lngLat,
                });
            });
            map.on('mousemove', (event) => {
                const features = map.queryRenderedFeatures(event.point, {layers: qsoLayers});
                map.getCanvas().style.cursor = features.length > 0 ? 'pointer' : '';
            });
            map.on('mouseout', () => {
                map.getCanvas().style.cursor = '';
            });
            this.loadQsos();
        },
        selectContactFromTable(feature) {
            this.selectContact(feature, {
                fly: true,
                scrollMap: true,
            });
        },
        selectContact(feature, options = {}) {
            this.selectedContactId = this.contactId(feature);
            this.updateMapHighlight();

            if (options.scrollRow) {
                this.$nextTick(() => {
                    document.getElementById(this.contactRowId(feature))?.scrollIntoView({
                        block: 'nearest',
                        behavior: 'smooth',
                    });
                });
            }

            if (options.scrollMap) {
                this.$nextTick(() => {
                    document.getElementById('qso-map-shell')?.scrollIntoView({
                        block: 'start',
                        behavior: 'smooth',
                    });
                });
            }

            this.focusFeatureOnMap(feature, options);
        },
        focusFeatureOnMap(feature, options = {}) {
            if (! this.mapObject || ! feature?.geometry?.coordinates) {
                return;
            }

            const coordinates = this.popupCoordinates(feature, options.popupLngLat);

            if (options.fly) {
                this.mapObject.flyTo({
                    center: coordinates,
                    zoom: Math.max(this.mapObject.getZoom(), 5),
                    essential: true,
                });
            }

            this.openPopup(feature, coordinates);
        },
        popupCoordinates(feature, lngLat = null) {
            const coordinates = feature.geometry.coordinates.slice();

            if (lngLat !== null) {
                while (Math.abs(lngLat.lng - coordinates[0]) > 180) {
                    coordinates[0] += lngLat.lng > coordinates[0] ? 360 : -360;
                }
            }

            return coordinates;
        },
        openPopup(feature, coordinates) {
            this.activePopup?.remove();
            this.activePopup = new Popup({closeOnClick: false})
                .setLngLat(coordinates)
                .setHTML(this.buildPopupDescription(feature.properties))
                .addTo(this.mapObject);
        },
        buildPopupDescription(properties) {
            const mode = escape(properties.mode);
            const band = escape(properties.band);
            const toCallsign = escape(properties.to_callsign);
            const location = escape(properties.display_location ?? properties.to_country ?? '');
            const date = escape(properties.qso_date ?? properties.created_at ?? '');
            const frequency = escape(properties.frequency);
            const rstReceived = escape(properties.rst_received);
            const toGrid = escape(properties.to_grid);
            const distance = escape(this.formatDistance(
                properties.distance,
                properties.bearing_cardinal,
                properties.distance_estimated,
            ));
            const comments = String(properties.comments ?? '').trim();

            let html = '<div>';
            html += `<div><b>${mode} QSO w/ ${toCallsign}</b></div>`;
            html += `<div><b>Date:</b> ${date}</div>`;
            html += `<div><b>Band:</b> ${band}</div>`;
            html += `<div><b>Frequency:</b> ${frequency} MHz</div>`;

            if (location !== '') {
                html += `<div><b>Location:</b> ${location}</div>`;
            }
            if (rstReceived !== '') {
                html += `<div><b>RST Received:</b> ${rstReceived}</div>`;
            }
            if (toGrid !== '') {
                html += `<div><b>Grid:</b> ${toGrid}</div>`;
            }
            if (distance !== '-') {
                html += `<div><b>Distance:</b> ${distance}</div>`;
            }
            if (comments !== '') {
                html += `<div><b>Comments:</b> ${escape(comments)}</div>`;
            }

            html += '</div>';
            return html;
        },
        highlightFeature(feature) {
            this.hoveredContactId = this.contactId(feature);
            this.updateMapHighlight();
        },
        clearHighlight() {
            this.hoveredContactId = null;
            this.updateMapHighlight();
        },
        updateMapHighlight() {
            if (! this.mapObject?.getLayer?.('qso-highlight')) {
                return;
            }

            const contactId = this.hoveredContactId || this.selectedContactId;
            this.mapObject.setFilter('qso-highlight', contactId
                ? ['==', ['to-string', ['get', 'id']], contactId]
                : EMPTY_HIGHLIGHT_FILTER
            );
        },
        clearMissingSelection() {
            if (
                this.selectedContactId !== null
                && ! this.tableContacts.some((feature) => this.contactId(feature) === this.selectedContactId)
            ) {
                this.selectedContactId = null;
                this.activePopup?.remove();
            }

            this.updateMapHighlight();
        },
        contactId(feature) {
            return String(feature?.properties?.id ?? '');
        },
        contactKey(feature, index) {
            return this.contactId(feature) || `${feature?.properties?.qso_date ?? 'contact'}-${index}`;
        },
        contactRowId(feature) {
            return `qso-row-${this.contactId(feature)}`;
        },
        rowClass(feature) {
            return {
                'bg-brand-100': this.contactId(feature) === this.selectedContactId,
                'bg-white': this.contactId(feature) !== this.selectedContactId,
            };
        },
        parseDate(value) {
            if (! value) {
                return null;
            }

            const date = new Date(String(value).replace(' ', 'T'));

            return Number.isNaN(date.getTime()) ? null : date;
        },
        formatDate(value) {
            const date = this.parseDate(value);

            if (date === null) {
                return value || '-';
            }

            return new Intl.DateTimeFormat(undefined, {
                month: 'short',
                day: 'numeric',
                year: 'numeric',
                hour: 'numeric',
                minute: '2-digit',
            }).format(date);
        },
        formatImportSummary(value) {
            return RelativeUTCTime.format(value);
        },
        formatRelativeAge(ageDays) {
            if (ageDays === null || ageDays === undefined || ageDays === '') {
                return '';
            }

            const days = Number(ageDays);

            if (! Number.isFinite(days)) {
                return '';
            }

            if (days < 1) {
                return 'today';
            }

            if (days === 1) {
                return '1 day ago';
            }

            if (days < 30) {
                return `${days} days ago`;
            }

            if (days < 365) {
                const months = Math.floor(days / 30);

                return months === 1 ? '1 month ago' : `${months} months ago`;
            }

            const years = Math.floor(days / 365);

            return years === 1 ? '1 year ago' : `${years} years ago`;
        },
        formatDistance(value, cardinal = null, estimated = false) {
            if (value === null || value === undefined || value === '') {
                return cardinal || '-';
            }

            const numericDistance = Number(value);
            const distance = Number.isFinite(numericDistance)
                ? DISTANCE_FORMATTER.format(numericDistance)
                : String(value);
            const direction = cardinal ? ` ${cardinal}` : '';
            const prefix = estimated ? '~' : '';

            return `${prefix}${distance} mi${direction}`;
        },
        hasBearing(properties) {
            return properties?.bearing_degrees !== null
                && properties?.bearing_degrees !== undefined
                && properties?.bearing_cardinal;
        },
        directionArrowStyle(degrees) {
            const bearing = Number(degrees);

            if (! Number.isFinite(bearing)) {
                return {};
            }

            return {
                transform: `rotate(${bearing}deg)`,
            };
        },
        formatBearingTitle(properties) {
            const bearing = Number(properties?.bearing_degrees);
            const cardinal = properties?.bearing_cardinal;

            if (! Number.isFinite(bearing) || ! cardinal) {
                return '';
            }

            return `Bearing ${cardinal} (${Math.round(bearing)}°)`;
        },
        clearFilters() {
            this.searchTerm = '';
            this.currentBand = DEFAULT_BAND;
            this.currentMode = DEFAULT_MODE;
            this.currentSort = DEFAULT_SORT;
        },
        toggleHelp() {
            this.showHelp = !this.showHelp;
        }
    },
    watch: {
        currentBand() {
            this.loadQsos();
        },
        currentMode() {
            this.loadQsos();
        },
        currentSort() {
            this.loadQsos();
        },
        searchTerm() {
            this.queueLoadQsos();
        }
    },
    computed: {
        apiUrl() {
            const params = new URLSearchParams({
                limit: String(CONTACT_LIMIT),
            });

            if (this.searchTerm !== '') {
                params.set('search', this.searchTerm);
            }

            params.set('sort', this.currentSort);

            return `/api/radio/qsos/band/${encodeURIComponent(this.currentBand)}/mode/${encodeURIComponent(this.currentMode)}?${params.toString()}`;
        },
        tableContacts() {
            return this.contacts?.features ?? [];
        },
        qsoCount() {
            return this.tableContacts.length;
        },
        resultSummary() {
            const total = Number(this.resultMeta?.total ?? this.qsoCount);

            if (this.isResultLimited) {
                return `Showing ${this.qsoCount} of ${total} contacts`;
            }

            return `${this.qsoCount} contacts`;
        },
        isResultLimited() {
            return Number(this.resultMeta?.total ?? 0) > this.qsoCount;
        },
        currentSortLabel() {
            return this.sortOptions.find((option) => option.value === this.currentSort)?.label ?? 'Newest';
        },
        lastImportSummary() {
            if (! this.resultMeta?.last_imported_at) {
                return 'Import status unknown';
            }

            return this.formatImportSummary(this.resultMeta.last_imported_at);
        },
        bandOptions() {
            return ['All', ...this.bands];
        },
        modeOptions() {
            return ['All', ...this.modes];
        },
        hasActiveFilters() {
            return this.searchTerm !== ''
                || this.currentBand !== DEFAULT_BAND
                || this.currentMode !== DEFAULT_MODE
                || this.currentSort !== DEFAULT_SORT;
        }
    }
}
</script>

<style>
#map {
    min-height: 68vh;
}

.qso-table-scroll {
    max-height: 54vh;
}

.direction-arrow {
    display: inline-block;
    line-height: 1;
    transform-origin: center;
}

@media (max-width: 1279px) {
    #map {
        min-height: 58vh;
    }

    .qso-table-scroll {
        max-height: 60vh;
    }
}
</style>
