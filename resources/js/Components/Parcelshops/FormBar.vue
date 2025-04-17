<script setup>
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputLabel from "@/Components/InputLabel.vue";
import SelectOption from "@/Components/SelectOption.vue";
import {router} from "@inertiajs/vue3";
import NumberInput from "@/Components/NumberInput.vue";

const props = defineProps({
    carriers: Array,
    latitude: Number,
    longitude: Number,
    postal: String,
    number: Number,
    country: String,
    carrier: String,
    countries: Array
});

const emit = defineEmits('updateLocations');

const getAddress = async data => {

    const response = await axios.post('/locations', data);

    emit('updateLocations', response.data);

    return response.data;
}

const clicked = () => {

    const data = {
        country: props.country,
        latitude: props.latitude,
        longitude: props.longitude,
        postal: props.postal,
        number: props.number,
        carrier: props.carrier
    };

    getAddress(data);

    router.get('/map', data, {
        preserveState: true
    });
}
</script>

<template>
    <div class="flex flex-col">
        <div class="m-2 flex flex-col">
            <InputLabel value="Country"/>
            <SelectOption v-model="props.country" placeholder="Select a country..." :options="props.countries"/>
        </div>
        <div class="m-2 flex flex-col">
            <InputLabel value="Latitude"/>
            <NumberInput v-model="props.latitude"/>
        </div>
        <div class="m-2 flex flex-col">
            <InputLabel value="Longitude"/>
            <NumberInput v-model="props.longitude"/>
        </div>
        <div class="m-2 flex flex-col">
            <InputLabel value="Postal code"/>
            <TextInput v-model="props.postal"/>
        </div>
        <div class="m-2 flex flex-col">
            <InputLabel value="Number"/>
            <NumberInput v-model="props.number"/>
        </div>
        <div class="m-2 flex flex-col">
            <InputLabel value="Carrier"/>
            <SelectOption v-model="props.carrier" placeholder="Select a carrier..." :options="props.carriers"/>
        </div>
        <div class="m-2">
            <PrimaryButton @click.prevent="clicked" class="p-2 w-full justify-center">
                Submit
            </PrimaryButton>
        </div>
    </div>
</template>
