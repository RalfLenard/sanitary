<template>
    <div class="pagination-container">
      <!-- Previous Button -->
      <button 
        @click="changePage(props.pagination.current_page - 1)" 
        :disabled="props.pagination.current_page === 1"
        class="pagination-btn prev-btn"
      >
        Previous
      </button>
  
      <!-- Page Numbers -->
      <div class="pagination-pages">
        <button 
          v-for="page in pageNumbers" 
          :key="page" 
          @click="changePage(page)"
          :class="['pagination-btn', { 'active': page === props.pagination.current_page }]"
        >
          {{ page }}
        </button>
      </div>
  
      <!-- Next Button -->
      <button 
        @click="changePage(props.pagination.current_page + 1)" 
        :disabled="props.pagination.current_page === props.pagination.last_page"
        class="pagination-btn next-btn"
      >
        Next
      </button>
    </div>
  </template>
  
  <script setup>
  import { computed } from 'vue';
  
  const props = defineProps({
    pagination: Object, // Pagination metadata passed from the parent
    changePage: Function, // Method to handle page changes
  });
  
  const pageNumbers = computed(() => {
    const totalPages = props.pagination.last_page;
    const range = [];
  
    // Generate a list of pages to display
    for (let i = 1; i <= totalPages; i++) {
      range.push(i);
    }
  
    return range;
  });
  </script>
  
  <style scoped>
  .pagination-container {
    display: flex;
    justify-content: flex-end; /* Align to the right */
    align-items: center;
    gap: 10px;
  }
  
  .pagination-btn {
    background-color: white;
    border: 1px solid black;
    border-radius: 10px;
    color: black;
    padding: 8px 16px;
    cursor: pointer;
    font-size: 14px;
    transition: background-color 0.3s, color 0.3s;
    margin-top: 20px;
  }
  
  .pagination-btn:disabled {
    cursor: not-allowed;
    opacity: 0.5;
  }
  
  .pagination-btn:hover:not(:disabled) {
    background-color: black;
    color: white;
  }
  
  .pagination-btn.active {
    background-color: black;
    color: white;
  }
  
  .pagination-pages {
    display: flex;
    gap: 5px;
  }
  </style>