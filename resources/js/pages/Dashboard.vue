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
const selectedBarangay = ref('All');

// Summary data - Initialize with default values to prevent undefined errors
const summaryData = reactive({
  totalHealthCards: 0,
  healthCardGrowth: 0,
  activePermits: 450,
  permitGrowth: 3,
  newApplications: 120,
  applicationGrowth: 7,
  pendingRenewals: 50,
  renewalGrowth: 2
});

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
const rhuMonthlyChart = ref(null);
const barangayMonthlyChart = ref(null);

// Chart instances
let healthCardChartInstance = null;
let categoryChartInstance = null;
let sanitaryChartInstance = null;
let statusChartInstance = null;
let barangayChartInstance = null;
let rhuHealthCardChartInstance = null;
let rhuMonthlyChartInstance = null;
let barangayMonthlyChartInstance = null;

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
  },
  rhuMonthly: {
    labels: [],
    datasets: []
  },
  barangayMonthly: {
    labels: [],
    datasets: []
  },
  sanitaryMonthly: { // New chart data for sanitary monthly
    labels: [],
    datasets: []
  },
  sanitaryQuarterly: { // New chart data for sanitary quarterly
    labels: [],
    datasets: []
  }
});

// List of barangays for filter
const barangayList = computed(() => {
  if (props.chartData?.barangayMonthly?.datasets) {
    const barangays = props.chartData.barangayMonthly.datasets.map(dataset => dataset.label);
    return ['All', ...barangays];
  }
  return ['All'];
});

