<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import PlaceholderPattern from '../components/PlaceholderPattern.vue';
import { computed, defineProps } from 'vue'
import { 
  PlusIcon, 
  DownloadIcon, 
  SearchIcon, 
  FilterIcon, 
  MoreHorizontalIcon 
} from 'lucide-vue-next'

import AddPermitModal from "@/components/AddPermitModal.vue";


import { ref, reactive, onMounted, watch } from 'vue';
import { 
  CreditCard, 
  ClipboardCheck, 
  FileText, 
  RefreshCw, 
  TrendingUp, 
  TrendingDown 
} from 'lucide-vue-next';
import Chart from 'chart.js/auto';

// Accept props from the controller
const props = defineProps({
  chartData: Object,
  availableYears: Array,
});

const showAddDialog = ref(false);

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

// State
const yearFilter = ref(new Date().getFullYear());
const availableYears = ref([]);

onMounted(() => {
  if (props.availableYears) {
    availableYears.value = props.availableYears;
  }
});


// Chart references
const healthCardChart = ref(null);
const categoryChart = ref(null);
const sanitaryChart = ref(null);
const statusChart = ref(null);
const barangayChart = ref(null);
const rhuHealthCardChart = ref(null);

// Chart instances
let healthCardChartInstance = null;
let categoryChartInstance = null;
let sanitaryChartInstance = null;
let statusChartInstance = null;
let barangayChartInstance = null;
let rhuHealthCardChartInstance = null;

// Summary data
const summaryData = reactive({
  totalHealthCards: 0,
  healthCardGrowth: 0,
  activePermits: 0,
  permitGrowth: 0,
  newApplications: 0,
  applicationGrowth: 0,
  pendingRenewals: 0,
  renewalGrowth: 0
});

// Chart data - now using props from controller
const chartData = reactive({
  healthCards: {
    labels: [],
    datasets: []
  },
  categories: {
    labels: [],
    datasets: []
  },
  sanitary: {
    labels: [],
    datasets: []
  },
  status: {
    labels: [],
    datasets: []
  },
  barangay: {
    labels: [],
    datasets: []
  },
  rhuHealthCard: {
    labels: [],
    datasets: []
  }
});

// Initialize chart data from props
// const initializeChartData = () => {
//   if (!props.chartData) return;

//   // Update barangay chart data
//   if (props.chartData.barangay) {
//     chartData.barangay = {
//       labels: props.chartData.barangay.labels,
//       datasets: [{
//         label: 'Health Cards Issued',
//         data: props.chartData.barangay.data,
//         backgroundColor: 'rgba(79, 70, 229, 0.7)', // Indigo color
//         borderColor: 'rgb(79, 70, 229)',
//         borderWidth: 1
//       }]
//     };
//   }

