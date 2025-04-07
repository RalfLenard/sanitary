<template>
    <div
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 transition-opacity duration-300"
      v-if="show">
      <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full mx-4 p-6 relative">
        <!-- Close Button -->
        <button @click="closeModal" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
          ✕
        </button>
  
        <!-- Modal Header -->
        <div class="text-center">
          <h3 class="text-xl font-semibold text-gray-800">Update Health Card</h3>
        </div>
  
        <!-- Form -->
        <form @submit.prevent="submit" class="mt-4">
          <div class="space-y-4">
            <div>
              <label for="health_card_type" class="block text-sm font-medium text-gray-700">Health Card Type</label>
              <select id="health_card_type" v-model="form.health_card_type" required
                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                <option value="food">Food</option>
                <option value="non_food">Non-Food</option>
                <option value="others">Other</option>
              </select>
            </div>
            <div>
              <label for="full_name" class="block text-sm font-medium text-gray-700">Full Name</label>
              <input id="full_name" v-model="form.full_name" type="text" required placeholder="John Doe"
                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none" />
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label for="age" class="block text-sm font-medium text-gray-700">Age</label>
                <input id="age" v-model="form.age" type="number" placeholder="25"
                  class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none" />
              </div>
              <div>
                <label for="gender" class="block text-sm font-medium text-gray-700">Gender</label>
                <select id="gender" v-model="form.gender" required
                  class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                  <option value="">Select Gender</option>
                  <option value="male">Male</option>
                  <option value="female">Female</option>
                  <option value="other">Other</option>
                </select>
              </div>
            </div>
            <div>
              <label for="employment" class="block text-sm font-medium text-gray-700">Place of Employment</label>
              <input id="employment" v-model="form.place_of_employment" type="text" required placeholder="ABC Corp"
                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none" />
            </div>
            <div>
              <label for="designation" class="block text-sm font-medium text-gray-700">Designation</label>
              <input id="designation" v-model="form.designation" type="text" placeholder="Manager"
                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none" />
            </div>
            <div>
              <label for="barangay" class="block text-sm font-medium text-gray-700">Barangay</label>
              <input id="barangay" v-model="form.barangay" type="text" placeholder="Enter Barangay"
                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none" />
            </div>
  
            <div>
              <label for="rhu" class="block text-sm font-medium text-gray-700">RHU</label>
              <select id="rhu" v-model="form.rhu" 
                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                <option value="">Select rhu</option>
                <option value="RHU-I">RHU-I</option>
                <option value="RHU-II">RHU-II</option>
                <option value="RHU-III">RHU-III</option>
                <option value="RHU-IV">RHU-IV</option>
                <option value="RHU-V">RHU-V</option>
                <option value="RHU-VI">RHU-VI</option>
              </select>
            </div>
            <div>
              <label for="inspector" class="block text-sm font-medium text-gray-700">Inspector Name</label>
              <input id="inspector" v-model="form.inspector_name" type="text" placeholder="Enter Inspector Name"
                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none" />
            </div>
            <div>
              <label for="date_of_issuance" class="block text-sm font-medium text-gray-700">Date of Issuance</label>
              <input id="date_of_issuance" v-model="form.date_of_issuance" type="date" 
                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none" />
            </div>
          </div>
  
          <!-- Modal Footer -->
          <div class="mt-6 flex justify-end gap-3">
            <button type="button" @click="closeModal"
              class="px-4 py-2 border rounded-lg text-sm font-medium shadow-sm bg-gray-200 text-gray-700 hover:bg-gray-300">
              Cancel
            </button>
            <button type="submit"
              class="px-4 py-2 rounded-lg text-sm font-medium shadow-sm bg-black text-white hover:bg-gray-900">
              Add Card
            </button>
          </div>
        </form>
      </div>
    </div>
  </template>
  
  <script setup lang="ts">
  import { useForm } from "@inertiajs/vue3";
  import { defineProps, defineEmits, watch } from "vue";
  
  // Define props for the modal
  const props = defineProps({
    show: Boolean,
    editingCard: Object, // The health card data to edit
  });
  
  // Define emits to notify the parent component
  const emit = defineEmits(["closeModal", "refreshData"]);
  
  // Initialize the form using useForm and pre-fill with the editingCard prop
  const form = useForm({
    id: props.editingCard?.id || null,
    full_name: props.editingCard?.full_name || "",
    health_card_type: props.editingCard?.health_card_type || "non_food",
    age: props.editingCard?.age || null,
    gender: props.editingCard?.gender || "Male",
    place_of_employment: props.editingCard?.place_of_employment || "",
    designation: props.editingCard?.designation || "",
    date_of_issuance: props.editingCard?.date_of_issuance || "",
    barangay: props.editingCard?.barangay || "",
    inspector_name: props.editingCard?.inspector_name || "",
    rhu: props.editingCard?.rhu || "rhu-i",
  });
  
  // Watch for changes in the `editingCard` prop and update the form data
  watch(() => props.editingCard, (newCard) => {
    if (newCard) {
      form.id = newCard.id || null;
      form.full_name = newCard.full_name;
      form.health_card_type = newCard.health_card_type;
      form.age = newCard.age;
      form.gender = newCard.gender;
      form.place_of_employment = newCard.place_of_employment;
      form.designation = newCard.designation;
      form.barangay = newCard.barangay;
      form.inspector_name = newCard.inspector_name;
      form.rhu = newCard.rhu;
  
      // ✅ Convert date_of_issuance to "YYYY-MM-DD" format
      form.date_of_issuance = newCard.date_of_issuance
        ? newCard.date_of_issuance.split("T")[0] // Extract only "YYYY-MM-DD"
        : "";
    }
  }, { deep: true });
  
  // Close the modal
  const closeModal = () => {
    emit("closeModal");
  };
  
  // Submit the form to update the health card
  const submit = () => {
    if (!form.id) {
      console.error("Health card ID is missing.");
      return;
    }
  
    // Make the request using PUT
    form.put(route("updateHealthCard", { id: form.id }), {
      preserveScroll: true,
      onSuccess: () => {
        emit("closeModal");
        emit("refreshData");
      },
      onError: (errors) => {
        console.error("Failed to update health card:", errors);
      }
    });
  };
  </script>
  
  <style scoped>
  /* Add any styles for the modal here */
  </style>
  