<x-base-page title="Radio Contacts" vue="true">
    @push('styles')
        <link
            href="https://api.tiles.mapbox.com/mapbox-gl-js/v0.53.0/mapbox-gl.css"
            rel="stylesheet"
        />
    @endpush
    <qso-map
        mapbox-key="{{config('services.mapbox.token')}}"
        :config="@js($mapConfig)"
        :qth="@js($qth)"
    />
</x-base-page>
