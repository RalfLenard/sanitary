<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { computed, defineProps, ref, reactive, onMounted, watch, onUnmounted } from 'vue';
import {
  PlusIcon,
  DownloadIcon,
  SearchIcon,
  FilterIcon,
  MoreHorizontalIcon,
  CreditCard,
  ClipboardCheck,
  FileText,
  RefreshCw,
  TrendingUp,
  TrendingDown,
  Skull
} from 'lucide-vue-next';
import Chart from 'chart.js/auto';

// Props from controller
const props = defineProps({
  selectedYear: Number,
  chartData: Object,
  availableYears: Array,
  totalSanitaryCount: Number,
  totalSanitaryCounts: Number,
  totalDeath: Number,
  totalPermitPrinted: Number,
});

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Dashboard',
    href: '/dashboard',
  },
];

// State
const yearFilter = ref(props.selectedYear || new Date().getFullYear());
const selectedBarangay = ref('All');

// Summary data - computed from props
const summaryData = computed(() => ({
  totalHealthCards: props.chartData?.healthCards?.data?.reduce((sum: number, val: number) => sum + Number(val), 0) || 0,
  totalSanitaryCount: props.totalSanitaryCount || 0,
  totalSanitaryCounts: props.totalSanitaryCounts || 0,
  totalDeath: props.totalDeath || 0,
  totalPermitPrinted: props.totalPermitPrinted || 0,
}));

// Chart references
const healthCardChart = ref(null);
const categoryChart = ref(null);
const sanitaryChart = ref(null);
const statusChart = ref(null);
const barangayChart = ref(null);
const rhuHealthCardChart = ref(null);
const rhuMonthlyChart = ref(null);
const barangayMonthlyChart = ref(null);
const deathCertChart = ref(null);
const printedSanitary = ref(null);

// Chart instances
let chartInstances: { [key: string]: Chart | null } = {
  healthCard: null,
  category: null,
  sanitary: null,
  status: null,
  barangay: null,
  rhuHealthCard: null,
  rhuMonthly: null,
  barangayMonthly: null,
  deathCert: null,
  printedSanitary: null,
};

// List of barangays for filter
const barangayList = computed(() => {
  if (props.chartData?.barangayMonthly?.datasets) {
    const barangays = props.chartData.barangayMonthly.datasets.map((dataset: any) => dataset.label);
    return ['All', ...barangays];
  }
  return ['All'];
});

// Generate dynamic colors for charts
const generateColors = (count: number) => {
  const colors = [];
  for (let i = 0; i < count; i++) {
    const hue = Math.floor((360 / count) * i);
    colors.push(`hsl(${hue}, 70%, 60%)`);
  }
  return colors;
};

