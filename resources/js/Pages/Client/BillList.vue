<template>
  <AppLayout title="Final Billing">
    <template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">
        Final Billing List
      </h2>
    </template>

    <div class="py-12">
      <div class=" mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-xl sm:rounded-lg p-6">
          <!-- Table -->
           <div class="flex justify-between items-center mb-4">
            <div class="inline-flex items-center space-x-2">
              <h2 class="text-xl font-semibold text-gray-600">Bill Name : </h2>  
              <div class="flex rounded-md shadow-sm w-80 ">
                <multiselect deselect-label="Selected already" :options="bills" track-by="id" label="name" v-model="billForm.bill_id" :allow-empty="false"></multiselect>
              </div>
              <a @click="showBill"
              class="cursor-pointer inline-flex items-center px-3 py-3 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring focus:ring-indigo-300 disabled:opacity-25 transition">Show
              <i class="ml-1 fa fa-circle-right text-white"></i></a>
            </div>
            <div class="space-x-2">
            
              <a @click="doExcel"
              class="cursor-pointer inline-flex items-center px-3 py-3 bg-green-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring focus:ring-green-300 disabled:opacity-25 transition">Export
              <i class="ml-1 fa fa-download text-white"></i></a>
            </div>
            
           </div>
          
          <div class="overflow-x-auto" v-if="invoices">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                
                  <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    ISP
                  </th>
                  <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Invoice Number
                  </th>
                
                  <th class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Total Amount
                  </th>
                  <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Status
                  </th>
                  <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Invoice Actions
                  </th>
                  <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Receipt Actions
                  </th>
                  
                  <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Notify
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="invoice in invoices?.data" :key="invoice.id" class="hover:bg-gray-50">
                  <td class="px-3 py-4 whitespace-nowrap">{{ invoice.invoice_number }}</td>
                  <td class="px-3 py-4 whitespace-nowrap">{{ invoice.isp?.name }}</td>
                  
                  <td class="px-3 py-4 whitespace-nowrap text-right font-medium">${{ formatNumber(invoice.total_amount) }}</td>
                  <td class="px-3 py-4 whitespace-nowrap text-center">{{ invoice.payment_status }}</td>
                  <td class="px-3 py-4 whitespace-nowrap text-center space-x-2">
                  <Link 
                      :href="route('invoice.details', invoice.id)"
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
                  <button 
                   v-if="!invoice.invoice_url"
                  @click="generatePDF(invoice.id)"
                  class="inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                > <span>Invoice <i class="fa fa-file-pdf"></i></span>
                  </button>
                  <a :href="`/s/${invoice.invoice_url}`" target="_blank" v-else> <i class="fa fa-link"></i> </a>
                </td>
                <td class="px-3 py-4 whitespace-nowrap text-left space-x-2"> 
                  
                  <button
                  @click="openReceiptModal(invoice)"
                  target="_blank"
                  rel="noopener noreferrer"
                  :class="[
                    invoice.receipt_id 
                      ? 'inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md text-green-700 bg-green-100 hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500'
                      : 'inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md text-yellow-700 bg-yellow-100 hover:bg-yellow-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500'
                  ]"
                >
                  <span v-if="invoice.receipt_id">View</span>
                  <span v-else>Receipt</span>

                  </button>
                  <a :href="`/s/${invoice?.receipt_record.receipt_url}`" target="_blank" v-if="invoice.receipt_record?.receipt_url && invoice.receipt_id"> <i class="fa fa-link"></i> </a>
                  <button 
                  v-if="invoice.receipt_id && !invoice.receipt_record?.receipt_url"
                  @click="generateReceiptPDF(invoice.receipt_id)"
                  class="inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                > <span>Receipt <i class="fa fa-file-pdf"></i></span>
                  </button>
                 
                </td>
                
                <td class="px-3 py-4 whitespace-nowrap text-center space-x-2">
                  <a 
                  @click="doAlert('Will be enabled in production!')"
                  class="cursor-pointer inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-700 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
                >
                  <span>Send SMS</span>
                  </a>
                  <a 
                   @click="doAlert('Will be enabled in production!')"
                  
                 class="cursor-pointer inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-700 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
                >
                  <span>Send Email</span>
                  </a>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div class="mt-4" v-if="invoices">
            <Pagination :links="invoices?.links" />
          </div>
        </div>
      </div>
    </div>
    <EditInvoice
  :show="showEditModal"
  :invoice="selectedInvoice"
  @close="closeEditModal"
