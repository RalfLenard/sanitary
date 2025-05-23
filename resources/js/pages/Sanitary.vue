<script setup lang="ts">
import { ref, computed, defineProps, watch } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { MoreHorizontalIcon, SearchIcon, PlusIcon, FilterIcon } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import AddPermitModal from "@/components/AddPermitModal.vue";
import UpdatePermitModal from "@/components/UpdatePermitModal.vue";
import { Inertia } from '@inertiajs/inertia';

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Sanitary', href: '/sanitary' },
];

// Props
const props = defineProps<{ sanitaryPermits: Record<string, any>; quarterlyData: Record<string, any> }>();

// Reactive state
const searchTerm = ref('');
const activeActionMenu = ref<number | null>(null);
const isUpdateModalOpen = ref(false);
const selectedPermit = ref<Record<string, any> | null>(null);
const showAddDialog = ref(false);
// Quarter filter state
const selectedQuarter = ref<number | null>(null);
const isQuarterFilterOpen = ref(false);
const quarterlyData = ref([]);

const cardToDeleteId = ref<string | null>(null);
const isDeleteModalOpen = ref(false);

// Open confirmation modal
const confirmDelete = (id: string) => {
    cardToDeleteId.value = id;
    isDeleteModalOpen.value = true;
};

// Delete card using Inertia
const deleteCard = () => {
    if (!cardToDeleteId.value) return;

    Inertia.delete(route('sanitary.delete', cardToDeleteId.value), {
        onSuccess: () => {
            isDeleteModalOpen.value = false;
            cardToDeleteId.value = null;
        },
        onError: (errors) => {
            alert("Failed to delete the Sanitary Permit.");
        }
    });
};


const filteredPermits = computed(() => {
  let permits = props.sanitaryPermits.data;

  if (selectedQuarter.value !== null) {
    permits = permits.filter(permit => permit.quarter == selectedQuarter.value);
  }

  if (searchTerm.value) {
    const term = searchTerm.value.toLowerCase();
    permits = permits.filter(permit =>
      Object.values(permit).some(value =>
        value && value.toString().toLowerCase().includes(term)
      )
    );
  }

  return permits;
});

// Computed property for selected quarter label
const selectedQuarterLabel = computed(() => selectedQuarter.value ? `Q${selectedQuarter.value}` : "All Quarters");

// Watch for quarter changes
watch(selectedQuarter, (newQuarter) => console.log("Quarter changed to:", newQuarter));

// Set quarter filter
const setQuarterFilter = (quarter: number | null) => {
  selectedQuarter.value = quarter;
  isQuarterFilterOpen.value = false;
};

// Toggle action menu visibility
const toggleActionMenu = (permitId: number) => {
  activeActionMenu.value = activeActionMenu.value === permitId ? null : permitId;
};

// Open update modal
const openUpdateModal = (permit: Record<string, any>) => {
  selectedPermit.value = permit;
  isUpdateModalOpen.value = true;
  activeActionMenu.value = null;
};

// Close update modal
const closeUpdateModal = () => {
  isUpdateModalOpen.value = false;
  selectedPermit.value = null;
};

// Mark permit as inspected
const markAsInspected = (id: number) => {
  if (!id) return console.error("Permit ID is missing");
  useForm({}).put(route("sanitaryPermit.inspected", { id }), {
    preserveScroll: true,
    onSuccess: () => console.log(`Permit ${id} status updated to Inspected`),
    onError: (errors) => console.error("Error updating status:", errors)
  });
};

// Renew permit
const renewPermit = (id: number) => {
  if (!id) return console.error("Permit ID is missing");
  useForm({}).put(route("sanitaryPermit.renewal", { id }), {
    preserveScroll: true,
    onSuccess: () => console.log(`Permit ${id} successfully renewed!`),
    onError: (errors) => console.error("Error renewing permit:", errors)
  });
};

// Print certificate
const printCertificate = (permitId: number) => {
  if (!window.Laravel?.routes?.sanitary_print) {
    console.error("Laravel routes not defined.");
    return;
  }
  const url = window.Laravel.routes.sanitary_print.replace('__ID__', permitId.toString());
  window.open(url, '_blank');
};



// Advanced Search
const showAdvancedSearch = ref(false);
const isFilterOpen = ref(false);
const searchCriteria = ref({
  name_of_establishment: '',
  name_of_owner: '',
  barangay: '',
  permit_code: '',
  renewal_year: ''
});

// Perform an advanced search
const performAdvancedSearch = () => {
  showAdvancedSearch.value = false;
  router.get(route('sanitary'), searchCriteria.value, { preserveState: true });
};

// Fetch data for pagination
const fetchData = (url) => {
  router.get(url, {
    search: searchTerm.value, // ✅ Keep the search term
    name_of_establishment: searchCriteria.value.name_of_establishment,
    name_of_owner: searchCriteria.value.name_of_owner,
    barangay: searchCriteria.value.barangay,
    permit_code: searchCriteria.value.permit_code,
    renewal_year: searchCriteria.value.renewal_year,
    quarter: selectedQuarter.value, // ✅ Preserve selected quarter
  }, {
    preserveState: true,
    preserveScroll: true
  });
};