const initializeChartData = () => {
  if (!props.chartData) return;

  // Generate dynamic colors for many barangays
  const generateColors = (count) => {
    const colors = [];
    for (let i = 0; i < count; i++) {
      const hue = Math.floor((360 / count) * i); // evenly distributed hues
      colors.push(`hsl(${hue}, 70%, 60%)`);
    }
    return colors;
  };

  // Update barangay chart data
  if (props.chartData.barangay) {
    const labels = props.chartData.barangay.labels;
    const data = props.chartData.barangay.data;
    const backgroundColors = generateColors(labels.length);

    chartData.barangay = {
      labels,
      datasets: [{
        label: 'Health Cards Issued',
        data,
        backgroundColor: backgroundColors,
        borderColor: backgroundColors,
        borderWidth: 1
      }]
    };
  }


  // Update RHU health card data
  if (props.chartData.rhuHealthCard) {
    chartData.rhuHealthCard = {
      labels: props.chartData.rhuHealthCard.labels,
      datasets: [{
        label: 'Health Cards Issued',
        data: props.chartData.rhuHealthCard.data,
        backgroundColor: [
          'rgba(16, 185, 129, 0.7)', // Green
          'rgba(59, 130, 246, 0.7)', // Blue
          'rgba(245, 158, 11, 0.7)', // Amber
          'rgba(139, 92, 246, 0.7)'  // Purple
        ],
        borderColor: [
          'rgb(16, 185, 129)',
          'rgb(59, 130, 246)',
          'rgb(245, 158, 11)',
          'rgb(139, 92, 246)'
        ],
        borderWidth: 1
      }]
    };
  }

  // Update monthly health card data
  if (props.chartData.healthCards) {
    chartData.healthCards = {
      labels: props.chartData.healthCards.labels,
      datasets: [{
        label: 'Health Cards Issued',
        data: props.chartData.healthCards.data,
        backgroundColor: 'rgba(59, 130, 246, 0.5)',
        borderColor: 'rgb(59, 130, 246)',
        borderWidth: 2,
        tension: 0.3,
        fill: true
      }]
    };
  }

  // Update categories data
  if (props.chartData.categories) {
    chartData.categories = {
      labels: props.chartData.categories.labels,
      datasets: [{
        label: 'Health Cards by Category',
        data: props.chartData.categories.data,
        backgroundColor: [
          'rgba(16, 185, 129, 0.7)',
          'rgba(99, 102, 241, 0.7)',
          'rgba(245, 158, 11, 0.7)'
        ],
        borderColor: [
          'rgb(16, 185, 129)',
          'rgb(99, 102, 241)',
          'rgb(245, 158, 11)'
        ],
        borderWidth: 1
      }]
    };
  }

  // Calculate total health cards for summary
  if (props.chartData.healthCards && props.chartData.healthCards.data) {
    summaryData.totalHealthCards = props.chartData.healthCards.data.reduce((sum, val) => sum + Number(val), 0);
  }
};

// Initialize charts
const initCharts = () => {
  // Health Card Chart
  if (healthCardChartInstance) {
    healthCardChartInstance.destroy();
  }
  
  healthCardChartInstance = new Chart(healthCardChart.value, {
    type: 'line',
    data: chartData.healthCards,
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'top',
        },
        tooltip: {
          mode: 'index',
          intersect: false,
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          title: {
            display: true,
            text: 'Number of Cards'
          }
        }
      }
    }
  });
  
  // Category Chart
  if (categoryChartInstance) {
    categoryChartInstance.destroy();
  }
  
  categoryChartInstance = new Chart(categoryChart.value, {
    type: 'doughnut',
    data: chartData.categories,
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'right',
        },
        tooltip: {
          callbacks: {
            label: function(context) {
              const label = context.label || '';
              const value = context.raw || 0;
              const total = context.dataset.data.reduce((a, b) => a + Number(b), 0);
              const percentage = Math.round((value / total) * 100);
              return `${label}: ${value} (${percentage}%)`;
            }
          }
        }
      }
    }
  });
  
  // Sanitary Chart - keeping this for now, but it's not connected to backend data
  if (sanitaryChartInstance) {
    sanitaryChartInstance.destroy();
  }
  
  sanitaryChartInstance = new Chart(sanitaryChart.value, {
    type: 'bar',
    data: {
      labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
      datasets: [
        {
          label: 'New Permits',
          data: [25, 30, 35, 40, 45, 50, 55, 60, 65, 70, 75, 80],
          backgroundColor: 'rgba(16, 185, 129, 0.5)',
          borderColor: 'rgb(16, 185, 129)',
          borderWidth: 2
        },
        {
          label: 'Renewals',
          data: [35, 40, 45, 50, 55, 60, 65, 70, 75, 80, 85, 90],
          backgroundColor: 'rgba(245, 158, 11, 0.5)',
          borderColor: 'rgb(245, 158, 11)',
          borderWidth: 2
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'top',
        },
        tooltip: {
          mode: 'index',
          intersect: false,
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          title: {
            display: true,
            text: 'Number of Permits'
          }
        }
      }
    }
  });
  
  // Status Chart - keeping this for now, but it's not connected to backend data
  if (statusChartInstance) {
    statusChartInstance.destroy();
  }
  
  statusChartInstance = new Chart(statusChart.value, {
    type: 'pie',
    data: {
      labels: ['Active', 'Expired', 'Pending'],
      datasets: [{
        label: 'Application Status',
        data: [450, 100, 50],
        backgroundColor: [
          'rgba(16, 185, 129, 0.7)',
          'rgba(239, 68, 68, 0.7)',
          'rgba(245, 158, 11, 0.7)'
        ],
        borderColor: [
          'rgb(16, 185, 129)',
          'rgb(239, 68, 68)',
          'rgb(245, 158, 11)'
        ],
        borderWidth: 1,
        hoverOffset: 4
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'right',
        },
        tooltip: {
          callbacks: {
            label: function(context) {
              const label = context.label || '';
              const value = context.raw || 0;
              const total = context.dataset.data.reduce((a, b) => a + Number(b), 0);
              const percentage = Math.round((value / total) * 100);
              return `${label}: ${value} (${percentage}%)`;
            }
          }
        }
      }
    }
  });

  // Barangay Chart
  if (barangayChartInstance) {
    barangayChartInstance.destroy();
  }

  barangayChartInstance = new Chart(barangayChart.value, {
    type: 'bar',
    data: chartData.barangay,
    options: {
      indexAxis: 'y', // Horizontal bar chart for better display of many barangays
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: false, // Hide legend for this chart
        },
        title: {
          display: false,
        }
      },
      scales: {
        x: {
          beginAtZero: true,
          title: {
            display: true,
            text: 'Number of Health Cards'
          }
        },
        y: {
          title: {
            display: false,
          }
        }
      }
    }
  });

  // RHU Health Card Chart
  if (rhuHealthCardChartInstance) {
    rhuHealthCardChartInstance.destroy();
  }

  rhuHealthCardChartInstance = new Chart(rhuHealthCardChart.value, {
    type: 'bar',
    data: chartData.rhuHealthCard,
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: false,
        },
        tooltip: {
          callbacks: {
            label: function(context) {
              const label = context.dataset.label || '';
              const value = context.raw || 0;
              return `${label}: ${value}`;
            }
          }
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          title: {
            display: true,
            text: 'Number of Health Cards'
          }
        }
      }
    }
  });
};

