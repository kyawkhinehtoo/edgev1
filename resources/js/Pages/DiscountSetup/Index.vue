<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">Discount Setup</h2>
    </template>
    <div class="py-2">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-4">

          <div class="col-span-1">
            <span
              class="z-10 leading-snug font-normal text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-3">
              <i class="fas fa-search"></i>
            </span>
            <input type="text" placeholder="Search here..."
              class="form-input border-0 px-3 py-3 placeholder-gray-300 text-gray-600 relative bg-white rounded text-sm shadow outline-none focus:outline-none focus:ring pl-10"
              id="search" v-model="filter.general" @keyup.enter="searchDiscount" />
          </div>

          </div>
           <div class="grid grid-cols-1 sm:grid-cols-3 items-center justify-between mb-4 gap-2">
          <div class="col-span-1 w-full grid grid-rows-2 gap-2">
            <div class="w-full items-center justify-between flex">
                <span
              class="z-10 leading-snug font-normal text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-3">
              <i class="fas fa-search"></i>
            </span>
            <select v-model="filter.isp_id" @change="searchDiscount"
              class="form-input border-0 px-3 py-3 placeholder-gray-300 text-gray-600 relative bg-white rounded text-sm shadow outline-none focus:outline-none focus:ring pl-10 w-full">
              <option value="">All ISPs</option>
              <option v-for="isp in isps" :key="isp.id" :value="isp.id">{{ isp.name }}</option>
            </select>
            </div>  
            <div class="w-full items-center justify-between flex">
               <span
              class="z-10 leading-snug font-normal text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-3">
              <i class="fas fa-search"></i>
            </span>
            <select v-model="filter.port_sharing_service_id" @change="searchDiscount"
              class="form-input border-0 px-3 py-3 placeholder-gray-300 text-gray-600 relative bg-white rounded text-sm shadow outline-none focus:outline-none focus:ring pl-10 w-full">
              <option value="">All Plan</option>
              <option v-for="service in port_sharing_services" :key="service.id" :value="service.id">{{ service.name }}
              </option>
            </select>
            </div>
          </div>


          <div class="col-span-1 w-full grid grid-rows-2 gap-2">
            <div class="w-full flex">
                   <span
              class="z-10 leading-snug font-normal text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-3">
              <i class="fa fa-less-than-equal"></i></span>

            <input type="date" v-model="filter.start_date" @change="searchDiscount"
              class="form-input border-0 px-3 py-3 placeholder-gray-300 text-gray-600 relative bg-white rounded text-sm shadow outline-none focus:outline-none focus:ring pl-10 w-full"
              placeholder="Start Date" />
            </div>
             <div class="w-full items-center justify-between flex">
              <span
              class="z-10 leading-snug font-normal text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-3">
              <i class="fa fa-greater-than-equal"></i></span>
            <input type="date" v-model="filter.end_date" @change="searchDiscount"
              class="form-input border-0 px-3 py-3 placeholder-gray-300 text-gray-600 relative bg-white rounded text-sm shadow outline-none focus:outline-none focus:ring pl-10 w-full"
              placeholder="End Date" />
             </div>
          </div>

          <div class="col-span-1 w-full grid grid-rows-2 gap-2 self-start">
            <div>
              <span
              class="z-10 leading-snug font-normal text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-3">
              <i class="fas fa-search"></i>
            </span>
            <select v-model="filter.is_active" @change="searchDiscount"
              class="form-input border-0 px-3 py-3 placeholder-gray-300 text-gray-600 relative bg-white rounded text-sm shadow outline-none focus:outline-none focus:ring pl-10 w-full">
              <option value="">All Status</option>
              <option value="true">Active</option>
              <option value="false">Inactive</option>
            </select>
            </div>
            
        
             <div class="col-span-1">
            <button @click="openModal"
              class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition ">
              Create
            </button>
              </div>
          </div>

        </div>
       
        <div class="bg-white overflow-auto shadow-xl sm:rounded-lg" v-if="discounts.data">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  No.</th>
                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Name</th>
                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Service</th>
                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  ISP</th>
                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Start Date</th>
                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  End Date</th>
                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Rate Type</th>
                <!-- <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Discount %</th> -->
                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Active</th>
                <th class="relative px-6 py-3"><span class="sr-only">Action</span></th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 text-sm">
              <tr v-for="(discount, index) in discounts.data" :key="discount.id"
                :class="{ 'text-gray-400': !discount.is_active }">
                <td class="px-3 py-3 text-left text-md font-medium whitespace-nowrap">
                  {{ discounts.from + index }}</td>
                <td class="px-3 py-3 text-left text-md font-medium whitespace-nowrap">
                  {{ discount.name }}</td>
                <td class="px-3 py-3 text-left text-md font-medium whitespace-nowrap">
                  {{ discount.port_sharing_service?.name }}</td>
                <td class="px-3 py-3 text-left text-md font-medium whitespace-nowrap">
                  {{ discount.isp?.short_code }}</td>
                <td class="px-3 py-3 text-left text-md font-medium whitespace-nowrap">
                  {{ discount.start_date }}</td>
                <td class="px-3 py-3 text-left text-md font-medium whitespace-nowrap">
                  {{ discount.end_date }}</td>
                <td class="px-3 py-3 text-left text-md font-medium whitespace-nowrap">
                  {{ discount.rate_type }}</td>
                <!-- <td class="px-3 py-3 text-left text-md font-medium whitespace-nowrap">
                  {{ discount.discount_percentage }}</td> -->
                <td class="px-3 py-3 text-left text-md font-medium whitespace-nowrap">
                  {{ discount.is_active ? 'Yes' : 'No' }}</td>
                <td class="px-3 py-3 text-md font-medium whitespace-nowrap text-right">

                  <a href="#" @click="edit(discount)" class="text-yellow-600 hover:text-yellow-900">Edit</a>
                  |
                  <button @click="destroy(discount.id)" class="text-red-600 hover:text-red-900">Delete</button>
                </td>
              </tr>
            </tbody>
          </table>
          <Pagination class="mt-6" :links="discounts.links" />
        </div>
        <div ref="isOpen" class="fixed z-10 inset-0 overflow-y-auto ease-out duration-400" v-if="isOpen">
          <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity">
              <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            ​
            <div
              class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full"
              role="dialog" aria-modal="true" aria-labelledby="modal-headline">
              <form @submit.prevent="submit">
                <div class="shadow sm:rounded-md sm:overflow-hidden">
                  <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
                    <div class="col-span-1 sm:col-span-1">
                     
                      <div class="py-2">
                        <label for="name" class="block text-md font-medium text-gray-700">
                          Name</label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                          <input type="text" v-model="form.name" name="name" id="name"
                            class="focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"
                            placeholder="Name" required />
                        </div>
                       
                         <div class="py-2 text-red-600" v-if=" $page.props.errors?.name">
                          
                           {{ $page.props.errors.name }}
                        </div>
                      </div>
                      <div class="py-2">
                        <label for="port_sharing_service">Service Type</label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                        <multiselect 
                          v-model="form.port_sharing_service" 
                          :options="port_sharing_services"
                          :show-labels="false" 
                          :searchable="true"
                          :close-on-select="true"
                          :placeholder="'Select Service'"
                          label="name"
                          track-by="id"
                          class="w-full"
                          @select="(event) => {
                            form.port_sharing_service_id = event.id;
                            getFixRate(event.rate);
                          }"
                        >
                        </multiselect>
                        </div>
                      </div>
                      <div class="py-2">
                        <label for="type" class="block text-md font-medium text-gray-700">Discount
                          Type</label>
                        <div class="mt-2 flex items-center gap-2">
                          <label class="text-sm">Percentage%</label>
                          <label class="inline-flex relative cursor-pointer">
                            <input type="checkbox" class="sr-only peer" :checked="form.rate_type === 'fixed'"
                              @change="form.rate_type = form.rate_type === 'fixed' ? 'percentage' : 'fixed'">
                            <div class="w-14 h-8 p-1 shadow-sm bg-green-600 peer-focus:outline-none rounded-full peer
                                peer-checked:after:translate-x-6 peer-checked:after:border-white
                                after:content-[''] after:absolute after:top-[4px] after:left-[2px]
                                after:bg-white after:border-green-300 after:border after:rounded-full
                                after:h-6 after:w-6 after:transition-all
                                peer-checked:bg-indigo-600">
                            </div>
                          </label>
                          <label class="text-sm">Fixed Rate</label>

                        </div>
                      </div>

                      <div class="py-2" v-if="form.rate_type == 'fixed'">
                        <label for="rate" class="block text-gray-700 text-md font-bold">Service
                          Rate </label>
                        <div class="mb-4 ">
                          <div v-for="(rule, index) in form.fix_rate" :key="index" class="rule inline-flex items-center"
                            v-if="form.fix_rate && form.port_sharing_service">

                            <label class="block text-gray-700 text-sm font-bold">Ports
                            </label>
                            <div class="mt-1 flex rounded-md shadow-sm">
                              <input type="number" v-model="rule.min" placeholder="e.g., 10000"
                                class="focus:ring-indigo-500 focus:border-indigo-500 block w-full m-4 shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>
                            <label class="block text-gray-700 text-sm font-bold mr-2">Fees
                              :
                            </label>
                            <div class="mt-1 flex rounded-md shadow-sm">
                              <input type="number" v-model="rule.fees" placeholder="e.g., 25000"
                                class="focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-none rounded-l-md sm:text-sm border-gray-300">
                              <span
                                class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm uppercase">
                                MMK </span>
                            </div>
                            <button type="button" @click="removeRule(index)" class="m-2 btn"><i
                                class="fas fa-minus-circle text-yellow-600"></i></button>
                          </div>

                          <button type="button" @click="addRule" class="m-2 btn"><i
                              class="fas fa-plus-circle text-indigo-600"></i></button>
                        </div>
                      </div>
                      <div class="py-2" v-else>
                        <label for="discount_percentage" class="block text-md font-medium text-gray-700">Discount
                          Percentage
                        </label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                          <input type="text" v-model="form.discount_percentage" name="discount_percentage"
                            id="discount_percentage"
                            class="focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300 rounded-r-none border-r-0"
                            placeholder="e.g. 10%" required />
                          <span
                            class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm uppercase">
                            <i class="fa fas fa-percentage"></i> </span>
                        </div>
                      </div>
                      <div class="py-2">
                        <label for="discount_percentage" class="block text-md font-medium text-gray-700">ISP</label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                          <multiselect v-model="form.isp" :options="isps" :show-labels="false" :searchable="true"
                            :close-on-select="true" :placeholder="'Select ISP'" label="name" track-by="id"
                            class="w-full" @select="form.isp_id = $event.id">
                          </multiselect>
                        </div>
                      </div>
                      
                      <div class="py-2 grid grid-cols-2 gap-2">
                        <div>
                          <label for="start_date">Start Date</label>
                          <div class="mt-1 flex rounded-md shadow-sm">
                            <input type="date" v-model="form.start_date" name="start_date" id="start_date"
                              class="focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"
                              placeholder="Start Date" required />
                          </div>
                        </div>
                        <div>
                          <label for="end_date">End Date</label>
                          <div class="mt-1 flex rounded-md shadow-sm">
                            <input type="date" v-model="form.end_date" name="end_date" id="end_date"
                              class="focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"
                              placeholder="End Date" required />
                          </div>
                        </div>
                      </div>
                       <div class="py-2 text-red-600" v-if=" $attrs.errors?.conflict">
                        <i class="fa fa-warning fa-2x"></i>
                        {{ $attrs.errors?.conflict }}
                      </div>
                      <div class="py-2">
                        <label for="description" class="block text-md font-medium text-gray-700">Description</label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                          <textarea v-model="form.description" name="description" id="description"
                            class="focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"
                            placeholder="Description"></textarea>

                        </div>
                      </div>
                      <div class="py-2">
                        <label for="is_active" class="block text-md font-medium text-gray-700">Is Active ?</label>
                        <div class="mt-1 flex">

                          <input type="checkbox"
                            class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded mt-1"
                            name="is_active" v-model="form.is_active" value="true" />
                          <label class="flex-auto items-center ml-1" v-if="form.is_active == true">Active</label>
                          <label class="flex-auto items-center ml-1" v-if="form.is_active == false">Inactive</label>
                        </div>
                      </div>

                    </div>

                  </div>
                  <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                      <span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto">
                        <button type="submit"
                          class="inline-flex justify-center w-full px-4 py-2 bg-gray-800 border border-gray-300 rounded-md font-medium text-base leading-6 sm:text-sm text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150"
                          v-show="!editMode">Save</button>
                      </span>
                      <span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto">
                        <button type="submit"
                          class="inline-flex justify-center w-full px-4 py-2 bg-gray-800 border border-gray-300 rounded-md font-medium text-base leading-6 sm:text-sm text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150"
                          v-show="editMode">Update</button>
                      </span>
                      <span class="mt-3 flex w-full rounded-md shadow-sm sm:mt-0 sm:w-auto">
                        <button @click="closeModal" type="button"
                          class="inline-flex justify-center w-full rounded-md border border-gray-300 px-4 py-2 bg-white text-base leading-6 font-medium text-gray-700 shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue transition ease-in-out duration-150 sm:text-sm sm:leading-5">Cancel</button>
                      </span>
                    </div>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import {
  Link,
  router
} from '@inertiajs/vue3';
import {
  ref,
  reactive,
  watch
} from 'vue';
import Pagination from '@/Components/Pagination.vue';
import Multiselect from '@suadelabs/vue3-multiselect';

