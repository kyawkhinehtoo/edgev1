<script setup>
import { ref, computed, watch } from 'vue';
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import Badge from '@/Components/Badge.vue'
import Pagination from '@/Components/Pagination.vue'
const props = defineProps({
  subRootCauses: Array,
  rootCauses: Array,
  filters: Object
});

const search = ref('');
const selectedRootCause = ref(props.filters?.root_cause_id || '');

// Apply filter when root cause selection changes
watch(selectedRootCause, (value) => {
  router.get(
    route('sub-root-causes.index'),
    { root_cause_id: value },
    { preserveState: true, replace: true }
  );
});

const filteredSubRootCauses = computed(() => {
  if (!search.value) return props.subRootCauses;
  return props.subRootCauses.data.filter(subRootCause => 
    subRootCause.name.toLowerCase().includes(search.value.toLowerCase()) ||
    subRootCause.root_cause.name.toLowerCase().includes(search.value.toLowerCase())
  );
});

const clearFilters = () => {
  selectedRootCause.value = '';
  search.value = '';
  router.get(
    route('sub-root-causes.index'),
    {},
    { preserveState: true, replace: true }
  );
};
</script>

<template>
  <AppLayout title="Sub Root Causes">
    <template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">
        Sub Root Causes
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-xl sm:rounded-lg p-6">
          <div class="flex justify-between mb-6">
            <h3 class="text-lg font-medium">Sub Root Cause List</h3>
            <Link
              :href="route('sub-root-causes.create')"
              class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
            >
              Add New Sub Root Cause
            </Link>
          </div>
          
          <div class="space-y-4">
            <!-- Search input -->
            <div>
              <input
                v-model="search"
                type="text"
                placeholder="Search sub root causes..."
                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              />
            </div>
            
            <!-- Root Cause filter -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Filter by Root Cause</label>
              <div class="flex space-x-2">
                <select
                  v-model="selectedRootCause"
                  class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                >
                  <option value="">All Root Causes</option>
                  <option v-for="rootCause in rootCauses" :key="rootCause.id" :value="rootCause.id">
                    {{ rootCause.name }}
                  </option>
                </select>
                <button 
                  v-if="selectedRootCause || search"
                  @click="clearFilters"
                  class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300"
                >
                  Clear
                </button>
              </div>
            </div>
          </div>

          <table class="min-w-full divide-y divide-gray-200 mt-4">
            <thead>
              <tr>
                <th class="px-3 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                <th class="px-3 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Root Cause</th>
                <th class="px-3 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                <th class="px-3 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-3 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 text-xs">
              <tr v-for="subRootCause in filteredSubRootCauses.data" :key="subRootCause.id">
                <td class="px-3 py-4">{{ subRootCause.name }}</td>
                <td class="px-3 py-4 ">{{ subRootCause.root_cause.name }}</td>
                <td class="px-3 py-4">{{ subRootCause.description }}</td>
                <td class="px-3 py-4 whitespace-nowrap">
                  <Badge :color="subRootCause.status === 'active' ? 'green' : 'red'" :text="subRootCause.status" />
                </td>
                <td class="px-3 py-4 whitespace-nowrap">
                  <Link
                    :href="route('sub-root-causes.edit', subRootCause.id)"
                    class="text-indigo-600 hover:text-indigo-900 mr-3"
                  >
                    Edit
                  </Link>
                  <Link
                    :href="route('sub-root-causes.destroy', subRootCause.id)"
                    method="delete"
                    as="button"
                    class="text-red-600 hover:text-red-900"
                    @click="confirm('Are you sure you want to delete this sub root cause?')"
                  >
                    Delete
                  </Link>
                </td>
              </tr>
              <tr v-if="filteredSubRootCauses.data.length === 0">
                <td colspan="5" class="px-3 py-4 whitespace-nowrap text-sm text-gray-500 text-center">No sub root causes found</td>
              </tr>
            </tbody>
          </table>
          <Pagination :links="subRootCauses.links" class="mt-6" />
        </div>
      </div>
    </div>
  </AppLayout>
</template>