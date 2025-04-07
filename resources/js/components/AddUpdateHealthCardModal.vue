<template>
    <!-- Edit Health Card Modal -->
    <div v-if="show" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full mx-4">
            <div class="p-6 border-b flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-800">Edit Health Card</h3>
                <button @click="closeModal" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>
            <div class="p-6">
                <form @submit.prevent="submit" class="space-y-6">
                    <div class="grid gap-4">
                        <!-- Card Type -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Card Type</label>
                            <select v-model="form.health_card_type"
                                class="mt-1 block w-full rounded-lg border border-black px-3 py-2 focus:border-primary focus:ring-primary">
                                <option value="non_food">Non-Food</option>
                                <option value="food">Food</option>
                                <option value="others">Other</option>
                            </select>
                        </div>
                        <!-- Full Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Full Name</label>
                            <input v-model="form.full_name" type="text" required
                                class="mt-1 block w-full rounded-lg border border-black px-3 py-2 focus:border-primary focus:ring-primary" />
                        </div>
                        <!-- Age -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Age</label>
                            <input v-model="form.age" type="number"
                                class="mt-1 block w-full rounded-lg border border-black px-3 py-2 focus:border-primary focus:ring-primary" />
                        </div>
                        <!-- Gender -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Gender</label>
                            <input v-model="form.gender" type="text" placeholder="Enter Gender"
                                class="mt-1 block w-full rounded-lg border border-black px-3 py-2 focus:border-primary focus:ring-primary" />
                        </div>
                        <!-- Place of Employment -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Place of Employment</label>
                            <input v-model="form.place_of_employment" type="text"
                                class="mt-1 block w-full rounded-lg border border-black px-3 py-2 focus:border-primary focus:ring-primary" />
                        </div>
                        <!-- Designation -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Designation</label>
                            <input v-model="form.designation" type="text"
                                class="mt-1 block w-full rounded-lg border border-black px-3 py-2 focus:border-primary focus:ring-primary" />
                        </div>
                        <!-- Barangay -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Barangay</label>
                            <input v-model="form.barangay" type="text"
                                class="mt-1 block w-full rounded-lg border border-black px-3 py-2 focus:border-primary focus:ring-primary" />
                        </div>
                        <!-- Inspector -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Inspector</label>
                            <input v-model="form.inspector_name" type="text"
                                class="mt-1 block w-full rounded-lg border border-black px-3 py-2 focus:border-primary focus:ring-primary" />
                        </div>
                        <!-- Date of Issuance -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Date of Issuance</label>
                            <input v-model="form.date_of_issuance" type="date" required
                                class="mt-1 block w-full rounded-lg border border-black px-3 py-2 focus:border-primary focus:ring-primary" />
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end gap-4">
                        <!-- Cancel Button -->
                        <button type="button" @click="closeModal"
                            class="px-4 py-2 border rounded-lg text-sm font-medium shadow-sm bg-white text-gray-700 hover:bg-gray-100">
                            Cancel
                        </button>
                        <!-- Save Button -->
                        <button type="submit"
                            class="px-4 py-2 rounded-lg text-sm font-medium shadow-sm bg-primary text-white hover:bg-primary/90">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
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

if (props.editingCard && props.editingCard.date_of_issuance) {
    form.date_of_issuance = new Date(props.editingCard.date_of_issuance).toISOString().split("T")[0];
}



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