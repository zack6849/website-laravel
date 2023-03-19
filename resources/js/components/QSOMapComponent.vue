<template>
    <div>
        <div v-show="!state.loaded">
            Please wait, map loading...
        </div>
        <div id="map"/>
    </div>
</template>
<script>
import {Map, Popup} from 'maplibre-gl';
import {shallowRef, onMounted, onUnmounted, markRaw, reactive} from 'vue';
import {isMapboxURL, transformMapboxUrl} from 'maplibregl-mapbox-request-transformer'

export default {

    setup(props) {
        const mapContainer = shallowRef('map');
        const map = shallowRef(null);
        const state = reactive({
            loaded: false
        })


        const transformRequest = (url, resourceType) => {
            if (isMapboxURL(url)) {
                return transformMapboxUrl(url, resourceType, props.mapboxKey)
            }

            // Do any other transforms you want
            return {url}
        }

        onMounted(() => {

            const initialState = {lng: -82.42480, lat: 27.48750, zoom: 4};

            map.value = markRaw(new Map({
                container: mapContainer.value,
                style: `mapbox://styles/mapbox/outdoors-v12`,
                center: [initialState.lng, initialState.lat],
                zoom: initialState.zoom,
                transformRequest
            }));
            map.value.on('load', () => {
                state.loaded = true;
                onMapLoaded(map.value);
            });

        });
        onUnmounted(() => {
            map.value?.remove();
        });

        function onMapLoaded(map) {
            map.loadImage('/img/map-pin.png', function (error, image) {
                map.addImage('pin', image)
            });
            map.addSource('qsos', {
                type: 'geojson',
                data: '/api/radio/qsos/band/20m/mode/SSB',
                cluster: false,
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

            })

            map.addLayer({
                'id': 'qsos',
                'type': 'symbol',
                'source': 'qsos',
                'layout': {
                    'icon-image': 'pin',
                    'icon-size': 0.025,
                    "icon-allow-overlap": true,
                }
            });
        }

        return {
            map, mapContainer, state
        };
    },
    props: {
        mapboxKey: String,
    }
}

</script>
<style>
#map {
    min-height: 60vh;
}
</style>
