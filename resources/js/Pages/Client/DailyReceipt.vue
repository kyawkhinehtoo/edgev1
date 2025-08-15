<template>
  <app-layout>
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="font-semibold text-xl text-white leading-tight flex items-center">
          <i class="fas fa-receipt mr-3 text-blue-200"></i>
          Bill Receipt Report
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
          <p class="mt-1 text-xs text-gray-600">Filter and search through your bill receipts</p>
        </div>

        <!-- Advance Search -->
        <div class="bg-white shadow-lg rounded-lg border border-gray-200">
          <div class="px-4 py-3 border-b border-gray-200 bg-gradient-to-r from-indigo-50 to-blue-50">
            <h4 class="text-sm font-medium text-gray-900 flex items-center">
              <i class="fas fa-search mr-2 text-indigo-600"></i>
              Search Criteria
            </h4>
          </div>
          <div class="p-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">

              <!-- General Search -->
              <div class="space-y-1">
                <label for="general" class="text-xs font-medium text-gray-700 flex items-center">
                  <i class="fas fa-user mr-1 text-gray-500 text-xs"></i>
                  General Search
                </label>
                <div class="relative">
                  <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400 text-xs"></i>
                  </div>
                  <input 
                    type="text" 
                    v-model="form.general" 
                    name="general" 
                    id="general"
                    class="pl-7 pr-3 py-2 w-full border border-gray-300 rounded-md focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200 placeholder-gray-400 text-sm form-input focus-ring"
                    placeholder="Customer/Company Name etc." 
                    tabindex="1" 
                  />
                </div>
              </div>

              <!-- Receipt Date -->
              <div class="space-y-1">
                <label for="date" class="text-xs font-medium text-gray-700 flex items-center">
                  <i class="fas fa-calendar-alt mr-1 text-gray-500 text-xs"></i>
                  Receipt Date
                </label>
                <div class="relative">
                  <VueDatePicker 
                    v-model="form.date" 
                    :range="{ partialRange: false }" 
                    placeholder="Select date range" 
                    :enable-time-picker="false" 
                    model-type="yyyy-MM-dd" 
                    id="order_date" 
                    class="w-full border border-gray-300 rounded-md focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200 compact-datepicker"
                  />
                </div>
              </div>

              <!-- Bill Name -->
              <div class="space-y-1">
                <label for="bill_id" class="text-xs font-medium text-gray-700 flex items-center">
                  <i class="fas fa-file-invoice mr-1 text-gray-500 text-xs"></i>
                  Bill Name
                </label>
                <div v-if="bill_list.length !== 0" class="relative">
                  <multiselect 
                    deselect-label="Remove selection" 
                    :options="bill_list" 
                    track-by="id" 
                    label="name"
                    v-model="form.bill_id" 
                    :allow-empty="true" 
                    :multiple="true"
                    placeholder="Select bills"
                    class="custom-multiselect compact-multiselect"
                  />
                </div>
                <div v-else class="text-xs text-gray-500 italic">No bills available</div>
              </div>

              <!-- Payment Status -->
              <div class="space-y-1">
                <label for="payment_status" class="text-xs font-medium text-gray-700 flex items-center">
                  <i class="fas fa-credit-card mr-1 text-gray-500 text-xs"></i>
                  Payment Status
                </label>
                <div class="relative">
                  <select 
                    v-model="form.payment_status" 
                    name="payment_status" 
                    id="payment_status"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200 bg-white text-sm form-input focus-ring"
                  >
                    <option value="">All Status</option>
                    <option value="Paid">✅ Paid</option>
                    <option value="Pending">⏳ Unpaid</option>
                  </select>
                </div>
              </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-4 flex flex-col sm:flex-row gap-2 justify-between">
              <div class="flex flex-col sm:flex-row gap-2">
                <button 
                  @click="doSearch"
                  type="button"
                  class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-md transition-colors duration-200 focus:outline-none focus:ring-1 focus:ring-offset-1 focus:ring-indigo-500 shadow-sm btn-hover-scale text-sm"
                >
                  <i class="fas fa-search mr-1 text-xs"></i>
                  Search
                </button>
                <button 
                  @click="resetForm"
                  type="button"
                  class="inline-flex items-center justify-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-md transition-colors duration-200 focus:outline-none focus:ring-1 focus:ring-offset-1 focus:ring-gray-500 shadow-sm btn-hover-scale text-sm"
                >
                  <i class="fas fa-undo-alt mr-1 text-xs"></i>
                  Reset
                </button>
              </div>
              <button 
                @click="exportExcel" 
                type="button"
                class="inline-flex items-center justify-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-md transition-colors duration-200 focus:outline-none focus:ring-1 focus:ring-offset-1 focus:ring-green-500 shadow-sm btn-hover-scale text-sm"
              >
                <i class="fas fa-download mr-1 text-xs"></i>
                Export
              </button>
            </div>
          </div>
        </div>

        <!-- Statistics Cards -->
        <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-3">
          <div class="bg-white overflow-hidden shadow rounded-md border border-gray-200 card-hover">
            <div class="p-3">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-calendar-minus text-blue-600 text-xs"></i>
                  </div>
                </div>
                <div class="ml-3 flex-1">
                  <dl>
                    <dt class="text-xs font-medium text-gray-500 truncate">Yesterday Collection</dt>
                    <dd class="text-sm font-semibold text-gray-900">K {{ yesterday_collection.toLocaleString('en-US') }}</dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-white overflow-hidden shadow rounded-md border border-gray-200 card-hover">
            <div class="p-3">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-calendar-day text-green-600 text-xs"></i>
                  </div>
                </div>
                <div class="ml-3 flex-1">
                  <dl>
                    <dt class="text-xs font-medium text-gray-500 truncate">Today Collection</dt>
                    <dd class="text-sm font-semibold text-gray-900">K {{ today_collection.toLocaleString('en-US') }}</dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-white overflow-hidden shadow rounded-md border border-gray-200 card-hover">
            <div class="p-3">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-6 h-6 bg-purple-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-calculator text-purple-600 text-xs"></i>
                  </div>
                </div>
                <div class="ml-3 flex-1">
                  <dl>
                    <dt class="text-xs font-medium text-gray-500 truncate">Search Results Total</dt>
                    <dd class="text-sm font-semibold text-gray-900">K {{ select_total.toLocaleString('en-US') }}</dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Results Table -->
        <div class="mt-4">
          <div v-if="receipt_records.data && receipt_records.data.length > 0" class="bg-white shadow rounded-md border border-gray-200 overflow-hidden">
            <div class="px-4 py-2 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100">
              <div class="flex items-center justify-between">
                <h4 class="text-sm font-medium text-gray-900 flex items-center">
                  <i class="fas fa-table mr-1 text-gray-600 text-xs"></i>
                  Receipt Records
                </h4>
                <div class="text-xs text-gray-600">
                  <span class="font-medium">{{ receipt_records.data.length }}</span> of 
                  <span class="font-medium">{{ receipt_records.total }}</span> records
                </div>
              </div>
            </div>
            
            <!-- Desktop Table -->
            <div class="hidden lg:block overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th scope="col" class="px-3 py-2 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                      <div class="flex items-center">
                        <i class="fas fa-file-invoice-dollar mr-1 text-xs"></i>
                        Bill Name
                      </div>
                    </th>
                    <th scope="col" class="px-3 py-2 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                      <div class="flex items-center">
                        <i class="fas fa-calendar mr-1 text-xs"></i>
                        Bill For
                      </div>
                    </th>
                    <th scope="col" class="px-3 py-2 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                      <div class="flex items-center">
                        <i class="fas fa-hashtag mr-1 text-xs"></i>
                        Invoice #
                      </div>
                    </th>
                    <th scope="col" class="px-3 py-2 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                      <div class="flex items-center">
                        <i class="fas fa-network-wired mr-1 text-xs"></i>
                        ISP
                      </div>
                    </th>
                    <th scope="col" class="px-3 py-2 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                      <div class="flex items-center">
                        <i class="fas fa-money-bill mr-1 text-xs"></i>
                        Issue
                      </div>
                    </th>
                    <th scope="col" class="px-3 py-2 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                      <div class="flex items-center">
                        <i class="fas fa-receipt mr-1 text-xs"></i>
                        Receipt
                      </div>
                    </th>
                    <th scope="col" class="px-3 py-2 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                      <div class="flex items-center">
                        <i class="fas fa-calendar-check mr-1 text-xs"></i>
                        Date
                      </div>
                    </th>
                    <th scope="col" class="px-3 py-2 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                      <div class="flex items-center">
                        <i class="fas fa-info-circle mr-1 text-xs"></i>
                        Status
                      </div>
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="(row, index) in receipt_records.data" :key="row.id" 
                      :class="index % 2 === 0 ? 'bg-white' : 'bg-gray-50'"
                      class="hover:bg-indigo-50 transition-colors duration-150">
                    <td class="px-3 py-2 whitespace-nowrap">
                      <div class="text-xs font-medium text-gray-900">{{ row.bill_name }}</div>
                    </td>
                    <td class="px-3 py-2 whitespace-nowrap">
                      <div class="text-xs text-gray-700">{{ row.billing_period }}</div>
                    </td>
                    <td class="px-3 py-2 whitespace-nowrap">
                      <div class="text-xs font-mono text-gray-700 bg-gray-100 px-1 py-0.5 rounded text-center">{{ row.bill_number }}</div>
                    </td>
                    <td class="px-3 py-2 whitespace-nowrap">
                      <div class="text-xs text-gray-700">{{ row.isp_name }}</div>
                    </td>
                    <td class="px-3 py-2 whitespace-nowrap">
                      <div class="text-xs font-semibold text-gray-900">K {{ parseFloat(row.total_amount).toLocaleString('en-US') }}</div>
                    </td>
                    <td class="px-3 py-2 whitespace-nowrap">
                      <div class="text-xs font-semibold text-green-600">K {{ parseFloat(row.collected_amount).toLocaleString('en-US') }}</div>
                    </td>
                    <td class="px-3 py-2 whitespace-nowrap">
                      <div class="text-xs text-gray-700">{{ row.receipt_date || 'N/A' }}</div>
                    </td>
                    <td class="px-3 py-2 whitespace-nowrap">
                      <span v-if="row.status === 'Paid'" 
                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200 status-badge">
                        <i class="fas fa-check-circle mr-1 text-xs"></i>
                        Paid
                      </span>
                      <span v-else-if="row.status === 'Pending'" 
                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 border border-yellow-200 status-badge">
                        <i class="fas fa-clock mr-1 text-xs"></i>
                        Unpaid
                      </span>
                      <span v-else 
                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200 status-badge">
                        <i class="fas fa-question-circle mr-1 text-xs"></i>
                        {{ row.status || 'Unknown' }}
                      </span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Mobile Cards -->
            <div class="lg:hidden">
              <div class="space-y-3 p-3">
                <div v-for="row in receipt_records.data" :key="row.id" 
                     class="bg-white border border-gray-200 rounded-md p-3 shadow-sm hover:shadow-md transition-shadow duration-200 mobile-card">
                  <div class="flex items-start justify-between mb-2">
                    <div class="flex-1">
                      <h5 class="text-xs font-medium text-gray-900 mb-1">{{ row.bill_name }}</h5>
                      <p class="text-xs text-gray-500">{{ row.billing_period }}</p>
                    </div>
                    <span v-if="row.status === 'Paid'" 
                      class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 status-badge">
                      <i class="fas fa-check-circle mr-1 text-xs"></i>
                      Paid
                    </span>
                    <span v-else-if="row.status === 'Pending'" 
                      class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 status-badge">
                      <i class="fas fa-clock mr-1 text-xs"></i>
                      Unpaid
                    </span>
                    <span v-else 
                      class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 status-badge">
                      {{ row.status || 'Unknown' }}
                    </span>
                  </div>
                  
                  <div class="grid grid-cols-2 gap-2 text-xs">
                    <div>
                      <span class="text-gray-500">Invoice:</span>
                      <div class="font-mono bg-gray-100 px-1 py-0.5 rounded mt-0.5 text-center">{{ row.bill_number }}</div>
                    </div>
                    <div>
                      <span class="text-gray-500">ISP:</span>
                      <div class="font-medium mt-0.5">{{ row.isp_name }}</div>
                    </div>
                    <div>
                      <span class="text-gray-500">Issue:</span>
                      <div class="font-semibold text-gray-900 mt-0.5">K {{ parseFloat(row.total_amount).toLocaleString('en-US') }}</div>
                    </div>
                    <div>
                      <span class="text-gray-500">Receipt:</span>
                      <div class="font-semibold text-green-600 mt-0.5">K {{ parseFloat(row.collected_amount).toLocaleString('en-US') }}</div>
                    </div>
                    <div class="col-span-2">
                      <span class="text-gray-500">Date:</span>
                      <div class="mt-0.5">{{ row.receipt_date || 'N/A' }}</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Empty State -->
          <div v-else class="bg-white shadow rounded-md border border-gray-200 p-8 text-center">
            <div class="w-12 h-12 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-3">
              <i class="fas fa-search text-gray-400 text-lg"></i>
            </div>
            <h3 class="text-base font-medium text-gray-900 mb-2">No receipts found</h3>
            <p class="text-gray-600 mb-4 text-sm">Try adjusting your search criteria or date range.</p>
            <button @click="resetForm" 
                    class="inline-flex items-center px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-md transition-colors duration-200 text-sm">
              <i class="fas fa-undo-alt mr-1 text-xs"></i>
              Reset Filters
            </button>
          </div>
        </div>

        <!-- Pagination -->
        <div v-if="receipt_records.total && receipt_records.data && receipt_records.data.length > 0" class="mt-4">
          <div class="bg-white px-3 py-2 flex items-center justify-between border-t border-gray-200 sm:px-4 rounded-md shadow">
            <div class="flex-1 flex justify-between sm:hidden">
              <pagination class="w-full" :links="receipt_records.links" />
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
              <div>
                <p class="text-xs text-gray-700">
                  Showing
                  <span class="font-medium">{{ ((receipt_records.current_page - 1) * receipt_records.per_page) + 1 }}</span>
                  to
                  <span class="font-medium">{{ Math.min(receipt_records.current_page * receipt_records.per_page, receipt_records.total) }}</span>
                  of
                  <span class="font-medium">{{ receipt_records.total }}</span>
                  results
                </p>
              </div>
              <div>
                <pagination :links="receipt_records.links" />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import Pagination from "@/Components/Pagination";
