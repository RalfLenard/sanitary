<template>

    <Head title="Health Card" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl ">
            <div class="w-full bg-white rounded-lg shadow-md">
                <div class="flex flex-col p-6 border-b gap-4">
                    <div class="flex flex-row items-center justify-between">
                        <div>
                            <h2 class="text-xl font-semibold">Health Cards Dashboard</h2>
                            <p class="text-sm text-gray-500">Manage your health cards, add new ones, or print existing
                                cards.</p>
                        </div>
                        <div class="flex gap-2">
                            <button
                                class="inline-flex items-center px-4 py-2 border rounded-md text-sm font-medium shadow-sm bg-white text-gray-700 hover:bg-gray-50"
                                @click="generatePDF" :disabled="healthCards.length === 0">
                                <Printer class="mr-2 h-4 w-4" />
                                Print {{ selectedCards.length > 0 ? `(${selectedCards.length})` : 'All' }}
                            </button>
                            <button
                                class="inline-flex items-center px-4 py-2 rounded-md text-sm font-medium shadow-sm bg-primary text-white hover:bg-primary/90"
                                @click="showModal = true">
                                <Plus class="mr-2 h-4 w-4" />
                                Add New Card
                            </button>
                        </div>
                    </div>

                    <!-- Basic Search -->
                    <div class="flex flex-col md:flex-row gap-4">
                        <div class="relative flex-grow">
                            <Search class="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-gray-400" />
                            <input type="text" v-model="searchTerm" @input="performSearch"
                                placeholder="Search by name, card number, or type..."
                                class="pl-10 pr-4 py-2 w-full border rounded-md focus:ring-primary focus:border-primary" />
                        </div>
                        <div class="flex gap-2">
                            <select v-model="sortOption"
                                class="px-4 py-2 border rounded-md text-sm font-medium shadow-sm bg-white text-gray-700">
                                <option value="created-desc">Created At (Newest)</option>
                                <option value="created-asc">Created At (Oldest)</option>

                                <option value="name-asc">Name (A-Z)</option>
                                <option value="name-desc">Name (Z-A)</option>
                                <option value="date-asc">Issue Date (Oldest)</option>
                                <option value="date-desc">Issue Date (Newest)</option>
                                <option value="expiry-asc">Expiry Date (Soonest)</option>
                                <option value="expiry-desc">Expiry Date (Latest)</option>

                            </select>

                            <select v-model="categoryFilter"
                                class="px-4 py-2 border rounded-md text-sm font-medium shadow-sm bg-white text-gray-700">
                                <option value="all">All Categories</option>
                                <option value="food">Food</option>
                                <option value="non_food">Non-Food</option>
                                <option value="others">Others</option>
                                <option value="NOT_PRINTED">Not Printed</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <div class="rounded-md border">
                        <!-- Added overflow container -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50 sticky top-0 z-10">
                                    <tr>
                                        <th scope="col"
                                            class="w-[50px] px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            <input type="checkbox" ref="selectAllCheckbox" :checked="allSelected"
                                                @change="toggleSelectAll"
                                                class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded"
                                                aria-label="Select all cards" />
                                        </th>

                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                                            @click="setSorting('serialCode')">
                                            <div class="flex items-center">
                                                Serial Code
                                                <ArrowUpDown class="ml-1 h-4 w-4" />
                                            </div>
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                                            @click="setSorting('full_name')">
                                            <div class="flex items-center">
                                                Full Name
                                                <ArrowUpDown class="ml-1 h-4 w-4" />
                                            </div>
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Age
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Gender
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Place of Employment
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Designation
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                                            @click="setSorting('issueDate')">
                                            <div class="flex items-center">
                                                Issue Date
                                                <ArrowUpDown class="ml-1 h-4 w-4" />
                                            </div>
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                                            @click="setSorting('expiryDate')">
                                            <div class="flex items-center">
                                                Expiry Date
                                                <ArrowUpDown class="ml-1 h-4 w-4" />
                                            </div>
                                        </th>

                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hidden"
                                            @click="setSorting('created-desc')">
                                            <div class="flex items-center">
                                                Created At
                                                <ArrowUpDown class="ml-1 h-4 w-4" />
                                            </div>
                                        </th>

                                        <th scope="col"
                                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200" style="text-transform: uppercase;">
                                    <tr v-if="filteredCards.length === 0">
                                        <td colspan="11" class="px-6 py-12 text-center text-sm text-gray-500">
                                            No health cards found matching your criteria.
                                        </td>
                                    </tr>
                                    <tr v-for="card in filteredCards" :key="card.id" class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="checkbox" :checked="selectedCards.includes(card.id)"
                                                @change="toggleCardSelection(card.id)"
                                                class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded" />
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ card.print_code }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ card.full_name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ card.age }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ card.gender }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ card.place_of_employment }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ card.designation }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ formatDate(card.date_of_issuance) }}
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ formatDate(card.date_of_expiration) }}
                                        </td>

                                        <td class="hidden">
                                            {{ formatDate(card.created_at) }}
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex justify-end gap-2">
                                                <button class="text-gray-400 hover:text-gray-500"
                                                    @click="openEditModal(card)">
                                                    <Edit class="h-4 w-4" />
                                                    <span class="sr-only">Edit</span>
                                                </button>
                                                <button class="text-gray-400 hover:text-red-500"
                                                    @click="confirmDelete(card.id)">
                                                    <Trash2 class="h-4 w-4" />
                                                    <span class="sr-only">Delete</span>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div>
                        <!-- Improved Pagination -->
                        <div class="flex items-center justify-between mt-6">
                            <div class="text-sm text-gray-500">
                                Showing <span class="font-medium">{{ filteredCards.length }}</span> of <span
                                    class="font-medium">{{ props.pagination.total }}</span> results
                            </div>
                            <div class="flex items-center space-x-2">
                                <button @click="changePage(props.pagination.current_page - 1)"
                                    :disabled="props.pagination.current_page === 1"
                                    class="inline-flex items-center px-3 py-2 border rounded-md text-sm font-medium shadow-sm bg-white text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                                    Previous
                                </button>

                                <div class="flex items-center space-x-1">
                                    <!-- First page -->
                                    <button v-if="props.pagination.current_page > 3" @click="changePage(1)"
                                        class="inline-flex items-center px-3 py-2 border rounded-md text-sm font-medium shadow-sm bg-white text-gray-700 hover:bg-gray-50">
                                        1
                                    </button>

                                    <!-- Ellipsis if needed -->
                                    <span v-if="props.pagination.current_page > 4" class="px-2">...</span>

                                    <!-- Page before current -->
                                    <button v-if="props.pagination.current_page > 1"
                                        @click="changePage(props.pagination.current_page - 1)"
                                        class="inline-flex items-center px-3 py-2 border rounded-md text-sm font-medium shadow-sm bg-white text-gray-700 hover:bg-gray-50">
                                        {{ props.pagination.current_page - 1 }}
                                    </button>

                                    <!-- Current page -->
                                    <button
                                        class="inline-flex items-center px-3 py-2 border rounded-md text-sm font-medium shadow-sm bg-primary text-white">
                                        {{ props.pagination.current_page }}
                                    </button>

                                    <!-- Page after current -->
                                    <button v-if="props.pagination.current_page < props.pagination.last_page"
                                        @click="changePage(props.pagination.current_page + 1)"
                                        class="inline-flex items-center px-3 py-2 border rounded-md text-sm font-medium shadow-sm bg-white text-gray-700 hover:bg-gray-50">
                                        {{ props.pagination.current_page + 1 }}
                                    </button>

                                    <!-- Ellipsis if needed -->
                                    <span v-if="props.pagination.current_page < props.pagination.last_page - 3"
                                        class="px-2">...</span>

                                    <!-- Last page -->
                                    <button v-if="props.pagination.current_page < props.pagination.last_page - 2"
                                        @click="changePage(props.pagination.last_page)"
                                        class="inline-flex items-center px-3 py-2 border rounded-md text-sm font-medium shadow-sm bg-white text-gray-700 hover:bg-gray-50">
                                        {{ props.pagination.last_page }}
                                    </button>
                                </div>

                                <button @click="changePage(props.pagination.current_page + 1)"
                                    :disabled="props.pagination.current_page === props.pagination.last_page"
                                    class="inline-flex items-center px-3 py-2 border rounded-md text-sm font-medium shadow-sm bg-white text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                                    Next
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add Health Card Modal -->
            <AddHealthCardModal v-if="showModal" @close="closeModal" />

            <AddUpdateHealthCardModal :show="isModalOpen" :editingCard="selectedCard" @closeModal="closeModalEdit"
                @refreshData="refreshData" />

            <!-- Delete Confirmation Modal -->
            <div v-if="isDeleteModalOpen"
                class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
                    <div class="p-6">
                        <h3 class="text-lg font-medium">Are you sure?</h3>
                        <p class="mt-2 text-sm text-gray-500">
                            This action cannot be undone. This will permanently delete the health card.
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

        <FlashToast />
    </AppLayout>
