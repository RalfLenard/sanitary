<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3'; // Import useForm
import PlaceholderPattern from '../components/PlaceholderPattern.vue';
import { ref, defineProps } from 'vue';
import {
  PlusIcon,
  DownloadIcon,
  SearchIcon,
  FilterIcon,
  MoreHorizontalIcon
} from 'lucide-vue-next';

import AddPermitModal from "@/components/AddPermitModal.vue";
import UpdatePermitModal from "@/components/UpdatePermitModal.vue"; // Import the modal

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Sanitary',
    href: '/sanitary',
  },
];

const props = defineProps({
  sanitaryPermits: Array // Receive data from Laravel
});

const activeActionMenu = ref<number | null>(null);
const isUpdateModalOpen = ref(false);
const selectedPermit = ref<Record<string, any> | null>(null);
const showAddDialog = ref(false);

// Toggle the action menu
const toggleActionMenu = (permitId: number) => {
  activeActionMenu.value = activeActionMenu.value === permitId ? null : permitId;
};

// Open the update permit modal
const openUpdateModal = (permit: Record<string, any>) => {
  selectedPermit.value = permit; // Set the selected permit
  isUpdateModalOpen.value = true; // Open modal
  activeActionMenu.value = null; // Close the dropdown
};

// Close the update modal
const closeUpdateModal = () => {
  isUpdateModalOpen.value = false;
  selectedPermit.value = null;
};

// Mark permit as inspected
const markAsInspected = (id: number) => {
    if (!id) {
        console.error("Permit ID is missing");
        return;
    }

    const form = useForm({}); // Initialize empty form
    
    form.put(route("sanitaryPermit.inspected", { id }), {
        preserveScroll: true,
        onSuccess: () => {
            console.log(`Permit ${id} status updated to Inspected`);
        },
        onError: (errors) => {
            console.error("Error updating status:", errors);
        }
    });
};

// Renew permit function
const renewPermit = (id: number) => {
    if (!id) {
        console.error("Permit ID is missing");
        return;
    }

    const form = useForm({}); // Ensure useForm is initialized properly
    
    form.put(route("sanitaryPermit.renewal", { id }), {
        preserveScroll: true,
        onSuccess: () => {
            console.log(`Permit ${id} successfully renewed!`);
        },
        onError: (errors) => {
            console.error("Error renewing permit:", errors);
        }
    });
};

const printCertificate = (id) => {
    if (!id) {
        console.error("Permit ID is missing");
        return;
    }

    const url = window.Laravel.routes.sanitary_print.replace("__ID__", id);
    window.open(url, "_blank");
};
</script>


