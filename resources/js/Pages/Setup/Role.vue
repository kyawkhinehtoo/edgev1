<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">Role Setup (Internal)</h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
          <div class="flex justify-between mb-6">
            <div class="flex items-center flex-1">
              <div class="w-1/3">
                <input
                  v-model="search"
                  type="text"
                  placeholder="Search roles..."
                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                  @keyup.enter="searchTsp"
                >
              </div>
            </div>
            <button @click="openModal" 
              class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
              Add Role
            </button>
          </div>

          <table class="min-w-full divide-y divide-gray-200">
            <thead>
              <tr>
                <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">No.</th>
                <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Name</th>
                <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Permission</th>
                <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="row in roles.data" :key="row.id">
                <td class="px-6 py-4 whitespace-nowrap">{{ row.id }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ row.name }}</td>
                <td class="px-6 py-4">
                  <div v-html="getPerm(row.permissions)" class="flex flex-wrap gap-1"></div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <a href="#" @click="edit(row)" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                  <a href="#" @click="deleteRow(row)" class="text-red-600 hover:text-red-900">Delete</a>
                </td>
              </tr>
            </tbody>
          </table>

          <div ref="isOpen" class="fixed z-10 inset-0 overflow-y-auto ease-out duration-400" v-if="isOpen">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
              <div class="fixed inset-0 transition-opacity">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
              </div>
              ​
              <div
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-5xl sm:w-full"
                role="dialog" aria-modal="true" aria-labelledby="modal-headline">
                <nav class="flex max-w-72 gap-1">
                  <button
                  type="button"
                    @click="activeTab = 'data'"
                    :class="[
                      activeTab === 'data'
                        ? 'border-b-2  border-indigo-500 text-indigo-600'
                        : 'border-b border-gray-200 text-gray-500 hover:border-gray-300 hover:text-gray-700',
                   'bg-white   w-1/2 py-4 px-1 text-center border-0 border-b-2 font-medium text-sm focus:ring-0 focus:outline-none'
                    ]"
                  >
                    Role Information
                  </button>
                  <button
                   type="button"
                    @click="activeTab = 'permission'"
                    :class="[
                      activeTab === 'permission'
                      ? 'border-b-2  border-indigo-500 text-indigo-600'
                        : 'border-b border-gray-200 text-gray-500 hover:border-gray-300 hover:text-gray-700',
                      'bg-white  w-1/2 py-4 px-1 text-center border-0 border-b-2 font-medium text-sm focus:ring-0 focus:outline-none'
                    ]"
                  >
                    Data Edit Permission
                  </button>
                </nav>
                <form @submit.prevent="submit">
                  <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 min-h-screen">
                    <div class="">
                      <div v-if="activeTab === 'data'">
                        <div class="mb-4">
                          <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Name :</label>
                          <input type="text"
                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                            id="name" placeholder="Enter Role Name" v-model="form.name" />
                          <div v-if="$page.props.errors.name" class="text-red-500">{{ $page.props.errors.name[0] }}</div>
                        </div>
                        <div class="mb-4">
                          <fieldset class="mt-4 border border-solid border-gray-300 p-3 rounded-md">
                            <legend class="text-gray-700 text-sm font-bold"> <input
                              class="text-blue-500 text-sm w-6 h-6 mr-2 focus:ring-blue-400 focus:ring-opacity-25 border border-blue-300 rounded"
                              type="checkbox" v-model="form.admin_panel" />  Admin Panel </legend>

                            <div class="max-w-full text-sm flex">
                              <label class="inline-flex ml-2 text-sm">
                                <input
                                  class="text-gray-500 text-sm w-6 h-6 mr-2 focus:ring-gray-400 focus:ring-opacity-25 border border-gray-300 rounded"
                                  type="checkbox" v-model="form.activity_log" />
                                Enable Activity Log Access
                              </label>
                              <label class="inline-flex ml-2 text-sm">
                                <input
                                  class="text-gray-500 text-sm w-6 h-6 mr-2 focus:ring-gray-400 focus:ring-opacity-25 border border-gray-300 rounded"
                                  type="checkbox" v-model="form.system_setting" />
                              System Setting
                              </label>


                            </div>
                          </fieldset>
                        </div>
                        <div class="mb-4">
                          <fieldset class="mt-4 border border-solid border-gray-300 p-3 rounded-md">
                            <legend class="text-gray-700 text-sm font-bold"> <input
                              class="text-blue-500 text-sm w-6 h-6 mr-2 focus:ring-blue-400 focus:ring-opacity-25 border border-blue-300 rounded"
                              type="checkbox" v-model="form.customer_panel" />  Customer Data</legend>
                            <label class="inline-flex ml-2 text-sm">
                              <input
                                class="text-gray-500 text-sm w-6 h-6 mr-2 focus:ring-gray-400 focus:ring-opacity-25 border border-gray-300 rounded"
                                type="checkbox" v-model="form.read_customer" />
                              Read Only to Customer Data
                            </label>
                            <label class="inline-flex ml-2 text-sm">
                              <input
                                class="text-gray-500 text-sm w-6 h-6 mr-2 focus:ring-gray-400 focus:ring-opacity-25 border border-gray-300 rounded"
                                type="checkbox" v-model="form.delete_customer" />
                              Enable Delete Customer
                            </label>
                            <label class="inline-flex ml-2 text-sm">
                              <input
                                class="text-gray-500 text-sm w-6 h-6 mr-2 focus:ring-gray-400 focus:ring-opacity-25 border border-gray-300 rounded"
                                type="checkbox" v-model="form.enable_customer_export" />
                              Enable Export Data
                            </label>
                          
                          
                          
                          </fieldset>
                        </div>
                        <div class="mb-4">
                          <fieldset class="mt-4 border border-solid border-gray-300 p-3 rounded-md">
                            <legend class="text-gray-700 text-sm font-bold"><input
                              class="text-blue-500 text-sm w-6 h-6 mr-2 focus:ring-blue-400 focus:ring-opacity-25 border border-blue-300 rounded"
                              type="checkbox" v-model="form.incident_panel" />  Incident Control </legend>

                            <div class="max-w-full text-sm flex">

                              <label class="inline-flex ml-2">
                                <input
                                  class="text-indigo-500 w-6 h-6 mr-2 focus:ring-indigo-400 focus:ring-opacity-25 border border-gray-300 rounded"
                                  type="checkbox" v-model="form.read_incident" />
                                Incident Read Permission
                              </label>
                              <label class="inline-flex ml-2">
                                <input
                                  class="text-indigo-500 w-6 h-6 mr-2 focus:ring-indigo-400 focus:ring-opacity-25 border border-gray-300 rounded"
                                  type="checkbox" v-model="form.write_incident" />
                                Incident Write Permission
                              </label>
                              <label class="inline-flex ml-2">
                                <input
                                  class="text-indigo-500 w-6 h-6 mr-2 focus:ring-indigo-400 focus:ring-opacity-25 border border-gray-300 rounded"
                                  type="checkbox" v-model="form.incident_only" />
                                Incident Only Access Permission
                              </label>


                            </div>
                          </fieldset>
                        </div>
                        <div class="mb-4">
                          <fieldset class="mt-4 border border-solid border-gray-300 p-3 rounded-md">
                            <legend class="text-gray-700 text-sm font-bold"><input
                              class="text-blue-500 text-sm w-6 h-6 mr-2 focus:ring-blue-400 focus:ring-opacity-25 border border-blue-300 rounded"
                              type="checkbox" v-model="form.billing_panel" />  Billing Control </legend>

                            <div class="max-w-full text-sm flex">
                              <label class="inline-flex ml-2">
                                <input
                                  class="text-red-500 w-6 h-6 mr-2 focus:ring-red-400 focus:ring-opacity-25 border border-gray-300 rounded"
                                  type="checkbox" v-model="form.bill_generation" />
                                Bill Generate
                              </label>
                              <label class="inline-flex ml-2">
                                <input
                                  class="text-red-500 w-6 h-6 mr-2 focus:ring-red-400 focus:ring-opacity-25 border border-gray-300 rounded"
                                  type="checkbox" v-model="form.bill_receipt" />
                                Bill Receipt
                              </label>
                              <label class="inline-flex ml-2">
                                <input
                                  class="text-red-500 w-6 h-6 mr-2 focus:ring-red-400 focus:ring-opacity-25 border border-gray-300 rounded"
                                  type="checkbox" v-model="form.edit_invoice" />
                                Edit Invoice Permission
                              </label>
                              <label class="inline-flex ml-2">
                                <input
                                  class="text-red-500 w-6 h-6 mr-2 focus:ring-red-400 focus:ring-opacity-25 border border-gray-300 rounded"
                                  type="checkbox" v-model="form.delete_invoice" />
                                Delete Invoice Permission
                              </label>

                            </div>
                          </fieldset>
                        </div>
                       
                        <div class="mb-4">
                          <fieldset class="mt-4 border border-solid border-gray-300 p-3 rounded-md">
                            <legend class="text-gray-700 text-sm font-bold"><input
                              class="text-blue-500 text-sm w-6 h-6 mr-2 focus:ring-blue-400 focus:ring-opacity-25 border border-blue-300 rounded"
                              type="checkbox" v-model="form.report_panel" /> Report Control </legend>

                            <div class="max-w-full text-sm flex">
                              <label class="inline-flex ml-2">
                                <input
                                  class="text-red-500 w-6 h-6 mr-2 focus:ring-red-400 focus:ring-opacity-25 border border-gray-300 rounded"
                                  type="checkbox" v-model="form.incident_report" />
                                View Incident Report
                              </label>
                              <label class="inline-flex ml-2">
                                <input
                                  class="text-red-500 w-6 h-6 mr-2 focus:ring-red-400 focus:ring-opacity-25 border border-gray-300 rounded"
                                  type="checkbox" v-model="form.bill_report" />
                                View Bill Receipt Report
                              </label>
                              <label class="inline-flex ml-2">
                                <input
                                  class="text-red-500 w-6 h-6 mr-2 focus:ring-red-400 focus:ring-opacity-25 border border-gray-300 rounded"
                                  type="checkbox" v-model="form.radius_report" />
                                View Radius User Report
                              </label>
                              <label class="inline-flex ml-2">
                                <input
                                  class="text-red-500 w-6 h-6 mr-2 focus:ring-red-400 focus:ring-opacity-25 border border-gray-300 rounded"
                                  type="checkbox" v-model="form.ip_report" />
                                View IP Usage Report
                              </label>

                            </div>
                          </fieldset>
                        </div>
                        <div class="mb-4">
                          <fieldset class="mt-4 border border-solid border-gray-300 p-3 rounded-md">
                            <legend class="text-gray-700 text-sm font-bold">Customer Status </legend>
  
                            <div class="mt-1 flex rounded-md shadow-sm" v-if="customerStatus.length !== 0">
                              <multiselect deselect-label="Selected already" :options="customerStatus" track-by="id" label="name"
                                v-model="form.customer_status" :allow-empty="true" :multiple="true" :taggable="true">
                              </multiselect>
                            </div>
                          </fieldset>
                        </div>
                    </div>
                    <!--end of data -->
                    <div v-if="activeTab === 'permission'">
                      <div class="mb-4 ">
                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-4">Customer Permission</h4>
                      <!-- Customer Permission -->
                       <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div v-for="(permission, index) in availablePermission" :key="index" class="flex items-center">
                          <jet-checkbox 
                            :id="`permission_${index}`" 
                            :value="permission.value"
                            v-model:checked="form.permissions" 
                          />
                          <jet-label :for="`permission_${index}`" :value="permission.label" class="ml-2" />
                        </div>
                      </div>
                    </div>
                    </div>
                  </div>
                  </div>
                  <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto">
                      <button wire:click.prevent="submit" type="submit"
                        class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition"
                        v-show="!editMode">Save</button>
                    </span>
                    <span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto">
                      <button wire:click.prevent="submit" type="submit"
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
        </div>
        <span v-if="roles.links">
          <pagination class="mt-6" :links="roles.links" />
        </span>
      </div>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import Pagination from "@/Components/Pagination";
