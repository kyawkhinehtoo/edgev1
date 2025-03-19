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
                    <label for="township" class="block text-sm font-medium text-gray-700"><span
                        class="text-red-500">*</span>
                      Township </label>
                    <div class="mt-1 flex rounded-md shadow-sm" v-if="townships.length !== 0">
                      <multiselect deselect-label="Selected already" :options="townships" track-by="id" label="name"
                        v-model="form.township" placeholder="Select Township" :allow-empty="false" 
                        :disabled="checkPerm('township_id')" required>
                      </multiselect>
                    </div>
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
                        v-on:keypress="isNumber(event)" :disabled="checkPerm('location')" />
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
                        v-on:keypress="isNumber(event)" :disabled="checkPerm('location')" />
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
                        required :disabled="checkPerm('address')" />
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
                    <label for="ftth_id" class="block text-sm font-medium text-gray-700"><span
                        class="text-red-500">*</span>
                      Customer ID </label>
                    <div class="mt-1 flex rounded-md shadow-sm">
                      <span
                        class="z-10 leading-snug font-normal text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
                        <i class="fas fa-id-badge"></i>
                      </span>
                      <input v-model="form.ftth_id" type="text" name="ftth_id" id="ftth_id"
                        class="pl-10 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"
                        required :disabled="checkPerm('ftth_id')" />
                    </div>
                    <p v-show="$page.props.errors.ftth_id" class="mt-2 text-sm text-red-500">{{
                      $page.props.errors.ftth_id }}</p>
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
                    <label for="package" class="block text-sm font-medium text-gray-700"><span
                        class="text-red-500">*</span>
                      Package </label>
                    <div class="mt-1 flex rounded-md shadow-sm" v-if="packages">
                      <multiselect deselect-label="Selected already" :options="packages" track-by="id" label="name"
                        v-model="form.package" :allow-empty="false" :disabled="checkPerm('package_id')">
                      </multiselect>
                    </div>
                    <p v-show="$page.props.errors.package" class="mt-2 text-sm text-red-500">{{
                      $page.props.errors.package }}</p>
                  </div>
                  <div class="col-span-1 sm:col-span-1">
                    <label class="block text-sm font-medium text-gray-700">
                      Installation Timeline
                    </label>
                    <div class="mt-1 p-2 space-x-2 inline-flex">
                      <div class="flex items-center">
                        <input type="radio" 
                               id="24hours" 
                               name="installation_timeline" 
                               value="24" 
                               v-model="form.installation_timeline"
                               class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300"
                               :disabled="checkPerm('installation_timeline')" />
                        <label for="24hours" class="ml-2 text-sm text-gray-700">24 Hours</label>
                      </div>
                      <div class="flex items-center">
                        <input type="radio" 
                               id="48hours" 
                               name="installation_timeline" 
                               value="48" 
                               v-model="form.installation_timeline"
                               class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300"
                               :disabled="checkPerm('installation_timeline')" />
                        <label for="48hours" class="ml-2 text-sm text-gray-700">48 Hours</label>
                      </div>
                    </div>
                    <p v-show="$page.props.errors.installation_timeline" class="mt-2 text-sm text-red-500">
                      {{ $page.props.errors.installation_timeline }}
                    </p>
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
                        v-model="form.status" :allow-empty="false" :disabled="checkPerm('status_id')">
                      </multiselect>
                    </div>
                    <p v-show="$page.props.errors.status" class="mt-2 text-sm text-red-500">{{
                      $page.props.errors.status
                    }}</p>
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
    townships: Object,
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
  },
  setup(props) {

    let pop_devices = ref("");
    let res_packages = ref("");
    let pppoe_auto = ref(false);
    const dnInfo = ref(null);

    const popDevices = ref([]);
    const dnOptions = ref([]);
    const snOptions = ref([]);    

    const form = useForm({
      id: null,
      name: "",
      phone_1: "",
      address: "",
      latitude: "",
      longitude: "",
      order_date: "",
      installation_date: "",
      ftth_id: "",
      package: "",
      status: "",
      subcom: "",
      township: "",
      prefer_install_date: "",
      pop_id: "",
      dn_id: "",
      sn_id: "",
      splitter_no: "",
      installation_remark: "",
      fc_used: "",
      fc_damaged: "",
      onu_serial: "",
      onu_power: "",
      fiber_distance: "",
      vlan: "",
      bundles: "",
      pop_device_id: "",
      gpon_ontid: "",
      port_balance: "",
      order_remark:"",
      partner_id: "",
      created_by: "",
      isp_id: "",
      installation_timeline: 48,
    });

    function resetForm() {
      form.reset();
    }

    // SN Port Number
    const snPortNoOptions = ref(
      Array.from({ length: 16 }, (v, i) => ({ id: i + 1, name: `SN Port ${i + 1}` }))
    );

    const gponOnuIdOptions = ref(
      Array.from({ length: 127 }, (v, i) => ({ id: i, name: `OnuID${i}` }))
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

    function goID() {
      let city_code = form.township['city_code'];
      let city_id = form.township['city_id'];
      var data = props.max_id.filter((id) => id.id == city_id)[0];
      form.ftth_id = city_code + ('000000' + (parseInt(data.value) + 1)).slice(-6) + 'FX';
    }
    function fillPppoe() {
      if (!form.pppoe_account) {
        if (form.ftth_id && form.township) {
          // let dn_no = getNumber(form.dn_id['name']);
          // let sn_no = getNumber(form.sn_id['name']);
          // let city_code = form.township['city_code'];
          // var data = getNumber(form.ftth_id);
          // let psw = dn_no.toString() + sn_no.toString() + ('00000' + (parseInt(data))).slice(-5);
          // let pppoe = city_code + psw + '@FIP';
          form.pppoe_account = form.ftth_id;
          pppoe_auto.value = true;
        }
      }

    }
    function getAbbreviation(name) {
      // Remove any text inside parentheses
      name = name.replace(/\(.*?\)/g, '').trim();

      // Split the name by spaces to get individual words
      const words = name.split(/\s+/);

      // Initialize an abbreviation string
      let abbreviation = '';

      // Loop through the words to construct the abbreviation
      for (let i = 0; i < words.length; i++) {
        // Only add the first letter of each word until abbreviation reaches 3 characters
        abbreviation += words[i][0].toUpperCase();
        if (abbreviation.length === 3) break;
      }

      // Get the current year
      const currentYear = new Date().getFullYear();

      // If the abbreviation is less than 3 characters, pad it (optional) and add the current year
      return abbreviation.padEnd(3, abbreviation[abbreviation.length - 1] || '') + '@' + currentYear + 'FIP';
    }
    function generatePassword() {
      // var chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
      // var passwordLength = 8;
      // var password = "";
      // for (var i = 0; i <= passwordLength; i++) {
      //   var randomNumber = Math.floor(Math.random() * chars.length);
      //   password += chars.substring(randomNumber, randomNumber + 1);
      // }
      if (!form.pppoe_password) {
        // if (form.ftth_id) {
        //   form.pppoe_password = password;
        // }
        if (form.ftth_id && form.name && form.pppoe_account) {

          form.pppoe_password = getAbbreviation(form.name);
        }
      }
    }
    function getNumber(data) {
      const string = data;
      const regex = /\d+/g;
      const matches = string.match(regex);
      const integerValue = matches ? parseInt(matches.join('')) : 0;
      return integerValue;
    }
    function isEmptyObject(value) {
      // Check if it's an array
      if (Array.isArray(value)) {
        console.log('array');
        // If the array is empty, return true
        if (value.length === 0) {
          console.log('empty array');
          return true;
        }
        // Check if the array contains only empty objects
        return value.every(item => typeof item === 'object' && Object.keys(item).length === 0);
      }

      // Check if it's an object
      if (value && typeof value === 'object') {
        return Object.keys(value).length === 0;
      }

      // If it's neither an object nor an array, return false
      return false;
    }
    // Fetch OLTs by POP
    const fetchPopDevices = async () => {
      if (!form.pop_id) {
        popDevices.value = [];
        return;
      }
      try {
        const response = await fetch(`/getOLTByPOP/${form.pop_id['id']}`);
        const data = await response.json();
        popDevices.value = data || [];
      } catch (error) {
        console.error("Failed to fetch POP devices", error);
      }
    };

    // Fetch DNs by OLT
    const fetchDNs = async () => {
      if (!form.pop_device_id) {
        dnOptions.value = [];
        return;
      }
      try {
        const response = await fetch(`/getDNByOLT/${form.pop_device_id['id']}`);
        const data = await response.json();
        dnOptions.value = data || [];
      } catch (error) {
        console.error("Failed to fetch DNs", error);
      }
    };

    // Fetch SNs by DN
    const fetchSNs = async () => {
      if (!form.dn_id) {
        snOptions.value = [];
        return;
      }
      try {
        const response = await fetch(`/getDnId/${form.dn_id['id']}`);
        const data = await response.json();
        snOptions.value = data || [];
      } catch (error) {
        console.error("Failed to fetch SNs", error);
      }
    };
    const filteredPops = ref([]);
    watch(
      () => form.township,
      async (newTownship) => {
        if (newTownship && props.user?.user_type=='internal') {
          try {
            // Reset POP-related fields
            form.pop_id = null;
            form.pop_device_id = null;
            form.dn_id = null;
            form.sn_id = null;
            if (newTownship.partner_id) {
              form.partner = props.partners.find(p => p.id === newTownship.partner_id);
            }
            // Filter POPs based on township
            const response = await fetch(`/getPOPsByTownship/${newTownship.id}`);
            const data = await response.json();
            filteredPops.value = data || [];
          
          } catch (error) {
            console.error("Failed to fetch POPs for township", error);
            filteredPops.value = [];
          }
        } else {
          filteredPops.value = [];
          form.partner = null;
        }
      }
    );
    watch(
      () => form.pop_id,
      ()=>{
        fetchPopDevices();
        form.sn_id = null;
        form.dn_id = null;
        form.pop_device_id = null;
        form.gpon_frame = null;
        form.gpon_slot = null;
        form.gpon_port = null;
        form.gpon_ontid = null;
        form.port_balance = null;
      },
    );
    watch(
      () => form.pop_device_id,
      () => {
        fetchDNs(); 
        form.dn_id = null;
        form.sn_id = null;
      }
    );
    watch(
      () => form.dn_id,
      () => {
        fetchSNs(); 
        dnInfo.value = `Frame${form.dn_id.gpon_frame}/Slot${form.dn_id.gpon_slot}/Port${form.dn_id.gpon_port}`;
        form.sn_id = null;
      }
    );
    onMounted(() => {
     // form.township = props.townships.filter((d) => d.id == 1)[0];
      form.status = props.status_list[0];
  //    goID();
    });
    return { form, submit, resetForm, isNumber, checkPerm, goID, fillPppoe, pppoe_auto, generatePassword,  res_packages, isEmptyObject, pop_devices, snPortNoOptions, gponOnuIdOptions, dnInfo,popDevices, dnOptions,snOptions,filteredPops };
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
