<template>
  <div v-if="show" class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50">
    <div class="fixed inset-0 transform transition-all">
      <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
    </div>

    <div class="mb-6 bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:w-full sm:max-w-2xl sm:mx-auto">
      <div class="p-6">
        <h2 class="text-lg font-medium text-gray-900 mb-4">
          Edit Invoice Item
        </h2>
        
        <form @submit.prevent="submit">
          <div class="grid grid-cols-1 gap-4">
            <div>
              <label class="block font-medium text-sm text-gray-700">
                Customer ID
              </label>
              <input
                v-model="form.ftth_id"
                type="text"
                class="mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
                disabled
              />
            </div>
            <div>
              <label class="block font-medium text-sm text-gray-700">
                Customer Name
              </label>
              <input
                v-model="form.customer_name"
                type="text"
                class="mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
                disabled
              />
            </div>
            <div>
              <label class="block font-medium text-sm text-gray-700">
                Description
              </label>
              <input
                v-model="form.description"
                type="text"
                class="mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
                disabled
              />
            </div>

            <div>
              <label class="block font-medium text-sm text-gray-700">
                Type
              </label>
              <input
                v-model="form.type"
                type="text"
                class="mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
                disabled
              />
            </div>

            <div>
              <label class="block font-medium text-sm text-gray-700">
                Start Date
              </label>
              <input
                v-model="form.start_date"
                type="date"
                class="mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
              />
            </div>

            <div>
              <label class="block font-medium text-sm text-gray-700">
                End Date
              </label>
              <input
                v-model="form.end_date"
                type="date"
                class="mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
              />
            </div>

            <div>
              <label class="block font-medium text-sm text-gray-700">
                Unit Price
              </label>
              <input
                v-model="form.unit_price"
                type="number"
                step="0.01"
                class="mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
              />
            </div>

            <div>
              <label class="block font-medium text-sm text-gray-700">
                Total Amount
              </label>
              <input
                v-model="form.total_amount"
                type="number"
                step="0.01"
                class="mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
              />
            </div>
          </div>

          <div class="mt-6 flex justify-end space-x-3">
            <button
              type="button"
              class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:ring focus:ring-blue-200 active:text-gray-800 active:bg-gray-50 disabled:opacity-25 transition"
              @click="closeModal"
            >
              Cancel
            </button>
            <button
              type="submit"
              class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition"
              :disabled="form.processing"
            >
              Save Changes
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import { defineComponent, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'

export default defineComponent({
  props: {
    show: Boolean,
    invoiceItem: {
      type: Object,
      default: () => ({})
    },
  },

  emits: ['close'],

  setup(props, { emit }) {
    const form = useForm({
      id: '',
      ftth_id: '',
      customer_name: '',
      description: '',
      type: '',
      start_date: '',
      end_date: '',
      unit_price: '',
      total_amount: '',
    })

    watch(() => props.invoiceItem, (newValue) => {
      if (newValue) {
        form.id = newValue.id
        form.ftth_id = newValue.customer?.ftth_id
        form.customer_name = newValue.customer?.name
        form.description = newValue.description
        form.type = newValue.type
        form.start_date = newValue.start_date
        form.end_date = newValue.end_date
        form.unit_price = newValue.unit_price
        form.total_amount = newValue.total_amount
      }
    }, { immediate: true, deep: true })
 
    const submit = () => {
      form.put(route('tempInvoiceItems.update', form.id), {
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
    }
  },
})
</script>