import Multiselect from "@suadelabs/vue3-multiselect";
import { reactive, ref, onMounted, computed} from "vue";
import { router } from '@inertiajs/vue3';
import JetCheckbox from '@/Jetstream/Checkbox.vue';
import JetLabel from '@/Jetstream/InputLabel.vue';
export default {
  name: "Role",
  components: {
    AppLayout,
    Pagination,
    Multiselect,
    JetCheckbox,
    JetLabel
  },
  props: {
    roles: Object,
    col: {
      type: Array,
      required: true
    },
    customerStatus:Object,
    menus: Object,
    errors: Object,
  },
  setup(props) {
    const activeTab = ref('data');
    const form = reactive({
      id: null,
      name: null,
      permissions: [],
      delete_customer: null,
      read_customer: null,
      read_incident: null,
      write_incident: null,
      edit_invoice: null,
      bill_generation: null,
      bill_receipt: null,
      radius_read: null,
      radius_write: null,
      incident_report: null,
      bill_report: null,
      radius_report: null,
      incident_only: null,
      read_only_ip: null,
      add_ip: null,
      edit_ip: null,
      delete_ip: null,
      ip_report: null,
      enable_customer_export: null,
      activity_log: null,
      system_setting: null,
      admin_panel : null,
      customer_panel : null,
      incident_panel : null,
      billing_panel : null,
      report_panel : null,
      customer_status:null,
    });
    const search = ref("");
    let editMode = ref(false);
    let isOpen = ref(false);

    function resetForm() {
      form.name = null;
      form.permissions = [];
      form.read_customer = null;
      form.delete_customer = null;
      form.read_incident = null;
      form.write_incident = null;
      form.edit_invoice = null;
      form.delete_invoice = null;
      form.bill_generation = null;
      form.bill_receipt = null;
      form.radius_read = null;
      form.radius_write = null;
      form.incident_report = null;
      form.bill_report = null;
      form.radius_report = null;
      form.incident_only = null;
      form.read_only_ip = null;
      form.add_ip = null;
      form.edit_ip = null;
      form.delete_ip = null;
      form.ip_report = null;
      form.enable_customer_export = null;
      form.activity_log = null;
      form.system_setting = null;
      form.admin_panel = null;
      form.customer_panel = null;
      form.incident_panel = null;
      form.billing_panel = null;
      form.report_panel = null;
      form.customer_status = null;
    }
    function submit() {
      if (!editMode.value) {
        form._method = "POST";
        router.post("/role", form, {
          preserveState: true,
          onSuccess: (page) => {
            closeModal();
            resetForm();
            Toast.fire({
              icon: "success",
              title: page.props.flash.message,
            });
          },
          onError: (errors) => {
            closeModal();
            console.log("error ..".errors);
          },
        });
      } else {
        form._method = "PUT";
        router.post("/role/" + form.id, form, {
          onSuccess: (page) => {
            closeModal();
            resetForm();
            Toast.fire({
              icon: "success",
              title: page.props.flash.message,
            });
          },

          onError: (errors) => {
            closeModal();
            console.log("error ..".errors);
          },
        });
      }
    }
    function edit(data) {
      form.id = data.id;
      form.name = data.name;
     
      form.permissions =  data.permissions || [],
      form.read_customer = (data.read_customer) ? true : false;
      form.delete_customer = (data.delete_customer) ? true : false;
      form.read_incident = (data.read_incident) ? true : false;
      form.write_incident = (data.write_incident) ? true : false;
      form.edit_invoice = (data.edit_invoice) ? true : false;
      form.delete_invoice = (data.delete_invoice) ? true : false;
      form.bill_generation = (data.bill_generation) ? true : false;
      form.bill_receipt = (data.bill_receipt) ? true : false;
      form.radius_read = (data.radius_read) ? true : false;
      form.radius_write = (data.radius_write) ? true : false;
      form.incident_report = (data.incident_report) ? true : false;
      form.bill_report = (data.bill_report) ? true : false;
      form.radius_report = (data.radius_report) ? true : false;
      form.incident_only = (data.incident_only) ? true : false;
      form.read_only_ip = (data.read_only_ip) ? true : false;
      form.add_ip = (data.add_ip) ? true : false;
      form.edit_ip = (data.edit_ip) ? true : false;
      form.delete_ip = (data.delete_ip) ? true : false;
      form.ip_report = (data.ip_report) ? true : false;
      form.enable_customer_export = (data.enable_customer_export) ? true : false;
      form.activity_log = (data.activity_log) ? true : false;
      form.system_setting = (data.system_setting) ? true : false;
      form.admin_panel = (data.admin_panel)? true : false;
      form.customer_panel = (data.customer_panel)? true : false;
      form.incident_panel = (data.incident_panel)? true : false;
      form.billing_panel = (data.billing_panel)? true : false;
      form.report_panel = (data.report_panel)? true : false;
      form.customer_status = data.customer_status;
      editMode.value = true;
      openModal();
    }

    function deleteRow(data) {
      if (!confirm("Are you sure want to remove?")) return;
      data._method = "DELETE";
      router.post("/role/" + data.id, data, {
        preserveState: true,
        onSuccess: (page) => {
          closeModal();
          resetForm();
          Toast.fire({
            icon: "success",
            title: page.props.flash.message,
          });
        },
        onError: (errors) => {
          closeModal();
          console.log("error ..".errors);
        },
      });
      closeModal();
      resetForm();
    }
    function openModal() {
      isOpen.value = true;
    }
    function getPerm(d) {
      let perm_array = "";
      let perm = "";
      let count = 0;
      if (d) {
      
        d.forEach((e) => {
          count++;
          if (count % 6 === 0) {
            perm += '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">' + e + "</span><br />";
          } else {
            perm += '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">' + e + "</span>";
          }
        });
      }
      return perm;
    }
    const availablePermission = computed(() => {
      return props.col.map(column => ({
        value: column.name,
        label: column.name.replace('_', ' ').toUpperCase()
      }));
    });
    const closeModal = () => {
      isOpen.value = false;
      resetForm();
      editMode.value = false;
      activeTab.value = 'data';
    };
    const searchTsp = () => {
      console.log("search value is" + search.value);
      router.get("/role/", { role: search.value }, { preserveState: true });
    };

    onMounted(() => {
      //   form.permission = props.col.filter((d) => d.name == 1)[0],
      props.col.map(function (x) {
        return (x.col_data = "<label :class='capitalize'>" + x.name + "</label>");
      });

    });
    return { form, submit, getPerm, editMode, isOpen, openModal, closeModal, edit, deleteRow, searchTsp, search,activeTab ,availablePermission};
  },
};
</script>
