<script setup>
import { Inertia } from '@inertiajs/inertia';

// Method for handling logout
const handleLogout = async () => {
  try {
    // Send logout request
    await Inertia.post('/logout');  // This will send a POST request to the logout route in Laravel

    // After logging out, you can redirect to the login page or home page
    Inertia.visit('/login');  // Redirect to login page or another page of your choice
  } catch (error) {
    console.error('Logout failed:', error);
  }
}
</script>

<template>
  <div class="min-h-screen bg-gradient-to-r from-blue-500 to-teal-500 flex items-center justify-center">
    <div class="max-w-lg w-full bg-white p-8 rounded-lg shadow-lg">
      <h1 class="text-3xl font-semibold text-center text-gray-800 mb-6">
        Welcome to the Guest Dashboard
      </h1>
      <p class="text-lg text-gray-600 text-center mb-4">
        Access is limited, but you're still welcome to explore the essential features of our system.
      </p>
      <p class="text-md text-gray-600 text-center mb-4">
        As a guest, you can view certain public data and get an overview of the most recent updates. Please log in or sign up for full access to additional features.
      </p>
      <p class="text-md text-gray-600 text-center mb-4">
        We're glad you're here! Feel free to explore the basic functions and see how we can help you.
      </p>
      <p class="text-md text-gray-600 text-center">
        For further assistance, don't hesitate to reach out to our support team.
      </p>

      <!-- Logout Button -->
      <div class="text-center mt-6">
        <button
          @click="handleLogout"
          class="bg-red-500 text-white py-2 px-4 rounded-lg hover:bg-red-600 transition duration-300"
        >
          Logout
        </button>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* You can add custom styles if needed */
</style>
