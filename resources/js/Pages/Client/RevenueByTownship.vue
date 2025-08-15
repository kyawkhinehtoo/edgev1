<template>
  <app-layout>
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="font-semibold text-xl text-white leading-tight flex items-center">
          <i class="fas fa-chart-bar mr-3 text-blue-200"></i>
          Revenue by Township Report
        </h2>
        <div class="text-sm text-blue-200">
          <i class="fas fa-calendar-alt mr-1"></i>
          {{ new Date().toLocaleDateString() }}
        </div>
      </div>
    </template>

    <div class="py-4 bg-gray-50 min-h-screen">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Report Preference Header -->
        <div class="mb-4">
          <h3 class="text-base font-semibold text-gray-900 flex items-center">
            <i class="fas fa-filter mr-2 text-indigo-600"></i>
            Report Filters
          </h3>
          <p class="mt-1 text-xs text-gray-600">Filter revenue data by city and year</p>
        </div>

        <!-- Filter Section -->
        <div class="bg-white shadow-lg rounded-lg border border-gray-200">
          <div class="px-4 py-3 border-b border-gray-200 bg-gradient-to-r from-indigo-50 to-blue-50">
            <h4 class="text-sm font-medium text-gray-900 flex items-center">
              <i class="fas fa-search mr-2 text-indigo-600"></i>
              Filter Criteria
            </h4>
          </div>
          <div class="p-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

              <!-- City Filter -->
              <div class="space-y-1">
                <label for="city_id" class="text-xs font-medium text-gray-700 flex items-center">
                  <i class="fas fa-city mr-1 text-gray-500 text-xs"></i>
                  City
                </label>
                <div v-if="city_list.length !== 0" class="relative">
                  <select 
                    v-model="form.city_id" 
                    name="city_id" 
                    id="city_id"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200 bg-white text-sm form-input focus-ring"
                  >
                    <option value="">All Cities</option>
                    <option v-for="city in city_list" :key="city.id" :value="city.id">{{ city.name }}</option>
                  </select>
                </div>
                <div v-else class="text-xs text-gray-500 italic">No cities available</div>
              </div>

              <!-- Year Filter -->
              <div class="space-y-1">
                <label for="year" class="text-xs font-medium text-gray-700 flex items-center">
                  <i class="fas fa-calendar mr-1 text-gray-500 text-xs"></i>
                  Year
                </label>
                <div class="relative">
                  <select 
                    v-model="form.year" 
                    name="year" 
                    id="year"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200 bg-white text-sm form-input focus-ring"
                  >
                    <option v-for="year in yearOptions" :key="year" :value="year">{{ year }}</option>
                  </select>
                </div>
              </div>

              <!-- Revenue Type Filter -->
              <div class="space-y-1">
                <label for="revenue_type" class="text-xs font-medium text-gray-700 flex items-center">
                  <i class="fas fa-money-bill-wave mr-1 text-gray-500 text-xs"></i>
                  Revenue Type
                </label>
                <div class="relative">
                  <select 
                    v-model="form.revenue_type" 
                    name="revenue_type" 
                    id="revenue_type"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200 bg-white text-sm form-input focus-ring"
                  >
                    <option value="all">All Revenue</option>
                    <option value="receivables">Receivables Only</option>
                  </select>
                </div>
              </div>

              <!-- Actions -->
              <div class="space-y-1 flex items-end">
                <div class="flex gap-2 w-full">
                  <button 
                    @click="generateReport"
                    type="button"
                    class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-md transition-colors duration-200 focus:outline-none focus:ring-1 focus:ring-offset-1 focus:ring-indigo-500 shadow-sm btn-hover-scale text-sm"
                  >
                    <i class="fas fa-chart-bar mr-1 text-xs"></i>
                    Generate
                  </button>
                  <button 
                    @click="exportExcel" 
                    type="button"
                    class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-md transition-colors duration-200 focus:outline-none focus:ring-1 focus:ring-offset-1 focus:ring-green-500 shadow-sm btn-hover-scale text-sm"
                  >
                    <i class="fas fa-download mr-1 text-xs"></i>
                    Export
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Revenue Table -->
        <div class="mt-4">
          <div v-if="revenueData && revenueData.length > 0" class="bg-white shadow rounded-md border border-gray-200 overflow-hidden">
            <div class="px-4 py-2 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100">
              <div class="flex items-center justify-between">
                <h4 class="text-sm font-medium text-gray-900 flex items-center">
                  <i class="fas fa-table mr-1 text-gray-600 text-xs"></i>
                  {{ form.revenue_type === 'receivables' ? 'Receivables' : 'Revenue' }} by Township - {{ form.year }}
                </h4>
                <div class="text-xs text-gray-600">
                  {{ townships.length }} townships
                </div>
              </div>
            </div>
            
            <!-- Desktop Table -->
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th scope="col" class="px-3 py-2 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider sticky left-0 bg-gray-50 z-10">
                      <div class="flex items-center">
                        <i class="fas fa-calendar mr-1 text-xs"></i>
                        Month
                      </div>
                    </th>
                    <th 
                      v-for="township in townships" 
                      :key="township.id" 
                      scope="col" 
                      class="px-3 py-2 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider min-w-[100px]"
                    >
                      {{ township.short_code }}
                    </th>
                    <th scope="col" class="px-3 py-2 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider bg-yellow-50 min-w-[100px]">
                      <div class="flex items-center justify-center">
                        <i class="fas fa-calculator mr-1 text-xs"></i>
                        Total
                      </div>
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="(monthData, index) in revenueData" :key="monthData.month" 
                      :class="index % 2 === 0 ? 'bg-white' : 'bg-gray-50'"
                      class="hover:bg-indigo-50 transition-colors duration-150">
                    <td class="px-3 py-2 whitespace-nowrap sticky left-0 bg-inherit z-10 border-r border-gray-200">
                      <div class="text-xs font-medium text-gray-900">{{ monthData.month }}</div>
                    </td>
                    <td 
                      v-for="township in townships" 
                      :key="township.id" 
                      class="px-3 py-2 whitespace-nowrap text-center"
                    >
                      <div class="text-xs font-semibold text-gray-900">
                        {{ formatCurrency(getRevenueForTownship(monthData, township.id)) }}
                      </div>
                    </td>
                    <td class="px-3 py-2 whitespace-nowrap text-center bg-yellow-50 border-l border-gray-200">
                      <div class="text-xs font-bold text-gray-900">
                        {{ formatCurrency(monthData.total) }}
                      </div>
                    </td>
                  </tr>
                  <!-- Total Row -->
                  <tr class="bg-yellow-100 border-t-2 border-yellow-300">
                    <td class="px-3 py-2 whitespace-nowrap sticky left-0 bg-yellow-100 z-10 border-r border-yellow-300">
                      <div class="text-xs font-bold text-gray-900">TOTAL</div>
                    </td>
                    <td 
                      v-for="township in townships" 
                      :key="'total-' + township.id" 
                      class="px-3 py-2 whitespace-nowrap text-center"
                    >
                      <div class="text-xs font-bold text-gray-900">
                        {{ formatCurrency(getTownshipTotal(township.id)) }}
                      </div>
                    </td>
                    <td class="px-3 py-2 whitespace-nowrap text-center bg-yellow-200 border-l border-yellow-300">
                      <div class="text-xs font-bold text-red-600">
                        {{ formatCurrency(getGrandTotal()) }}
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Empty State -->
          <div v-else class="bg-white shadow rounded-md border border-gray-200 p-8 text-center">
            <div class="w-12 h-12 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-3">
              <i class="fas fa-chart-bar text-gray-400 text-lg"></i>
            </div>
            <h3 class="text-base font-medium text-gray-900 mb-2">No revenue data found</h3>
            <p class="text-gray-600 mb-4 text-sm">Select a city and year, then click Generate to view revenue data.</p>
            <button @click="generateReport" 
                    class="inline-flex items-center px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-md transition-colors duration-200 text-sm">
              <i class="fas fa-chart-bar mr-1 text-xs"></i>
              Generate Report
            </button>
          </div>
        </div>
      </div>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import { useForm } from "@inertiajs/vue3";
