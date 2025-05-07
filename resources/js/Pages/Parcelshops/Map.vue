<script setup>
import {ref, onMounted, toRaw} from 'vue';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import FormBar from '@/Components/Parcelshops/FormBar.vue';

const defaultZoom = ref(13);
const map = ref(null);
const markers = [];

const props = defineProps({
    locations: Object,
    carriers: Array,
    icons: Object,
    defaultMarkerIcon: String,
    latitude: Number,
    longitude: Number,
    postal: String,
    number: Number,
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

const closedMessage = 'closed';

const weekdayView = (weekday, message) => {
    return `
        <div class="flex justify-between">
            <div>
                ${weekday}
            </div>
            <div class="ml-2">
                ${message ?? closedMessage}
            </div>
        </div>
    `;
};

const locationView = location => {
    return `
        <div>
            <h3 class="text-sm font-bold">${location.name}</h3>
            <div>
                <div class="my-3 flex flex-col justify-between">
                    <div>
                        ${location.street} ${location.number}
                    </div>
                    <div>
                        ${location.postal_code} ${location.city}
                    </div>
                </div>
                <div class="flex flex-col">
                    ${weekdayView('Monday', location.monday)}
                    ${weekdayView('Tuesday', location.tuesday)}
                    ${weekdayView('Wednesday', location.wednesday)}
                    ${weekdayView('Thursday', location.thursday)}
                    ${weekdayView('Friday', location.friday)}
                    ${weekdayView('Saturday', location.saturday)}
                    ${weekdayView('Sunday', location.sunday)}
                </div>
            </div>
        </div>
    `;
}

const addMarker = location => {
    const marker = new L.Marker([location.latitude, location.longitude], {
        title: location.name,
        icon: L.icon(iconOptions(props.icons[location.carrier]))
    }).addTo(toRaw(map.value)).bindPopup(locationView(location));

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
