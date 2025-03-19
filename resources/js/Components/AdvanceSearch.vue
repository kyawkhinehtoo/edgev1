<template>
  <!-- Advance Search -->

  <div class="bg-white shadow sm:rounded-t-lg py-2 px-2 md:px-2">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-2 w-full">
      <div class="col-span-1 sm:col-span-1">
        <label for="sh_general" class="block text-sm font-medium text-gray-700">General </label>
        <div class="mt-1 flex rounded-md shadow-sm">
          <span
            class="z-10 leading-snug font-normal  text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
            <i class="fas fa-user"></i>
          </span>
          <input type="text" v-model="sh_general" name="sh_general" id="sh_general"
            class="pl-10 py-2.5 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"
            placeholder="Customer/Company Name etc." tabindex="1" />
        </div>

      </div>
      
      <div class="col-span-1 sm:col-span-1">
        <label for="sh_township" class="block text-sm font-medium text-gray-700">Township </label>
        <div class="mt-1 flex rounded-md shadow-sm">
          <span
            class="z-10 leading-snug font-normal  text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
            <i class="fas fa-user"></i>
          </span>
          <select id="sh_township" v-model="sh_township" name="sh_township"
            class="pl-10 block w-full py-2.5 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
            tabindex="3">
            <option value="0">-Choose Township -</option>
            <option value="empty">-No Township -</option>
            <option v-for="row in townships" v-bind:key="row.id" :value="row.id">{{ row.name }}</option>
          </select>
        </div>

      </div>
      <div class="col-span-1 sm:col-span-1">
        <label for="sh_sn" class="block text-sm font-medium text-gray-700">Package </label>
        <div class="mt-1 flex rounded-md shadow-sm" v-if="packages">
          <multiselect deselect-label="Selected already" :options="packages" track-by="id" label="name" v-model="sh_package"
            :allow-empty="true"></multiselect>
        </div>
      </div>
      <div class="col-span-1 sm:col-span-1" v-if="user.user_type != 'subcon'">
        <label for="sh_status" class="block text-sm font-medium text-gray-700">Customer Status </label>
        <div class="mt-1 flex rounded-md shadow-sm">
          <span
            class="z-10 leading-snug font-normal  text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
            <i class="fas fa-user"></i>
          </span>
          <select id="sh_status" v-model="sh_status" name="sh_status"
            class="pl-10 block w-full py-2.5 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
            tabindex="7">
            <option value="0">-Choose Status -</option>
            <option v-for="row in status" v-bind:key="row.id" :value="row.id">{{ row.name }}</option>
          </select>
        </div>

      </div>
      <div class="col-span-1 sm:col-span-1">
        <label for="sh_prefer" class="block text-sm font-medium text-gray-700">Prefer Date </label>
        <div class="mt-1 flex rounded-md shadow-sm">
          
            <VueDatePicker v-model="sh_prefer" :range="{ partialRange: true }" placeholder="Please choose Prefer Installation  Date" 
            :enable-time-picker="false" model-type="yyyy-MM-dd" id="sh_prefer"
            class="text-gray-400 text-sm focus:ring focus:ring-indigo-500 focus:border-indigo-500 focus:ring-opacity-10 focus:outline-none" />
        </div>

      </div>
      <div class="col-span-1 sm:col-span-1">
        <label for="sh_installation" class="block text-sm font-medium text-gray-700">Installation Date </label>
        <div class="mt-1 flex rounded-md shadow-sm">
            <VueDatePicker v-model="sh_installation" :range="{ partialRange: true }" placeholder="Please choose Installation Date" 
            :enable-time-picker="false" model-type="yyyy-MM-dd" id="sh_installation"
            class="text-gray-400 text-sm focus:ring focus:ring-indigo-500 focus:border-indigo-500 focus:ring-opacity-10 focus:outline-none" />
        </div>

      </div>
      <div class="col-span-1 sm:col-span-1" v-if="user.user_type != 'subcon' && user.user_type != 'isp'">
        <label for="partner" class="block text-sm font-medium text-gray-700">Partner</label>
        <div class="mt-1 flex rounded-md shadow-sm" v-if="partners">
          <multiselect deselect-label="Selected already" :options="partners" track-by="id" label="name"
            v-model="sh_partner" :allow-empty="true"></multiselect>
        </div>
      </div>
      <!-- POP -->
      <div class="col-span-1 sm:col-span-1" v-if="user.user_type != 'subcon' && user.user_type != 'isp'">
        <label for="partner" class="block text-sm font-medium text-gray-700">POP</label>
        <div class="mt-1 flex rounded-md shadow-sm" v-if="Pops">
          <multiselect deselect-label="Selected already" :options="Pops" track-by="id" label="site_name"
            v-model="sh_pop" :allow-empty="true"></multiselect>
        </div>
        <div v-else class="mt-1">
          <input type="text"
              class="py-2 focus:ring-0 flex-1 block w-full rounded-md sm:text-sm border-gray-200 text-gray-400"
              value="Please Choose Partner" disabled />
       </div>
      </div>
      <!-- POP Device -->
      <div class="col-span-1 sm:col-span-1" v-if="user.user_type != 'subcon' && user.user_type != 'isp'">
        <label for="partner" class="block text-sm font-medium text-gray-700">Pop Devices</label>
        <div class="mt-1 flex rounded-md shadow-sm" v-if="popDevices">
          <multiselect deselect-label="Selected already" :options="popDevices" track-by="id" label="device_name"
            v-model="sh_pop_device" :allow-empty="true"></multiselect>
        </div>
        <div v-else class="mt-1">
          <input type="text"
              class="py-2 focus:ring-0 flex-1 block w-full rounded-md sm:text-sm border-gray-200 text-gray-400"
              value="Please Choose POP" disabled />
      </div>
      </div>
      <div class="col-span-1 sm:col-span-1" v-if="user.user_type != 'subcon' && user.user_type != 'isp'">
        <label for="sh_dn" class="block text-sm font-medium text-gray-700">DN </label>
        <div class="mt-1 flex rounded-md shadow-sm" v-if="Dns">
          <multiselect deselect-label="Selected already" :options="Dns" track-by="name" label="name" v-model="sh_dn"
            :allow-empty="true"  placeholder="Please Choose DN"></multiselect>
        </div>
        <div v-else class="mt-1">
          <input type="text"
              class="py-2 focus:ring-0 flex-1 block w-full rounded-md sm:text-sm border-gray-200 text-gray-400"
              value="Please Choose POP Devices" disabled />
      </div>
      </div>
      <div class="col-span-1 sm:col-span-1" v-if="user.user_type != 'subcon' && user.user_type != 'isp'">
        <label for="sh_sn" class="block text-sm font-medium text-gray-700">SN </label>
        <div class="mt-1 flex rounded-md shadow-sm" v-if="Sns">
          <multiselect deselect-label="Selected already" :options="Sns" track-by="id" label="name" v-model="sh_sn"
            :allow-empty="true"></multiselect>
        </div>
        <div v-else>
          <input type="text"
            class="py-2.5 mt-1 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm text-gray-500 border-gray-300"
            value="Please Choose SN" disabled />
        </div>

      </div>
      <!-- third row -->
      
      <div class="col-span-1 sm:col-span-1" v-if="user.user_type != 'subcon' && user.user_type != 'isp'"> 
        <label for="onu_serial" class="block text-sm font-medium text-gray-700">ONU Serial </label>
        <div class="mt-1 flex rounded-md shadow-sm" v-if="onuSerials">
          <multiselect deselect-label="Selected already" :options="onuSerials" track-by="onu_serial" label="onu_serial"
            v-model="sh_onu_serial" :allow-empty="true"></multiselect>
        </div>
      </div>
      <div class="col-span-1 sm:col-span-1" v-if="user.user_type != 'subcon' && user.user_type != 'isp'">
        <label for="sale_person" class="block text-sm font-medium text-gray-700">ISP </label>
        <div class="mt-1 flex rounded-md shadow-sm" v-if="isps">
          <multiselect deselect-label="Selected already" :options="isps" track-by="id" label="name"
            v-model="sh_isp" :allow-empty="true"></multiselect>
        </div>
      </div>
      <div class="col-span-1 sm:col-span-1" v-if="user.user_type != 'subcon' && user.user_type != 'isp'">
        <label for="installation_team" class="block text-sm font-medium text-gray-700">Installation Team</label>
        <div class="mt-1 flex rounded-md shadow-sm" v-if="installationTeams">
          <multiselect deselect-label="Selected already" :options="installationTeams" track-by="id" label="name"
            v-model="sh_installation_team" :allow-empty="true"></multiselect>
        </div>
      </div>
     
      <div class="col-span-1 sm:col-span-1" v-if=" user.user_type != 'isp'">
        <label for="assign_date" class="block text-sm font-medium text-gray-700">Subcon Assign Date</label>
        <div class="mt-1 flex rounded-md shadow-sm">
        <VueDatePicker v-model="sh_assign_date" :range="{ partialRange: true }" placeholder="Please choose Assign Installation Date" 
        :enable-time-picker="false" model-type="yyyy-MM-dd" id="assign_date"
        class="text-gray-400 text-sm focus:ring focus:ring-indigo-500 focus:border-indigo-500 focus:ring-opacity-10 focus:outline-none" />
        </div>
      </div>
      <div class="col-span-1 sm:col-span-1" >
        <label for="sh_installation_status" class="block text-sm font-medium text-gray-700">Installation Status</label>
        <div class="mt-1 flex rounded-md shadow-sm" v-if="installationStatus">
          <multiselect deselect-label="Selected already" :options="installationStatus" track-by="id" label="name"
            v-model="sh_installation_status" :allow-empty="true"></multiselect>
        </div>
      </div>
      <div class="col-span-1 sm:col-span-1" >
        <label for="sh_installation_timeline" class="block text-sm font-medium text-gray-700">Installation Timeline</label>
   
          <div class="mt-1 p-2 space-x-2 inline-flex">
           
            <div class="flex items-center">
              <input type="radio" 
                     id="24hours" 
                     name="installation_timeline" 
                     value="24" 
                     v-model="sh_installation_timeline"
                     class="focus:ring-red-500 h-4 w-4 text-red-600 border-gray-300"
                    />
              <label for="24hours" class="ml-2 text-sm text-gray-700">24 Hours</label>
            </div>
            <div class="flex items-center">
              <input type="radio" 
                     id="48hours" 
                     name="installation_timeline" 
                     value="48" 
                     v-model="sh_installation_timeline"
                     class="focus:ring-yellow-500 h-4 w-4 text-yellow-600 border-gray-300"
                     />
              <label for="48hours" class="ml-2 text-sm text-gray-700">48 Hours</label>
            </div>
            <div class="flex items-center">
              <input type="radio" 
                     id="all" 
                     name="installation_timeline" 
                     value="" 
                     v-model="sh_installation_timeline"
                     class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300"
                    />
              <label for="24hours" class="ml-2 text-sm text-gray-700">All </label>
            </div>
          </div>
      
      </div>
      <!-- end third row -->
    </div>
  </div>
  <div class="mb-2 py-2 px-2 md:px-2 bg-white shadow rounded-b-lg flex justify-between">
    <div class="flex">
      <a @click="doSearch"
        class="cursor-pointer inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">Search
        <i class="ml-1 fa fa-search text-white" tabindex="9"></i></a>
      <a @click="clearSearch"
        class="ml-2 cursor-pointer inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-400 active:bg-gray-500 focus:outline-none focus:border-gray-500 focus:ring focus:ring-gray-200 disabled:opacity-25 transition">Reset
        <i class="ml-1 fa fa-undo-alt text-white" tabindex="10"></i></a>
    </div>
    <div class="flex" v-if="user?.role?.enable_customer_export">
      <a @click="doExcel"
        class="cursor-pointer inline-flex items-center px-4 py-2 bg-green-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring focus:ring-green-300 disabled:opacity-25 transition">Export
        Excel <i class="ml-1 fa fa-download text-white" tabindex="11"></i></a>
    </div>
  </div>
  <!-- End of Advance Search -->
