<template>
  <AppLayout title="Invoice Details">
    <template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">
        Invoice Details
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
          <!-- Invoice Header -->
          <div class="mb-6 grid grid-cols-4 gap-4" v-if="tempInvoiceItems.data.length > 0">
            <!-- Invoice Info - Column 1 -->
            <div class="space-y-2">
              <h3 class="text-lg font-semibold">Invoice Info</h3>
              <div class="text-lg space-y-1">
                <p><span class="font-medium">ISP:</span> {{ tempInvoiceItems.data[0].temp_invoice.isp?.name }}</p>
                <p><span class="font-medium">Bill Period:</span> {{ tempInvoiceItems.data[0].temp_invoice?.temp_bill?.billing_period }}</p>
                <p><span class="font-medium">Status:</span> {{ tempInvoiceItems.data[0].temp_invoice?.temp_bill?.status }}</p>
              </div>
            </div>

            <!-- Customer Stats - Column 2 -->
            <div class="space-y-2">
              <h3 class="text-lg font-semibold">Summary Overview</h3>
              <div class="grid grid-cols-2 gap-2">
                <div class="bg-blue-50 rounded p-2 text-center">
                  <p class="text-xs text-blue-600">MRC</p>
                  <p class="text-lg font-semibold text-blue-700">{{ tempInvoiceItems.data[0].temp_invoice.total_mrc_customer }}</p>
                </div>
                <div class="bg-blue-50 rounded p-2 text-center">
                    <p class="text-xs text-blue-600">Total MRC Amount</p>
                    <p class="text-lg font-semibold text-blue-700">{{ formatNumber(tempInvoiceItems.data[0].temp_invoice.total_mrc_amount) }}</p>
                  </div>
                <div class="bg-green-50 rounded p-2 text-center">
                  <p class="text-xs text-green-600">New</p>
                  <p class="text-lg font-semibold text-green-700">{{ tempInvoiceItems.data[0].temp_invoice.total_new_customer }}</p>
                </div>
               
                  <div class="bg-green-50 rounded p-2 text-center">
                    <p class="text-xs text-green-600">Total New Amount</p>
                    <p class="text-lg font-semibold text-green-700">{{ formatNumber(tempInvoiceItems.data[0].temp_invoice.total_installation_amount) }}</p>
                  </div>
                  <div class="bg-yellow-50 rounded p-2 text-center">
                    <p class="text-xs text-yellow-600">Additional</p>
                    <p class="text-lg font-semibold text-yellow-700">{{ tempInvoiceItems.data[0].temp_invoice.additional_description }}</p>
                  </div>
                 
                    <div class="bg-yellow-50 rounded p-2 text-center">
                      <p class="text-xs text-yellow-600">Total New Amount</p>
                      <p class="text-lg font-semibold text-yellow-700">{{ formatNumber(tempInvoiceItems.data[0].temp_invoice.additional_fees) }}</p>
                    </div>
                
              </div>
            </div>

            <!-- Summary - Column 3 -->
            <div class="space-y-2 col-span-2">
              <h3 class="text-lg font-semibold">Summary Brakedown</h3>
              <div class="bg-gray-50 rounded p-2 text-sm">
                <div class="grid grid-cols-5 gap-2 border-b border-gray-200 pb-1 mb-1">
                  <div class="col-span-2 font-medium text-gray-600">Description</div>
                  <div class="text-center font-medium text-gray-600">QTY</div>
                  <div class="text-right font-medium text-gray-600">Price</div>
                  <div class="text-right font-medium text-gray-600">Total</div>
                </div>
                <div v-for="tempInvoice in tempInvoices" 
                     :key="tempInvoice.category" 
                     class="grid grid-cols-5 gap-2 py-1 hover:bg-gray-100">
                  <div class="col-span-2 truncate">{{ tempInvoice.category }}</div>
                  <div class="text-center">{{ tempInvoice.total_customers }}</div>
                  <div class="text-right">{{ formatNumber(tempInvoice.unit_price) }}</div>
                  <div class="text-right font-medium">{{ formatNumber(tempInvoice.total_amount) }}</div>
                </div>
              </div>
            </div>
          </div>

          <!-- Invoice Items -->
          <div class="mt-8">
            <div class="flex justify-between items-center mb-4">
              <h3 class="text-lg font-semibold">Invoice Items</h3>
              <div class="flex space-x-4">
                <div class="w-48">
                  <select
                    v-model="filters.type"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                  >
                    <option value="">All Types</option>
                    <option value="FullRecurring">Full Recurring</option>
                    <option value="ProRatedRecurring">Pro-rated Recurring</option>
                    <option value="NewInstallation">New Installation</option>
                  </select>
                </div>
                <div class="w-64">
                  <input
                    v-model="filters.search"
                    type="text"
                    placeholder="Search customer name..."
                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                  >
                </div>
                <div>
                  <a @click="doExcel"
                  class="cursor-pointer inline-flex items-center px-2 py-2 bg-green-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring focus:ring-green-300 disabled:opacity-25 transition">Export
                  <i class="ml-1 fa fa-download text-white"></i></a>
                </div>
              </div>
            </div>

            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Period</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Unit Price</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="item in filteredItems" :key="item.id" class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">{{ item.customer.ftth_id }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ item.customer.name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ item.type }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      {{ formatDate(item.start_date) }} - {{ formatDate(item.end_date) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">${{ item.unit_price }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">${{ item.total_amount }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <button
                        @click="openEditModal(item)"
                        class="text-indigo-600 hover:text-indigo-900 mr-3"
                      >
                        Edit
                      </button>
                      <button
                        @click="deleteItem(item)"
                        class="text-red-600 hover:text-red-900"
                      >
                        Delete
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

             <!-- Pagination -->
          <div class="mt-4">
            <Pagination :links="tempInvoiceItems.links" />
          </div>
          </div>

          <!-- Actions -->
          <div class="mt-6 flex justify-end space-x-3">
            <Link
              :href="route('tempBilling')"
              class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded"
            >
              Back to List
            </Link>
          </div>
        </div>
      </div>
    </div>

    <EditTempInvoiceItem
      :show="showEditModal"
      :invoiceItem="selectedItem"
      @close="closeEditModal"
    />
  </AppLayout>
</template>

<script>
import { defineComponent } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import { Link, router } from '@inertiajs/vue3'
import Pagination from '@/Components/Pagination.vue'
import EditTempInvoiceItem from '@/Pages/Client/EditTempInvoiceItem.vue'

export default defineComponent({
  components: {
    AppLayout,
    Pagination,
    Link,
    EditTempInvoiceItem,
  },

  props: {
    tempInvoiceItems: Object,
    tempInvoices: Object,
  },

  data() {
    return {
      showEditModal: false,
      selectedItem: null,
      filters: {
        type: '',
        search: ''
      }
    }
  },

  computed: {
    filteredItems() {
      return this.tempInvoiceItems.data.filter(item => {
        const matchesType = !this.filters.type || item.type === this.filters.type
        const matchesSearch = !this.filters.search || 
          item.customer.name.toLowerCase().includes(this.filters.search.toLowerCase())
        return matchesType && matchesSearch
      })
    }
  },

  methods: {
    formatDate(date) {
      return date ? new Date(date).toLocaleDateString() : ''
    },

    openEditModal(item) {
        console.log(item) // Add this line to check the item object
      this.selectedItem = item
      this.showEditModal = true
    },

    closeEditModal() {
      this.showEditModal = false
      this.selectedItem = null
    },

    deleteItem(item) {
      if (confirm('Are you sure you want to delete this item?')) {
        router.delete(route('tempInvoiceItems.destroy', item.id), {
          onSuccess: () => {
            // The page will automatically refresh with updated data
          },
        })
      }
    },
    
    formatNumber(value) {
      return new Intl.NumberFormat('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
      }).format(value || 0)
    },
    doExcel() {
      let parm = Object.create({});
        parm.id = this.tempInvoiceItems.data[0]?.temp_invoice_id;

      axios.post("/exportTempBillingItemExcel",  parm, { responseType: "blob" }).then((response) => {
        console.log(response);
        var a = document.createElement("a");
        document.body.appendChild(a);
        a.style = "display: none";
        var blob = new Blob([response.data], {
          type: response.headers["content-type"],
        });
        const link = document.createElement("a");
        link.href = window.URL.createObjectURL(blob);
        link.download = `temp_billings_${this.tempInvoiceItems.data[0].temp_invoice.isp?.name}_${this.tempInvoiceItems.data[0]?.temp_invoice?.temp_bill?.bill_number}_${new Date().toISOString().split('T')[0]}.xlsx`;
        link.click();
      });
    },
  },
})
</script>