const performSearch = () => {
  router.get(route('sanitary'), {
    search: searchTerm.value,
    name_of_establishment: searchCriteria.value.name_of_establishment,
    name_of_owner: searchCriteria.value.name_of_owner,
    barangay: searchCriteria.value.barangay,
    permit_code: searchCriteria.value.permit_code,
    renewal_year: searchCriteria.value.renewal_year,
    quarter: selectedQuarter.value
  }, {
    preserveState: true,
    preserveScroll: true
  });
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
              <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between mb-4">
                <!-- Quick Search Input -->
                <div class="relative w-full sm:w-96">
                  <SearchIcon class="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground" />
                  <input v-model="searchTerm" @input="performSearch" placeholder="Search permits..."
                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 pl-8 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2" />
                </div>


                <!-- Advanced Search Button -->
                <button @click="showAdvancedSearch = true"
                  class="inline-flex items-center justify-center rounded-md border border-input bg-background px-3 py-2 text-sm font-medium shadow-sm hover:bg-accent hover:text-accent-foreground h-10">
                  <FilterIcon class="mr-2 h-4 w-4" />
                  Advanced Search
                </button>
              </div>

              <!-- Advanced Search Modal -->
              <div v-if="showAdvancedSearch"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
                <div class="bg-white rounded-lg p-6 shadow-lg w-96">
                  <h2 class="text-lg font-semibold mb-4">Advanced Search</h2>

                  <label class="block text-sm font-semibold mb-1">Establishment Name</label>
                  <input v-model="searchCriteria.name_of_establishment" class="w-full border rounded-md p-2 mb-2"
                    placeholder="Enter name">

                  <label class="block text-sm font-semibold mb-1">Owner Name</label>
                  <input v-model="searchCriteria.name_of_owner" class="w-full border rounded-md p-2 mb-2"
                    placeholder="Owner name">

                  <label class="block text-sm font-semibold mb-1">Barangay</label>
                  <input v-model="searchCriteria.barangay" class="w-full border rounded-md p-2 mb-2"
                    placeholder="Barangay">


                  <label class="block text-sm font-semibold mb-1">Renewal Year</label>
                  <input v-model="searchCriteria.renewal_year" class="w-full border rounded-md p-2 mb-2"
                    placeholder="YYYY">

                  <!-- Search & Close Buttons -->
                  <div class="flex justify-end gap-2 mt-4">
                    <button @click="showAdvancedSearch = false"
                      class="px-4 py-2 bg-gray-500 text-white rounded-md">Close</button>
                    <button @click="performAdvancedSearch"
                      class="px-4 py-2 bg-blue-600 text-white rounded-md">Search</button>
                  </div>
                </div>
              </div>
              <!-- Quarter Filter Dropdown -->
              <div class="relative">
                <button @click="isFilterOpen = !isFilterOpen"
                  class="inline-flex items-center justify-center rounded-md border border-input bg-background px-3 py-2 text-sm font-medium shadow-sm hover:bg-gray-100 h-10 w-48">
                  <FilterIcon class="mr-2 h-4 w-4 text-gray-500" />
                  {{ selectedQuarterLabel }}
                </button>

                <!-- Dropdown Menu -->
                <transition enter-active-class="transition ease-out duration-100" enter-from-class="opacity-0 scale-95"
                  enter-to-class="opacity-100 scale-100" leave-active-class="transition ease-in duration-75"
                  leave-from-class="opacity-100 scale-100" leave-to-class="opacity-0 scale-95">
                  <div v-if="isFilterOpen"
                    class="absolute right-0 z-20 mt-2 w-56 origin-top-right bg-white divide-y divide-gray-100 rounded-md shadow-md ring-1 ring-black ring-opacity-5">
                    <div class="p-2">
                      <button @click="setQuarterFilter(null)"
                        class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        All Quarters
                      </button>
                      <button @click="setQuarterFilter(1)"
                        class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        First Quarter
                      </button>
                      <button @click="setQuarterFilter(2)"
                        class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        Second Quarter
                      </button>
                      <button @click="setQuarterFilter(3)"
                        class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        Third Quarter
                      </button>
                      <button @click="setQuarterFilter(4)"
                        class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        Fourth Quarter
                      </button>
                    </div>
                  </div>
                </transition>
              </div>
            </div>

            <div class="rounded-md border">
              <table class="w-full text-sm border-collapse ">
                <thead class="bg-gray-100 sticky top-0 z-10">
                  <tr class="border-b">
                    <th class="h-12 px-4 text-left font-medium text-gray-600">ID</th>
                    <th class="h-12 px-4 text-left font-medium text-gray-600">Business Name</th>
                    <th class="h-12 px-4 text-left font-medium text-gray-600">Owner</th>
                    <th class="h-12 px-4 text-left font-medium hidden md:table-cell">Contact</th>
                    <th class="h-12 px-4 text-left font-medium hidden md:table-cell">Barangay</th>
                    <th class="h-12 px-4 text-left font-medium hidden md:table-cell">Line of Business</th>
                    <th class="h-12 px-4 text-left font-medium hidden md:table-cell">Year</th>
                    <th class="h-12 px-4 text-left font-medium">Status</th>
                    <th class="h-12 px-4 text-right font-medium">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="permit in filteredPermits" :key="permit.id"
                    class="border-b transition-colors hover:bg-gray-50 even:bg-gray-50"  style="text-transform: uppercase;">
                    <td class="p-4 font-medium">{{ permit.id }}</td>
                    <td class="p-4">{{ permit.name_of_establishment }}</td>
                    <td class="p-4">{{ permit.name_of_owner }}</td>
                    <td class="p-4 hidden md:table-cell">{{ permit.contact_number }}</td>
                    <td class="p-4 hidden md:table-cell">{{ permit.barangay }}</td>
                    <td class="p-4 hidden md:table-cell">{{ permit.line_of_business }}</td>
                    <td class="p-4 hidden md:table-cell">{{ permit.renewal_year }}</td>
                    <td class="p-4">
                      <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold" :class="{
                        'bg-green-100 text-green-800': permit.status === 'Active',
                        'bg-red-100 text-red-800': permit.status === 'Expired',
                        'bg-yellow-100 text-yellow-800': permit.status === 'Suspended'
                      }">
                        {{ permit.status }}
                      </span>
                    </td>
                    <td class="p-4 text-right">
                      <div class="relative">
                        <button @click="toggleActionMenu(permit.id)"
                          class="inline-flex h-8 w-8 items-center justify-center rounded-md border border-gray-300 bg-white text-gray-600 shadow-sm hover:bg-gray-100">
                          <MoreHorizontalIcon class="h-4 w-4" />
                          <span class="sr-only">Open menu</span>
                        </button>
                        <div v-if="activeActionMenu === permit.id"
                          class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md border bg-white shadow-lg">
                          <div class="p-2 space-y-1">
                            <button @click="openUpdateModal(permit)"
                              class="w-full flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md">
                              Edit Permit
                            </button>
                            <!-- <button @click="markAsInspected(permit.id)"
                              class="w-full flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md">
                              Inspect Done
                            </button> -->
                            <button @click="renewPermit(permit.id)"
                              class="w-full flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md">
                              Renew Permit
                            </button>
                            <button @click="printCertificate(permit.id)"
                              class="w-full flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md">
                              Print Certificate
                            </button>
                            <button  @click="confirmDelete(permit.id)"
                              class="w-full flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md">
                              Delete
                            </button>
                          </div>
                        </div>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>

              <!-- No results message -->
              <div v-if="filteredPermits.length === 0" class="p-4 text-center text-muted-foreground">
                No permits found.
              </div>



            </div>

            <!-- Pagination -->
            <div class="mt-4 flex justify-end space-x-4">
              <!-- Previous Button -->
              <button v-if="sanitaryPermits.prev_page_url" @click="fetchData(sanitaryPermits.prev_page_url)"
                class="px-3 py-1 text-sm font-medium text-gray-600 bg-gray-200 rounded-md hover:bg-gray-300 disabled:opacity-50 disabled:cursor-not-allowed">
                Prev
              </button>

              <!-- Page Info -->
              <span class="self-center text-sm font-medium text-gray-700">
                Page {{ sanitaryPermits.current_page }} of {{ sanitaryPermits.last_page }}
              </span>

              <!-- Next Button -->
              <button v-if="sanitaryPermits.next_page_url" @click="fetchData(sanitaryPermits.next_page_url)"
                class="px-3 py-1 text-sm font-medium text-gray-600 bg-gray-200 rounded-md hover:bg-gray-300 disabled:opacity-50 disabled:cursor-not-allowed">
                Next
              </button>
            </div>



          </div>
        </div>

        <!-- Update Permit Modal -->
        <UpdatePermitModal :show="isUpdateModalOpen" :permit="selectedPermit" @close="closeUpdateModal" />

        <div v-if="isDeleteModalOpen"
                class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
                    <div class="p-6">
                        <h3 class="text-lg font-medium">Are you sure?</h3>
                        <p class="mt-2 text-sm text-gray-500">
                            This action cannot be undone. This will permanently delete the Sanitary Permit.
                        </p>
                    </div>
                    <div class="px-6 py-4 bg-gray-50 flex justify-end gap-3 rounded-b-lg">
                        <button type="button"
                            class="px-4 py-2 border rounded-md text-sm font-medium shadow-sm bg-white text-gray-700 hover:bg-gray-50"
                            @click="isDeleteModalOpen = false">
                            Cancel
                        </button>
                        <button type="button"
                            class="px-4 py-2 rounded-md text-sm font-medium shadow-sm bg-red-600 text-white hover:bg-red-700"
                            @click="deleteCard">
                            Delete
                        </button>
                    </div>
                </div>
            </div>
      </div>
    </div>
  </AppLayout>
</template>