<template>

  <Head title="Sanitary" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
      <div class="flex flex-col gap-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
          <h1 class="text-3xl font-bold">Sanitary Permits</h1>
          <div class="flex items-center gap-2">
            <button @click="showAddDialog = true"
              class="inline-flex items-center justify-center rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground shadow transition-colors hover:bg-primary/90">
              <PlusIcon class="mr-2 h-4 w-4" />
              Add New Permit
            </button>
            <!-- Add Permit Modal -->
            <AddPermitModal :show="showAddDialog" @close="showAddDialog = false" />
          </div>
        </div>

        <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
          <div class="flex flex-col space-y-1.5 p-6 pb-3">
          </div>
          <div class="p-6 pt-0">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between mb-4">
              <div class="relative w-full sm:w-96">
                <SearchIcon class="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground" />
                <input v-model="searchTerm" placeholder="Search permits..."
                  class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 pl-8 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2" />
              </div>
              <div class="flex items-center gap-2">
                <div class="relative">
                  <button @click="isFilterOpen = !isFilterOpen"
                    class="inline-flex items-center justify-center rounded-md border border-input bg-background px-3 py-2 text-sm font-medium shadow-sm hover:bg-accent hover:text-accent-foreground h-10">
                    <FilterIcon class="mr-2 h-4 w-4" />
                    Filter
                  </button>
                  <div v-if="isFilterOpen"
                    class="absolute right-0 z-10 mt-2 w-56 origin-top-right rounded-md border bg-popover text-popover-foreground shadow-md">
                    <div class="p-2">
                      <button @click="setStatusFilter('all'); isFilterOpen = false"
                        class="relative flex w-full cursor-default select-none items-center rounded-sm px-2 py-1.5 text-sm outline-none hover:bg-accent hover:text-accent-foreground">
                        All Statuses
                      </button>
                      <button @click="setStatusFilter('active'); isFilterOpen = false"
                        class="relative flex w-full cursor-default select-none items-center rounded-sm px-2 py-1.5 text-sm outline-none hover:bg-accent hover:text-accent-foreground">
                        Active Only
                      </button>
                      <button @click="setStatusFilter('expired'); isFilterOpen = false"
                        class="relative flex w-full cursor-default select-none items-center rounded-sm px-2 py-1.5 text-sm outline-none hover:bg-accent hover:text-accent-foreground">
                        Expired Only
                      </button>
                      <button @click="setStatusFilter('suspended'); isFilterOpen = false"
                        class="relative flex w-full cursor-default select-none items-center rounded-sm px-2 py-1.5 text-sm outline-none hover:bg-accent hover:text-accent-foreground">
                        Suspended Only
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="rounded-md border">
              <table class="w-full caption-bottom text-sm">
                <thead class="[&_tr]:border-b">
                  <tr class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                    <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">ID</th>
                    <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">Business Name</th>
                    <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">Name of Owner</th>
                    <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground hidden md:table-cell">
                      Contact</th>
                    <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground hidden md:table-cell">
                      Barangay</th>
                    <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground hidden md:table-cell">
                      Line of Business</th>
                    <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground hidden md:table-cell">
                      Year</th>
                    <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">Status</th>
                    <th class="h-12 px-4 text-right align-middle font-medium text-muted-foreground">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="permit in props.sanitaryPermits" :key="permit.id"
                    class="border-b transition-colors hover:bg-muted/50">
                    <td class="p-4 align-middle font-medium">{{ permit.id }}</td>
                    <td class="p-4 align-middle">{{ permit.name_of_establishment }}</td>
                    <td class="p-4 align-middle">{{ permit.name_of_owner }}</td>
                    <td class="p-4 align-middle">{{ permit.contact_number }}</td>
                    <td class="p-4 align-middle">{{ permit.barangay }}</td>
                    <td class="p-4 align-middle">{{ permit.line_of_business }}</td>
                    <td class="p-4 align-middle">{{ permit.renewal_year }}</td>

                    <td class="p-4 align-middle">
                      <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold" :class="{
                        'bg-primary text-primary-foreground': permit.status === 'Active',
                        'bg-destructive text-destructive-foreground': permit.status === 'Expired',
                        'bg-muted text-muted-foreground': permit.status === 'Suspended'
                      }">
                        {{ permit.status }}
                      </span>
                    </td>
                    <td class="p-4 align-middle text-right">
                      <div class="relative">
                        <!-- Action Button -->
                        <button @click="toggleActionMenu(permit.id)"
                          class="inline-flex h-8 w-8 items-center justify-center rounded-md border border-input bg-transparent p-0 text-sm font-medium shadow-sm hover:bg-accent hover:text-accent-foreground">
                          <MoreHorizontalIcon class="h-4 w-4" />
                          <span class="sr-only">Open menu</span>
                        </button>

                        <!-- Action Menu -->
                        <div v-if="activeActionMenu === permit.id"
                          class="absolute right-0 z-10 mt-2 w-56 origin-top-right rounded-md border bg-popover text-popover-foreground shadow-md">
                          <div class="p-2">
                            <button @click="openUpdateModal(permit)"
                              class="relative flex w-full cursor-default select-none items-center rounded-sm px-2 py-1.5 text-sm outline-none hover:bg-accent hover:text-accent-foreground">
                              Edit Permit
                            </button>

                            <button @click="markAsInspected(permit.id)" 
                                class="relative flex w-full cursor-default select-none items-center rounded-sm px-2 py-1.5 text-sm outline-none hover:bg-accent hover:text-accent-foreground">
                                Inspect Done
                            </button>


                            <button 
                                @click="renewPermit(permit.id)" 
                                class="relative flex w-full cursor-pointer select-none items-center rounded-sm px-2 py-1.5 text-sm outline-none hover:bg-accent hover:text-accent-foreground">
                                Renew Permit
                            </button>


                            <button 
                                @click="printCertificate(permit.id)" 
                                class="relative flex w-full cursor-pointer select-none items-center rounded-sm px-2 py-1.5 text-sm outline-none hover:bg-accent hover:text-accent-foreground">
                                Print Certificate
                            </button>


                          </div>
                        </div>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Update Permit Modal -->
        <UpdatePermitModal :show="isUpdateModalOpen" :permit="selectedPermit" @close="closeUpdateModal" />
      </div>
    </div>
  </AppLayout>
</template>
