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

const showAddDialog = ref(false);

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];



// State
const yearFilter = ref(new Date().getFullYear());
const availableYears = ref([2023, 2024, 2025]);

// Chart references
const healthCardChart = ref(null);
const categoryChart = ref(null);
const sanitaryChart = ref(null);
const statusChart = ref(null);

// Chart instances
let healthCardChartInstance = null;
let categoryChartInstance = null;
let sanitaryChartInstance = null;
let statusChartInstance = null;

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

// Chart data
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
  }
});

// Generate random data for charts
const generateRandomData = (year) => {
  const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
  
  // Health Cards per month
  const healthCardData = months.map(() => Math.floor(Math.random() * 100) + 50);
  
  // Categories
  const foodCount = Math.floor(Math.random() * 500) + 300;
  const nonFoodCount = Math.floor(Math.random() * 400) + 200;
  const otherCount = Math.floor(Math.random() * 200) + 100;
  
  // Sanitary permits
  const newPermits = months.map(() => Math.floor(Math.random() * 40) + 20);
  const renewals = months.map(() => Math.floor(Math.random() * 60) + 30);
  
  // Status distribution
  const activeCount = Math.floor(Math.random() * 600) + 400;
  const expiredCount = Math.floor(Math.random() * 200) + 50;
  const pendingCount = Math.floor(Math.random() * 150) + 30;
  
  // Update summary data
  summaryData.totalHealthCards = healthCardData.reduce((sum, val) => sum + val, 0);
  summaryData.healthCardGrowth = Math.floor(Math.random() * 20) - 5; // -5% to +15%
  
  summaryData.activePermits = activeCount;
  summaryData.permitGrowth = Math.floor(Math.random() * 15) - 3; // -3% to +12%
  
  summaryData.newApplications = newPermits.reduce((sum, val) => sum + val, 0);
  summaryData.applicationGrowth = Math.floor(Math.random() * 25) - 8; // -8% to +17%
  
  summaryData.pendingRenewals = pendingCount;
  summaryData.renewalGrowth = Math.floor(Math.random() * 18) - 6; // -6% to +12%
  
  // Update chart data
  chartData.healthCards = {
    labels: months,
    datasets: [{
      label: 'Health Cards Issued',
      data: healthCardData,
      backgroundColor: 'rgba(59, 130, 246, 0.5)',
      borderColor: 'rgb(59, 130, 246)',
      borderWidth: 2,
      tension: 0.3,
      fill: true
    }]
  };
  
  chartData.categories = {
    labels: ['Food', 'Non-Food', 'Other'],
    datasets: [{
      label: 'Health Cards by Category',
      data: [foodCount, nonFoodCount, otherCount],
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
  
  chartData.sanitary = {
    labels: months,
    datasets: [
      {
        label: 'New Permits',
        data: newPermits,
        backgroundColor: 'rgba(16, 185, 129, 0.5)',
        borderColor: 'rgb(16, 185, 129)',
        borderWidth: 2
      },
      {
        label: 'Renewals',
        data: renewals,
        backgroundColor: 'rgba(245, 158, 11, 0.5)',
        borderColor: 'rgb(245, 158, 11)',
        borderWidth: 2
      }
    ]
  };
  
  chartData.status = {
    labels: ['Active', 'Expired', 'Pending'],
    datasets: [{
      label: 'Application Status',
      data: [activeCount, expiredCount, pendingCount],
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
  };
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
              const total = context.dataset.data.reduce((a, b) => a + b, 0);
              const percentage = Math.round((value / total) * 100);
              return `${label}: ${value} (${percentage}%)`;
            }
          }
        }
      }
    }
  });
  
  // Sanitary Chart
  if (sanitaryChartInstance) {
    sanitaryChartInstance.destroy();
  }
  
  sanitaryChartInstance = new Chart(sanitaryChart.value, {
    type: 'bar',
    data: chartData.sanitary,
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
  
  // Status Chart
  if (statusChartInstance) {
    statusChartInstance.destroy();
  }
  
  statusChartInstance = new Chart(statusChart.value, {
    type: 'pie',
    data: chartData.status,
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
              const total = context.dataset.data.reduce((a, b) => a + b, 0);
              const percentage = Math.round((value / total) * 100);
              return `${label}: ${value} (${percentage}%)`;
            }
          }
        }
      }
    }
  });
};

// Refresh data
const refreshData = () => {
  generateRandomData(yearFilter.value);
  initCharts();
};

// Watch for year changes
watch(yearFilter, () => {
  refreshData();
});

// Initialize on mount
onMounted(() => {
  generateRandomData(yearFilter.value);
  initCharts();
});
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
      <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
          <div class="container mx-auto py-10">
    <div class="w-full bg-white rounded-lg shadow-md p-6">
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

      <!-- Charts Row 1 -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
          <h3 class="text-lg font-semibold text-gray-900 mb-4">Health Cards Issued Per Month</h3>
          <div class="h-80">
            <canvas ref="healthCardChart"></canvas>
          </div>
        </div>

        <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
          <h3 class="text-lg font-semibold text-gray-900 mb-4">Health Cards by Category</h3>
          <div class="h-80">
            <canvas ref="categoryChart"></canvas>
          </div>
        </div>
      </div>

      <!-- Charts Row 2 -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
          <h3 class="text-lg font-semibold text-gray-900 mb-4">Sanitary Permits - New vs Renewals</h3>
          <div class="h-80">
            <canvas ref="sanitaryChart"></canvas>
          </div>
        </div>

        <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
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
