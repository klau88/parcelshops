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
    number: String,
    country: String,
    countries: Array,
    selectedCarrier: String
});

let latitude = ref(props.latitude);
let longitude = ref(props.longitude);
let postal = ref(props.postal);
let number = ref(props.number);
let country = ref(props.country);

const getAddressFromLatLng = async (latitude, longitude) => {
    const response = await axios.get('https://nominatim.openstreetmap.org/reverse', {
        params: {
            format: 'json',
            lat: latitude,
            lon: longitude,
        },
    });

    postal.value = response.data.address.postcode;
    number.value = response.data.address.house_number;
    country.value = response.data.address.country_code.toUpperCase();

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
        latitude.value = event.target._latlng.lat;
        longitude.value = event.target._latlng.lng;
        getAddressFromLatLng(latitude.value, longitude.value);
    });

    map.value.on('click', event => {
        latitude.value = event.latlng.lat;
        longitude.value = event.latlng.lng;
        marker.setLatLng([latitude.value, longitude.value]).update();
        getAddressFromLatLng(latitude.value, longitude.value);
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
            :latitude="latitude"
            :longitude="longitude"
            :postal="postal"
            :number="number"
            :country="country"
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
