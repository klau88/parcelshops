<script setup>
const model = defineModel();

defineProps({
    placeholder: String,
    options: Array
});
</script>

<template>
    <select v-model="model">
        <option v-if="placeholder" disabled>{{ placeholder }}</option>
        <option v-for="option of options" v-text="option" :value="option"></option>
    </select>
</template>
