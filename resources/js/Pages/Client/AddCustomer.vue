<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">Customer Details</h2>
    </template>
    <div class="py-2">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="mt-5 md:mt-0">
          <form @submit.prevent="submit">
            <div class="shadow sm:rounded-md sm:overflow-hidden">
              <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
                <h6 class="md:min-w-full text-indigo-700 text-xs uppercase font-bold block pt-1 no-underline">
                  Customer
                  Basic Information</h6>
                <div class="grid grid-cols-1 sm:grid-cols-4 gap-2">
                  <div class="col-span-1 sm:col-span-1">
                    <label for="name" class="block text-sm font-medium text-gray-700"><span
                        class="text-red-500">*</span>
                      Customer Name </label>
                    <div class="mt-1 flex rounded-md shadow-sm">
                      <span
                        class="z-10 leading-snug font-normal text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
                        <i class="fas fa-user"></i>
                      </span>
                      <input type="text" v-model="form.name" name="name" id="name"
                        class="pl-10 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"
                        placeholder="Customer Name" required :disabled="checkPerm('name')" />
                    </div>
                    <p v-show="$page.props.errors.name" class="mt-2 text-sm text-red-500">{{ $page.props.errors.name
                      }}
                    </p>
                  </div>
                  <div class="col-span-1 sm:col-span-1">
                    <label for="city" class="block text-sm font-medium text-gray-700"><span
                        class="text-red-500">*</span>
                      City </label>
                    <div class="mt-1 flex rounded-md shadow-sm" v-if="cities.length !== 0">
                      <multiselect deselect-label="Selected already" :options="cities" track-by="id" label="name"
                        v-model="form.city" placeholder="Select City" :allow-empty="false" 
                        @update:modelValue="form.city_id = $event?.id"
                       
                       required>
                      </multiselect>
                    </div>
                    <p v-show="$page.props.errors.city_id" class="mt-2 text-sm text-red-500">{{
                      $page.props.errors.city_id }}</p>
                  </div>
                  <div class="col-span-1 sm:col-span-2">
                    <label for="township" class="block text-sm font-medium text-gray-700"><span
                        class="text-red-500">*</span>
                      Township </label>
                    <div class="mt-1 flex rounded-md shadow-sm" v-if="filteredTownships?.length !== 0">
                      <multiselect deselect-label="Selected already" :options="filteredTownships" track-by="id" label="name"
                        v-model="form.township" placeholder="Select Township" :allow-empty="false" 
                        @update:modelValue="form.township_id = $event?.id"
                       required>
                      </multiselect>
                    </div>
                    
                      <div class="mt-1 border-gray-200 border p-2 rounded-md text-gray-600" v-else>Please Select City First</div>
                    
                    <p v-show="$page.props.errors.township_id" class="mt-2 text-sm text-red-500">{{
                      $page.props.errors.township_id }}</p>
                  </div>
                  <div class="col-span-1 sm:col-span-2">
                    <label for="phone_1" class="block text-sm font-medium text-gray-700"><span
                        class="text-red-500">*</span>
                      Phone No. (e.g. 09-123/09-456)</label>
                    <div class="mt-1 flex rounded-md shadow-sm">
                      <span
                        class="z-10 leading-snug font-normal absolute text-center text-gray-300 bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
                        <i class="fas fa-phone"></i>
                      </span>
                      <input type="text" v-model="form.phone_1" name="phone_1" id="phone_1"
                        class="pl-10 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"
                        placeholder="e.g 0945000111" required :disabled="checkPerm('phone_1')" />
                    </div>
                    <p v-show="$page.props.errors.phone_1" class="mt-2 text-sm text-red-500">{{
                      $page.props.errors.phone_1 }}</p>
                  </div>
                

                  
                  <div class="col-span-1 sm:col-span-1">
                    <label for="latitude" class="block text-sm font-medium text-gray-700">Latitude </label>
                    <div class="mt-1 flex rounded-md shadow-sm">
                      <span
                        class="z-10 leading-snug font-normal text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-3">
                        <i class="fas fa-location-arrow"></i>
                      </span>
                      <input type="text" v-model="form.latitude" name="latitude" id="latitude"
                        class="pl-10 mt-1 form-input focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"
                        v-on:keypress="isNumber(event)"  />
                    </div>
                    <p v-show="$page.props.errors.latitude" class="mt-2 text-sm text-red-500">{{
                      $page.props.errors.latitude }}</p>
                  </div>
                  <div class="col-span-1 sm:col-span-1">
                    <label for="longitude" class="block text-sm font-medium text-gray-700">Longitude </label>
                    <div class="mt-1 flex rounded-md shadow-sm">
                      <span
                        class="z-10 leading-snug font-normal  text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-3">
                        <i class="fas fa-location-arrow"></i>
                      </span>
                      <input type="text" v-model="form.longitude" name="longitude" id="longitude"
                        class="pl-10 mt-1 form-input focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"
                        v-on:keypress="isNumber(event)"  />
                    </div>
                    <p v-show="$page.props.errors.longitude" class="mt-2 text-sm text-red-500">{{
                      $page.props.errors.longitude }}</p>
                  </div>
                  
                  <div class="col-span-1 sm:col-span-2">
                    <label for="address" class="block text-sm font-medium text-gray-700"><span
                        class="text-red-500">*</span>
                      Full Address </label>
                    <div class="mt-1 flex rounded-md shadow-sm">
                      <span
                        class="z-10 leading-snug font-normal absolute text-center text-gray-300 bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
                        <i class="fas fa-map-marker-alt"></i>
                      </span>
                      <textarea v-model="form.address" name="address" id="address"
                        class="pl-10 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"
                      />
                    </div>
                    <p v-show="$page.props.errors.address" class="mt-2 text-sm text-red-500">{{
                      $page.props.errors.address }}</p>
                  </div>
                </div>
                <hr class="my-4 md:min-w-full" />
                <h6 class="md:min-w-full text-indigo-700 text-xs uppercase font-bold block pt-1 no-underline">
                  Order
                  Information</h6>
                <div class="grid grid-cols-1 sm:grid-cols-4 gap-2">
                  <div class="col-span-1 sm:col-span-1 w-full">
                    <label for="isp_ftth_id" class="block text-sm font-medium text-gray-700"><span
                        class="text-red-500">*</span>
                      Customer ID </label>
                    <div class="mt-1 flex rounded-md shadow-sm">
                      <span
                        class="z-10 leading-snug font-normal text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
                        <i class="fas fa-id-badge"></i>
                      </span>
                      <input v-model="form.isp_ftth_id" type="text" name="isp_ftth_id" id="isp_ftth_id"
                        class="pl-10 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"
                        required :disabled="checkPerm('isp_ftth_id')" />
                    </div>
                    <p v-show="$page.props.errors.isp_ftth_id" class="mt-2 text-sm text-red-500">{{
                      $page.props.errors.isp_ftth_id }}</p>
                  </div>
                  <div class="col-span-1 sm:col-span-1">
                    <label for="order_date" class="block text-sm font-medium text-gray-700"><span
                        class="text-red-500">*</span> Order Date </label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                          <input type="date" 
                                 v-model="form.order_date" 
                                 name="order_date" 
                                 id="order_date"
                                 :min="new Date().toISOString().split('T')[0]"
                                 class="focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"
                                 required 
                                 :disabled="checkPerm('order_date')" />
                        </div>
                    <p v-show="$page.props.errors.order_date" class="mt-2 text-sm text-red-500">{{
                      $page.props.errors.order_date }}</p>
                  </div>
                  <div class="col-span-1 sm:col-span-1">
                    <label for="prefer_install_date" class="block text-sm font-medium text-gray-700"><span
                        class="text-red-500">*</span> Prefer
                      Installation Date </label>
                    <div class="mt-1 flex rounded-md shadow-sm">
                      <span
                        class="z-10 leading-snug font-normal text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
                        <i class="fas fa-tools"></i>
                      </span>
                      <input v-model="form.prefer_install_date" type="date" name="prefer_install_date"
                        id="prefer_install_date"
                        :min="new Date().toISOString().split('T')[0]"
                        class="pl-10 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"
                        :disabled="checkPerm('prefer_install_date')" />
                    </div>
                  </div>
                  <div class="col-span-1 sm:col-span-1">
                    <label for="status" class="block text-sm font-medium text-gray-700"><span
                        class="text-red-500">*</span>
                      Customer Status </label>
                    <div class="mt-1 flex rounded-md shadow-sm" v-if="status_list.length !== 0">
                      <multiselect deselect-label="Selected already" :options="status_list" track-by="id" label="name"
                        v-model="form.status" :allow-empty="false" disabled>
                      </multiselect>
                    </div>
                    <p v-show="$page.props.errors.status" class="mt-2 text-sm text-red-500">{{
                      $page.props.errors.status
                    }}</p>
                  </div>
                  <div class="col-span-1 sm:col-span-1">
                    <label for="bandwidth" class="block text-sm font-medium text-gray-700"><span
                        class="text-red-500">*</span>
                      Bandwith </label>
                    <div class="mt-1 flex rounded-md shadow-sm" >
                      <input type="number" v-model="form.bandwidth" name="bandwidth"
                      id="bandwidth"
                      class="focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-none rounded-l-md sm:text-sm border-gray-300"
                      placeholder="20, 30 e.g. " v-on:keypress="isNumber($event)"
                       :disabled="checkPerm('bandwidth')"
                      required />
                  <span
                      class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                      Mbps </span>
                    </div>
                    <p v-show="$page.props.errors.bandwidth" class="mt-2 text-sm text-red-500">{{
                      $page.props.errors.bandwidth }}</p>
                  </div>
               
                  <div class="col-span-1 sm:col-span-1">
                    <label for="service_type" class="block text-sm font-medium text-gray-700"><span
                        class="text-red-500">*</span>
                      Service Type </label>
                    <div class="mt-1 flex rounded-md shadow-sm">
                      <select v-model="form.service_type" name="service_type" id="service_type"
                        class="focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"
                        :disabled="checkPerm('service_type')">
                        <option value="FTTH">FTTH (Default)</option>
                        <option value="DIA">DIA</option>
                        <option value="IPLC">IPLC</option>
                        <option value="DPLC">DPLC</option>
                      </select>
                    </div>
                    <p v-show="$page.props.errors.service_type" class="mt-2 text-sm text-red-500">{{
                      $page.props.errors.service_type }}</p>
                  </div>
                  <template v-if="form.service_type=='FTTH'"> 
                     <div class="col-span-1 sm:col-span-2">
                    <label for="installation_service_id" class="block text-sm font-medium text-gray-700"><span
                        class="text-red-500">*</span>
                      Installation </label>
                    <div class="mt-1 flex rounded-md shadow-sm" v-if="installationServices">
                      <multiselect deselect-label="Selected already" :options="installationServices" track-by="id" label="name"
                        v-model="form.installation_service" :allow-empty="false" 
                        @update:modelValue="form.installation_service_id = $event?.id"
                        :disabled="checkPerm('installation_service_id')">
                      </multiselect>
                    </div>
                    <p v-show="$page.props.errors.installation_service_id" class="mt-2 text-sm text-red-500">{{
                      $page.props.errors.installation_service_id }}</p>
                  </div>
                 
                  <div class="col-span-1 md:col-span-2">
                    <label for="bundle" class="block text-sm font-medium text-gray-700">
                      Additional Materials Request (Leave blank for own materials)
                    </label>
                    <div class="mt-1 flex rounded-md shadow-sm" v-if="bundle_equiptments.length !== 0">
                      <multiselect deselect-label="Selected already" :options="bundle_equiptments" track-by="id"
                        label="name" v-model="form.bundles" :allow-empty="true" :disabled="checkPerm('bundle')"
                        :multiple="true" :taggable="false"></multiselect>
                    </div>
                    <p v-show="$page.props.errors.bundles" class="mt-2 text-sm text-red-500">{{
                      $page.props.errors.bundles }}</p>
                  </div>
                  </template>
                  <div class="col-span-1 sm:col-span-2">
                    <label for="package" class="block text-sm font-medium text-gray-700"><span
                        class="text-red-500">*</span>
                      Maintenance </label>
                    <div class="mt-1 flex rounded-md shadow-sm " v-if="maintenanceServices">
                      <multiselect deselect-label="Selected already" :options="maintenanceServices" track-by="id" label="name"
                        v-model="form.maintenance_service" :allow-empty="false"
                        @update:modelValue="form.maintenance_service_id = $event?.id"
                        :disabled="checkPerm('maintenance_service_id')" :class="text-xs">
                      </multiselect>
                    </div>
                    <p v-show="$page.props.errors.maintenance_service_id" class="mt-2 text-sm text-red-500">{{
                      $page.props.errors.maintenance_service_id }}</p>
                  </div>
                 
               
                 
                 <div class="col-span-1 sm:col-span-1">
                          <label for="pppoe_username" class="block text-sm font-medium text-gray-700"> PPPOE
                            Account</label>
                          <div class="mt-1 flex rounded-md shadow-sm">
                            <span
                              class="z-10 leading-snug font-normal text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
                              <i class="fas fa-tools"></i>
                            </span>
                            <input v-model="form.pppoe_username" type="text" name="pppoe_username" id="pppoe_username"
                              class="pl-10 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"
                              placeholder="Enter PPPOE Account" :disabled="checkPerm('pppoe_username')" />
                            <p v-show="$page.props.errors.pppoe_username" class="mt-2 text-sm text-red-500">{{
                              $page.props.errors.pppoe_username }}</p>
                          </div>
                        </div>
                        <div class="col-span-1 sm:col-span-1">
                          <label for="pppoe_password" class="block text-sm font-medium text-gray-700"> PPPOE
                            Password</label>
                          <div class="mt-1 flex rounded-md shadow-sm">
                            <span
                              class="z-10 leading-snug font-normal text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
                              <i class="fas fa-tools"></i>
                            </span>
                            <input v-model="form.pppoe_password" type="text" name="pppoe_password" id="pppoe_password"
                              class="pl-10 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"
                              placeholder="Enter PPPOE Password" :disabled="checkPerm('pppoe_password')" />
                            <p v-show="$page.props.errors.pppoe_password" class="mt-2 text-sm text-red-500">{{
                              $page.props.errors.pppoe_password }}</p>
                          </div>
                        </div>

                  
                  <div class="col-span-1 sm:col-span-2">
                    <label for="order_remark" class="block text-sm font-medium text-gray-700"> Order
                      Remark </label>
                    <div class="mt-1 flex rounded-md shadow-sm">
                      <span
                        class="z-10 leading-snug font-normal text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
                        <i class="fas fa-comment"></i>
                      </span>
                      <textarea name="order_remark" v-model="form.order_remark" id="order_remark"
                        class="pl-10 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"
                        :disabled="checkPerm('order_remark')" />
                    </div>
                    <p v-show="$page.props.errors.order_remark" class="mt-2 text-sm text-red-500">{{
                      $page.props.errors.order_remark }}</p>
                  </div>
                  
                  <div class="col-span-1 sm:col-span-1" v-if="user.user_type=='internal'">
                    <label for="isp" class="block text-sm font-medium text-gray-700"> ISP Assignment</label>
                    <div class="mt-1 flex rounded-md shadow-sm" v-if="isps.length !== 0">
                      <multiselect deselect-label="Selected already" :options="isps" track-by="id" label="name"
                        v-model="form.isp_id" :allow-empty="true" :disabled="checkPerm('isp_id')"></multiselect>
                    </div>
                    <p v-show="$page.props.errors.isp_id" class="mt-2 text-sm text-red-500">{{
                      $page.props.errors.isp_id
                    }}</p>
                  </div>
               
                </div>
                
              </div>
              <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                <Link :href="route('customer.index')"
                  class="inline-flex justify-center py-2 px-4 border border-transparent  text-sm font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700 shadow-sm focus:outline-none focus:border-blue-300 focus:shadow-outline-blue transition ease-in-out duration-150">Back</Link>

                <button @click="resetForm" type="button"
                  class="ml-2 inline-flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-gray-600 hover:bg-gray-700 shadow-sm focus:outline-none focus:border-blue-300 focus:shadow-outline-blue transition ease-in-out duration-150">Reset</button>

                <button wire:click.prevent="submit" type="submit"
                  class="ml-2 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Save</button>
              </div>
            </div>
          </form>
        </div>
        <!-- end of mt-5 md: -->
      </div>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import Multiselect from "@suadelabs/vue3-multiselect";
