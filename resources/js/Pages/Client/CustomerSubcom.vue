<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">Customer Database </h2>
    </template>

    <div class="py-2">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <!-- Search -->
        <div class="relative mb-2">
          <span class="absolute left-3 top-3 text-blueGray-300">
            <i class="fas fa-search"></i>
          </span>
          <input
            type="text"
            id="search"
            v-model="search"
            @keyup.enter="searchTsp"
            placeholder="Search here..."
            class="w-full md:w-64 pl-10 py-2.5 border-gray-300 rounded shadow text-sm text-blueGray-600 bg-white placeholder-blueGray-300"
          />
        </div>

        <!-- Advanced Search Toggle -->
        <div class="flex justify-between items-center mb-2 px-1 md:px-0">
          <a href="#" class="font-semibold text-xs text-gray-600" @click.prevent="toggleAdv">
            Advance Search
            <i :class="show_search ? 'fas fa-chevron-up' : 'fas fa-chevron-down'" class="ml-2 text-blueGray-400"></i>
          </a>
          <Link
           v-if="$page.props.login_type == 'internal' || $page.props.login_type == 'isp' "
            href="/customer/create"
            method="get"
            as="button"
            class="px-4 py-2 bg-gray-800 text-white rounded-md text-xs font-semibold hover:bg-gray-700"
          >
            Create
          </Link>
        </div>

        <div v-show="show_search">
          <advance-search @search_parameter="goSearch" />
        </div>

        <!-- Cards -->
        <div class="hidden md:grid md:grid-cols-2 lg:grid-cols-4 gap-2 mb-4">
          <Card
          icon="fa-users"
          bgColor="blue"
          label="Active Customers"
          :count="active"
          @click="goAction('active')"
        />
        
        <Card
          icon="fa-chart-line"
          bgColor="green"
          label="To Install"
          :count="installation_request"
          @click="goAction('to_install')"
        />
        
        <Card
          icon="fa-circle-pause"
          bgColor="yellow"
          label="Suspended Customers"
          :count="suspense"
          @click="goAction('suspense')"
        />
        
        <Card
          icon="fa-stop"
          bgColor="red"
          label="Terminated Customers"
          :count="terminate"
          @click="goAction('terminate')"
        />
        </div>
       <!-- Mobile View -->
       <div class="grid gap-4 sm:hidden">
        <div
          v-for="row in customers.data"
          :key="row.id"
          :class="`p-4 bg-white rounded-lg shadow-md ${getRowClass(row)}`"
        >
          <div class="flex justify-between items-center mb-2">
            <h3 class="text-sm font-bold" :class="getRowClass(row)">Customer ID: {{ row.ftth_id }}</h3>
            <div class="flex space-x-2">
              <Link
                :href="route('customer.edit', row.id)"
                method="get"
                as="button"
                class="text-indigo-400 hover:text-indigo-600"
              >
                <i class="fas fa-edit"></i>
              </Link>
              <!-- <button
                @click="deleteRow(row)"
                class="text-red-500 hover:text-red-700"
              >
                <i class="fas fa-trash"></i>
              </button> -->
            </div>
          </div>
          <p class="text-sm text-gray-600"><strong>Name:</strong> {{ row.name }}</p>
          <p class="text-sm text-gray-600"><strong>Package:</strong> {{ row.bandwidth }}</p>
          <p class="text-sm text-gray-600"><strong>Township:</strong> {{ row.township_name }}</p>
          <p class="text-sm text-gray-600"><strong>Status:</strong> {{ row.status.name }}</p>
          <p class="text-sm text-gray-600"><strong>Order Date:</strong> {{ row.order_date }}</p>
          <p class="text-sm text-gray-600"><strong>Prefer Install Date:</strong> {{ row.prefer_install_date }}</p>
        </div>
      </div>
        <!-- Table -->
        <div class="hidden sm:block bg-white shadow-xl sm:rounded-lg overflow-auto" v-if="customers.data">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th @click="sortBy('ftth_id')" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase cursor-pointer hover:bg-gray-100">
                  UID <i :class="getSortIcon('ftth_id')" class="fas fa-sort ml-1"></i>
                </th>
                <th @click="sortBy('isp_ftth_id')" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase cursor-pointer hover:bg-gray-100">
                  CID <i :class="getSortIcon('isp_ftth_id')" class="fas fa-sort ml-1"></i>
                </th>
                <th @click="sortBy('order_date')" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase cursor-pointer hover:bg-gray-100">
                  Order Date <i :class="getSortIcon('order_date')" class="fas fa-sort ml-1"></i>
                </th>
                <th @click="sortBy('subcom_assign_date')" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase cursor-pointer hover:bg-gray-100">
                  Assign Date <i :class="getSortIcon('subcom_assign_date')" class="fas fa-sort ml-1"></i>
                </th>
                <th @click="sortBy('prefer_install_date')" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase cursor-pointer hover:bg-gray-100">
                  Prefer Date <i :class="getSortIcon('prefer_install_date')" class="fas fa-sort ml-1"></i>
                </th>
                <th @click="sortBy('way_list_date')" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase cursor-pointer hover:bg-gray-100">
                  WayList Date <i :class="getSortIcon('way_list_date')" class="fas fa-sort ml-1"></i>
                </th>
                <th @click="sortBy('name')" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase cursor-pointer hover:bg-gray-100">
                  Name <i :class="getSortIcon('name')" class="fas fa-sort ml-1"></i>
                </th>
                <th @click="sortBy('bandwidth')" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase cursor-pointer hover:bg-gray-100">
                  Package <i :class="getSortIcon('bandwidth')" class="fas fa-sort ml-1"></i>
                </th>
                <th @click="sortBy('township')" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase cursor-pointer hover:bg-gray-100">
                  Township <i :class="getSortIcon('township')" class="fas fa-sort ml-1"></i>
                </th>
                <th @click="sortBy('installation_status')" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase cursor-pointer hover:bg-gray-100">
                  Installation Status <i :class="getSortIcon('installation_status')" class="fas fa-sort ml-1"></i>
                </th>
                <th @click="sortBy('status')" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase cursor-pointer hover:bg-gray-100">
                  Status <i :class="getSortIcon('status')" class="fas fa-sort ml-1"></i>
                </th>
                <th class="px-3 py-3">
                  <a @click="goAll" class="cursor-pointer text-gray-200 hover:text-gray-500">
                    <i class="fas fa-undo"></i>
                  </a>
                </th>
              </tr>
             
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="row in customers.data" :key="row.id" :class="getRowClass(row)">
                
                <td class="px-3 py-3 text-xs font-medium">{{ row.ftth_id }}</td>
                <td class="px-3 py-3 text-xs font-medium">{{ row.isp_ftth_id }}</td>
                <td class="px-3 py-3 text-xs font-medium">{{ row.order_date }}</td>
                <td class="px-3 py-3 text-xs font-medium">{{ row.subcom_assign_date }}</td>
                <td class="px-3 py-3 text-xs font-medium">{{ row.prefer_install_date }}</td>
                <td class="px-3 py-3 text-xs font-medium">{{ row.way_list_date }}</td>
                <td class="px-3 py-3 text-xs font-medium">{{ row.name }}</td>
                <td class="px-3 py-3 text-xs font-medium">{{ row.bandwidth }}</td>
                <td class="px-3 py-3 text-xs font-medium">{{ row.township_name }}</td>
                <td class="px-3 py-3 text-xs font-medium">
                  {{ 
                    row.installation_status 
                      ? row.installation_status.split('_')
                        .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                        .join(' ') 
                      : '' 
                  }}
                </td>
                <td class="px-3 py-3 text-xs font-medium">{{ row.status.name }}</td>
                <td class="px-3 py-3 text-right">
                  <Link :href="route('customer.edit', row.id)" method="get" as="button" class="text-indigo-400 hover:text-indigo-600 mr-2">
                    <i class="fas fa-folder"></i>
                  </Link>
                  <span v-if="user?.role?.delete_customer">
                    <Link href="#" @click="deleteRow(row)" class="text-yellow-600 hover:text-yellow-900 ml-2">
                      <i class="fas fa-trash"></i>
                    </Link>
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <span v-if="customers.total" class="block mt-4 text-xs text-gray-600">
          {{ customers.data.length }} Customers in Current Page. Total Number of Customers: {{ customers.total }}
        </span>

        <pagination class="mt-6" v-if="customers.links" :links="customers.links" />
      </div>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import Pagination from "@/Components/Pagination";