</template>

<script setup lang="ts">
// Script content remains unchanged
import { ref, reactive, computed, watch, onMounted } from 'vue';
import { Printer, Plus, Edit, Trash2, Search, SlidersHorizontal, ArrowUpDown } from 'lucide-vue-next';
import { defineProps } from 'vue';
import { Head, useForm, router, usePage } from '@inertiajs/vue3';
import { Inertia } from '@inertiajs/inertia';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import AddHealthCardModal from "@/components/AddHealthCardModal.vue";
import AddUpdateHealthCardModal from "@/components/AddUpdateHealthCardModal.vue";
import PaginationHealthCard from "@/components/PaginationHealtCard.vue";
import { route } from 'ziggy-js';
import FlashToast from '@/components/FlashToast.vue'; 

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [{ title: 'Health Card', href: '/health-card' }];

const showModal = ref(false);
const closeModal = () => { showModal.value = false; };
// Props for Health Cards
const props = defineProps<{
    healthCards: Array<{
        id: string;
        full_name: string;
        cardNumber: string;
        issueDate: string;
        date_of_expiration: string;
        created_at: string;
        type: string;
        status: string;
        health_card_type: string;
        print_code: string;
        age: number;
        gender: string;
        place_of_employment: string;
        designation: string;
        date_of_issuance: string;
        confirmed: boolean; // ✅ added confirmed here
    }>;
    pagination: {
        current_page: number;
        last_page: number;
        total: number;
    };
    filters: {
        search: string;
        category: string;
        sort: string;
    };
}>();


