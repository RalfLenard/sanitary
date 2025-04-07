<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

interface Props {
    className?: HTMLAttributes['class'];
}

defineProps<Props>();
</script>

<template>
    <div class="image-container">
      <img 
        src="/images/Official Logo - MHO.png" 
        :class="['logo', className]" 
        v-bind="$attrs" 
        alt="MHO Logo" 
      />
    </div>
  </template>
  
  <style scoped>
  .image-container {
    display: flex;
    justify-content: center; /* Center image horizontally */
    align-items: center; /* Center image vertically */
    padding: 10px;
    background-color: #f8f9fa; /* Light background for contrast */
    border-radius: 10px; /* Rounded corners for the container */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Subtle shadow for the container */
  }
  
  .logo {
    max-width: 1000%; /* Ensure it scales responsively */
    max-height: 1000px; /* Limit the height */
    border-radius: 8px; /* Rounded corners on the image */
    transition: transform 0.3s ease-in-out; /* Smooth transform on hover */
  }
  
  .logo:hover {
    transform: scale(1.05); /* Slight zoom effect on hover */
  }
  </style>
  
