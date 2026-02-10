<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { 
  MoreHorizontalIcon, 
  SearchIcon, 
  PlusIcon, 
  FilterIcon, 
  PrinterIcon, 
  Trash2Icon,
  Edit3Icon,
  RefreshCwIcon,
  AlertCircleIcon
} from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import AddPermitModal from "@/components/AddPermitModal.vue";
import UpdatePermitModal from "@/components/UpdatePermitModal.vue";
import { Inertia } from '@inertiajs/inertia';

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Sanitary', href: '/sanitary' },
];

// Props from Controller
const props = defineProps<{ 
    sanitaryPermits: any; 
    quarterData: any[]; 
    filters?: any 
}>();

// --- Reactive state ---
const searchTerm = ref(props.filters?.search || '');
const activeActionMenu = ref<number | null>(null);
const isUpdateModalOpen = ref(false);
const selectedPermit = ref<Record<string, any> | null>(null);
const showAddDialog = ref(false);

// Advanced Search & Filter state
const showAdvancedSearch = ref(false);
const isFilterOpen = ref(false);
const searchCriteria = ref({
  name_of_establishment: props.filters?.name_of_establishment || '',
  name_of_owner: props.filters?.name_of_owner || '',
  barangay: props.filters?.barangay || '',
  sanitary_code: props.filters?.sanitary_code || '',
  renewal_year: props.filters?.renewal_year || new Date().getFullYear(),
  quarter: props.filters?.quarter || null,
});

// Confirmation Modals State
const isDeleteModalOpen = ref(false);
const isRenewModalOpen = ref(false);
const isPrintModalOpen = ref(false);
const pendingPermitAction = ref<any>(null);

// --- Functions ---

const performSearch = () => {
  router.get(route('sanitary'), {
    search: searchTerm.value,
    ...searchCriteria.value
  }, {
    preserveState: true,
    preserveScroll: true,
    replace: true 
  });
};

const fetchData = (url: string) => {
  router.get(url, {
    search: searchTerm.value,
    ...searchCriteria.value
  }, {
    preserveState: true,
    preserveScroll: true
  });
};

const setQuarterFilter = (quarter: number | null) => {
  searchCriteria.value.quarter = quarter;
  isFilterOpen.value = false;
  performSearch();
};

const selectedQuarterLabel = computed(() => 
  searchCriteria.value.quarter ? `Quarter ${searchCriteria.value.quarter}` : "All Quarters"
);

// --- Action Handlers ---

const openUpdateModal = (permit: Record<string, any>) => {
  selectedPermit.value = permit;
  isUpdateModalOpen.value = true;
  activeActionMenu.value = null;
};

// Delete Logic
const confirmDelete = (permit: any) => {
  pendingPermitAction.value = permit;
  isDeleteModalOpen.value = true;
  activeActionMenu.value = null;
};

const executeDelete = () => {
  if (!pendingPermitAction.value) return;
  Inertia.delete(route('sanitary.delete', pendingPermitAction.value.id), {
    onSuccess: () => {
      isDeleteModalOpen.value = false;
      pendingPermitAction.value = null;
    }
  });
};

// Renewal Logic
const confirmRenewal = (permit: any) => {
  pendingPermitAction.value = permit;
  isRenewModalOpen.value = true;
  activeActionMenu.value = null;
};

const executeRenewal = () => {
  if (!pendingPermitAction.value) return;
  useForm({}).put(route("sanitaryPermit.renewal", { id: pendingPermitAction.value.id }), {
    preserveScroll: true,
    onSuccess: () => {
      isRenewModalOpen.value = false;
      pendingPermitAction.value = null;
    }
  });
};

// Print Logic
const confirmPrint = (permit: any) => {
  pendingPermitAction.value = permit;
  isPrintModalOpen.value = true;
  activeActionMenu.value = null;
};

const executePrint = () => {
  if (!pendingPermitAction.value) return;
  const url = window.Laravel.routes.sanitary_print.replace('__ID__', pendingPermitAction.value.id.toString());
  window.open(url, '_blank');
  isPrintModalOpen.value = false;
  pendingPermitAction.value = null;
};

