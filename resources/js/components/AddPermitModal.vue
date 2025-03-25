<template>
  <div v-if="show" class="fixed inset-0 z-50 bg-background/80 backdrop-blur-sm" @click.self="$emit('close')">
    <div class="fixed left-[50%] top-[50%] z-50 max-h-[85vh] w-[90vw] max-w-[550px] translate-x-[-50%] translate-y-[-50%] rounded-lg border bg-background p-6 shadow-lg">
      <!-- Header -->
      <div class="flex justify-between items-center pb-4">
        <h3 class="text-lg font-semibold">Add New Sanitary Permit</h3>
        <button @click="$emit('close')" class="exit-btn">&times;</button>
      </div>
      <p class="text-sm text-muted-foreground">Enter the details for the new sanitary permit.</p>

      <!-- Form -->
      <form @submit.prevent="submit">
        <div class="grid gap-4 py-4">
          <div class="grid grid-cols-2 gap-4">
            <div class="grid gap-2">
              <label class="text-sm font-medium">Business Name</label>
              <input v-model="form.name_of_establishment" class="input-field" />
           
            </div>
            <div class="grid gap-2">
              <label class="text-sm font-medium">Owner Name</label>
              <input v-model="form.name_of_owner" class="input-field" />
              
            </div>
          </div>
          <div class="grid gap-2">
            <label class="text-sm font-medium">Barangay</label>
            <input v-model="form.barangay" class="input-field" />
         
          </div>
          <div class="grid gap-2">
            <label class="text-sm font-medium">Contact Number</label>
            <input v-model="form.contact_number" class="input-field" />
         
          </div>
          <div class="grid gap-2">
            <label class="text-sm font-medium">Line of Business</label>
            <input v-model="form.line_of_business" class="input-field" />
           
          </div>
          <div class="grid gap-2">
            <label class="text-sm font-medium">Inspector</label>
            <input v-model="form.inspector_name" class="input-field" />
          
          </div>
          <div class="grid gap-2">
            <label class="text-sm font-medium">Renewal Year</label>
            <input v-model="form.renewal_year" type="number" class="input-field" />
           
          </div>
        </div>

        <!-- Footer Buttons -->
        <div class="flex justify-end space-x-3 pt-6">
          <button type="button" @click="$emit('close')" class="btn-secondary">Cancel</button>
          <button type="submit" class="btn-primary" :disabled="form.processing">Save Permit</button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { useForm } from "@inertiajs/vue3";
import { defineProps, defineEmits } from "vue";

defineProps({ show: Boolean });
const emit = defineEmits(["close"]);

// ✅ Ensure `name_of_establishment` is included
const form = useForm({
  name_of_establishment: "", // Added this missing field
  name_of_owner: "",
  barangay: "",
  contact_number: "",
  line_of_business: "",
  inspector_name: "",
  renewal_year: new Date().getFullYear(), // Default to current year
});

const submit = () => {
  console.log("Submitting form:", form); // Debugging
  form.post(route("newPermit"), {
    onSuccess: () => {
      emit("close"); // Close modal after successful submission
    },
  });
};
</script>


<style scoped>
.input-field {
  @apply flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2;
}
.btn-primary {
  @apply inline-flex items-center justify-center rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground shadow transition-colors hover:bg-primary/90;
}
.btn-secondary {
  @apply inline-flex items-center justify-center rounded-md bg-gray-300 px-4 py-2 text-sm font-medium text-gray-700 shadow transition-colors hover:bg-gray-400;
}
.exit-btn {
  @apply text-2xl text-gray-500 hover:text-gray-700 cursor-pointer;
}
.error-text {
  @apply text-sm text-red-500;
}
</style>