/>
<div ref="isOpen" class="fixed z-10 inset-0 overflow-y-auto ease-out duration-400" v-if="isOpen">
  <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
    <div class="fixed inset-0 transition-opacity">
      <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
    </div>
    <!-- This element is to trick the browser into centering the modal contents. -->
    <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>​
    <div
      class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-7xl sm:w-full"
      role="dialog" aria-modal="true" aria-labelledby="modal-headline">
      <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex justify-between w-full">
        <h1 class="flex text-indigo-700 text-md uppercase font-bold  pt-1 no-underline">Receipt Form</h1>
        <i class="flex fa fa-2x fas fa-times-circle text-red-500 hover:text-red-800 cursor-pointer"
          @click="closeModal"></i>
      </div>
      <form @submit.prevent="submit">
        <div class="shadow overflow-hidden border-b border-gray-200 p-4">
          <p v-show="$page.props.errors.receipt_date" class="mt-2 text-sm text-red-500 block">{{
            $page.props.errors.receipt_date
          }}</p>
          <p v-show="$page.props.errors.collected_amount" class="mt-2 text-sm text-red-500 block">{{
            $page.props.errors.collected_amount
          }}</p>
          <p v-show="$page.props.errors.user" class="mt-2 text-sm text-red-500 block"> Received By field is required </p>
          <div class="grid grid-cols-1 md:grid-cols-4 w-full">

            <div class="col-span-2 sm:col-span-2 border-2" :style="{ backgroundColor: $page.props.accent_color, borderColor:$page.props.theme_color }">
              <h1 class="text-white text-lg font-semibold mt-1 px-2">RECEIPT ENTRY</h1>
            </div>
            <div class="col-span-2 sm:col-span-2 border-b-2 border-marga justify-end flex">

              <span class="inline-flex text-sm p-2">Payment Date: </span><input type="date"
                class="py-2 focus:ring-indigo-500 focus:border-indigo-500 inline-flex sm:text-sm border-2 border-gray-300"
                v-model="form.receipt_date" />

            </div>


          </div>
          <div class="grid grid-cols-1 md:grid-cols-4 gap-6 w-full py-4">
            <div class="col-span-3 sm:col-span-3 border-2 border-marga p-4">
              <p>ISP : {{ form.bill_to }}</p>
              <p>Attention : {{ form.attn }}</p>
              <p>Payment Description : {{ form.service_description }}</p>
              <p>Billing Period: {{ form.billing_period }}</p>
            </div>
            <div class="col-span-1 sm:col-span-1 flex flex-col justify-between">
              <div class="border-2 border-marga p-2 text-center flex flex-col">
                <span class="font-semibold text-md">Reference Invoice :</span> <span class="text-sm">  {{ form.invoice_number
                }}</span>
              </div>
              <div class="border-2 border-marga p-2 text-center flex flex-col mt-2" v-if="form.receipt_number">
                <span class="font-semibold text-md" >Receipt Number :</span> <span class="text-sm">  {{ form.receipt_number
                }}</span>
              </div>
              <div class="border-2 border-marga p-2 text-center flex flex-col mt-2">
                <span class="font-semibold text-md">Bill Number:</span> <span class="text-sm"> {{ form.bill_number
                  }}</span>
              </div>
             
            </div>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-4 gap-6 w-full">
            <div class="py-4 col-span-1 sm:col-span-1 border-2 border-marga text-center flex flex-col">
              <span class="font-semibold text-md">Amount (MMK):</span> <span class="text-sm"> {{ form.total_amount
                }}</span>
            </div>
            <div class="py-4 col-span-3 sm:col-span-3 border-2 border-marga text-center flex flex-col">
              <span class="font-semibold text-md">Amount In Word:</span> <span class="text-sm capitalize"> {{
                form.amount_in_word
              }}</span>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-6 w-full py-2 gap-6">
            <div class="col-span-1 sm:col-span-1">
              <label class="block mt-2">Received Amount</label>
            </div>
            <div class="col-span-1 sm:col-span-1 border-b-2 border-marga"><input type="text"
                class="py-2 px-0 inline-flex sm:text-sm border-0 focus:ring-0 w-full"
                v-model="form.collected_amount"  @change="calc" /></div>

            <div class="col-span-1 sm:col-span-1">
              <label class="block mt-2">Payment Channel</label>
            </div>
            <div class="col-span-3 sm:col-span-3 border-b-2 border-marga">
              <div class="flex">
                <label class="flex-auto items-center mt-1"> <input type="radio"
                    class="shadow hover:shadow-md text-blue-400"  name="type"
                    v-model="form.type" value="cb" />
                  <span class="ml-2 text-gray-700 text-xs font-semibold">CB Bank</span>
                </label>
                <label class="flex-auto items-center mt-1"> <input type="radio"
                    class="shadow hover:shadow-md text-blue-600" name="type" v-model="form.type"
                    value="kbz_bank" /><span class="ml-2 text-gray-700 text-xs font-semibold">KBZ Bank</span> </label>
                <label class="flex-auto items-center mt-1"> <input type="radio"
                    class="shadow hover:shadow-md text-red-400" name="type" v-model="form.type"
                    value="quick_pay" /><span class="ml-2 text-gray-700 text-xs font-semibold">KBZ Quickpay</span>
                </label>
                <label class="flex-auto items-center mt-1"> <input type="radio"
                    class="shadow hover:shadow-md text-red-600" name="type" v-model="form.type"
                    value="aya_bank" /><span class="ml-2 text-gray-700 text-xs font-semibold">AYA Bank</span> </label>
                <label class="flex-auto items-center mt-1"> <input type="radio"
                    class="shadow hover:shadow-md text-green-600" name="type"
                    v-model="form.type" value="citizen_bank" /><span
                    class="ml-2 text-gray-700 text-xs font-semibold">Citizen Bank</span>
                </label>
                <label class="flex-auto items-center mt-1"> <input type="radio"
                    class="shadow hover:shadow-md text-indigo-600" name="type" v-model="form.type"
                    value="cash" /><span class="ml-2 text-gray-700 text-xs font-semibold">Cash</span> </label>
                <label class="flex-auto items-center mt-1"> <input type="radio"
                    class="shadow hover:shadow-md text-indigo-400" name="type" v-model="form.type"
                    value="offset" /><span class="ml-2 text-gray-700 text-xs font-semibold">Offset</span> </label>
              </div>
            </div>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-6 w-full py-2 gap-6">
            <div class="col-span-1 sm:col-span-1">
              <label class="block mt-2" v-if="outstanding">Outstanding Amount:</label>
              <label class="block mt-2" v-else>Surplus Amount:</label>
            </div>
            <div class="col-span-1 sm:col-span-1 border-b-2 border-marga"><input type="text"
                class="py-2 px-0 inline-flex sm:text-sm border-0 focus:ring-0 w-full" v-model="form.extra_amount" />
            </div>
            <div class="col-span-1 sm:col-span-1">
              <label class="block mt-2">Transition ID:</label>
            </div>
            <div class="col-span-3 sm:col-span-3 border-b-2 border-marga">
              <input type="text" class="py-2 px-0 inline-flex sm:text-sm border-0 focus:ring-0 w-full"
                v-model="form.transition" />
            </div>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-6 w-full py-2 gap-6">
            <div class="col-span-1 sm:col-span-1">
              <label class="block mt-2">Received By:</label>
            </div>
            <div class="col-span-1 sm:col-span-1 border-b-2 border-marga">
              <multiselect :class="border - 0" deselect-label="Selected already" :options="billingTeam" track-by="id"
                label="name" v-model="form.user" :allow-empty="false" v-if="billingTeam"></multiselect>

            </div>

            <div class="col-span-1 sm:col-span-1">
              <label class="block mt-2">Remark:</label>
            </div>
            <div class="col-span-3 sm:col-span-3 border-b-2 border-marga"><textarea
                class="py-2 px-0 inline-flex sm:text-sm border-0 focus:ring-0 w-full"
                v-model="form.remark"></textarea>
            </div>
          </div>
        </div>
        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse" v-if="!editMode">
          <span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto">
            <button @click="saveReceipt" type="button"
              class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition"
              v-if="!form.receipt_status">GO !</button>
          </span>
          <span class="mt-3 flex w-full rounded-md shadow-sm sm:mt-0 sm:w-auto">
            <button @click="closeModal" type="button"
              class="inline-flex justify-center w-full rounded-md border border-gray-300 px-4 py-2 bg-white text-base leading-6 font-medium text-gray-700 shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue transition ease-in-out duration-150 sm:text-sm sm:leading-5">Cancel</button>
          </span>
        </div>
      </form>
    </div>
  </div>
