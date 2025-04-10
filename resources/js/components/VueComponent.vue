<template>
    <div>
        <h2>This is a Vue Component inside Blade</h2>
        <p>Dynamic Data: {{ message }}</p>
    </div>
</template>

<script setup>
import { ref } from 'vue';

const message = ref("Hello from Vue!");
</script>
