<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import PlaceholderPattern from '../components/PlaceholderPattern.vue';
import { ref, computed } from 'vue'
import { 
  PlusIcon, 
  DownloadIcon, 
  SearchIcon, 
  FilterIcon, 
  MoreHorizontalIcon 
} from 'lucide-vue-next'

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];




// Sample data for sanitary permits
const permitData = ref([
  {
    id: "SP-2023-001",
    businessName: "Fresh Mart Grocery",
    owner: "John Smith",
    address: "123 Main St, Downtown",
    issueDate: "2023-01-15",
    expiryDate: "2023-12-31",
    status: "Active",
  },
  {
    id: "SP-2023-002",
    businessName: "Sunrise Bakery",
    owner: "Maria Garcia",
    address: "456 Oak Ave, Westside",
    issueDate: "2023-02-10",
    expiryDate: "2023-12-31",
    status: "Active",
  },
  {
    id: "SP-2023-003",
    businessName: "Golden Restaurant",
    owner: "Robert Chen",
    address: "789 Pine St, Northend",
    issueDate: "2023-02-22",
    expiryDate: "2023-12-31",
    status: "Active",
  },
  {
    id: "SP-2023-004",
    businessName: "City Pharmacy",
    owner: "Sarah Johnson",
    address: "101 Elm St, Eastside",
    issueDate: "2023-03-05",
    expiryDate: "2023-12-31",
    status: "Active",
  },
  {
    id: "SP-2023-005",
    businessName: "Lakeside Cafe",
    owner: "Michael Brown",
    address: "202 Lake Rd, Southside",
    issueDate: "2023-03-18",
    expiryDate: "2023-12-31",
    status: "Expired",
  },
  {
    id: "SP-2023-006",
    businessName: "Fresh Seafood Market",
    owner: "David Wilson",
    address: "303 Harbor Dr, Portside",
    issueDate: "2023-04-02",
    expiryDate: "2023-12-31",
    status: "Suspended",
  },
  {
    id: "SP-2023-007",
    businessName: "Green Valley Farm Store",
    owner: "Lisa Martinez",
    address: "404 Valley Rd, Countryside",
    issueDate: "2023-04-15",
    expiryDate: "2023-12-31",
    status: "Active",
  },
])

const searchTerm = ref('')
const statusFilter = ref('all')
const isFilterOpen = ref(false)
const activeActionMenu = ref(null)
const showAddDialog = ref(false)

const filteredPermits = computed(() => {
  return permitData.value.filter((permit) => {
    const matchesSearch = 
      permit.businessName.toLowerCase().includes(searchTerm.value.toLowerCase()) ||
      permit.owner.toLowerCase().includes(searchTerm.value.toLowerCase()) ||
      permit.id.toLowerCase().includes(searchTerm.value.toLowerCase())
    
    const matchesStatus = 
      statusFilter.value === 'all' || 
      permit.status.toLowerCase() === statusFilter.value.toLowerCase()
    
    return matchesSearch && matchesStatus
  })
})

function setStatusFilter(status) {
  statusFilter.value = status
}

function toggleActionMenu(id) {
  if (activeActionMenu.value === id) {
    activeActionMenu.value = null
  } else {
    activeActionMenu.value = id
  }
}
</script>