import { onMounted, ref, computed } from "vue";
import axios from "axios";

export default {
  name: "RevenueByTownship",
  components: {
    AppLayout,
  },
  props: {
    city_list: Object,
    errors: Object,
  },
  setup(props) {
    const revenueData = ref([]);
    const townships = ref([]);
    
    const form = useForm({
      city_id: "",
      year: new Date().getFullYear(),
      revenue_type: "all",
    });

    const yearOptions = computed(() => {
      const currentYear = new Date().getFullYear();
      const years = [];
      for (let i = currentYear; i >= currentYear - 5; i--) {
        years.push(i);
      }
      return years;
    });

    const generateReport = () => {
      form.post("/revenue-by-township", {
        onSuccess: (response) => {
          console.log('Success response:', response);
          if (response.props && response.props.revenueData) {
            revenueData.value = response.props.revenueData;
            townships.value = response.props.townships || [];
            console.log('Revenue data set:', revenueData.value);
            console.log('Townships set:', townships.value);
          }
        },
        onError: (errors) => {
          console.log('Errors:', errors);
        },
      });
    };

    const exportExcel = () => {
      axios.post("/revenue-by-township/export", form.data(), { 
        responseType: "blob" 
      }).then((response) => {
        const blob = new Blob([response.data], {
          type: response.headers["content-type"],
        });
        const link = document.createElement("a");
        link.href = window.URL.createObjectURL(blob);
        link.download = `revenue_by_township_${form.year}_${new Date().getTime()}.xlsx`;
        link.click();
      }).catch((error) => {
        console.error("Export failed:", error);
      });
    };

    const formatCurrency = (amount) => {
      if (!amount || amount === 0) return '0';
      return parseInt(amount).toLocaleString('en-US');
    };

    const getRevenueForTownship = (monthData, townshipId) => {
      const township = monthData.townships.find(t => t.township_id === townshipId);
      return township ? township.revenue : 0;
    };

    const getTownshipTotal = (townshipId) => {
      return revenueData.value.reduce((total, monthData) => {
        return total + getRevenueForTownship(monthData, townshipId);
      }, 0);
    };

    const getGrandTotal = () => {
      return revenueData.value.reduce((total, monthData) => {
        return total + (monthData.total || 0);
      }, 0);
    };

    return { 
      form, 
      revenueData, 
      townships, 
      yearOptions,
      generateReport, 
      exportExcel, 
      formatCurrency,
      getRevenueForTownship,
      getTownshipTotal,
      getGrandTotal
    };
  },
};
</script>

<style scoped>
/* Enhanced Form Controls */
.form-input {
  transition: all 0.2s ease-in-out;
}

.form-input:hover {
  border-color: #9ca3af;
}

.form-input:focus {
  border-color: #6366f1;
  box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.5);
}

/* Focus Styles */
.focus-ring:focus {
  outline: none;
  box-shadow: 0 0 0 2px #6366f1, 0 0 0 4px rgba(99, 102, 241, 0.2);
}

/* Button Hover Effects */
.btn-hover-scale {
  transition: transform 0.2s ease-in-out;
}

.btn-hover-scale:hover {
  transform: scale(1.05);
}

/* Table Styling */
.sticky {
  position: sticky;
}

.z-10 {
  z-index: 10;
}

/* Custom Scrollbar */
.overflow-x-auto::-webkit-scrollbar {
  height: 6px;
}

.overflow-x-auto::-webkit-scrollbar-track {
  background-color: #f3f4f6;
  border-radius: 9999px;
}

.overflow-x-auto::-webkit-scrollbar-thumb {
  background-color: #d1d5db;
  border-radius: 9999px;
}

.overflow-x-auto::-webkit-scrollbar-thumb:hover {
  background-color: #9ca3af;
}
</style>
