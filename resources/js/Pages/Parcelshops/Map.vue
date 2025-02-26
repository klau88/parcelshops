<script setup>
// import {LMap, LTileLayer, LMarker} from 'vue3-leaflet';
import {ref, onMounted} from 'vue';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import FormBar from "@/Components/Parcelshops/FormBar.vue";

const defaultLat = ref(51.860948);
const defaultLng = ref(4.394288);
const defaultZoom = ref(13);
const map = ref(null);

const props = defineProps({
    locations: {
        type: Array
    },
    carriers: {
        type: Array
    },
    icons: {
        type: Array
    },
    defaultMarkerIcon: {
        type: String
    }
});

onMounted(() => {
    map.value = L.map('map').setView([defaultLat.value, defaultLng.value], defaultZoom.value);
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map.value);

    const iconOptions = {
        title: 'Company name',
        // draggable: true,
        icon: L.icon({
            iconUrl: props.defaultMarkerIcon
        })
    }

    const marker = new L.Marker([defaultLat.value, defaultLng.value], iconOptions);
    marker.addTo(map.value)

    for (const location of props.locations) {
        new L.Marker([location.latitude, location.longitude], {
            title: location.name,
            icon: L.icon({
                iconUrl: props.icons[location.type]
            })
        }).addTo(map.value).bindPopup(location.name);
    }
});
</script>

<template>
    <FormBar :carriers="carriers"/>
    <div id="map">
    </div>
</template>

<style scoped>
#map {
    width: 100%;
    height: 100vh;
}
</style>