// Process chart data from props
const processedChartData = computed(() => {
  if (!props.chartData) return {};

  return {
    healthCards: {
      labels: props.chartData.healthCards?.labels || [],
      datasets: [{
        label: 'Health Cards Issued',
        data: props.chartData.healthCards?.data || [],
        backgroundColor: 'rgba(59, 130, 246, 0.5)',
        borderColor: 'rgb(59, 130, 246)',
        borderWidth: 2,
        tension: 0.3,
        fill: true
      }]
    },
    categories: {
      labels: props.chartData.categories?.labels || [],
      datasets: [{
        label: 'Health Cards by Category',
        data: props.chartData.categories?.data || [],
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
    },
    barangay: {
      labels: props.chartData.barangay?.labels || [],
      datasets: [{
        label: 'Health Cards Issued',
        data: props.chartData.barangay?.data || [],
        backgroundColor: generateColors(props.chartData.barangay?.labels?.length || 0),
        borderWidth: 1
      }]
    },
    rhuHealthCard: {
      labels: props.chartData.rhuHealthCard?.labels || [],
      datasets: [{
        label: 'Health Cards Issued',
        data: props.chartData.rhuHealthCard?.data || [],
        backgroundColor: [
          'rgba(16, 185, 129, 0.7)',
          'rgba(59, 130, 246, 0.7)',
          'rgba(245, 158, 11, 0.7)',
          'rgba(139, 92, 246, 0.7)'
        ],
        borderColor: [
          'rgb(16, 185, 129)',
          'rgb(59, 130, 246)',
          'rgb(245, 158, 11)',
          'rgb(139, 92, 246)'
        ],
        borderWidth: 1
      }]
    },
    rhuMonthly: {
      labels: props.chartData.rhuMonthly?.labels || [],
      datasets: (props.chartData.rhuMonthly?.datasets || []).map((dataset: any, index: number) => {
        const hue = Math.floor((360 / (props.chartData.rhuMonthly?.datasets?.length || 1)) * index);
        return {
          ...dataset,
          backgroundColor: `hsla(${hue}, 70%, 60%, 0.2)`,
          borderColor: `hsl(${hue}, 70%, 60%)`,
          borderWidth: 2,
          tension: 0.3,
          fill: false
        };
      })
    },
    barangayMonthly: {
      labels: props.chartData.barangayMonthly?.labels || [],
      datasets: (props.chartData.barangayMonthly?.datasets || []).map((dataset: any, index: number) => {
        const hue = Math.floor((360 / (props.chartData.barangayMonthly?.datasets?.length || 1)) * index);
        return {
          ...dataset,
          backgroundColor: `hsla(${hue}, 70%, 60%, 0.2)`,
          borderColor: `hsl(${hue}, 70%, 60%)`,
          borderWidth: 2,
          tension: 0.3,
          fill: false
        };
      })
    },
    deathCert: {
      labels: props.chartData.deathCert?.labels || [],
      datasets: [{
        label: 'Death Certificates Issued',
        data: props.chartData.deathCert?.data || [],
        backgroundColor: 'rgba(239, 68, 68, 0.5)',
        borderColor: 'rgb(239, 68, 68)',
        borderWidth: 2,
        tension: 0.3,
        fill: true
      }]
    },
    printedPermits: {
      labels: props.chartData.printedPermits?.labels || [],
      datasets: [{
        label: 'Sanitary Printed Permit Issued',
        data: props.chartData.printedPermits?.data || [],
        backgroundColor: 'rgba(59, 130, 246, 0.5)',
        borderColor: 'rgb(59, 130, 246)',
        borderWidth: 2,
        tension: 0.3,
        fill: true
      }]
    },
    sanitaryMonthly: props.chartData.sanitaryMonthly || { labels: [], datasets: [] },
    sanitaryQuarterly: props.chartData.sanitaryQuarterly || { labels: [], datasets: [] }
  };
});

// Destroy all chart instances
const destroyCharts = () => {
  Object.values(chartInstances).forEach(instance => {
    if (instance) {
      instance.destroy();
    }
  });
  // Reset all instances
  Object.keys(chartInstances).forEach(key => {
    chartInstances[key] = null;
  });
};

// Initialize charts
const initCharts = () => {
  try {
    destroyCharts();

    const chartData = processedChartData.value;

    // Health Card Chart
    if (healthCardChart.value && chartData.healthCards) {
      chartInstances.healthCard = new Chart(healthCardChart.value, {
        type: 'line',
        data: chartData.healthCards,
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: { position: 'top' },
            tooltip: { mode: 'index', intersect: false }
          },
          scales: {
            y: {
              beginAtZero: true,
              title: { display: true, text: 'NUMBER OF CARDS' }
            }
          }
        }
      });
    }

    // Category Chart
    if (categoryChart.value && chartData.categories) {
      chartInstances.category = new Chart(categoryChart.value, {
        type: 'doughnut',
        data: chartData.categories,
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: { position: 'right' },
            tooltip: {
              callbacks: {
                label: function (context: any) {
                  const label = context.label || '';
                  const value = context.raw || 0;
                  const total = context.dataset.data.reduce((a: number, b: number) => a + Number(b), 0);
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
    if (sanitaryChart.value && chartData.sanitaryMonthly) {
      chartInstances.sanitary = new Chart(sanitaryChart.value, {
        type: 'bar',
        data: chartData.sanitaryMonthly,
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: { position: 'top' },
            tooltip: { mode: 'index', intersect: false }
          },
          scales: {
            y: {
              beginAtZero: true,
              title: { display: true, text: 'NUMBER OF PERMITS' }
            }
          }
        }
      });
    }

    // Status Chart - Quarterly data
    if (statusChart.value && chartData.sanitaryQuarterly) {
      chartInstances.status = new Chart(statusChart.value, {
        type: 'bar',
        data: chartData.sanitaryQuarterly,
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: { position: 'top' },
            tooltip: { mode: 'index', intersect: false }
          },
          scales: {
            y: {
              beginAtZero: true,
              title: { display: true, text: 'NUMBER OF PERMITS' }
            }
          }
        }
      });
    }

    // Barangay Chart
    if (barangayChart.value && chartData.barangay) {
      chartInstances.barangay = new Chart(barangayChart.value, {
        type: 'bar',
        data: chartData.barangay,
        options: {
          indexAxis: 'y',
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: { display: false }
          },
          scales: {
            x: {
              beginAtZero: true,
              title: { display: true, text: 'NUMBER OF HEALTH CARDS' }
            }
          }
        }
      });
    }

    // RHU Health Card Chart
    if (rhuHealthCardChart.value && chartData.rhuHealthCard) {
      chartInstances.rhuHealthCard = new Chart(rhuHealthCardChart.value, {
        type: 'bar',
        data: chartData.rhuHealthCard,
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: { display: false }
          },
          scales: {
            y: {
              beginAtZero: true,
              title: { display: true, text: 'NUMBER OF HEALTH CARDS' }
            }
          }
        }
      });
    }

    // RHU Monthly Chart
    if (rhuMonthlyChart.value && chartData.rhuMonthly) {
      chartInstances.rhuMonthly = new Chart(rhuMonthlyChart.value, {
        type: 'line',
        data: chartData.rhuMonthly,
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: { position: 'top' },
            tooltip: { mode: 'index', intersect: false }
          },
          scales: {
            y: {
              beginAtZero: true,
              title: { display: true, text: 'NUMBER OF HEALTH CARDS' }
            },
            x: {
              title: { display: true, text: 'MONTH' }
            }
          }
        }
      });
    }

    // Barangay Monthly Chart
    if (barangayMonthlyChart.value && chartData.barangayMonthly) {
      let initialDatasets = [...chartData.barangayMonthly.datasets];
      if (selectedBarangay.value === 'All' && initialDatasets.length > 10) {
        initialDatasets = initialDatasets.slice(0, 10);
      } else if (selectedBarangay.value !== 'All') {
        initialDatasets = initialDatasets.filter((dataset: any) =>
          dataset.label === selectedBarangay.value
        );
      }

      chartInstances.barangayMonthly = new Chart(barangayMonthlyChart.value, {
        type: 'line',
        data: {
          labels: chartData.barangayMonthly.labels,
          datasets: initialDatasets
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: { position: 'top', display: true },
            tooltip: { mode: 'index', intersect: false }
          },
          scales: {
            y: {
              beginAtZero: true,
              title: { display: true, text: 'NUMBER OF HEALTH CARDS' }
            },
            x: {
              title: { display: true, text: 'MONTH' }
            }
          }
        }
      });
    }

    // Death Certificate Chart
    if (deathCertChart.value && chartData.deathCert) {
      chartInstances.deathCert = new Chart(deathCertChart.value, {
        type: 'line',
        data: chartData.deathCert,
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: { position: 'top', display: true },
            tooltip: { mode: 'index', intersect: false }
          },
          scales: {
            y: {
              beginAtZero: true,
              title: { display: true, text: 'NUMBER OF DEATH CERTIFICATES' }
            },
            x: {
              title: { display: true, text: 'MONTH' }
            }
          }
        }
      });
    }

    // Printed Sanitary Chart
    if (printedSanitary.value && chartData.printedPermits) {
      chartInstances.printedSanitary = new Chart(printedSanitary.value, {
        type: 'line',
        data: chartData.printedPermits,
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: { position: 'top', display: true },
            tooltip: { mode: 'index', intersect: false }
          },
          scales: {
            y: {
              beginAtZero: true,
              title: { display: true, text: 'NUMBER OF PRINTED SANITARY' }
            },
            x: {
              title: { display: true, text: 'MONTH' }
            }
          }
        }
      });
    }

  } catch (error) {
    console.error('Error initializing charts:', error);
  }
};

