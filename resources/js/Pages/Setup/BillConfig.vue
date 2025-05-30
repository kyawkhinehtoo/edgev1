<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">Bill Configuration</h2>
    </template>

    <div class="py-2">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <form @submit.prevent="submit">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
              <div class="">
                <div class="mb-4">
                  <label for="max_suspension_month" class="block text-gray-700 text-sm font-bold mb-2">Max Suspension Allowed Month :</label>
                  <div class="mt-1 flex rounded-md shadow-sm">
                    <input type="number" v-model="form.max_suspension_month" name="max_suspension_month" id="max_suspension_month"
                      class="focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-none rounded-l-md sm:text-sm border-gray-300"
                      placeholder="Month" />
                    <span
                      class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                      Month </span>
                  
                  </div>

                </div>
                <div class="mb-4">
                  <label for="prepaid_day" class="block text-gray-700 text-sm font-bold mb-2">Port Maintenance Fees Per Month :</label>
                  <div class="mt-1 flex rounded-md shadow-sm">
                    <input type="number" v-model="form.port_maintenance_fee" name="port_maintenance_fee" id="port_maintenance_fee"
                    class="focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-none rounded-l-md sm:text-sm border-gray-300"
                    placeholder="MMK" />
                  <span
                    class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                    MMK </span>
                  </div>
                </div>
                
              </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
              <span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto">
                <button type="submit"
                  class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition"
                  v-show="!editMode">Save</button>
              </span>
              <span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto">
                <button type="submit"
                  class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition"
                  v-show="editMode">Update</button>
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
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import Pagination from "@/Components/Pagination";
import { reactive, ref, onMounted, onUpdated } from "vue";
import Multiselect from "@suadelabs/vue3-multiselect";
import { router } from '@inertiajs/vue3';
export default {
  name: "BillConfig",
  components: {
    AppLayout,
    Pagination,
    Multiselect
  },
  props: {
    billconfig: Object,
    errors: Object,

  },

  setup(props) {
    const form = reactive({
      id: null,
     
      max_suspension_month: null,
      port_maintenance_fee: null,
     
    });
    const customer_type = [
      {
        "id": '1',
        "name": "Normal Customer"
      },
      {
        "id": '2',
        "name": "VIP Customer",
      },
      {
        "id": '3',
        "name": "Partner Customer",
      },
      {
        "id": '4',
        "name": "Office Staff",
      }
    ];
    let editMode = ref(false);
    function submit() {
      if (!editMode.value) {
        form._method = "POST";
        router.post("/billconfig", form, {
          preserveState: true,
          onSuccess: (page) => {
            Toast.fire({
              icon: "success",
              title: page.props.flash.message,
            });
          },
          onError: (errors) => {
            console.log("error ..".errors);
          },
        });
      } else {
        form._method = "PUT";
        router.post("/billconfig/" + form.id, form, {
          onSuccess: (page) => {
            Toast.fire({
              icon: "success",
              title: page.props.flash.message,
            });
          },

          onError: (errors) => {
            console.log("error ..".errors);
          },
        });
      }
    }
    function edit(data) {
      editMode.value = true;
      form.id = data.id;
      form.max_suspension_month = data.max_suspension_month;
      form.port_maintenance_fee = data.port_maintenance_fee;
    
    }

    onMounted(() => {
      if (props.billconfig) {
        edit(props.billconfig);
      }
    });
    onUpdated(() => { });
    return { form, submit, editMode, customer_type };
  },
};
</script>