import { reactive, ref, onMounted, watch } from "vue";
import { router,Link,useForm } from '@inertiajs/vue3';
export default {
  name: "AddCustomer",
  components: {
    AppLayout,
    Multiselect,
    Link
  },
  props: {
    packages: Object,
    projects: Object,
    status_list: Object,
    subcoms: Object,
    dn: Object,
    pops: Object,
    max_id: Array,
    errors: Object,
    user: Object,
    bundle_equiptments: Object,
    partners:Object,
    isps:Object,
    userPerm:Array,
    installationServices: Object,
    portSharingServices: Object,
    maintenanceServices: Object,
    cities: Object,
  },
  setup(props) {

    let res_packages = ref("");
    const filteredTownships = ref([]);

    const form = useForm({
      id: null,
      name: "",
      phone_1: "",
      city: "",
      city_id: "",
      address: "",
      latitude: "",
      longitude: "",
      order_date: "",
      installation_date: "",
      isp_ftth_id: "",
      package: "",
      status: props.status_list.find((item) => item.id == 1),
      township: "",
      prefer_install_date: "",
      order_remark:"",
      isp_id: "",
      installation_service: "",
      installation_service_id: "",
      maintenance_service: "",
      maintenance_service_id: "",
      bandwidth: "",
      bundles: "",
      township_id: "",
      service_type: "FTTH",
      pppoe_username: "",
      pppoe_password: "",
    });

    function resetForm() {
      form.reset();
    }

   
     const getTownshipByCityId = async (cityId) => {
      if (!cityId) {
        filteredTownships.value = [];
        form.township = '';
        return;
      }
      try {
        const response = await fetch(`/getTownshipByCityId/${cityId}`);
        const data = await response.json();
        filteredTownships.value = data || [];
        console.log('fetch Township Data');
      } catch (error) {
        console.error("Failed to fetch Township Data", error);
      }
    };
    watch(
      () => form.city,
      (newCity) => {
        getTownshipByCityId(newCity?.id);
        form.township = ''; // Reset township when city changes
      }
    );

    function submit() {
      form._method = "POST";
      router.post("/customer", form, {
        preserveState: true,
        onSuccess: (page) => {
          console.log("success.. ");
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
    function isNumber(evt) {
      evt = evt ? evt : window.event;
      var charCode = evt.which ? evt.which : evt.keyCode;
      if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode !== 46) {
        evt.preventDefault();
      } else {
        return true;
      }
    }
    function checkPerm(data) {
      const my_role = props.user?.role;
        if(my_role){
          if (my_role?.read_customer) {
            return true;
          }
        }
      return !props.userPerm.includes(data);
    }

  
 

   
    onMounted(() => {
     // form.township = props.townships.filter((d) => d.id == 1)[0];
      //form.status = props.status_list[0];
  

    });
    return { form, submit, resetForm, isNumber, checkPerm, res_packages, filteredTownships, getTownshipByCityId };
  },
};
</script>
<style>
input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

/* Firefox */
input[type="number"] {
  -moz-appearance: textfield;
}

/* .multiselect__input, .multiselect__single{
    outline:none !important;
    font-size: 0.875rem !important;
    line-height: 1.25rem !important;
    margin-bottom: 2px !important;
    margin-top: 2px !important;
}
.multiselect__input select{
    font-size: 0.875rem !important;
}
.multiselect__input:focus{
    outline-offset: 0;
    padding-top:0px;
} */
</style>