import { useForm } from "@inertiajs/vue3";
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';
import { onMounted, onUpdated, provide, ref } from "vue";
import { router } from '@inertiajs/vue3';
import Multiselect from "@suadelabs/vue3-multiselect";
export default {
  name: "DailyReceipt",
  components: {
    AppLayout,
    VueDatePicker,
    Multiselect,
    Pagination,
  },
  props: {
    receipt_records: Object,
    bill_list: Object,
    yesterday_collection: Object,
    today_collection: Object,
    select_total: Object,
    errors: Object,
  },
  setup(props) {
    const formatter = ref({
      date: "YYYY-MM-DD",
      month: "MMM",
    });
    const form = useForm({
      general: null,
      date: null,
      bill_id: null,
      payment_status: "",
    });
    function resetForm() {
      form.general = null;
      form.date = null;
      form.bill_id = null;
      form.payment_status = "";
    }
    const doSearch = () => {
      form.post("/dailyreceipt/show", {
        onSuccess: (page) => { },
        onError: (errors) => {
          console.log(errors);
        },
      });
    };
    function exportExcel() {
      axios.post("/exportReceipt", form, { responseType: "blob" }).then((response) => {
        var a = document.createElement("a");
        document.body.appendChild(a);
        a.style = "display: none";
        var blob = new Blob([response.data], {
          type: response.headers["content-type"],
        });
        const link = document.createElement("a");
        link.href = window.URL.createObjectURL(blob);
        link.download = `receipts${new Date().getTime()}.xlsx`;
        link.click();
      });
    }
    return { doSearch, resetForm, exportExcel, form, formatter };
  },
};
</script>
<style scoped>
/* Custom Multiselect Styling */
.custom-multiselect {
  border: 1px solid #d1d5db;
  border-radius: 0.375rem;
}

