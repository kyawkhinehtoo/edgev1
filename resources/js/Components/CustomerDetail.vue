<template>
  <div v-if="!customer_detail[0]" class="w-full flex flex-col items-center justify-center">
    <div class="animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-purple-500"></div>
    <h2 class="text-center text-gray-600 text-sm font-semibold mt-2">Loading...</h2>
  </div>
  <div v-if="customer_detail[0]">
    <div class="bg-white shadow overflow-hidden sm:rounded-lg  text-left">
      <div class="border-t border-gray-200">
        <dl>
          <div class="bg-gray-50 px-2 py-2 sm:grid sm:grid-cols-4 sm:gap-4 sm:px-6">
            <dt class="text-sm font-medium text-gray-500">Customer ID</dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-3">{{ customer_detail[0].ftth_id }} | {{ customer_detail[0]?.isp_ftth_id }} </dd>
          </div>
         <div class="bg-white px-2 py-2 sm:grid sm:grid-cols-4 sm:gap-4 sm:px-6">
            <dt class="text-sm font-medium text-gray-500">Customer Name</dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-3">{{ customer_detail[0].name }}</dd>
          </div>
          <div class="bg-gray-50 px-2 py-2 sm:grid sm:grid-cols-4 sm:gap-4 sm:px-6">
            <dt class="text-sm font-medium text-gray-500">Contact Number</dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-3">{{ customer_detail[0].phone_1 }} {{ customer_detail[0].phone_2 }}</dd>
          </div>
          <div class="bg-white px-2 py-2 sm:grid sm:grid-cols-4 sm:gap-4 sm:px-6">
            <dt class="text-sm font-medium text-gray-500">Full Address</dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-3">{{ customer_detail[0].address }}</dd>
          </div>
          <div class="bg-gray-50 px-2 py-2 sm:grid sm:grid-cols-4 sm:gap-4 sm:px-6" v-if=" customer_detail[0].location">
            <dt class="text-sm font-medium text-gray-500">Location</dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-3">
              <a :href="'https://www.google.com/maps/search/?api=1&query=' + customer_detail[0].location" target="_blank">{{ customer_detail[0].location }}</a>
            </dd>
          </div>
      
          <div class="bg-white px-2 py-2 sm:grid sm:grid-cols-4 sm:gap-4 sm:px-6">
            <dt class="text-sm font-medium text-gray-500">Applied Mbps</dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-3">
              {{ customer_detail[0].bandwidth }}  Mbps
            </dd>            
          </div>
          <div class="bg-gray-50 px-2 py-2 sm:grid sm:grid-cols-4 sm:gap-4 sm:px-6" v-if=" customer_detail[0].sn_splitter_name">
            <dt class="text-sm font-medium text-gray-500">SN Box Name</dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-3">
              {{ customer_detail[0].sn_splitter_name }} 
           
            </dd>
          </div>
          <div class="bg-white px-2 py-2 sm:grid sm:grid-cols-4 sm:gap-4 sm:px-6" v-if=" customer_detail[0].sn_location">
            <dt class="text-sm font-medium text-gray-500">SN Box Location</dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-3">
              <a :href="'https://www.google.com/maps/search/?api=1&query=' + customer_detail[0].sn_location" target="_blank">{{ customer_detail[0].sn_location }}</a>
           
            </dd>
          </div>
          <div class="bg-gray-50 px-2 py-2 sm:grid sm:grid-cols-4 sm:gap-4 sm:px-6" v-if=" customer_detail[0].dn_splitter_name">
            <dt class="text-sm font-medium text-gray-500">DN Box Name</dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-3">
              {{ customer_detail[0].dn_splitter_name }} 
           
            </dd>
          </div>
          <div class="bg-white px-2 py-2 sm:grid sm:grid-cols-4 sm:gap-4 sm:px-6" v-if=" customer_detail[0].dn_location">
            <dt class="text-sm font-medium text-gray-500">DN Box Location</dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-3">
              <a :href="'https://www.google.com/maps/search/?api=1&query=' + customer_detail[0].dn_location" target="_blank">{{ customer_detail[0].dn_location }}</a>
           
            </dd>
          </div>
        </dl>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from "vue";
export default {
  name: "CustomerDetail",
  props: ["data"],
  setup(props) {
    const customer_detail = ref("");
    const getLog = async () => {
      let url = "/getCustomer/" + props.data;
      try {
        const res = await fetch(url);
        const data = await res.json();
        return data;
      } catch (err) {
        console.error(err);
      }
    };
    const calculate = () => {
      getLog().then((d) => {
        customer_detail.value = d;
      });
    };
    onMounted(() => {
      calculate();
    });
    return { customer_detail };
  },
};
</script>

<style>
</style>