<template>
    <div class="flex flex-col gap-6">
      <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-3xl font-bold">Sanitary Permits</h1>
        <div class="flex items-center gap-2">
          <button @click="showAddDialog = true" class="btn-primary">
          <PlusIcon class="mr-2 h-4 w-4" />
          Add New Permit
        </button>

        <AddPermitDialog :show="showAddDialog" @close="showAddDialog = false" />

        </div>
      </div>
      
      <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
        <div class="flex flex-col space-y-1.5 p-6 pb-3">
          <h3 class="text-lg font-semibold">Sanitary Permits Management</h3>
          <p class="text-sm text-muted-foreground">
            Manage and track all sanitary permits issued to businesses.
          </p>
        </div>
        <div class="p-6 pt-0">
          <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between mb-4">
            <div class="relative w-full sm:w-96">
              <SearchIcon class="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground" />
              <input
                v-model="searchTerm"
                placeholder="Search permits..."
                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 pl-8 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
              />
            </div>
            <div class="flex items-center gap-2">
              <div class="relative">
                <button 
                  @click="isFilterOpen = !isFilterOpen"
                  class="inline-flex items-center justify-center rounded-md border border-input bg-background px-3 py-2 text-sm font-medium shadow-sm hover:bg-accent hover:text-accent-foreground h-10"
                >
                  <FilterIcon class="mr-2 h-4 w-4" />
                  Filter
                </button>
                <div 
                  v-if="isFilterOpen" 
                  class="absolute right-0 z-10 mt-2 w-56 origin-top-right rounded-md border bg-popover text-popover-foreground shadow-md"
                >
                  <div class="p-2">
                    <button 
                      @click="setStatusFilter('all'); isFilterOpen = false"
                      class="relative flex w-full cursor-default select-none items-center rounded-sm px-2 py-1.5 text-sm outline-none hover:bg-accent hover:text-accent-foreground"
                    >
                      All Statuses
                    </button>
                    <button 
                      @click="setStatusFilter('active'); isFilterOpen = false"
                      class="relative flex w-full cursor-default select-none items-center rounded-sm px-2 py-1.5 text-sm outline-none hover:bg-accent hover:text-accent-foreground"
                    >
                      Active Only
                    </button>
                    <button 
                      @click="setStatusFilter('expired'); isFilterOpen = false"
                      class="relative flex w-full cursor-default select-none items-center rounded-sm px-2 py-1.5 text-sm outline-none hover:bg-accent hover:text-accent-foreground"
                    >
                      Expired Only
                    </button>
                    <button 
                      @click="setStatusFilter('suspended'); isFilterOpen = false"
                      class="relative flex w-full cursor-default select-none items-center rounded-sm px-2 py-1.5 text-sm outline-none hover:bg-accent hover:text-accent-foreground"
                    >
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
                  <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">Permit ID</th>
                  <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">Business Name</th>
                  <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">Owner</th>
                  <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground hidden md:table-cell">Issue Date</th>
                  <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground hidden md:table-cell">Expiry Date</th>
                  <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">Status</th>
                  <th class="h-12 px-4 text-right align-middle font-medium text-muted-foreground">Actions</th>
                </tr>
              </thead>
              <tbody class="[&_tr:last-child]:border-0">
                <tr 
                  v-for="permit in filteredPermits" 
                  :key="permit.id"
                  class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted"
                >
                  <td class="p-4 align-middle font-medium">{{ permit.id }}</td>
                  <td class="p-4 align-middle">{{ permit.businessName }}</td>
                  <td class="p-4 align-middle">{{ permit.owner }}</td>
                  <td class="p-4 align-middle hidden md:table-cell">{{ permit.issueDate }}</td>
                  <td class="p-4 align-middle hidden md:table-cell">{{ permit.expiryDate }}</td>
                  <td class="p-4 align-middle">
                    <span 
                      class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold"
                      :class="{
                        'bg-primary text-primary-foreground': permit.status === 'Active',
                        'bg-destructive text-destructive-foreground': permit.status === 'Expired',
                        'bg-muted text-muted-foreground': permit.status === 'Suspended'
                      }"
                    >
                      {{ permit.status }}
                    </span>
                  </td>
                  <td class="p-4 align-middle text-right">
                    <div class="relative">
                      <button 
                        @click="toggleActionMenu(permit.id)"
                        class="inline-flex h-8 w-8 items-center justify-center rounded-md border border-input bg-transparent p-0 text-sm font-medium shadow-sm hover:bg-accent hover:text-accent-foreground"
                      >
                        <MoreHorizontalIcon class="h-4 w-4" />
                        <span class="sr-only">Open menu</span>
                      </button>
                      <div 
                        v-if="activeActionMenu === permit.id" 
                        class="absolute right-0 z-10 mt-2 w-56 origin-top-right rounded-md border bg-popover text-popover-foreground shadow-md"
                      >
                        <div class="p-2">
                          <button 
                            @click="activeActionMenu = null"
                            class="relative flex w-full cursor-default select-none items-center rounded-sm px-2 py-1.5 text-sm outline-none hover:bg-accent hover:text-accent-foreground"
                          >
                            View Details
                          </button>
                          <button 
                            @click="activeActionMenu = null"
                            class="relative flex w-full cursor-default select-none items-center rounded-sm px-2 py-1.5 text-sm outline-none hover:bg-accent hover:text-accent-foreground"
                          >
                            Edit Permit
                          </button>
                          <button 
                            @click="activeActionMenu = null"
                            class="relative flex w-full cursor-default select-none items-center rounded-sm px-2 py-1.5 text-sm outline-none hover:bg-accent hover:text-accent-foreground"
                          >
                            Renew Permit
                          </button>
                          <button 
                            @click="activeActionMenu = null"
                            class="relative flex w-full cursor-default select-none items-center rounded-sm px-2 py-1.5 text-sm outline-none hover:bg-accent hover:text-accent-foreground"
                          >
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
      
      <!-- Add Permit Dialog -->
      <div v-if="showAddDialog" class="fixed inset-0 z-50 bg-background/80 backdrop-blur-sm" @click="showAddDialog = false"></div>
      <div v-if="showAddDialog" class="fixed left-[50%] top-[50%] z-50 max-h-[85vh] w-[90vw] max-w-[550px] translate-x-[-50%] translate-y-[-50%] rounded-lg border bg-background p-6 shadow-lg">
        <div class="flex flex-col space-y-1.5 pb-6">
          <h3 class="text-lg font-semibold">Add New Sanitary Permit</h3>
          <p class="text-sm text-muted-foreground">
            Enter the details for the new sanitary permit.
          </p>
        </div>
        <div class="grid gap-4 py-4">
          <div class="grid grid-cols-2 gap-4">
            <div class="grid gap-2">
              <label for="businessName" class="text-sm font-medium">Business Name</label>
              <input id="businessName" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2" />
            </div>
            <div class="grid gap-2">
              <label for="owner" class="text-sm font-medium">Owner Name</label>
              <input id="owner" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2" />
            </div>
          </div>
          <div class="grid gap-2">
            <label for="address" class="text-sm font-medium">Business Address</label>
            <input id="address" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2" />
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div class="grid gap-2">
              <label for="issueDate" class="text-sm font-medium">Issue Date</label>
              <input id="issueDate" type="date" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2" />
            </div>
            <div class="grid gap-2">
              <label for="expiryDate" class="text-sm font-medium">Expiry Date</label>
              <input id="expiryDate" type="date" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2" />
            </div>
          </div>
          <div class="grid gap-2">
            <label for="status" class="text-sm font-medium">Status</label>
            <select id="status" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2">
              <option value="" disabled selected>Select status</option>
              <option value="active">Active</option>
              <option value="expired">Expired</option>
              <option value="suspended">Suspended</option>
            </select>
          </div>
        </div>
        <div class="flex justify-end pt-6">
          <button 
            @click="showAddDialog = false"
            class="inline-flex items-center justify-center rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground shadow transition-colors hover:bg-primary/90"
          >
            Save Permit
          </button>
        </div>
      </div>
    </div>
  </template>