export default {
  name: 'DiscountSetupIndex',
  components: {
    AppLayout,
    Multiselect,
    Pagination
  },
  props: {
    discounts: Object,
    port_sharing_services: Object,
    isps: Object
  },
  setup(props) {
    const search = ref('');
    const rateRules = ref([{
      min: 10000,
      fees: 8000
    },
    {
      min: 10001,
      fees: 7500
    }
    ]);
    const editMode = ref(false);
    const isOpen = ref(false);
    const filter = reactive({
      isp_id: '',
      port_sharing_service_id: '',
      start_date: '',
      end_date: '',
      is_active: ''
    });
    const form = reactive({
      id: null,
      name: '',
      port_sharing_service: null,
      port_sharing_service_id: null,
      isp: null,
      isp_id: null,
      start_date: null,
      end_date: null,
      fix_rate: rateRules.value,
      discount_percentage: 0,
      description: null,
      created_by: null,
      rate_type: 'percentage',
      is_active: true
    });
    function resetForm() {
      form.id = null;
      form.name = '';
      form.port_sharing_service = null;
      form.port_sharing_service_id = null;
      form.isp = null;
      form.isp_id = null;
      form.start_date = null;
      form.end_date = null;
      form.fix_rate = rateRules.value;
      form.discount_percentage = 0;
      form.description = null;
      form.created_by = null;
      form.rate_type = 'percentage';
      form.is_active = true;
    }
    function edit(discount) {
      editMode.value = true;
      isOpen.value = true;
      form.id = discount.id;
      form.name = discount.name;
      form.port_sharing_service = discount.port_sharing_service;
      form.port_sharing_service_id = discount.port_sharing_service_id;
      form.isp = discount.isp;
      form.isp_id = discount.isp_id;
      form.start_date = discount.start_date;
      form.end_date = discount.end_date;
      form.fix_rate = (isJsonString(discount.fix_rate)) ? JSON.parse(discount.fix_rate) : rateRules.value;
      form.discount_percentage = discount.discount_percentage || 0;
      form.description = discount.description || '';
      form.created_by = discount.created_by || null;
      form.rate_type = discount.rate_type || 'percentage';
      form.is_active = discount.is_active ? true : false;
    }
    function searchDiscount() {
      router.get(route('discount-setup.index'), {
        general: filter.general,
        isp_id: filter.isp_id,
        port_sharing_service_id: filter.port_sharing_service_id,
        start_date: filter.start_date,
        end_date: filter.end_date,
        is_active: filter.is_active == 'true' ? 1 : filter.is_active == 'false' ? false : ''
      }, { preserveState: true });
    }

    function destroy(id) {
      if (confirm('Are you sure?')) {
        router.delete(route('discount-setup.destroy', id));
      }
    }

    function openModal() {
      isOpen.value = true;
    }

    function closeModal() {
      isOpen.value = false;
      editMode.value = false;
      resetForm();
    }

    function addRule() {
      form.fix_rate.push({
        min: '',
        fees: ''
      });
    }

    function removeRule(index) {
      form.fix_rate.splice(index, 1);
    }
    const parseRange = (range) => {
      const parts = range.split('-');
      if (parts.length === 1) {
        const value = parseInt(parts[0].replace('<', '').trim(), 10);
        return [0, value];
      } else {
        const min = parseInt(parts[0].trim(), 10);
        const max = parseInt(parts[1].trim(), 10);
        return [min, max];
      }
    };

    function isJsonString(str) {
      try {
        JSON.parse(str);
      } catch (e) {
        return false;
      }
      return true;
    }
    const getFixRate = (rate) => {
      console.log("form.port_sharing_service", rate);
      if (rate ) {
        form.fix_rate = (isJsonString(rate)) ? JSON.parse(rate) : rateRules.value;
      }
    }
    
    
    function submit() {
      if (!editMode.value) {
        form._method = "POST";
        router.post("/discount-setup", form, {
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

            console.log("error ..", errors);
          },
        });
      } else {
        form._method = "PUT";
        router.post("/discount-setup/" + form.id, form, {
          onSuccess: (page) => {
            closeModal();
            resetForm();
            Toast.fire({
              icon: "success",
              title: page.props.flash.message,
            });
          },
          onError: (errors) => {
            console.log("error ..", errors);
          },
        });
      }
    }
    return {
      search,
      rateRules,
      editMode,
      isOpen,
      form,
      filter,
      searchDiscount,
      destroy,
      openModal,
      closeModal,
      addRule,
      removeRule,
      edit,
      submit,
      getFixRate
    };
  }
};
</script>