// Refresh data - now just reinitializes charts with existing data
const refreshData = () => {
  initCharts();
};

// Initialize on mount
onMounted(() => {
  initializeChartData();
  initCharts();
  
  // Set some default values for summary data that's not from the backend
  summaryData.healthCardGrowth = 5;
  summaryData.activePermits = 450;
  summaryData.permitGrowth = 3;
  summaryData.newApplications = 120;
  summaryData.applicationGrowth = 7;
  summaryData.pendingRenewals = 50;
  summaryData.renewalGrowth = 2;
});
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
      <div class="flex h-full flex-1 flex-col gap-4 rounded-xl">
          <div class="w-full py-10">
    <div class="w-full bg-white rounded-lg shadow-md p-4 md:p-6">
      <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">Dashboard Overview</h1>
          <p class="text-sm text-gray-500">Health cards and sanitary permits analytics</p>
        </div>
        <div class="mt-4 md:mt-0 flex items-center gap-3">
          <select 
            v-model="yearFilter" 
            class="px-3 py-2 border rounded-md text-sm font-medium shadow-sm bg-white text-gray-700"
          >
            <option v-for="year in availableYears" :key="year" :value="year">{{ year }}</option>
          </select>
          <button 
            class="inline-flex items-center px-4 py-2 rounded-md text-sm font-medium shadow-sm bg-primary text-white hover:bg-primary/90"
            @click="refreshData"
          >
            <RefreshCw class="mr-2 h-4 w-4" />
            Refresh Data
          </button>
        </div>
      </div>

      <!-- Summary Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
          <div class="flex items-center">
            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
              <CreditCard class="h-6 w-6" />
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-500">Total Health Cards</p>
              <h3 class="text-2xl font-bold text-gray-900">{{ summaryData.totalHealthCards }}</h3>
            </div>
          </div>
          <div class="mt-4 flex items-center">
            <span :class="[
              'text-xs font-medium',
              summaryData.healthCardGrowth >= 0 ? 'text-green-600' : 'text-red-600'
            ]">
              <TrendingUp v-if="summaryData.healthCardGrowth >= 0" class="inline h-3 w-3 mr-1" />
              <TrendingDown v-else class="inline h-3 w-3 mr-1" />
              {{ Math.abs(summaryData.healthCardGrowth) }}% from last month
            </span>
          </div>
        </div>

        <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
          <div class="flex items-center">
            <div class="p-3 rounded-full bg-green-100 text-green-600">
              <ClipboardCheck class="h-6 w-6" />
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-500">Active Permits</p>
              <h3 class="text-2xl font-bold text-gray-900">{{ summaryData.activePermits }}</h3>
            </div>
          </div>
          <div class="mt-4 flex items-center">
            <span :class="[
              'text-xs font-medium',
              summaryData.permitGrowth >= 0 ? 'text-green-600' : 'text-red-600'
            ]">
              <TrendingUp v-if="summaryData.permitGrowth >= 0" class="inline h-3 w-3 mr-1" />
              <TrendingDown v-else class="inline h-3 w-3 mr-1" />
              {{ Math.abs(summaryData.permitGrowth) }}% from last month
            </span>
          </div>
        </div>

        <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
          <div class="flex items-center">
            <div class="p-3 rounded-full bg-purple-100 text-purple-600">
              <FileText class="h-6 w-6" />
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-500">New Applications</p>
              <h3 class="text-2xl font-bold text-gray-900">{{ summaryData.newApplications }}</h3>
            </div>
          </div>
          <div class="mt-4 flex items-center">
            <span :class="[
              'text-xs font-medium',
              summaryData.applicationGrowth >= 0 ? 'text-green-600' : 'text-red-600'
            ]">
              <TrendingUp v-if="summaryData.applicationGrowth >= 0" class="inline h-3 w-3 mr-1" />
              <TrendingDown v-else class="inline h-3 w-3 mr-1" />
              {{ Math.abs(summaryData.applicationGrowth) }}% from last month
            </span>
          </div>
        </div>

        <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
          <div class="flex items-center">
            <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
              <RefreshCw class="h-6 w-6" />
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-500">Pending Renewals</p>
              <h3 class="text-2xl font-bold text-gray-900">{{ summaryData.pendingRenewals }}</h3>
            </div>
          </div>
          <div class="mt-4 flex items-center">
            <span :class="[
              'text-xs font-medium',
              summaryData.renewalGrowth >= 0 ? 'text-green-600' : 'text-red-600'
            ]">
              <TrendingUp v-if="summaryData.renewalGrowth >= 0" class="inline h-3 w-3 mr-1" />
              <TrendingDown v-else class="inline h-3 w-3 mr-1" />
              {{ Math.abs(summaryData.renewalGrowth) }}% from last month
            </span>
          </div>
        </div>
      </div>

      <!-- Barangay Chart (Full Width) -->
      <div class="mb-8">
        <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm w-full">
          <h3 class="text-lg font-semibold text-gray-900 mb-4">Health Cards Issued Per Barangay</h3>
          <div class="h-[600px]"> <!-- Taller height for the 45 barangays -->
            <canvas ref="barangayChart"></canvas>
          </div>
        </div>
      </div>

      <!-- RHU Health Card Chart (Full Width) -->
      <div class="mb-8">
        <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm w-full">
          <h3 class="text-lg font-semibold text-gray-900 mb-4">Health Cards Issued Per RHU</h3>
          <div class="h-80">
            <canvas ref="rhuHealthCardChart"></canvas>
          </div>
        </div>
      </div>

      <!-- Charts Row 1 -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm w-full">
          <h3 class="text-lg font-semibold text-gray-900 mb-4">Health Cards Issued Per Month</h3>
          <div class="h-80">
            <canvas ref="healthCardChart"></canvas>
          </div>
        </div>

        <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm w-full">
          <h3 class="text-lg font-semibold text-gray-900 mb-4">Health Cards by Category</h3>
          <div class="h-80">
            <canvas ref="categoryChart"></canvas>
          </div>
        </div>
      </div>

      <!-- Charts Row 2 -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm w-full">
          <h3 class="text-lg font-semibold text-gray-900 mb-4">Sanitary Permits - New vs Renewals</h3>
          <div class="h-80">
            <canvas ref="sanitaryChart"></canvas>
          </div>
        </div>

        <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm w-full">
          <h3 class="text-lg font-semibold text-gray-900 mb-4">Application Status Distribution</h3>
          <div class="h-80">
            <canvas ref="statusChart"></canvas>
          </div>
        </div>
      </div>
    </div>
  </div>
            
        </div>

      
    </AppLayout>
</template>