import AdvanceSearch from "@/Components/AdvanceSearch";
import Card from "@/Components/Card"; // Reusable card component
import { ref,provide } from "vue";
import { router, Link } from "@inertiajs/vue3";

export default {
  components: {
    AppLayout,
    Pagination,
    AdvanceSearch,
    Card,
    Link,
  },
  props: {
    customers: Object,
    active: Number,
    suspense: Number,
    installation_request: Number,
    terminate: Number,
    customers: Object,
    packages: Object,
    package_speed: Object,
    partners: Object,
    townships: Object,
    status: Object,
    errors: Object,
    dn: Object,
    radius: Boolean,
    user: Object,
    isps: Object,
    installationTeams: Object,
    onuSerials: Object,
  },
  setup(props) {
    provide('packages', props.packages);
    provide('partners', props.partners);
    provide('townships', props.townships);
    provide('status', props.status);
    provide('dn', props.dn);
    provide('package_speed', props.package_speed);
    provide('isps', props.isps);
    provide('installationTeams', props.installationTeams);
    provide('onuSerials', props.onuSerials);
    provide('user', props.user);
    const search = ref("");
    const show_search = ref(false);

    const toggleAdv = () => (show_search.value = !show_search.value);

    const searchTsp = () => router.post("/customer/search", { keyword: search.value }, { preserveState: true });

    const goSearch = (params) => router.post("/customer/search", params, { preserveState: true });

    const goAction = (statusType) => {
      if(statusType === 'to_install')
    {
      return router.post("/customer/search", { status: 1 }, { preserveState: true });
    }
      return router.post("/customer/search", { status_type: statusType }, { preserveState: true });
    };

    const goAll = () => router.post("/customer/search", { status: 0 }, { preserveState: true });

    const deleteRow = (data) => {
      if (confirm("Are you sure you want to delete?")) {
        router.post(`/customer/${data.id}`, { _method: "DELETE" });
      }
    };

   
    const getRowClass = (row) => {
      // Define row colors based on row properties (e.g., row.color or row.status)
      return row.status.color ? `text-${row.status.color}` : "";
    };
    const currentSort = ref('');
    const currentSortDir = ref('asc');

    const sortBy = (field) => {
      if (currentSort.value === field) {
        currentSortDir.value = currentSortDir.value === 'asc' ? 'desc' : 'asc';
      } else {
        currentSort.value = field;
        currentSortDir.value = 'asc';
      }
      
      router.post("/customer/search", { 
        sort: field,
        direction: currentSortDir.value 
      }, { 
        preserveState: true 
      });
    };

    const getSortIcon = (field) => {
      if (currentSort.value === field) {
        return currentSortDir.value === 'asc' ? 'fa-sort-up' : 'fa-sort-down';
      }
      return 'fa-sort';
    };

    return {
      search,
      show_search,
      toggleAdv,
      searchTsp,
      goSearch,
      goAction,
      goAll,
      deleteRow,
      getRowClass,
      sortBy,
      getSortIcon,
    };
  },
};
</script>