// Filter barangay monthly data
const filterBarangayMonthlyData = () => {
  if (!chartInstances.barangayMonthly || !processedChartData.value.barangayMonthly) return;

  const chartData = processedChartData.value.barangayMonthly;

  if (selectedBarangay.value === 'All') {
    const allDatasets = [...chartData.datasets];
    const limitedDatasets = allDatasets.slice(0, 10);
    chartInstances.barangayMonthly.data.datasets = limitedDatasets;
  } else {
    const filteredDataset = chartData.datasets.filter((dataset: any) =>
      dataset.label === selectedBarangay.value
    );
    chartInstances.barangayMonthly.data.datasets = filteredDataset;
  }

  chartInstances.barangayMonthly.update();
};

// Handle year filter change
const handleYearChange = () => {
  router.get('/dashboard', { year: yearFilter.value }, {
    preserveState: false,
    preserveScroll: true
  });
};

// Refresh data
const refreshData = () => {
  router.reload();
};

// Watch for changes
watch(selectedBarangay, () => {
  filterBarangayMonthlyData();
});

watch(() => yearFilter.value, () => {
  handleYearChange();
});

// Initialize on mount
onMounted(() => {
  try {
    initCharts();
  } catch (error) {
    console.error('Error in onMounted:', error);
  }
});