</div>
  </AppLayout>
</template>
<script setup>
import { ref } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import Pagination from '@/Components/Pagination.vue'
import EditInvoice from './EditInvoice.vue'
import { router, Link ,useForm} from "@inertiajs/vue3"
import axios from 'axios'
import Multiselect from "@suadelabs/vue3-multiselect";
import { toWords } from "number-to-words";
const props = defineProps({
  invoices: {
      type: Object,
      default: () => ({})
    },
  bills: Object,
  selectedBill: Object,
  smsgateway: Object,
  billingTeam:Object,
})
const outstanding = ref(false);
const showEditModal = ref(false)
const selectedInvoice = ref(null)
const isOpen = ref(false)
const editMode = ref(false)
const billForm = useForm({
      bill_id: props.selectedBill? props.selectedBill:null,
    });
const form = useForm({
      id: null,
      bill_id:props.selectedBill? props.selectedBill?.id:null,
      invoice_number: null,
      isp_id: null,
      billing_period: null,
      bill_number: null,
      issue_date: null,
      due_date: null,
      bill_to: null,
      attn: null,
      service_description: null,
      total_amount: null,
      receipt_date: null,
      user: null,
      collected_amount: null,
      type: 'cash',
      transition: null,
      remark: null,
      currency: 'mmk',
      receipt_status: null,
      extra_amount: null,
      amount_in_word:null,
      receipt_number:null,
})
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
const calc = () => {
      if (parseInt(form.collected_amount) < parseInt(form.total_amount)) {
        outstanding.value = true;
        form.extra_amount = parseInt(form.total_amount) - parseInt(form.collected_amount);
      } else {
        outstanding.value = false;
        form.extra_amount = parseInt(form.collected_amount) - parseInt(form.total_amount);
      }

      if (isNaN(form.extra_amount)) {
        form.extra_amount = 0;
      }

    }