// Reactive variables for filtering, with default values
const categoryFilter = ref(props.filters.category || 'all');

// Watchers to handle filter changes and ensure data reload on category change
watch(() => categoryFilter.value, (newCategory) => {
    // Ensure page number stays intact and change category filter only
    const currentPage = props.pagination.current_page;
    Inertia.visit(`/health-card?page=${currentPage}&search=${props.filters.search || ''}&category=${newCategory}&sort=${sortOption.value || ''}`, {
        method: 'get',
        preserveState: false, // We want to reload the page
        replace: true,
    });
});

const sortOption = ref(props.filters.sort || 'created-desc');

watch(() => props.filters.sort, (newSort) => {
    sortOption.value = newSort;
});

// Computed Filtered Health Cards
const filteredCards = computed(() => {
    let result = [...props.healthCards];

    // Apply category filter
    // Apply category filter
    if (categoryFilter.value !== 'all') {
        if (categoryFilter.value === 'NOT_PRINTED') {
            // Filter for not printed cards (where confirmed is 0)
            result = result.filter(card => card.confirmed === 0);
        } else {
            // Filter by health card type
            result = result.filter(card => card.health_card_type === categoryFilter.value);
        }
    }


    // Apply sorting
    if (sortOption.value === 'created-asc') {
        result.sort((a, b) => new Date(a.created_at).getTime() - new Date(b.created_at).getTime());
    } else if (sortOption.value === 'created-desc') {
        result.sort((a, b) => new Date(b.created_at).getTime() - new Date(a.created_at).getTime());
    } else if (sortOption.value === 'date-asc') {
        result.sort((a, b) => new Date(a.issueDate).getTime() - new Date(b.issueDate).getTime());
    } else if (sortOption.value === 'date-desc') {
        result.sort((a, b) => new Date(b.issueDate).getTime() - new Date(a.issueDate).getTime());
    } else if (sortOption.value === 'expiry-asc') {
        result.sort((a, b) => new Date(a.date_of_expiration).getTime() - new Date(b.date_of_expiration).getTime());
    } else if (sortOption.value === 'expiry-desc') {
        result.sort((a, b) => new Date(b.date_of_expiration).getTime() - new Date(a.date_of_expiration).getTime());
    } else if (sortOption.value === 'name-asc') {
        result.sort((a, b) => (a.full_name || '').localeCompare(b.full_name || ''));
    } else if (sortOption.value === 'name-desc') {
        result.sort((a, b) => (b.full_name || '').localeCompare(a.full_name || ''));
    }

    return result;
});