.compact-multiselect .custom-multiselect :deep(.multiselect__tags) {
  border: none;
  border-radius: 0.375rem;
  background-color: white;
  min-height: 36px;
  padding: 0.25rem;
}

.custom-multiselect :deep(.multiselect__tags) {
  border: none;
  border-radius: 0.375rem;
  background-color: white;
  min-height: 36px;
  padding: 0.25rem;
}

.custom-multiselect :deep(.multiselect__input) {
  background-color: transparent;
  border: none;
  font-size: 0.85rem;
}

.custom-multiselect :deep(.multiselect__placeholder) {
  color: #9ca3af;
  font-size: 0.85rem;
  margin-bottom: 0;
  padding: 0.125rem 0;
}

.custom-multiselect :deep(.multiselect__select) {
  height: 2.3rem;
}

.custom-multiselect :deep(.multiselect__tag) {
  background-color: #e0e7ff;
  color: #3730a3;
  border: 1px solid #c7d2fe;
  border-radius: 0.25rem;
  font-size: 0.85rem;
  margin: 1px;
  padding: 2px 4px;
}

.custom-multiselect :deep(.multiselect__tag-icon:hover) {
  background-color: #c7d2fe;
}

.custom-multiselect :deep(.multiselect__option--highlight) {
  background-color: #e0e7ff;
  color: #312e81;
}