const openReceiptModal = (data) => {

      form.id = data.id;
      form.bill_id = data.bill_id;
      form.invoice_number = data.invoice_number;
      form.isp_id = data.isp_id;
      form.billing_period = data.bill.billing_period;
      form.bill_number = data.bill.bill_number;
      form.issue_date = data.issue_date;
      form.due_date = data.due_date;
      form.bill_to = data.isp.name;
      form.attn = data.isp.contact_person;
      form.service_description = 'Payment for ' + data.invoice_number;
      form.total_amount = data.total_amount;
      form.amount_in_word = data.total_amount? toWords(parseInt(data.total_amount)) + ' MMK':'';
      form.receipt_date = data?.receipt_record?.receipt_date;
      form.user = data?.receipt_record?.collected_person? props.billingTeam.filter((team)=> team.id=data?.receipt_record?.collected_person)[0] :null;
      form.collected_amount = data?.receipt_record?.collected_amount? data?.receipt_record?.collected_amount :data.total_amount;
      form.type = (data?.receipt_record?.payment_channel)? data?.receipt_record?.payment_channel :'cash';
      form.transition = data?.receipt_record?.transition;
      form.remark = data?.receipt_record?.remark;
      form.currency = (data?.receipt_record?.currency) ? data?.receipt_record?.currency : 'mmk';
      form.receipt_status = data?.receipt_record?.receipt_status;
      form.extra_amount = '';
      form.receipt_number = data?.receipt_record?.receipt_number;
 
      calc();
      editMode.value =    form.receipt_number ?true:false;
      isOpen.value = true;

}