const toggleActionMenu = (permitId: number) => {
  activeActionMenu.value = activeActionMenu.value === permitId ? null : permitId;
};
</script>

<template>
  <Head title="Sanitary" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 space-y-6">
      
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
          <h1 class="text-3xl font-bold tracking-tight">Sanitary Permits</h1>
          <p class="text-muted-foreground">Manage and track sanitary inspections for {{ searchCriteria.renewal_year }}.</p>
        </div>
        <button @click="showAddDialog = true"
          class="inline-flex items-center justify-center rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-blue-700 transition">
          <PlusIcon class="mr-2 h-4 w-4" /> Add New Permit
        </button>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div v-for="data in quarterlyData" :key="data.quarter" 
             class="bg-white p-4 rounded-xl border shadow-sm space-y-2"
             :class="{'ring-2 ring-blue-500': searchCriteria.quarter == data.quarter}">
          <div class="text-xs font-bold text-gray-500 uppercase">Quarter {{ data.quarter }}</div>
          <div class="flex justify-between items-end">
            <div>
              <div class="text-2xl font-bold">{{ data.new_businesses + data.renewals }}</div>
              <div class="text-[10px] text-muted-foreground uppercase">Total Permits</div>
            </div>
            <div class="text-right text-[11px]">
              <div class="text-green-600 font-medium">New: {{ data.new_businesses }}</div>
              <div class="text-blue-600 font-medium">Renewed: {{ data.renewals }}</div>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-xl border shadow-sm p-4">
        <div class="flex flex-col md:flex-row gap-4 justify-between">
          <div class="flex flex-1 gap-2 items-center">
            <select v-model="searchCriteria.renewal_year" @change="performSearch" 
              class="h-10 rounded-md border border-input bg-background px-3 text-sm focus:ring-2 focus:ring-blue-500">
              <option v-for="year in [2026, 2025, 2024, 2023]" :key="year" :value="year">Year {{ year }}</option>
            </select>

            <div class="relative flex-1 max-w-sm">
              <SearchIcon class="absolute left-3 top-3 h-4 w-4 text-gray-400" />
              <input v-model="searchTerm" @input="performSearch" placeholder="Quick search..."
                class="h-10 w-full rounded-md border border-input bg-background pl-9 pr-3 text-sm focus:ring-2 focus:ring-blue-500" />
            </div>
            
            <button @click="showAdvancedSearch = true"
              class="inline-flex items-center h-10 px-3 py-2 border rounded-md text-sm font-medium hover:bg-gray-50">
              <FilterIcon class="mr-2 h-4 w-4" /> Advanced
            </button>
          </div>

          <div class="relative">
            <button @click="isFilterOpen = !isFilterOpen"
              class="inline-flex items-center justify-between w-48 h-10 px-4 py-2 border rounded-md text-sm font-medium bg-white hover:bg-gray-50">
              {{ selectedQuarterLabel }}
              <FilterIcon class="ml-2 h-3 w-3 text-gray-400" />
            </button>
            <div v-if="isFilterOpen" class="absolute right-0 z-20 mt-2 w-48 bg-white border rounded-md shadow-xl py-1">
              <button @click="setQuarterFilter(null)" class="block w-full text-left px-4 py-2 text-sm hover:bg-gray-100">All Quarters</button>
              <button v-for="q in [1,2,3,4]" :key="q" @click="setQuarterFilter(q)" class="block w-full text-left px-4 py-2 text-sm hover:bg-gray-100">Quarter {{ q }}</button>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-xl border shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-sm text-left border-collapse uppercase">
            <thead class="bg-gray-50 text-gray-600 font-semibold border-b">
              <tr>
                <th class="p-4">Establishment</th>
                <th class="p-4">Owner</th>
                <th class="p-4 hidden lg:table-cell">Barangay</th>
                <th class="p-4 text-center">Year/Qtr</th>
                <th class="p-4">Status</th>
                <th class="p-4 text-right">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              <tr v-for="permit in sanitaryPermits.data" :key="permit.id" class="hover:bg-gray-50 transition-colors">
                <td class="p-4 font-medium text-gray-900">
                  <div class="text-sm">{{ permit.name_of_establishment }}</div>
                  <div class="text-[10px] text-gray-400 font-mono">{{ permit.sanitary_code }}</div>
                </td>
                <td class="p-4">{{ permit.name_of_owner }}</td>
                <td class="p-4 hidden lg:table-cell text-gray-500">{{ permit.barangay }}</td>
                <td class="p-4 text-center font-mono text-xs">
                  {{ permit.renewal_year }} / Q{{ permit.quarter }}
                </td>
                <td class="p-4">
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                    :class="{
                      'bg-green-100 text-green-800': permit.status === 'Active',
                      'bg-red-100 text-red-800': permit.status === 'Expired',
                      'bg-yellow-100 text-yellow-800': permit.status === 'Suspended'
                    }">
                    {{ permit.status }}
                  </span>
                </td>
                <td class="p-4 text-right relative">
                  <button @click="toggleActionMenu(permit.id)" class="p-2 hover:bg-gray-100 rounded-full transition">
                    <MoreHorizontalIcon class="h-4 w-4 text-gray-500" />
                  </button>
                  
                  <div v-if="activeActionMenu === permit.id" 
                    class="absolute right-4 mt-1 w-48 bg-white border rounded-lg shadow-xl z-30 py-1 overflow-hidden text-left">
                    <button @click="openUpdateModal(permit)" class="flex items-center w-full px-4 py-2 text-xs hover:bg-gray-50">
                      <Edit3Icon class="mr-2 h-3 w-3 text-gray-400" /> Edit Record
                    </button>
                    <button @click="confirmRenewal(permit)" class="flex items-center w-full px-4 py-2 text-xs hover:bg-gray-50 text-blue-600">
                      <RefreshCwIcon class="mr-2 h-3 w-3" /> Renew Permit
                    </button>
                    <button @click="confirmPrint(permit)" class="flex items-center w-full px-4 py-2 text-xs hover:bg-gray-50">
                      <PrinterIcon class="mr-2 h-3 w-3 text-gray-400" /> Print Certificate
                    </button>
                    <div class="border-t my-1"></div>
                    <button @click="confirmDelete(permit)" class="flex items-center w-full px-4 py-2 text-xs hover:bg-gray-50 text-red-600">
                      <Trash2Icon class="mr-2 h-3 w-3" /> Delete
                    </button>
                  </div>
                </td>
              </tr>
              <tr v-if="sanitaryPermits.data.length === 0">
                <td colspan="6" class="p-8 text-center text-gray-500">No records found for the selected criteria.</td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="px-4 py-3 bg-gray-50 border-t flex items-center justify-between">
          <div class="text-xs text-gray-500">
            Showing {{ sanitaryPermits.from }} to {{ sanitaryPermits.to }} of {{ sanitaryPermits.total }} results
          </div>
          <div class="flex gap-2">
            <button v-if="sanitaryPermits.prev_page_url" @click="fetchData(sanitaryPermits.prev_page_url)"
              class="px-3 py-1 text-xs border bg-white rounded-md hover:bg-gray-100">Previous</button>
            <button v-if="sanitaryPermits.next_page_url" @click="fetchData(sanitaryPermits.next_page_url)"
              class="px-3 py-1 text-xs border bg-white rounded-md hover:bg-gray-100">Next</button>
          </div>
        </div>
      </div>
    </div>

    <AddPermitModal :show="showAddDialog" @close="showAddDialog = false" />
    <UpdatePermitModal :show="isUpdateModalOpen" :permit="selectedPermit" @close="isUpdateModalOpen = false" />

    <div v-if="showAdvancedSearch" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
      <div class="bg-white rounded-xl shadow-2xl w-full max-w-md p-6">
        <h2 class="text-lg font-bold mb-4">Advanced Search</h2>
        <div class="space-y-3">
          <div>
            <label class="text-xs font-semibold mb-1 block">Establishment Name</label>
            <input v-model="searchCriteria.name_of_establishment" class="w-full border rounded-md p-2 text-sm" placeholder="Search establishment...">
          </div>
          <div>
            <label class="text-xs font-semibold mb-1 block">Owner Name</label>
            <input v-model="searchCriteria.name_of_owner" class="w-full border rounded-md p-2 text-sm" placeholder="Search owner...">
          </div>
          <div>
            <label class="text-xs font-semibold mb-1 block">Barangay</label>
            <input v-model="searchCriteria.barangay" class="w-full border rounded-md p-2 text-sm" placeholder="Search barangay...">
          </div>
        </div>
        <div class="flex justify-end gap-2 mt-6">
          <button @click="showAdvancedSearch = false" class="px-4 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-md">Cancel</button>
          <button @click="performSearch(); showAdvancedSearch = false" class="px-4 py-2 text-sm bg-blue-600 text-white rounded-md hover:bg-blue-700">Apply Filters</button>
        </div>
      </div>
    </div>

    <div v-if="isDeleteModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
      <div class="bg-white rounded-lg p-6 max-w-sm w-full shadow-2xl">
        <div class="flex items-center gap-3 text-red-600 mb-2">
          <AlertCircleIcon class="h-6 w-6" />
          <h3 class="text-lg font-bold">Confirm Deletion</h3>
        </div>
        <p class="text-sm text-gray-500">
          Are you sure you want to delete the permit for <strong>{{ pendingPermitAction?.name_of_establishment }}</strong>? This action cannot be undone.
        </p>
        <div class="flex justify-end gap-3 mt-6">
          <button @click="isDeleteModalOpen = false" class="px-4 py-2 text-sm border rounded-md hover:bg-gray-50">Cancel</button>
          <button @click="executeDelete" class="px-4 py-2 text-sm bg-red-600 text-white rounded-md hover:bg-red-700 font-medium">Delete Permanently</button>
        </div>
      </div>
    </div>

    <div v-if="isRenewModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
      <div class="bg-white rounded-lg p-6 max-w-sm w-full shadow-2xl">
        <div class="flex items-center gap-3 text-blue-600 mb-2">
          <RefreshCwIcon class="h-6 w-6" />
          <h3 class="text-lg font-bold">Renew Permit</h3>
        </div>
        <p class="text-sm text-gray-500">
          Do you want to renew the permit for <strong>{{ pendingPermitAction?.name_of_establishment }}</strong> for the next period?
        </p>
        <div class="flex justify-end gap-3 mt-6">
          <button @click="isRenewModalOpen = false" class="px-4 py-2 text-sm border rounded-md hover:bg-gray-50">Cancel</button>
          <button @click="executeRenewal" class="px-4 py-2 text-sm bg-blue-600 text-white rounded-md hover:bg-blue-700 font-medium">Yes, Renew</button>
        </div>
      </div>
    </div>

    <div v-if="isPrintModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
      <div class="bg-white rounded-lg p-6 max-w-sm w-full shadow-2xl">
        <div class="flex items-center gap-3 text-gray-700 mb-2">
          <PrinterIcon class="h-6 w-6" />
          <h3 class="text-lg font-bold">Print Certificate</h3>
        </div>
        <p class="text-sm text-gray-500">
          Would you like to generate and print the sanitary certificate for <strong>{{ pendingPermitAction?.name_of_establishment }}</strong>?
        </p>
        <div class="flex justify-end gap-3 mt-6">
          <button @click="isPrintModalOpen = false" class="px-4 py-2 text-sm border rounded-md hover:bg-gray-50">Cancel</button>
          <button @click="executePrint" class="px-4 py-2 text-sm bg-gray-900 text-white rounded-md hover:bg-gray-800 font-medium">Print Now</button>
        </div>
      </div>
    </div>

  </AppLayout>
</template>