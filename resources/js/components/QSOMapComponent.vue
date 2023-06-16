<template>
    <div>
        <div v-show="!this.loaded">
            Please wait, map loading...
        </div>
        <div class="flex justify-between bg-gray-200 p-2">
            <h2>Showing <span v-text="this.qsoCount"/> QSOs</h2>
            <div id="controls">
                <select class="form-control" v-model="currentBand">
                    <option v-for="band in bands" :value="band" :key="band" v-text="band">
                    </option>
                </select>
                <select v-model="currentMode">
                    <option v-for="mode in modes" :value="mode" :key="mode" v-text="mode">
                    </option>
                </select>
            </div>
        </div>
        <div id="map"/>
    </div>
</template>
<script>
import {Map, Popup} from 'maplibre-gl';
import {isMapboxURL, transformMapboxUrl} from 'maplibregl-mapbox-request-transformer'

export default {
    props: ['mapboxKey', 'config'],
    data() {
        return {
            loaded: false,
            contacts: [],
            bands: [],
            modes: [],
            mapObject: {},
            currentMode: 'SSB',
            currentBand: '20M',
        }
    },
    mounted() {
        this.initMap();
        this.fetchBands();
        this.fetchModes();
    },
    methods: {
        initMap() {
            const transformRequest = (url, resourceType) => {
                if (isMapboxURL(url)) {
                    return transformMapboxUrl(url, resourceType, this.mapboxKey)
                }
                // Do any other transforms you want
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
            axios.get(this.apiUrl).then(response => {
                //we should have photos.
                if (response.status === 200) {
                    this.contacts = response.data;
                    this.updateMap();
                }
            });
        },
        fetchBands() {
            axios.get('api/radio/bands').then(response => {
                //we should have photos.
                if (response.status === 200) {
                    this.bands = response.data.sort();
                }
            });
        },
        fetchModes() {
            axios.get('api/radio/modes').then(response => {
                //we should have photos.
                if (response.status === 200) {
                    this.modes = response.data;
                }
            });
        },
        updateMap() {
            this.mapObject.getSource('qsos').setData(this.contacts);
        },
        onMapLoad(map) {
            map.loadImage('/img/map-pin.png', (error, image) => {
                map.addImage('pin', image)
            });
            map.loadImage('/img/pota-logo.png', (error, image) => {
                if(error){
                    console.log(error);
                }
                map.addImage('tree', image)
            });
            map.addSource('qsos', {
                type: 'geojson',
                data: {
                    type: 'FeatureCollection',
                    features: []
                },
            });
            map.addLayer({
                'id': 'qsos',
                'type': 'symbol',
                'source': 'qsos',
                'layout': {
                    'icon-image': '{icon}',
                    'icon-size':{ type: 'identity', property: 'icon_size' } ,
                    "icon-allow-overlap": true,
                }
            });
            map.on('click', 'qsos', function (e) {
                var coordinates = e.features[0].geometry.coordinates.slice();
                var description = e.features[0].properties.description;
                // Ensure that if the map is zoomed out such that multiple
                // copies of the feature are visible, the popup appears
                // over the copy being pointed to.
                while (Math.abs(e.lngLat.lng - coordinates[0]) > 180) {
                    coordinates[0] += e.lngLat.lng > coordinates[0] ? 360 : -360;
                }
                new Popup()
                    .setLngLat(coordinates)
                    .setHTML(description)
                    .addTo(map);
            });
            this.loadQsos();
        }
    },
    watch: {
        currentBand: function (val) {
            this.loadQsos();
        },
        currentMode: function (val) {
            this.loadQsos();
        }
    },
    computed: {
        apiUrl() {
            return `/api/radio/qsos/band/${this.currentBand}/mode/${this.currentMode}`;
        },
        qsoCount() {
            return this.contacts?.features?.length ?? '...';
        }
    }
}

</script>
<style>
#map {
    min-height: 80vh;
}
</style>
