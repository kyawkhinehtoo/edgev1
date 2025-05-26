<script setup>
import { ref, computed, watch } from 'vue';
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import Badge from '@/Components/Badge.vue'
import Pagination from '@/Components/Pagination.vue'

const props = defineProps({
  rootCauses: Object,
  filters: Object
});

const search = ref(props.filters?.search || '');
const isInstallation = ref(props.filters?.is_installation || '');
const isMaintenance = ref(props.filters?.is_maintenance || '');
const isPending = ref(props.filters?.is_pending || '');

// Watch for changes in filters and update the URL
watch([search, isInstallation, isMaintenance, isPending], debounce(() => {
  router.get(
    route('root-causes.index'),
    {
      search: search.value,
      is_installation: isInstallation.value,
      is_maintenance: isMaintenance.value,
      is_pending: isPending.value
    },
    {
      preserveState: true,
      replace: true
    }
  );
}, 300));

// Debounce function to prevent too many requests
function debounce(fn, delay = 300) {
  let timeout;
  return (...args) => {
    clearTimeout(timeout);
    timeout = setTimeout(() => fn(...args), delay);
  };
}

const clearFilters = () => {
  search.value = '';
  isInstallation.value = '';
  isMaintenance.value = '';
  isPending.value = '';
  
  router.get(
    route('root-causes.index'),
    {},
    {
      preserveState: true,
      replace: true
    }
  );
};
</script>

<template>
  <AppLayout title="Root Causes">
    <template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">
        Root Causes
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-xl sm:rounded-lg p-6">
          <div class="flex justify-between mb-6">
            <h3 class="text-lg font-medium">Root Cause List</h3>
            <Link
              :href="route('root-causes.create')"
              class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
            >
              Add New Root Cause
            </Link>
          </div>
          
          <div class="grid grid-cols-4 gap-6">
            <!-- Search input -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Search </label>
              <input
                v-model="search"
                type="text"
                placeholder="Search root causes..."
                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              />
              </div>

              <!-- Type filter -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Filter by Type</label>
                <select
                  v-model="selectedType"
                  class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                >
                  <option value="">All Types</option>
                  <option value="installation">Installation</option>
                  <option value="maintenance">Maintenance</option>
                </select>
              </div>
              
              <!-- Pending Reason filter -->
              <div >
                <label class="block text-sm font-medium text-gray-700 mb-1">Pending Reason</label>
                <select
                  v-model="isPending"
                  class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                >
                  <option value="">All</option>
                  <option value="true">Yes</option>
                  <option value="false">No</option>
                </select>
              </div>
              
              <!-- Clear filters button -->
              <div class="flex items-end">
                <button 
                  v-if="selectedType || isPending !== '' || search"
                  @click="clearFilters"
                  class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300"
                >
                  Clear Filters
                </button>
              </div>
    
          </div>

          <table class="min-w-full divide-y divide-gray-200 mt-4">
            <thead>
              <tr>
                <th class="px-3 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                <th class="px-3 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                <th class="px-3 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sub Causes</th>
                <th class="px-3 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Installation</th>
                <th class="px-3 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Maintenance</th>
                <th class="px-3 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pending</th>
                <th class="px-3 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-3 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 text-xs">
              <tr v-for="rootCause in rootCauses.data" :key="rootCause.id">
                <td class="px-3 py-4 whitespace-nowrap">{{ rootCause.name }}</td>
                <td class="px-3 py-4">{{ rootCause.description }}</td>
                <td class="px-3 py-4 whitespace-nowrap">{{ rootCause.sub_root_causes.length }}</td>
                <td class="px-3 py-4 whitespace-nowrap">
                  <Badge :color="rootCause.is_installation ? 'green' : 'gray'" :text="rootCause.is_installation ? 'Yes' : 'No'" />
                </td>
                <td class="px-3 py-4 whitespace-nowrap">
                  <Badge :color="rootCause.is_maintenance ? 'blue' : 'gray'" :text="rootCause.is_maintenance ? 'Yes' : 'No'" />
                </td>
                <td class="px-3 py-4 whitespace-nowrap">
                  <Badge :color="rootCause.is_pending ? 'yellow' : 'gray'" :text="rootCause.is_pending ? 'Yes' : 'No'" />
                </td>
                <td class="px-3 py-4 whitespace-nowrap">
                  <Badge :color="rootCause.status === 'active' ? 'green' : 'red'" :text="rootCause.status" />
                </td>
                <td class="px-3 py-4 whitespace-nowrap">
                  <Link
                    :href="route('root-causes.edit', rootCause.id)"
                    class="text-indigo-600 hover:text-indigo-900 mr-3"
                  >
                    Edit
                  </Link>
                  <Link
                    :href="route('root-causes.destroy', rootCause.id)"
                    method="delete"
                    as="button"
                    class="text-red-600 hover:text-red-900"
                    @click="confirm('Are you sure you want to delete this root cause?')"
                  >
                    Delete
                  </Link>
                </td>
              </tr>
              <tr v-if="filteredRootCauses?.data.length === 0">
                <td colspan="7" class="px-3 py-4 whitespace-nowrap text-sm text-gray-500 text-center">No root causes found</td>
              </tr>
            </tbody>
          </table>
          <Pagination :links="rootCauses.links" class="mt-6" />
        </div>
      </div>
    </div>

  </AppLayout>
</template>