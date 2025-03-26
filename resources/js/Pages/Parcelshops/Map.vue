<script setup>
import {ref, onMounted, toRaw} from 'vue';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import FormBar from '@/Components/Parcelshops/FormBar.vue';

const defaultZoom = ref(13);
const map = ref(null);
const markers = [];

const props = defineProps({
    locations: Array,
    carriers: Array,
    icons: Array,
    defaultMarkerIcon: String,
    parameters: Array,
    latitude: Number,
    longitude: Number,
    postal: String,
    number: String,
    country: String,
    countries: Array,
    selectedCarrier: String
});

const getAddressFromLatLng = async (latitude, longitude) => {
    const response = await axios.get('https://nominatim.openstreetmap.org/reverse', {
        params: {
            format: 'json',
            lat: latitude,
            lon: longitude,
        },
    });

    props.postal = response.data.address.postcode;
    props.number = response.data.address.house_number;
    props.country = response.data.address.country_code.toUpperCase();

    return response.data.address;
}

const addMarker = location => {
    const marker = new L.Marker([location.latitude, location.longitude], {
        title: location.name,
        icon: L.icon(iconOptions(props.icons[location.carrier]))
    }).addTo(toRaw(map.value)).bindPopup(location.name);

    markers.push(marker);
}

const updateLocations = locations => {
    for (const marker of markers) {
        marker.remove();
    }

    for (const location of locations) {
        addMarker(location);
    }
}

const iconOptions = icon => {
    return {
        iconUrl: icon,
        iconSize: [36, 51],
        iconAnchor: [18, 51],
        popupAnchor: [0, -51]
    }
};

onMounted(() => {
    map.value = L.map('map').setView([props.latitude, props.longitude], defaultZoom.value);
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map.value);

    const marker = new L.Marker([props.latitude, props.longitude], {
        title: 'Current location',
        draggable: true,
        icon: L.icon(iconOptions(props.defaultMarkerIcon))
    }).addTo(map.value).bindPopup(`<p>${props.latitude} ${props.longitude}</p>`);

    marker.on('dragend', event => {
        props.latitude = event.target._latlng.lat;
        props.longitude = event.target._latlng.lng;
        getAddressFromLatLng(event.target._latlng.lat, event.target._latlng.lng);
    });

    map.value.on('click', event => {
        props.latitude = event.latlng.lat;
        props.longitude = event.latlng.lng;
        marker.setLatLng([event.latlng.lat, event.latlng.lng]).update();
        getAddressFromLatLng(props.latitude, props.longitude);
    });

    for (const location of props.locations) {
        addMarker(location);
    }
});
</script>

<template>
    <div class="flex w-full">
        <FormBar
            :carriers="props.carriers"
            :latitude="props.latitude"
            :longitude="props.longitude"
            :postal="props.postal"
            :number="props.number"
            :country="props.country"
            :countries="props.countries"
            :carrier="props.selectedCarrier"
            @updateLocations="updateLocations"
        />
        <div id="map">
        </div>
    </div>
</template>

<style scoped>
#map {
    width: 100%;
    height: 100vh;
}
</style>