</template>

<script>
import { reactive, inject, ref,watch } from "vue";
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';
import Multiselect from "@suadelabs/vue3-multiselect";
export default {
  name: "AdvanceSearch",
  emits: ["search_parameter"],
  components: {
    VueDatePicker,
    Multiselect,
  },
  setup(props, context) {
    let res_sn = ref("");
    const packages = inject("packages");
    const partners = inject("partners");
    const townships = inject("townships");
    const status = inject("status");
 
    const package_speed = inject("package_speed");
    const isps = inject("isps");
    const installationTeams = inject("installationTeams");
    const onuSerials = inject("onuSerials");
    const user = inject("user");

    const formatter = ref({
      date: "YYYY-MM-DD",
      month: "MMM",
    });
    let sh_general = ref("");
    let sh_installation = ref("");
    let sh_prefer = ref("");
    let sh_package = ref(0);
    let sh_township = ref(0);
    let sh_status = ref(0);
    let sh_partner = ref(0);
    let sh_dn = ref(0);
    let sh_sn = ref(0);
    let sh_package_speed = ref(0);
    let sh_vlan = ref(0);
    let sh_onu_serial = ref(0);
    let sh_installation_team = ref(0);
    let sh_isp = ref(0);
    let sh_assign_date = ref(0);
    let sh_installation_status = ref("");
    let sh_installation_timeline = ref("");
    let sh_pop = ref("");
    let sh_pop_device = ref("");
    const Pops = ref([]);
    const popDevices = ref(null);
    const Dns = ref(null);
    const Sns = ref(null);
    const clearSearch = () => {
      let myurl = Object.create({});
      sh_general.value = "";
      sh_package.value = 0;
      sh_township.value = 0;
      sh_partner.value = 0;
      sh_status.value = 0;
      sh_dn.value = 0;
      sh_sn.value = 0;
      sh_vlan.value = 0;
      sh_onu_serial.value = 0;
      sh_installation_team.value = 0;
      sh_isp.value = 0;
      sh_package_speed.value = 0;
      sh_installation.value = "";
      sh_prefer.value = "";
      res_sn.value = null;
      sh_assign_date="";
      sh_installation_status.value = "";
      sh_installation_timeline.value = "";
      context.emit("search_parameter", myurl);
    };
    const doExcel = () => {
      let myurl = Object.create({});

      if (sh_general.value != "") {
        myurl.general = sh_general.value;
      }
      if (sh_package.value != 0) {
        myurl.package = sh_package.value;
      }
      if (sh_township.value != 0) {
        myurl.township = sh_township.value;
      }
      if (sh_partner.value != 0) {
        myurl.partner = sh_partner.value;
      }

      if (sh_status.value != 0) {
        myurl.status = sh_status.value;
      }
      if (sh_dn.value != 0) {
        myurl.dn = sh_dn.value;
      }
      if (sh_sn.value != 0) {
        myurl.sn = sh_sn.value;
      }
      if (sh_pop.value != 0) {
        myurl.pop = sh_pop.value;
      }
      if (sh_pop_device.value != 0) {
        myurl.pop_device = sh_pop_device.value;
      }
      if (sh_vlan.value != 0) {
        myurl.sh_vlan = sh_vlan.value;
      }
      if (sh_onu_serial.value != 0) {
        myurl.sh_onu_serial = sh_onu_serial.value;
      }
      if (sh_installation_team.value != 0) {
        myurl.sh_installation_team = sh_installation_team.value;
      }
      if (sh_isp.value != 0) {
        myurl.sh_isp = sh_isp.value;
      }
      if (sh_package_speed.value != 0) {
        myurl.package_speed = sh_package_speed.value;
      }
      if (sh_installation.value?.from != "" && sh_installation.value?.to != "") {
        myurl.installation = sh_installation.value;
      }
      if (sh_prefer.value?.from != "" && sh_prefer.value?.to != "") {
        myurl.prefer = sh_prefer.value;
      }
      if (sh_assign_date.value?.from != "" && sh_assign_date.value?.to != "") {
        myurl.assign_date = sh_assign_date.value;
      }
      if ( sh_installation_status.value != "") {
        myurl.installation_status = sh_installation_status.value['id'];
      }
      if ( sh_installation_timeline.value != "") {
        myurl.sh_installation_timeline = sh_installation_timeline.value;
      }
      axios.post("/exportExcel", myurl, { responseType: "blob" }).then((response) => {
        console.log(response);
        var a = document.createElement("a");
        document.body.appendChild(a);
        a.style = "display: none";
        var blob = new Blob([response.data], {
          type: response.headers["content-type"],
        });
        const link = document.createElement("a");
        link.href = window.URL.createObjectURL(blob);
        link.download = `customers_${new Date().getTime()}.xlsx`;
        link.click();
      });
    };

    const doSearch = () => {
      let myurl = Object.create({});

      if (sh_general.value != "") {
        myurl.general = sh_general.value;
      }
      if (sh_package.value != 0) {
        myurl.package = sh_package.value;
      }
      if (sh_township.value != 0) {
        myurl.township = sh_township.value;
      }
      if (sh_partner.value != 0) {
        myurl.partner = sh_partner.value;
      }

      if (sh_status.value != 0) {
        myurl.status = sh_status.value;
      }
      if (sh_dn.value != 0) {
        myurl.dn = sh_dn.value;
      }
      if (sh_sn.value != 0) {
        myurl.sn = sh_sn.value;
      }
      if (sh_pop.value != 0) {
        myurl.pop = sh_pop.value;
      }
      if (sh_pop_device.value != 0) {
        myurl.pop_device = sh_pop_device.value;
      }

      if (sh_package_speed.value != 0) {
        myurl.package_speed = sh_package_speed.value;
      }
      if (sh_installation.value?.from != "" && sh_installation.value?.to != "") {
        myurl.installation = sh_installation.value;
      }
      if (sh_prefer.value?.from != "" && sh_prefer.value?.to != "") {
        myurl.prefer = sh_prefer.value;
      }
      if (sh_vlan.value != 0) {
        myurl.sh_vlan = sh_vlan.value;
      }
      if (sh_onu_serial.value != 0) {
        myurl.sh_onu_serial = sh_onu_serial.value;
      }
      if (sh_installation_team.value != 0) {
        myurl.sh_installation_team = sh_installation_team.value;
      }
      if (sh_isp.value != 0) {
        myurl.sh_isp = sh_isp.value;
      }
      if (sh_assign_date.value?.from != "" && sh_assign_date.value?.to != "") {
        myurl.assign_date = sh_assign_date.value;
      }
      if ( sh_installation_status.value != "") {
        myurl.installation_status = sh_installation_status.value['id'];
      }
      if ( sh_installation_timeline.value != "") {
        myurl.sh_installation_timeline = sh_installation_timeline.value;
      }
      context.emit("search_parameter", myurl);
    };
    const fetchPOP = async () => {
      if (!sh_partner.value) {
        Pops.value = [];
        return;
      }
      try {
        const response = await fetch(`/getPop/${sh_partner.value['id']}`);
        const data = await response.json();
        Pops.value = data || [];
        console.log('fetch POPs',Pops.value);
      } catch (error) {
        console.error("Failed to fetch POPs", error);
      }
    }; 
    const fetchPopDevices = async () => {
      if (!sh_pop.value) {
        popDevices.value = [];
        return;
      }
      try {
        const response = await fetch(`/getOLTByPOP/${sh_pop.value['id']}`);
        const data = await response.json();
        popDevices.value = data || [];
        console.log('fetch POP Devices');
      } catch (error) {
        console.error("Failed to fetch POP devices", error);
      }
    };

    // Fetch DNs by OLT
    const fetchDNs = async () => {
      if (!sh_pop_device.value) {
        Dns.value = [];
        return;
      }
      try {
        const response = await fetch(`/getDNByOLT/${sh_pop_device.value['id']}`);
        const data = await response.json();
        Dns.value = data || [];
      } catch (error) {
        console.error("Failed to fetch DNs", error);
      }
    };
    const fetchSNs = async () => {
      if (!sh_dn.value) {
        Sns.value = [];
        return;
      }
      try {
        const response = await fetch(`/getDnId/${sh_dn.value['id']}`);
        const data = await response.json();
        Sns.value = data || [];
      } catch (error) {
        console.error("Failed to fetch SNs", error);
      }
    };
    watch(
      () => sh_partner.value,
      ()=>{
        fetchPOP();
        sh_pop.value=null;
      }
    );
    watch(
      () => sh_pop.value,
      ()=>{
        fetchPopDevices();
        sh_pop_device.value = null;
      }
    );
    watch(
      () => sh_pop_device.value,
      ()=>{
        fetchDNs();
        sh_dn.value = null;
        sh_sn.value = null;
      }
    );
    watch(
      () => sh_dn.value,
      ()=>{
        fetchSNs();  
      }
    );
    
    const installationStatus = ref([
      { id: 'team_assigned', name: 'Team Assigned' },
      { id: 'cable_done', name: 'Cable Done' },
      { id: 'config_done', name: 'Config Done' },
      { id: 'completed', name: 'Completed' },
      { id: 'customer_cancel', name: 'Customer Cancel' },
      { id: 'pending_odb_issue', name: 'Pending ODB Issue' },
      { id: 'pending_port_full', name: 'Pending Port Full' },
      { id: 'pending_reappointment', name: 'Pending Reappointment' },
    ]);
    return {
      doExcel,
      formatter,
      sh_general,
      sh_general,
      sh_installation,
      sh_prefer,
      sh_package,
      sh_township,
      sh_status,
      sh_partner,
      sh_dn,
      sh_sn,
      sh_package_speed,
      sh_assign_date,
      sh_installation_status,
      sh_installation_timeline,
      installationStatus,
      packages,
      partners,
      townships,
      status,
      package_speed,
      doSearch,
      clearSearch,
   
      isps,
      installationTeams,
      onuSerials,
      sh_vlan,
      sh_onu_serial,
      sh_installation_team,
      sh_isp,
      user,
      Pops,
      popDevices,
      Dns,
      Sns,
      sh_pop,
      sh_pop_device,
   
    };
  },
};
</script>

<style>
.dp__main * {
  font-size: 1em !important;
}
</style>