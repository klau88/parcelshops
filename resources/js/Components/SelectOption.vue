<script setup>
const selectedCarrier = defineModel();

defineProps({
    carriers: {
        type: Array
    }
});
</script>

<template>
    <select v-model="selectedCarrier">
        <option v-for="carrier of carriers" v-text="carrier" :value="carrier"></option>
    </select>
</template>
