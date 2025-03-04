<script setup>
const model = defineModel();

defineProps({
    placeholder: String,
    options: Array
});
</script>

<template>
    <select v-model="model" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        <option v-if="placeholder" disabled selected>{{ placeholder }}</option>
        <option v-for="option of options" v-text="option.text" :value="option.value"></option>
    </select>
</template>