// Change Page Logic
const changePage = (page) => {
    if (page >= 1 && page <= props.pagination.last_page) {
        Inertia.visit(`/health-card?page=${page}&search=${props.filters.search || ''}&category=${categoryFilter.value || ''}&sort=${sortOption.value || ''}`, {
            method: 'get',
            preserveState: true,
            replace: true,
        });
    }
};
// Format Date Function
const formatDate = (dateString: string) => {
    if (!dateString) return "N/A";
    return new Date(dateString).toLocaleDateString("en-US", {
        year: "numeric",
        month: "long",
        day: "numeric",
    });
};

// Checkbox Selection Logic
const selectedCards = ref<string[]>([]);
const selectAllCheckbox = ref<HTMLInputElement | null>(null);

const allSelected = computed(() =>
    filteredCards.value.length > 0 && selectedCards.value.length === filteredCards.value.length
);

const someSelected = computed(() =>
    selectedCards.value.length > 0 && selectedCards.value.length < filteredCards.value.length
);

function toggleCardSelection(cardId: string) {
    const index = selectedCards.value.indexOf(cardId);
    if (index === -1) {
        selectedCards.value.push(cardId);
    } else {
        selectedCards.value.splice(index, 1);
    }
}

function toggleSelectAll() {
    if (allSelected.value) {
        selectedCards.value = [];
    } else {
        selectedCards.value = filteredCards.value.map(card => card.id);
    }
}

// Watch for changes and update the indeterminate state
watch(selectedCards, () => {
    if (selectAllCheckbox.value) {
        selectAllCheckbox.value.indeterminate = someSelected.value;
    }
});

const generatePDF = () => {
    console.log("Button clicked!"); // Log for debugging

    if (selectedCards.value.length === 0) {
        alert("Please select at least one card to generate a PDF.");
        return;
    }

    // Check if Laravel routes are defined
    const page = usePage();
    if (!page.props.routes.generate_pdf) {
        console.error("Laravel routes not defined.");
        return;
    }

    // Generate the URL by appending selected card IDs as a query parameter
    const url = page.props.routes.generate_pdf + '?selected_health_cards=' + selectedCards.value.join(',');

    // Open the generated PDF in a new tab
    window.open(url, '_blank');
};

const searchTerm = ref('');

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

    Inertia.delete(route('health-card.delete', cardToDeleteId.value), {
        onSuccess: () => {
            FlashToast.success('Success', 'Health card deleted successfully.', 3000); // Show success toast for 3 seconds
            isDeleteModalOpen.value = false;
            cardToDeleteId.value = null;
        },
        onError: (errors) => {
            FlashToast.error('Error', 'Failed to delete the health card.', 3000); // Show error toast for 3 seconds
            alert("Failed to delete the health card.");
        }
    });
};

const isModalOpen = ref(false);
const selectedCard = ref(null); // This will hold the card data for editing

// Open the modal with the selected health card data
const openEditModal = (card) => {
    selectedCard.value = card; // Set the card to be edited
    isModalOpen.value = true; // Show the modal
};

// Close the modal
const closeModalEdit = () => {
    isModalOpen.value = false; // Hide the modal
};

// Refresh the data after a successful update
const refreshData = () => {
    // You can trigger an API call here to fetch the updated list of health cards
    // this.$inertia.get('/health-cards');
};

const searchCriteria = ref({
    full_name: '',
    place_of_employement: '',
    barangay: '',
    permit_code: '',
    renewal_year: ''
});

const performSearch = () => {
    router.get(route('healthCard'), {
        search: searchTerm.value,
        full_name: searchCriteria.value.full_name,
        place_of_employement: searchCriteria.value.place_of_employement,
    }, {
        preserveState: true,
        preserveScroll: true
    });
};

const sortingColumn = ref(null);
const sortingOrder = ref('asc');

const setSorting = (column) => {
    if (sortingColumn.value === column) {
        sortingOrder.value = sortingOrder.value === 'asc' ? 'desc' : 'asc';
    } else {
        sortingColumn.value = column;
        sortingOrder.value = 'asc';
    }
};
</script>

<style scoped>
/* Add some styling for the pagination */
.pagination-button {
    transition: all 0.2s ease;
}

.pagination-button:hover:not(:disabled) {
    transform: translateY(-1px);
}

.pagination-button:active:not(:disabled) {
    transform: translateY(0);
}
</style>