// Initialize chart data from props
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
    const labels = props.chartData.barangay.labels || [];
    const data = props.chartData.barangay.data || [];
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

  // Update barangay monthly data from controller
  if (props.chartData.barangayMonthly) {
    chartData.barangayMonthly = {
      labels: props.chartData.barangayMonthly.labels || [],
      datasets: props.chartData.barangayMonthly.datasets || []
    };
    
    // Enhance datasets with better styling
    if (chartData.barangayMonthly.datasets) {
      chartData.barangayMonthly.datasets = chartData.barangayMonthly.datasets.map((dataset, index) => {
        // Generate a consistent color based on the barangay name
        const hue = Math.floor((360 / chartData.barangayMonthly.datasets.length) * index);
        
        return {
          ...dataset,
          backgroundColor: `hsla(${hue}, 70%, 60%, 0.2)`,
          borderWidth: 2,
          tension: 0.3,
          fill: false
        };
      });
    }
  }

  // Update RHU health card data
  if (props.chartData.rhuHealthCard) {
    chartData.rhuHealthCard = {
      labels: props.chartData.rhuHealthCard.labels || [],
      datasets: [{
        label: 'Health Cards Issued',
        data: props.chartData.rhuHealthCard.data || [],
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

  // Update RHU monthly health card data
  if (props.chartData.rhuMonthly) {
    // Create a default dataset if none exists or if datasets is not an array
    let datasets = [];
    
    // Check if datasets exists and is an array
    if (props.chartData.rhuMonthly.datasets && Array.isArray(props.chartData.rhuMonthly.datasets)) {
      datasets = props.chartData.rhuMonthly.datasets.map((dataset, index) => {
        // Generate different colors for each RHU
        const hue = Math.floor((360 / props.chartData.rhuMonthly.datasets.length) * index);
        return {
          ...dataset,
          backgroundColor: `hsla(${hue}, 70%, 60%, 0.2)`,
          borderWidth: 2,
          tension: 0.3,
          fill: false
        };
      });
    } else {
      // Create an empty dataset if none exists
      datasets = [];
    }

    chartData.rhuMonthly = {
      labels: props.chartData.rhuMonthly.labels || [],
      datasets: datasets
    };
  } else {
    // If rhuMonthly doesn't exist, create an empty chart
    chartData.rhuMonthly = {
      labels: [],
      datasets: []
    };
  }

  // Update monthly health card data
  if (props.chartData.healthCards) {
    chartData.healthCards = {
      labels: props.chartData.healthCards.labels || [],
      datasets: [{
        label: 'Health Cards Issued',
        data: props.chartData.healthCards.data || [],
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
      labels: props.chartData.categories.labels || [],
      datasets: [{
        label: 'Health Cards by Category',
        data: props.chartData.categories.data || [],
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
  
  // Update sanitary monthly data (NEW)
  if (props.chartData.sanitaryMonthly) {
    chartData.sanitaryMonthly = props.chartData.sanitaryMonthly;
  }
  
  // Update sanitary quarterly data (NEW)
  if (props.chartData.sanitaryQuarterly) {
    chartData.sanitaryQuarterly = props.chartData.sanitaryQuarterly;
  }

  // Calculate total health cards for summary
  if (props.chartData.healthCards && props.chartData.healthCards.data) {
    const total = props.chartData.healthCards.data.reduce((sum, val) => sum + Number(val), 0);
    summaryData.totalHealthCards = total;
  }
};

// Filter barangay monthly data based on selected barangay
const filterBarangayMonthlyData = () => {
  if (!chartData.barangayMonthly || !chartData.barangayMonthly.datasets) return;
  
  if (selectedBarangay.value === 'All') {
    // Show all barangays (limit to top 10 for better visibility)
    const allDatasets = [...chartData.barangayMonthly.datasets];
    const limitedDatasets = allDatasets.slice(0, 10); // Limit to top 10 for readability
    
    if (barangayMonthlyChartInstance) {
      barangayMonthlyChartInstance.data.datasets = limitedDatasets;
      barangayMonthlyChartInstance.update();
    }
  } else {
    // Filter to show only the selected barangay
    const filteredDataset = chartData.barangayMonthly.datasets.filter(dataset => 
      dataset.label === selectedBarangay.value
    );
    
    if (barangayMonthlyChartInstance) {
      barangayMonthlyChartInstance.data.datasets = filteredDataset;
      barangayMonthlyChartInstance.update();
    }
  }
};

// Watch for changes in the selected barangay
watch(selectedBarangay, () => {
  filterBarangayMonthlyData();
});

// Initialize charts
const initCharts = () => {
  try {
    // Health Card Chart
    if (healthCardChartInstance) {
      healthCardChartInstance.destroy();
    }
    
    if (healthCardChart.value) {
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
                text: 'NUMBER OF CARDS'
              }
            }
          }
        }
      });
    }
    
    // Category Chart
    if (categoryChartInstance) {
      categoryChartInstance.destroy();
    }
    
    if (categoryChart.value) {
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
    }
    
    // Sanitary Chart - Monthly data
    if (sanitaryChartInstance) {
      sanitaryChartInstance.destroy();
    }

    if (sanitaryChart.value) {
      sanitaryChartInstance = new Chart(sanitaryChart.value, {
        type: 'bar',
        data: chartData.sanitaryMonthly,
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
                text: 'NUMBER OF PERMITS'
              }
            }
          }
        }
      });
    }

    // Status Chart - Now using quarterly data as a bar chart
    if (statusChartInstance) {
      statusChartInstance.destroy();
    }

    if (statusChart.value) {
      statusChartInstance = new Chart(statusChart.value, {
        type: 'bar',
        data: chartData.sanitaryQuarterly,
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
                text: 'NUMBER OF PERMITS'
              }
            }
          }
        }
      });
    }

    // Barangay Chart
    if (barangayChartInstance) {
      barangayChartInstance.destroy();
    }

    if (barangayChart.value) {
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
                text: 'NUMBER OF HEALTH CARDS'
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
    }

    // RHU Health Card Chart
    if (rhuHealthCardChartInstance) {
      rhuHealthCardChartInstance.destroy();
    }

    if (rhuHealthCardChart.value) {
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
                text: 'NUMBER OF HEALTH CARDS'
              }
            }
          }
        }
      });
    }

    // RHU Monthly Chart
    if (rhuMonthlyChartInstance) {
      rhuMonthlyChartInstance.destroy();
    }

    if (rhuMonthlyChart.value) {
      rhuMonthlyChartInstance = new Chart(rhuMonthlyChart.value, {
        type: 'line',
        data: chartData.rhuMonthly,
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
                text: 'NUMBER OF HEALTH CARDS'
              }
            },
            x: {
              title: {
                display: true,
                text: 'MONTH'
              }
            }
          }
        }
      });
    }
    
    // Barangay Monthly Chart
    if (barangayMonthlyChartInstance) {
      barangayMonthlyChartInstance.destroy();
    }
    
    if (barangayMonthlyChart.value) {
      // Initially show only top 10 barangays for better readability
      let initialDatasets = [...chartData.barangayMonthly.datasets];
      if (selectedBarangay.value === 'All' && initialDatasets.length > 10) {
        initialDatasets = initialDatasets.slice(0, 10);
      } else if (selectedBarangay.value !== 'All') {
        initialDatasets = initialDatasets.filter(dataset => 
          dataset.label === selectedBarangay.value
        );
      }
      
      const chartConfig = {
        type: 'line',
        data: {
          labels: chartData.barangayMonthly.labels,
          datasets: initialDatasets
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              position: 'top',
              display: true,
            },
            tooltip: {
              mode: 'index',
              intersect: false,
            },
            title: {
              display: false,
              text: 'HEALTH CARDS ISSUED PER BARANGAY MONTHLY'
            }
          },
          scales: {
            y: {
              beginAtZero: true,
              title: {
                display: true,
                text: 'NUMBER OF HEALTH CARDS'
              }
            },
            x: {
              title: {
                display: true,
                text: 'MONTH'
              }
            }
          }
        }
      };
      
      barangayMonthlyChartInstance = new Chart(barangayMonthlyChart.value, chartConfig);
    }
  } catch (error) {
    console.error('Error initializing charts:', error);
  }
};

