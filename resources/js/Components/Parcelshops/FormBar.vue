<script setup>
import {defineProps} from 'vue';

const props = defineProps({
    carriers: {
        type: String
    }
});
</script>

<template>
    <div class="flex">
        <div class="">
            <span>Latitude</span>
            <input type="text">
        </div>
        <div class="ml-2">
            <span>Longitude</span>
            <input type="text">
        </div>
        <div class="ml-2">
            <span>Postal</span>
            <input type="text">
        </div>
        <div class="ml-2">
            <span>Carrier</span>
            <select>
                <option v-for="carrier of carriers" v-text="carrier" :value="carrier"></option>
            </select>
        </div>
    </div>
</template>