.custom-multiselect :deep(.multiselect__option--selected) {
  background-color: #eef2ff;
  color: #3730a3;
}

.custom-multiselect :deep(.multiselect__content-wrapper) {
  border: 1px solid #e5e7eb;
  border-radius: 0.375rem;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

/* Date Picker Styling */
.compact-datepicker :deep(.dp__input) {
  padding: 0.5rem 0.85rem 0.5rem 1.8rem;
  border: 1px solid #d1d5db;
  border-radius: 0.375rem;
  font-size: 0.85rem;
}

:deep(.dp__input) {
  padding: 0.5rem 0.85rem;
  border: 1px solid #d1d5db;
  border-radius: 0.375rem;
  font-size: 0.85rem;
}

:deep(.dp__input:focus) {
  outline: none;
  box-shadow: 0 0 0 1px #6366f1;
  border-color: #6366f1;
}

/* Table Hover Effects */
.table-hover-row {
  transition: all 0.2s ease-in-out;
}

.table-hover-row:hover {
  background: linear-gradient(to right, #eef2ff, #eff6ff);
}

/* Loading Animation */
.loading-pulse {
  animation: pulse 1.5s ease-in-out infinite;
}

@keyframes pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.5;
  }
}

/* Custom Scrollbar */
.custom-scrollbar::-webkit-scrollbar {
  width: 6px;
  height: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
  background-color: #f3f4f6;
  border-radius: 9999px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
  background-color: #d1d5db;
  border-radius: 9999px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background-color: #9ca3af;
}

/* Button Hover Effects */
.btn-hover-scale {
  transition: transform 0.2s ease-in-out;
}

.btn-hover-scale:hover {
  transform: scale(1.05);
}

/* Card Shadow Effects */
.card-hover {
  transition: box-shadow 0.3s ease-in-out;
}

.card-hover:hover {
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
}

/* Status Badge Animations */
.status-badge {
  transition: all 0.2s ease-in-out;
}

.status-badge:hover {
  transform: scale(1.05);
}

/* Mobile Card Animations */
.mobile-card {
  transition: all 0.2s ease-in-out;
}

.mobile-card:hover {
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
  border-color: #c7d2fe;
}

/* Focus Styles */
.focus-ring:focus {
  outline: none;
  box-shadow: 0 0 0 2px #6366f1, 0 0 0 4px rgba(99, 102, 241, 0.2);
}

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

/* Gradient backgrounds */
.gradient-header {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.gradient-card {
  background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}

/* Improved shadows */
.shadow-soft {
  box-shadow: 0 2px 15px 0 rgba(0, 0, 0, 0.1);
}

.shadow-medium {
  box-shadow: 0 4px 25px 0 rgba(0, 0, 0, 0.1);
}
</style>