// Refresh data - now just reinitializes charts with existing data
const refreshData = () => {
  initCharts();
};

// Initialize on mount
onMounted(() => {
  try {
    initializeChartData();
    initCharts();
  } catch (error) {
    console.error('Error in onMounted:', error);
  }
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
          <h1 class="text-2xl font-bold text-gray-900">DASHBOARD OVERVIEW</h1>
          <p class="text-sm text-gray-500">HEALTH CARDS AND SANITARY PERMITS ANALYTICS</p>
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
            REFRESH DATA
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
              <p class="text-sm font-medium text-gray-500">TOTAL HEALTH CARDS</p>
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
              {{ Math.abs(summaryData.healthCardGrowth) }}% FROM LAST MONTH
            </span>
          </div>
        </div>

        <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
          <div class="flex items-center">
            <div class="p-3 rounded-full bg-green-100 text-green-600">
              <ClipboardCheck class="h-6 w-6" />
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-500">ACTIVE PERMITS</p>
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
              {{ Math.abs(summaryData.permitGrowth) }}% FROM LAST MONTH
            </span>
          </div>
        </div>

        <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
          <div class="flex items-center">
            <div class="p-3 rounded-full bg-purple-100 text-purple-600">
              <FileText class="h-6 w-6" />
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-500">NEW APPLICATIONS</p>
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
              {{ Math.abs(summaryData.applicationGrowth) }}% FROM LAST MONTH
            </span>
          </div>
        </div>

        <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
          <div class="flex items-center">
            <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
              <RefreshCw class="h-6 w-6" />
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-500">PENDING RENEWALS</p>
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
              {{ Math.abs(summaryData.renewalGrowth) }}% FROM LAST MONTH
            </span>
          </div>
        </div>
      </div>

      <!-- NEW: Barangay Monthly Chart (Full Width) -->
      <div class="mb-8">
        <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm w-full">
          <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900">HEALTH CARDS ISSUED PER BARANGAY MONTHLY</h3>
            <div class="mt-2 md:mt-0">
              <select 
                v-model="selectedBarangay" 
                class="px-3 py-1 border rounded-md text-sm font-medium shadow-sm bg-white text-gray-700"
              >
                <option v-for="barangay in barangayList" :key="barangay" :value="barangay">
                  {{ barangay }}
                </option>
              </select>
            </div>
          </div>
          <div class="h-80">
            <canvas ref="barangayMonthlyChart"></canvas>
          </div>
        </div>
      </div>

      <!-- Barangay Chart (Full Width) -->
      <div class="mb-8">
        <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm w-full">
          <h3 class="text-lg font-semibold text-gray-900 mb-4">HEALTH CARDS ISSUED PER BARANGAY</h3>
          <div class="h-[600px]"> <!-- Taller height for the 45 barangays -->
            <canvas ref="barangayChart"></canvas>
          </div>
        </div>
      </div>

      <!-- RHU Health Card Chart (Full Width) -->
      <div class="mb-8">
        <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm w-full">
          <h3 class="text-lg font-semibold text-gray-900 mb-4">HEALTH CARDS ISSUED PER RHU</h3>
          <div class="h-80">
            <canvas ref="rhuHealthCardChart"></canvas>
          </div>
        </div>
      </div>

      <!-- RHU Monthly Chart (Full Width) -->
      <div class="mb-8">
        <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm w-full">
          <h3 class="text-lg font-semibold text-gray-900 mb-4">HEALTH CARDS ISSUED PER RHU MONTHLY</h3>
          <div class="h-80">
            <canvas ref="rhuMonthlyChart"></canvas>
          </div>
        </div>
      </div>

      <!-- Charts Row 1 -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm w-full">
          <h3 class="text-lg font-semibold text-gray-900 mb-4">HEALTH CARDS ISSUED PER MONTH</h3>
          <div class="h-80">
            <canvas ref="healthCardChart"></canvas>
          </div>
        </div>

        <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm w-full">
          <h3 class="text-lg font-semibold text-gray-900 mb-4">HEALTH CARDS BY CATEGORY</h3>
          <div class="h-80">
            <canvas ref="categoryChart"></canvas>
          </div>
        </div>
      </div>

      <!-- Charts Row 2 -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm w-full">
          <h3 class="text-lg font-semibold text-gray-900 mb-4">SANITARY PERMITS - NEW VS RENEWALS</h3>
          <div class="h-80">
            <canvas ref="sanitaryChart"></canvas>
          </div>
        </div>

        <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm w-full">
          <h3 class="text-lg font-semibold text-gray-900 mb-4">SANITARY PERMITS - QUARTERLY (NEW VS RENEWALS)</h3>
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


