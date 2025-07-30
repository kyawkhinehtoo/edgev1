<template>
  <div v-if="!customer_detail[0]" class="w-full flex flex-col items-center justify-center">
    <div class="animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-purple-500"></div>
    <h2 class="text-center text-gray-600 text-sm font-semibold mt-2">Loading...</h2>
  </div>
  <div v-if="customer_detail[0]">
    <div class="bg-white shadow overflow-hitden sm:rounded-lg  text-left">
      <div class="border-t border-gray-200">
        <table class="w-full">
          <tbody>
          <tr class="otd:bg-white even:bg-gray-50 dark:otd:bg-gray-900/50 dark:even:bg-gray-950">
            <td class="p-2.5 text-sm font-medium text-gray-500">Customer ID</td>
            <td class="p-2.5 text-sm text-gray-900 sm:mt-0 sm:col-span-3">{{ customer_detail[0].ftth_id }} | {{
              customer_detail[0]?.isp_ftth_id }} </td>
          </tr>
          <tr class="otd:bg-white even:bg-gray-50 dark:otd:bg-gray-900/50 dark:even:bg-gray-950">
            <td class="p-2.5 text-sm font-medium text-gray-500">Customer Name</td>
            <td class="p-2.5 text-sm text-gray-900 sm:mt-0 sm:col-span-3">{{ customer_detail[0].name }}</td>
          </tr>
          <tr class="otd:bg-white even:bg-gray-50 dark:otd:bg-gray-900/50 dark:even:bg-gray-950">

            <td class="p-2.5 text-sm font-medium text-gray-500">Contact Number</td>
            <td class="p-2.5 text-sm text-gray-900 sm:mt-0 sm:col-span-3">{{ customer_detail[0].phone_1 }} {{
              customer_detail[0].phone_2 }}</td>
          </tr>
          <tr class="otd:bg-white even:bg-gray-50 dark:otd:bg-gray-900/50 dark:even:bg-gray-950">
            <td class="p-2.5 text-sm font-medium text-gray-500">Full Address</td>
            <td class="p-2.5 text-sm text-gray-900 sm:mt-0 sm:col-span-3">{{ customer_detail[0].address }}</td>
          </tr>
          <tr class="otd:bg-white even:bg-gray-50 dark:otd:bg-gray-900/50 dark:even:bg-gray-950"
            v-if="customer_detail[0].location">
            <td class="p-2.5 text-sm font-medium text-gray-500">Location</td>
            <td class="p-2.5 text-sm text-gray-900 sm:mt-0 sm:col-span-3">
              <a :href="'https://www.google.com/maps/search/?api=1&query=' + customer_detail[0].location"
                target="_blank">{{ customer_detail[0].location }}</a>
            </td>
          </tr>
          <tr class="otd:bg-white even:bg-gray-50 dark:otd:bg-gray-900/50 dark:even:bg-gray-950"
            v-if="customer_detail[0].location">
            <td class="p-2.5 text-sm font-medium text-gray-500">Actual Location</td>
            <td class="p-2.5 text-sm text-gray-900 sm:mt-0 sm:col-span-3">
              <a :href="'https://www.google.com/maps/search/?api=1&query=' + customer_detail[0].actual_location"
                target="_blank">{{ customer_detail[0].actual_location }}</a>
            </td>
          </tr>
          <tr class="otd:bg-white even:bg-gray-50 dark:otd:bg-gray-900/50 dark:even:bg-gray-950">
            <td class="p-2.5 text-sm font-medium text-gray-500">Applied Mbps</td>
            <td class="p-2.5 text-sm text-gray-900 sm:mt-0 sm:col-span-3">
              {{ customer_detail[0].bandwidth }} Mbps
            </td>
          </tr>
          <template v-if="user?.user_type != 'isp'">
            <tr class="otd:bg-white even:bg-gray-50 dark:otd:bg-gray-900/50 dark:even:bg-gray-950"
            v-if="customer_detail[0].dn_splitter_name">
            <td class="p-2.5 text-sm font-medium text-gray-500">DN Box Name</td>
            <td class="p-2.5 text-sm text-gray-900 sm:mt-0 sm:col-span-3">
              {{ customer_detail[0].dn_splitter_name }}

            </td>
          </tr>
          <tr class="otd:bg-white even:bg-gray-50 dark:otd:bg-gray-900/50 dark:even:bg-gray-950"
            v-if="customer_detail[0].dn_location">
            <td class="p-2.5 text-sm font-medium text-gray-500">DN Box Location</td>
            <td class="p-2.5 text-sm text-gray-900 sm:mt-0 sm:col-span-3">
              <a :href="'https://www.google.com/maps/search/?api=1&query=' + customer_detail[0].dn_location"
                target="_blank">{{ customer_detail[0].dn_location }}</a>

            </td>
          </tr>
          <tr class="otd:bg-white even:bg-gray-50 dark:otd:bg-gray-900/50 dark:even:bg-gray-950"
            v-if="customer_detail[0].sn_splitter_name">
            <td class="p-2.5 text-sm font-medium text-gray-500">SN Box Name</td>
            <td class="p-2.5 text-sm text-gray-900 sm:mt-0 sm:col-span-3">
              {{ customer_detail[0].sn_splitter_name }}

            </td>
          </tr>
          <tr class="otd:bg-white even:bg-gray-50 dark:otd:bg-gray-900/50 dark:even:bg-gray-950"
            v-if="customer_detail[0].sn_location">
            <td class="p-2.5 text-sm font-medium text-gray-500">SN Box Location</td>
            <td class="p-2.5 text-sm text-gray-900 sm:mt-0 sm:col-span-3">
              <a :href="'https://www.google.com/maps/search/?api=1&query=' + customer_detail[0].sn_location"
                target="_blank">{{ customer_detail[0].sn_location }}</a>

            </td>
          </tr>
          <tr class="otd:bg-white even:bg-gray-50 dark:otd:bg-gray-900/50 dark:even:bg-gray-950"
            v-if="customer_detail[0]?.port_number">
            <td class="p-2.5 text-sm font-medium text-gray-500">SN Port</td>
            <td class="p-2.5 text-sm text-gray-900 sm:mt-0 sm:col-span-3">
              {{ customer_detail[0]?.port_number }}

            </td>
          </tr>
          <template v-if="customer_detail[0].type == 'relocation'">
          <tr class="otd:bg-white even:bg-gray-50 dark:otd:bg-gray-900/50 dark:even:bg-gray-950"
            v-if="customer_detail[0]?.new_dn_name">
            <td class="p-2.5 text-sm font-medium text-gray-500">New DN Box Name</td>
            <td class="p-2.5 text-sm text-gray-900 sm:mt-0 sm:col-span-3">
              {{ customer_detail[0].new_dn_name }}

            </td>
          </tr>
          <tr class="otd:bg-white even:bg-gray-50 dark:otd:bg-gray-900/50 dark:even:bg-gray-950"
            v-if="customer_detail[0]?.new_dn_location">
            <td class="p-2.5 text-sm font-medium text-gray-500">New DN Box Location</td>
            <td class="p-2.5 text-sm text-gray-900 sm:mt-0 sm:col-span-3">
              <a :href="'https://www.google.com/maps/search/?api=1&query=' + customer_detail[0].new_dn_location"
                target="_blank">{{ customer_detail[0].new_dn_location }}</a>

            </td>
          </tr>
          <tr class="otd:bg-white even:bg-gray-50 dark:otd:bg-gray-900/50 dark:even:bg-gray-950"
            v-if="customer_detail[0]?.new_sn_name">
            <td class="p-2.5 text-sm font-medium text-gray-500">New SN Box Name</td>
            <td class="p-2.5 text-sm text-gray-900 sm:mt-0 sm:col-span-3">
              {{ customer_detail[0]?.new_sn_name }}

            </td>
          </tr>
          <tr class="otd:bg-white even:bg-gray-50 dark:otd:bg-gray-900/50 dark:even:bg-gray-950"
            v-if="customer_detail[0]?.new_sn_location">
            <td class="p-2.5 text-sm font-medium text-gray-500">New SN Box Location</td>
            <td class="p-2.5 text-sm text-gray-900 sm:mt-0 sm:col-span-3">
              <a :href="'https://www.google.com/maps/search/?api=1&query=' + customer_detail[0].new_sn_location"
                target="_blank">{{ customer_detail[0].new_sn_location }}</a>

            </td>
          </tr>
          <tr class="otd:bg-white even:bg-gray-50 dark:otd:bg-gray-900/50 dark:even:bg-gray-950"
            v-if="customer_detail[0]?.new_port_number">
            <td class="p-2.5 text-sm font-medium text-gray-500">New SN Port</td>
            <td class="p-2.5 text-sm text-gray-900 sm:mt-0 sm:col-span-3">
              {{ customer_detail[0]?.new_port_number }}

            </td>
          </tr>
            </template>

          </template>
          
      
          </tbody>

        </table>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from "vue";
export default {
  name: "CustomerDetail",
  props: ["data","user"],
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

<style></style>