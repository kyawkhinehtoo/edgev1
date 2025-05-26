<template>
  <AppLayout title="Temporary Billing">
    <template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">
        Temporary Billing List
      </h2>
    </template>

    <div class="py-12">
      <div class=" mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
          <!-- Table -->
           <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold text-gray-600 mb-2">Bill Name : {{ tempInvoices.data[0]?.temp_bill?.bill_number }} </h2>
            <div class="space-x-2">
              <a @click="doSave"
              class="cursor-pointer inline-flex items-center px-3 py-3 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 active:bg-yellow-800 focus:outline-none focus:border-yellow-800 focus:ring focus:ring-yellow-300 disabled:opacity-25 transition">Save Final
              <i class="ml-1 fa fa-save text-white"></i></a>
              <a @click="doExcel"
              class="cursor-pointer inline-flex items-center px-3 py-3 bg-green-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring focus:ring-green-300 disabled:opacity-25 transition">Export
              <i class="ml-1 fa fa-download text-white"></i></a>
            </div>
            
           </div>
          
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                
                  <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    ISP
                  </th>
                  <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                    MRC Customers
                  </th>
                  <th class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                    MRC Amount
                  </th>
                  <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                    New Customers
                  </th>
                  <th class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Installation Amount
                  </th>
                  <th class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Sub Total
                  </th>
                  <th class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Tax (%)
                  </th>
                  <th class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Discount
                  </th>
                  <th class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Total Amount
                  </th>
                  <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Actions
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="invoice in tempInvoices.data" :key="invoice.id" class="hover:bg-gray-50">
         
                  <td class="px-3 py-4 whitespace-nowrap">{{ invoice.isp?.name }}</td>
                  <td class="px-3 py-4 whitespace-nowrap text-center">
                    <span class="px-2 py-1 text-sm font-bold rounded-full bg-blue-100 text-blue-800">
                      {{ invoice.total_mrc_customer }}
                    </span>
                  </td>
                  <td class="px-3 py-4 whitespace-nowrap text-right">${{ formatNumber(invoice.total_mrc_amount) }}</td>
                  <td class="px-3 py-4 whitespace-nowrap text-center">
                    <span class="px-2 py-1 text-sm font-bold rounded-full bg-green-100 text-green-800">
                      {{ invoice.total_new_customer }}
                    </span>
                  </td>
                  <td class="px-3 py-4 whitespace-nowrap text-right">${{ formatNumber(invoice.total_installation_amount) }}</td>
                  <td class="px-3 py-4 whitespace-nowrap text-right">${{ formatNumber(invoice.sub_total) }}</td>
                  <td class="px-3 py-4 whitespace-nowrap text-right">{{ invoice.tax_percent }}%</td>
                  <td class="px-3 py-4 whitespace-nowrap text-right">${{ formatNumber(invoice.discount_amount) }}</td>
                  <td class="px-3 py-4 whitespace-nowrap text-right font-medium">${{ formatNumber(invoice.total_amount) }}</td>
                  <td class="px-3 py-4 whitespace-nowrap text-center space-x-2">
                    <Link 
                      :href="route('tempInvoice.details', invoice.id)"
                      class="inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                      View
                    </Link>
                    <button
                      @click="openEditModal(invoice)"
                      class="inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                    >
                      Edit
                    </button>
                    
                  <a 
                  :href="`/pdfpreview1/${invoice.id}`" 
                  target="_blank"
                  rel="noopener noreferrer"
                  class="inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md text-yellow-700 bg-yellow-100 hover:bg-yellow-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500"
                >
                  <span>Invoice</span>
                  </a>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div class="mt-4">
            <Pagination :links="tempInvoices.links" />
          </div>
        </div>
      </div>
    </div>
    <EditTempInvoice
  :show="showEditModal"
  :invoice="selectedInvoice"
  @close="closeEditModal"
/>
<div ref="openBillName" class="fixed z-10 inset-0 overflow-y-auto ease-out duration-400" v-if="openBillName">
  <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
    <div class="fixed inset-0 transition-opacity">
      <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
    </div>
    <!-- This element is to trick the browser into centering the modal contents. -->
    <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>​
    <div
      class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
      role="dialog" aria-modal="true" aria-labelledby="modal-headline">
      <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
        <div class="">
          <div class="mb-4">
            <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Bill Name:</label>
            <input type="text"
              class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
              id="name" placeholder="Enter Bill Name" v-model="form.bill_name" />
            <div v-if="$page.props.errors.bill_name" class="text-red-500">{{ $page.props.errors.bill_name }}
            </div>
          </div>
        </div>
      </div>
      <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
        <span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto">
          <button @click="saveFinal" type="button"
            class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">GO
            !</button>
        </span>
        <span class="mt-3 flex w-full rounded-md shadow-sm sm:mt-0 sm:w-auto">
          <button @click="closeFinal" type="button"
            class="inline-flex justify-center w-full rounded-md border border-gray-300 px-4 py-2 bg-white text-base leading-6 font-medium text-gray-700 shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue transition ease-in-out duration-150 sm:text-sm sm:leading-5">Cancel</button>
        </span>
      </div>
    </div>
  </div>
</div>
  </AppLayout>
</template>
<script setup>
import { ref } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import Pagination from '@/Components/Pagination.vue'
import EditTempInvoice from './EditTempInvoice.vue'
import { router, Link ,useForm} from "@inertiajs/vue3"
import axios from 'axios'

const props = defineProps({
  tempInvoices: Object,
  packages: Array,
  townships: Array,
  status: Array,
  bill: Array,
})

const showEditModal = ref(false)
const selectedInvoice = ref(null)
const openBillName = ref(false)
const form = useForm({
      bill_name: props.tempInvoices.data[0]?.temp_bill?.bill_number,
    });
const formatNumber = (value) => {
  return new Intl.NumberFormat('en-US', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(value || 0)
}

const openEditModal = (invoice) => {
  selectedInvoice.value = invoice
  showEditModal.value = true
}

const closeEditModal = () => {
  showEditModal.value = false
  selectedInvoice.value = null
}

const doSave = () => {
  openBillName.value = true;
}
const saveFinal = () => {
  form.post("/saveFinal", {
    onSuccess: (page) => {
      Toast.fire({
              icon: "success",
              title: page.props.flash.message,
            });
      openBillName.value = false;
      form.reset();
    },
    onError: (errors) => {
      console.log(errors);
      form.errors = errors.errors;
    },
  }); 
}
const closeFinal = () => {
  openBillName.value = false;
}
const doExcel = () => {
  axios.post("/exportTempBillingExcel", {}, { 
    responseType: "blob",
    headers: {
      'Content-Type': 'application/json',
    }
  }).then((response) => {
    const blob = new Blob([response.data], {
      type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
    })
    const url = window.URL.createObjectURL(blob)
    const link = document.createElement("a")
    link.href = url
    link.download = `temp_billings_${props.tempInvoices.data[0]?.temp_bill?.bill_number}_${new Date().toISOString().split('T')[0]}.xlsx`
    document.body.appendChild(link)
    link.click()
    document.body.removeChild(link)
    window.URL.revokeObjectURL(url)
  }).catch(error => {
    console.error('Export failed:', error)
  })
}

// Make variables and functions available to template
defineExpose({
  form,
  showEditModal,
  selectedInvoice,
  openBillName,
  formatNumber,
  openEditModal,
  closeEditModal,
  doSave,
  doExcel,
  saveFinal,
  closeFinal
})
</script>