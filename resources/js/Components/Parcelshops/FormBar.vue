<script setup>
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputLabel from "@/Components/InputLabel.vue";
import SelectOption from "@/Components/SelectOption.vue";
import {router} from "@inertiajs/vue3";

const props = defineProps({
    carriers: String,
    latitude: Number,
    longitude: Number,
    postal: String,
    number: Number,
    country: String,
    carrier: String,
    countries: Array
});

const emit = defineEmits('updateLocations');

const getAddress = async () => {

    const response = await axios.post('/locations', {
        carrier: props.carrier,
        country: props.country,
        latitude: props.latitude,
        longitude: props.longitude,
        postal: props.postal,
        number: props.number
    });

    emit('updateLocations', response.data);

    return response.data;
}

const clicked = (event) => {
    event.preventDefault();

    getAddress();

    router.get('/map', {
        country: props.country,
        latitude: props.latitude,
        longitude: props.longitude,
        postal: props.postal,
        number: props.number,
        carrier: props.carrier
    }, {
        preserveState: true
    });
}
</script>

<template>
    <div class="flex flex-col">
        <div class="m-2 flex flex-col">
            <InputLabel value="Country"/>
            <SelectOption v-model="props.country" placeholder="Select a country..." :options="props.countries" />
        </div>
        <div class="m-2 flex flex-col">
            <InputLabel value="Latitude"/>
            <TextInput v-model="props.latitude"/>
        </div>
        <div class="m-2 flex flex-col">
            <InputLabel value="Longitude"/>
            <TextInput v-model="props.longitude"/>
        </div>
        <div class="m-2 flex flex-col">
            <InputLabel value="Postal code"/>
            <TextInput v-model="props.postal"/>
        </div>
        <div class="m-2 flex flex-col">
            <InputLabel value="Number"/>
            <TextInput v-model="props.number"/>
        </div>
        <div class="m-2 flex flex-col">
            <InputLabel value="Carrier"/>
            <SelectOption v-model="props.carrier" placeholder="Select a carrier..." :options="props.carriers"/>
        </div>
        <div class="m-2">
            <PrimaryButton @click="clicked" class="p-2 w-full justify-center">
                Click
            </PrimaryButton>
        </div>
    </div>
</template>
