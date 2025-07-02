<template>
  <div v-if="show" class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50">
    <div class="fixed inset-0 transform transition-all">
      <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
    </div>

    <div class="mb-6 bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:w-full sm:max-w-4xl sm:mx-auto">
      <div class="p-8">
        <!-- Invoice Header -->
        <div class="border-b pb-4 mb-6">
          <h1 class="text-2xl font-bold text-gray-800">INVOICE</h1>
          <div class="grid grid-cols-2 gap-8 mt-4">
            <div>
              <p class="text-gray-600">Bill To:</p>
              <p class="font-medium mt-1">{{ form.isp_name }}</p>
              <p class="text-sm text-gray-500">Bill Number: {{ form.bill_number }}</p>
            </div>
            <div class="text-right">
              <div class="mb-2">
                <label class="block text-sm text-gray-600">Issue Date:</label>
                <input
                  v-model="form.issue_date"
                  type="date"
                  class="mt-1 w-48 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                />
              </div>
              <div>
                <label class="block text-sm text-gray-600">Due Date:</label>
                <input
                  v-model="form.due_date"
                  type="date"
                  class="mt-1 w-48 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                />
              </div>
            </div>
          </div>
        </div>

        <!-- Invoice Items -->
        <!-- In the Invoice Items section, replace with: -->
        <div class="mb-8">
          <table class="w-full">
            <thead>
              <tr class="border-b">
                <th class="text-left py-3 px-2">Description</th>
                <th class="text-right py-3 px-2 w-32">Customers</th>
                <th class="text-right py-3 px-2 w-32">Amount</th>
              </tr>
            </thead>
            <tbody>
                <tr class="border-b">
                <td class="py-3 px-2">Monthly Port Leasing Charges</td>
                <td class="py-3 px-2 text-right">{{ form.total_port_leasing_customer }}</td>
                <td class="py-3 px-2 text-right">${{ form.total_port_leasing_amount }}</td>
                </tr>
                <tr class="border-b">
                <td class="py-3 px-2">Maintenance Charges</td>
                <td class="py-3 px-2 text-right">{{ form.total_maintenance_customer }}</td>
                <td class="py-3 px-2 text-right">${{ form.total_maintenance_amount }}</td>
                </tr>
                <tr class="border-b">
                <td class="py-3 px-2">Suspension Charges</td>
                <td class="py-3 px-2 text-right">{{ form.total_suspension_customer }}</td>
                <td class="py-3 px-2 text-right">${{ form.total_suspension_amount }}</td>
                </tr>
                
                <tr class="border-b">
                <td class="py-3 px-2">Installation Charges</td>
                <td class="py-3 px-2 text-right">{{ form.total_installation_customer }}</td>
                <td class="py-3 px-2 text-right">${{ form.total_installation_amount }}</td>
                </tr>
                <tr class="border-b">
                <td class="py-3 px-2">Relocation Charges</td>
                <td class="py-3 px-2 text-right">{{ form.total_relocation_customer }}</td>
                <td class="py-3 px-2 text-right">${{ form.total_relocation_amount }}</td>
                </tr>
                <tr class="border-b">
                <td class="py-3 px-2">Material Charges</td>
                <td class="py-3 px-2 text-right">{{ form.total_material_customer }}</td>
                <td class="py-3 px-2 text-right">${{ form.total_material_amount }}</td>
                </tr>
            
              <tr class="border-b">
                <td class="py-3 px-2">
                  <input
                    v-model="form.additional_description"
                    type="text"
                    placeholder="Additional Description"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                  />
                </td>
                <td class="py-3 px-2"></td>
                <td class="py-3 px-2">
                  <input
                    v-model="form.additional_fees"
                    type="number"
                    step="0.01"
                    class="w-full text-right border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                    @input="updateCalculations"
                  />
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Replace the Invoice Summary section with: -->
        <div class="bg-gray-50 p-4 rounded-lg">
          <div class="grid grid-cols-2 gap-4">
            <div></div>
            <div class="space-y-3">
              <div class="flex justify-between">
                <span class="font-medium">Subtotal:</span>
                <span>${{ form.sub_total }}</span>
              </div>
              <div class="flex justify-between items-center">
                <span class="font-medium">Discount:</span>
                <input
                  v-model="form.discount_amount"
                  type="number"
                  step="0.01"
                  class="w-32 text-right border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                  @input="updateCalculations"
                />
              </div>
              <div class="flex justify-between items-center">
                <span class="font-medium">Tax (%):</span>
                <input
                  v-model="form.tax_percent"
                  type="number"
                  step="0.01"
                  class="w-32 text-right border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                  @input="updateCalculations"
                />
              </div>
              <div class="flex justify-between">
                <span class="font-medium">Tax Amount:</span>
                <span>${{ form.tax_amount }}</span>
              </div>
              <div class="flex justify-between pt-3 border-t">
                <span class="font-bold">Total Amount:</span>
                <span class="font-bold">${{ form.total_amount }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Actions -->
        <div class="mt-8 flex justify-end space-x-3">
          <button
            type="button"
            class="px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-sm text-gray-700 hover:bg-gray-50"
            @click="closeModal"
          >
            Cancel
          </button>
          <button
            @click="submit"
            class="px-4 py-2 bg-blue-600 text-white rounded-md font-semibold text-sm hover:bg-blue-700"
            :disabled="form.processing"
          >
            Save Invoice
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { defineComponent, computed,watch } from 'vue'
import { useForm,router } from '@inertiajs/vue3'

export default defineComponent({
  props: {
    show: Boolean,
    invoice: {
      type: Object,
      default: () => ({})
    },
  },

  emits: ['close'],

  setup(props, { emit }) {
    const form = useForm({
      id: '',
      bill_number: '',
      isp_name: '',
      // total_mrc_amount: 0,
      // total_installation_amount: 0,
      // total_mrc_customer: 0,
      // total_new_customer: 0,
      total_port_leasing_customer : 0,
      total_maintenance_customer : 0,
      total_suspension_customer : 0,
      total_installation_customer : 0,
      total_relocation_customer : 0,
      total_material_customer : 0,
      total_port_leasing_amount : 0,
      total_maintenance_amount : 0,
      total_suspension_amount : 0,
      total_installation_amount : 0,
      total_relocation_amount : 0,
      total_material_amount : 0,

      additional_description: '',
      additional_fees: 0,
      sub_total: 0,
      discount_amount: 0,
      tax_percent: 0,
      tax_amount: 0,
      total_amount: 0,
      issue_date: '',
      due_date: '',
    })

    const updateCalculations = () => {
  // Calculate sub total
  form.sub_total =
    Number(form.total_port_leasing_amount) +
    Number(form.total_maintenance_amount) +
    Number(form.total_suspension_amount) +
    Number(form.total_installation_amount) +
    Number(form.total_relocation_amount) +
    Number(form.total_material_amount) +
    Number(form.additional_fees);

  // Calculate tax amount (after applying discount)
  form.tax_amount = ((form.sub_total - Number(form.discount_amount)) * Number(form.tax_percent)) / 100;

  // Calculate final total (ensure discount is subtracted before adding tax)
  form.total_amount = form.sub_total - Number(form.discount_amount) + Number(form.tax_amount);
};

    watch(() => props.invoice, (newValue) => {
      if (newValue) {
        form.id = newValue.id
        form.bill_number = newValue.temp_bill?.bill_number
        form.isp_name = newValue.isp?.name
       
        form.total_port_leasing_customer = newValue.total_port_leasing_customer
        form.total_maintenance_customer = newValue.total_maintenance_customer
        form.total_suspension_customer = newValue.total_suspension_customer
        form.total_installation_customer = newValue.total_installation_customer
        form.total_relocation_customer = newValue.total_relocation_customer
        form.total_material_customer = newValue.total_material_customer
        form.total_port_leasing_amount = newValue.total_port_leasing_amount
        form.total_maintenance_amount = newValue.total_maintenance_amount
        form.total_suspension_amount = newValue.total_suspension_amount
        form.total_installation_amount = newValue.total_installation_amount
        form.total_relocation_amount = newValue.total_relocation_amount
        form.total_material_amount = newValue.total_material_amount

        form.additional_description = newValue.additional_description
        form.additional_fees = newValue.additional_fees
        form.sub_total = newValue.sub_total
        form.discount_amount = newValue.discount_amount || 0
        form.tax_percent = newValue.tax_percent || 0
        form.tax_amount = newValue.tax_amount || 0
        form.total_amount = newValue.total_amount
        form.issue_date = newValue.issue_date
        form.due_date = newValue.due_date
        updateCalculations()
      }
    }, { immediate: true, deep: true })

    const submit = () => {
      form._method = 'PUT';
     router.post(route('tempInvoice.update', { id: form.id }), form, {
        onSuccess: (page) => {
          Toast.fire({
            icon: "success",
            title: page.props.flash.message,
          });
          closeModal()
        },
        onError: (errors) => {
          console.error("Error submitting form:", errors);
        }
      })
    }

    const closeModal = () => {
      form.reset()
      emit('close')
    }

    return {
      form,
      submit,
      closeModal,
      updateCalculations,
    }
  },
})
</script>