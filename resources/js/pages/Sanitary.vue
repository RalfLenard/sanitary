<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import { MoreHorizontalIcon, SearchIcon, PlusIcon, FilterIcon, CalendarIcon } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';
import { debounce } from 'lodash';

const props = defineProps<{ 
    sanitaryPermits: any; 
    quarterlyData: any;
    filters: any;
}>();

// State
const searchTerm = ref(props.filters.search || '');
const selectedYear = ref(props.filters.renewal_year || new Date().getFullYear());
const selectedQuarter = ref(props.filters.quarter || null);
const isFilterOpen = ref(false);
const showAddDialog = ref(false);

// Generate year options (e.g., last 5 years)
const yearOptions = computed(() => {
    const current = new Date().getFullYear();
    return Array.from({ length: 10 }, (_, i) => current - i);
});

// Centralized Search Function
const performSearch = debounce(() => {
    router.get(route('sanitary'), {
        search: searchTerm.value,
        renewal_year: selectedYear.value,
        quarter: selectedQuarter.value,
        // ... any other advanced criteria
    }, {
        preserveState: true,
        preserveScroll: true,
        replace: true
    });
}, 300);

const setQuarterFilter = (quarter: number | null) => {
    selectedQuarter.value = quarter;
    isFilterOpen.value = false;
    performSearch();
};

const setYearFilter = (year: number) => {
    selectedYear.value = year;
    performSearch();
};

const selectedQuarterLabel = computed(() => selectedQuarter.value ? `Q${selectedQuarter.value}` : "All Quarters");
</script>

<template>
    <AppLayout title="Sanitary Permits">
        <div class="p-6">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
                <h1 class="text-2xl font-bold">Sanitary Permits ({{ selectedYear }})</h1>
                
                <div class="flex flex-wrap gap-2">
                    <div class="relative">
                        <select 
                            v-model="selectedYear" 
                            @change="setYearFilter(selectedYear)"
                            class="pl-8 pr-4 py-2 border rounded-md bg-white text-sm focus:ring-2 focus:ring-blue-500 appearance-none"
                        >
                            <option v-for="year in yearOptions" :key="year" :value="year">Year: {{ year }}</option>
                        </select>
                        <CalendarIcon class="absolute left-2.5 top-2.5 h-4 w-4 text-gray-400" />
                    </div>

                    <div class="relative">
                        <button @click="isFilterOpen = !isFilterOpen" class="inline-flex items-center px-4 py-2 border rounded-md bg-white text-sm font-medium hover:bg-gray-50">
                            <FilterIcon class="mr-2 h-4 w-4" /> {{ selectedQuarterLabel }}
                        </button>
                        <div v-if="isFilterOpen" class="absolute right-0 z-50 mt-2 w-48 bg-white border rounded-md shadow-lg">
                            <button @click="setQuarterFilter(null)" class="block w-full text-left px-4 py-2 hover:bg-gray-100">All Quarters</button>
                            <button v-for="q in [1, 2, 3, 4]" :key="q" @click="setQuarterFilter(q)" class="block w-full text-left px-4 py-2 hover:bg-gray-100">Quarter {{ q }}</button>
                        </div>
                    </div>

                    <button @click="showAddDialog = true" class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium flex items-center hover:bg-blue-700">
                        <PlusIcon class="mr-2 h-4 w-4" /> Add Permit
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div v-for="data in quarterlyData" :key="data.quarter" 
                    :class="['p-4 rounded-lg border', selectedQuarter == data.quarter ? 'border-blue-500 bg-blue-50' : 'bg-white']">
                    <h3 class="text-sm font-semibold text-gray-500">Quarter {{ data.quarter }}</h3>
                    <div class="flex justify-between mt-2">
                        <p class="text-xs">New: <span class="font-bold">{{ data.new_businesses }}</span></p>
                        <p class="text-xs">Renewed: <span class="font-bold">{{ data.renewals }}</span></p>
                    </div>
                </div>
            </div>

            <div class="bg-white border rounded-xl overflow-hidden">
                <div class="p-4 border-b bg-gray-50">
                    <div class="relative max-w-sm">
                        <SearchIcon class="absolute left-3 top-2.5 h-4 w-4 text-gray-400" />
                        <input v-model="searchTerm" @input="performSearch" placeholder="Search by name, owner or code..." class="pl-10 w-full border rounded-md p-2 text-sm" />
                    </div>
                </div>
                
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 uppercase text-xs font-bold text-gray-600">
                        <tr>
                            <th class="p-4">Establishment</th>
                            <th class="p-4">Owner</th>
                            <th class="p-4">Barangay</th>
                            <th class="p-4">Year/Qtr</th>
                            <th class="p-4">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <tr v-for="permit in sanitaryPermits.data" :key="permit.id" class="hover:bg-gray-50 uppercase">
                            <td class="p-4 font-medium">{{ permit.name_of_establishment }}</td>
                            <td class="p-4">{{ permit.name_of_owner }}</td>
                            <td class="p-4">{{ permit.barangay }}</td>
                            <td class="p-4">{{ permit.renewal_year }} - Q{{ permit.quarter }}</td>
                            <td class="p-4">
                                <span class="px-2 py-1 rounded-full text-[10px] font-bold bg-green-100 text-green-700">ACTIVE</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>