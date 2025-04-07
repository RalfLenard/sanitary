<template>
    <div v-if="show" class="fixed inset-0 z-50 bg-background/80 backdrop-blur-sm" @click.self="$emit('close')">
        <div
            class="fixed left-[50%] top-[50%] z-50 max-h-[85vh] w-[90vw] max-w-[550px] translate-x-[-50%] translate-y-[-50%] rounded-lg border bg-background p-6 shadow-lg">
            
            <!-- Header -->
            <div class="flex justify-between items-center pb-4">
                <h3 class="text-lg font-semibold">Update Sanitary Permit</h3>
                <button @click="$emit('close')" class="exit-btn">&times;</button>
            </div>
            <p class="text-sm text-muted-foreground">Modify the details of the sanitary permit.</p>

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

                    <!-- Include Signature Toggle -->
                    <div class="flex items-center space-x-3">
                        <label class="text-sm font-medium">Include Signature</label>
                        <button
                            @click.prevent="form.has_signature = !form.has_signature"
                            class="relative w-12 h-6 flex items-center rounded-full bg-gray-300 transition-all"
                            :class="{ 'bg-blue-500': form.has_signature }"
                        >
                            <div
                                class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full shadow-md transition-all"
                                :class="{ 'translate-x-6': form.has_signature }"
                            ></div>
                        </button>
                    </div>
                </div>

                <!-- Footer Buttons -->
                <div class="flex justify-end space-x-3 pt-6">
                    <button type="button" @click="$emit('close')" class="btn-secondary">Cancel</button>
                    <button type="submit" class="btn-primary" :disabled="form.processing">Update Permit</button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { useForm } from "@inertiajs/vue3";
import { defineProps, defineEmits, watch } from "vue";

const props = defineProps({
    show: Boolean,
    permit: Object, // Pass existing permit data
});

const emit = defineEmits(["close"]);

// Prefill form with existing permit data
const form = useForm({
    id: props.permit?.id || null,
    name_of_establishment: props.permit?.name_of_establishment || "",
    name_of_owner: props.permit?.name_of_owner || "",
    barangay: props.permit?.barangay || "",
    contact_number: props.permit?.contact_number || "",
    line_of_business: props.permit?.line_of_business || "",
    inspector_name: props.permit?.inspector_name || "",
    renewal_year: props.permit?.renewal_year || new Date().getFullYear(),
    has_signature: props.permit?.has_signature || false, // ✅ Added has_signature field
});

// Watch for permit prop changes and update the form
watch(() => props.permit, (newPermit) => {
    if (newPermit) {
        console.log("Permit data received:", newPermit); // Debugging: Check if ID exists
        form.id = newPermit.id || null;
        form.name_of_establishment = newPermit.name_of_establishment;
        form.name_of_owner = newPermit.name_of_owner;
        form.barangay = newPermit.barangay;
        form.contact_number = newPermit.contact_number;
        form.line_of_business = newPermit.line_of_business;
        form.inspector_name = newPermit.inspector_name;
        form.renewal_year = newPermit.renewal_year;
        form.has_signature = newPermit.has_signature; // ✅ Ensures has_signature updates
    }
}, { deep: true });

// Submit update request
const submit = () => {
    if (!form.id) {
        console.error("Permit ID is missing.");
        return;
    }

    form.put(route("updatePermit", { id: form.id }), {
        preserveScroll: true,
        onSuccess: () => {
            emit("close");
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
</style>