// Cleanup on unmount
onUnmounted(() => {
  destroyCharts();
});


const showModal = ref(false);

const form = ref({
  rhu: '',
  start_date: '',
  end_date: '',
});

const generatePDF = () => {
  const { rhu, start_date, end_date } = form.value;

  // Open PDF in new tab
  const url = `/reports/rhu?rhu=${encodeURIComponent(rhu)}&start_date=${start_date}&end_date=${end_date}`;
  window.open(url, '_blank');

  // Close modal
  showModal.value = false;
};


const showModalPErmit = ref(false);

const formPermit = ref({
  quarter: "",
  start_date: "",
  end_date: "",
});

const generateQuarterPermitPDF = () => {
  const { quarter, start_date, end_date } = formPermit.value; // ✅ FIXED

  if (!quarter || !start_date || !end_date) {
    alert("Please fill in all fields");
    return;
  }

  // Open PDF in new tab
  const url = `/reports/permit?quarter=${encodeURIComponent(
    quarter
  )}&start_date=${start_date}&end_date=${end_date}`;
  window.open(url, "_blank");

  // Close modal
  showModalPErmit.value = false; // ✅ FIXED (was showModal)
};


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
              <p class="text-sm text-gray-500">HEALTH CARDS, SANITARY PERMITS AND DEATH CERTIFICATES ANALYTICS</p>
            </div>
            <div class="mt-4 md:mt-0 flex items-center gap-3">
              <select v-model="yearFilter"
                class="px-3 py-2 border rounded-md text-sm font-medium shadow-sm bg-white text-gray-700">
                <option v-for="year in availableYears" :key="year" :value="year">{{ year }}</option>
              </select>
              <button
                class="inline-flex items-center px-4 py-2 rounded-md text-sm font-medium shadow-sm bg-primary text-white hover:bg-primary/90"
                @click="refreshData">
                <RefreshCw class="mr-2 h-4 w-4" />
                REFRESH DATA
              </button>
            </div>
          </div>

          <!-- Summary Cards -->
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
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
            </div>

            <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
              <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                  <ClipboardCheck class="h-6 w-6" />
                </div>
                <div class="ml-4">
                  <p class="text-sm font-medium text-gray-500">ACTIVE PERMITS</p>
                  <h3 class="text-2xl font-bold text-gray-900">{{ summaryData.totalSanitaryCount }}</h3>
                </div>
              </div>
            </div>

            <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
              <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                  <FileText class="h-6 w-6" />
                </div>
                <div class="ml-4">
                  <p class="text-sm font-medium text-gray-500">NEW APPLICATIONS</p>
                  <h3 class="text-2xl font-bold text-gray-900">{{ summaryData.totalSanitaryCounts }}</h3>
                </div>
              </div>
            </div>

            <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
              <div class="flex items-center">
                <div class="p-3 rounded-full bg-indigo-100 text-indigo-600">
                  <FileText class="h-6 w-6" />
                </div>
                <div class="ml-4">
                  <p class="text-sm font-medium text-gray-500">TOTAL PRINTED PERMIT</p>
                  <h3 class="text-2xl font-bold text-gray-900">{{ summaryData.totalPermitPrinted }}</h3>
                </div>
              </div>
            </div>

            <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
              <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-600">
                  <Skull class="h-6 w-6" />
                </div>
                <div class="ml-4">
                  <p class="text-sm font-medium text-gray-500">TOTAL DEATH CERTIFICATES</p>
                  <h3 class="text-2xl font-bold text-gray-900">{{ summaryData.totalDeath }}</h3>
                </div>
              </div>
            </div>
          </div>

          <!-- Death Certificates Monthly Chart -->
          <div class="mb-8">
            <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm w-full">
              <h3 class="text-lg font-semibold text-gray-900 mb-4">DEATH CERTIFICATES ISSUED PER MONTH</h3>
              <div class="h-80">
                <canvas ref="deathCertChart"></canvas>
              </div>
            </div>
          </div>

          <!-- Printed Sanitary Monthly Chart -->
          <div class="mb-8">
            <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm w-full">
              <h3 class="text-lg font-semibold text-gray-900 mb-4">SANITARY PRINTED PER MONTH</h3>
              <div class="h-80">
                <canvas ref="printedSanitary"></canvas>
              </div>
            </div>
          </div>

          <!-- Barangay Monthly Chart -->
          <div class="mb-8">
            <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm w-full">
              <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">HEALTH CARDS ISSUED PER BARANGAY MONTHLY</h3>
                <div class="mt-2 md:mt-0">
                  <select v-model="selectedBarangay"
                    class="px-3 py-1 border rounded-md text-sm font-medium shadow-sm bg-white text-gray-700">
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

          <!-- Barangay Chart -->
          <div class="mb-8">
            <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm w-full">
              <h3 class="text-lg font-semibold text-gray-900 mb-4">HEALTH CARDS ISSUED PER BARANGAY</h3>
              <div class="h-[600px]">
                <canvas ref="barangayChart"></canvas>
              </div>
            </div>
          </div>

          <!-- RHU Health Card Chart -->
          <div class="mb-8">
            <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm w-full">
              <h3 class="text-lg font-semibold text-gray-900 mb-4">HEALTH CARDS ISSUED PER RHU</h3>
              <div class="h-80">
                <canvas ref="rhuHealthCardChart"></canvas>
              </div>
            </div>
          </div>

          <!-- RHU Monthly Chart -->
          <div class="mb-8">
            <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm w-full">

              <!-- Header with title + button aligned right -->
              <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">
                  HEALTH CARDS ISSUED PER RHU MONTHLY
                </h3>
                <button @click="showModal = true" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                  Generate PDF
                </button>
              </div>

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
              <!-- Header with title and button aligned -->
              <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">SANITARY PERMITS - QUARTERLY (NEW VS RENEWALS)</h3>
                <button @click="showModalPErmit = true"
                  class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                  Generate PDF
                </button>
              </div>

              <!-- Chart Container -->
              <div class="h-80">
                <canvas ref="statusChart"></canvas>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
  </AppLayout>

  <!-- Modal -->
  <div v-if="showModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
      <h2 class="text-lg font-semibold mb-4">Generate RHU Report</h2>

      <form @submit.prevent="generatePDF" class="space-y-4">
        <!-- RHU Name -->
        <div>
          <label class="block text-sm font-medium text-gray-700">RHU</label>
          <input v-model="form.rhu" type="text" required
            class="mt-1 block w-full border border-gray-300 rounded-lg p-2" />
        </div>

        <!-- Start Date -->
        <div>
          <label class="block text-sm font-medium text-gray-700">Start Date</label>
          <input v-model="form.start_date" type="date" required
            class="mt-1 block w-full border border-gray-300 rounded-lg p-2" />
        </div>

        <!-- End Date -->
        <div>
          <label class="block text-sm font-medium text-gray-700">End Date</label>
          <input v-model="form.end_date" type="date" required
            class="mt-1 block w-full border border-gray-300 rounded-lg p-2" />
        </div>

        <!-- Actions -->
        <div class="flex justify-end space-x-2 mt-4">
          <button type="button" @click="showModal = false" class="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400">
            Cancel
          </button>
          <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            Generate
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Modal -->
  <div v-if="showModalPErmit" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
      <h2 class="text-xl font-semibold mb-4">Generate Sanitary Permit Report</h2>

      <!-- Form -->
      <form @submit.prevent="generateQuarterPermitPDF">
        <!-- Quarter -->
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-1">Quarter</label>
          <select v-model="formPermit.quarter" class="w-full border rounded px-3 py-2">
            <option disabled value="">Select Quarter</option>
            <option value="1">1st Quarter</option>
            <option value="2">2nd Quarter</option>
            <option value="3">3rd Quarter</option>
            <option value="4">4th Quarter</option>
          </select>
        </div>

        <!-- Start Date -->
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
          <input type="date" v-model="formPermit.start_date" class="w-full border rounded px-3 py-2" />
        </div>

        <!-- End Date -->
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
          <input type="date" v-model="formPermit.end_date" class="w-full border rounded px-3 py-2" />
        </div>

        <!-- Buttons -->
        <div class="flex justify-end space-x-2">
          <button type="button" @click="showModalPErmit = false"
            class="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400">
            Cancel
          </button>
          <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            Generate
          </button>
        </div>
      </form>
    </div>
  </div>
</template>