const showBill = () => {
  router.post(route('showbill.show', billForm))
}
const doAlert = (message) => {
  Toast.fire({
    icon: 'warning',
    title: message
  });
}
const doExcel = () => {
  axios.post("/exportBillingExcel", billForm, { 
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
    link.download = `billings_${props.invoices.data[0]?.bill?.bill_number}_${new Date().toISOString().split('T')[0]}.xlsx`
    document.body.appendChild(link)
    link.click()
    document.body.removeChild(link)
    window.URL.revokeObjectURL(url)
  }).catch(error => {
    console.error('Export failed:', error)
  })
}
 function closeModal() {
      isOpen.value = false;
      form.reset();
      editMode.value = false;
    }
function saveReceipt() {
      form._method = "POST";
      router.post("/saveReceipt", form, {
        preserveState: true,
        onSuccess: (page) => {
          console.log(page);
          Toast.fire({
            icon: "success",
            title: page.props.flash.message,
          });
          closeModal();
        },
        onError: (errors) => {

          console.log("error ..".errors);
        },
        onStart: (pending) => {
          console.log("Loading .." + pending);
         
        },
      });
    }
  
    function generatePDF(data) {
      router.post("/getSinglePDF/" + data, data, {
        preserveState: true,
        onSuccess: (page) => {
          // loading.value = false;
          Toast.fire({
            icon: "success",
            title: page.props.flash.message,
          });
        },
        onError: (errors) => {
          closeModal();
          console.log("error ..".errors);
        },
        onStart: (pending) => {

          // loading.value = true;
        },
      });
    }
    function generateReceiptPDF(data) {
    router.post("/getReceiptPDF/" + data, data, {
        preserveState: true,
        onSuccess: (page) => {
          // loading.value = false;
          Toast.fire({
            icon: "success",
            title: page.props.flash.message,
          });
        },
        onError: (errors) => {
          closeModal();
          console.log("error ..".errors);
        },
        onStart: (pending) => {

          // loading.value = true;
        },
      });
    }
    function generateAllPDF() {
      router.post("/getAllPDF", parameter.value, {
        preserveState: true,
        onSuccess: (page) => {
          loading.value = false;
          Toast.fire({
            icon: "success",
            title: page.props.flash.message,
          });
        },
        onError: (errors) => {
          closeModal();
          console.log("error ..".errors);
        },
        onStart: (pending) => {

          loading.value = true;
        },
      });
    }
// Make variables and functions available to template
defineExpose({
  billForm,
  form,
  doAlert,
  selectedInvoice,
  formatNumber,
  openEditModal,
  closeEditModal,
  doExcel,
  showBill,
  openReceiptModal,
  calc,
  outstanding,
  saveReceipt,
  closeModal,
  generatePDF,
  generateAllPDF,
  generateReceiptPDF
})
</script>