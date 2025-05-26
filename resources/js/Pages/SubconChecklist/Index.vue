<script setup>
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import Badge from '@/Components/Badge.vue'

const props = defineProps({
  checklists: Array
});

const search = ref('');
const selectedServiceTypes = ref([]);

// Get unique service types from checklists
const serviceTypes = computed(() => {
  const types = new Set(props.checklists.map(checklist => checklist.service_type));
  return Array.from(types);
});

const filteredChecklists = computed(() => {
  let filtered = props.checklists;
  
  // Filter by search text
  if (search.value) {
    filtered = filtered.filter(checklist => 
      checklist.name.toLowerCase().includes(search.value.toLowerCase())
    );
  }
  
  // Filter by selected service types
  if (selectedServiceTypes.value.length > 0) {
    filtered = filtered.filter(checklist => 
      selectedServiceTypes.value.includes(checklist.service_type)
    );
  }
  
  return filtered;
});

const toggleServiceType = (type) => {
  const index = selectedServiceTypes.value.indexOf(type);
  if (index === -1) {
    selectedServiceTypes.value.push(type);
  } else {
    selectedServiceTypes.value.splice(index, 1);
  }
};

const isServiceTypeSelected = (type) => {
  return selectedServiceTypes.value.includes(type);
};

const clearFilters = () => {
  selectedServiceTypes.value = [];
  search.value = '';
};
</script>

<template>
  <AppLayout title="Subcon Checklists">
    <template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">
        Subcon Checklists
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-xl sm:rounded-lg p-6">
          <div class="flex justify-between mb-6">
            <h3 class="text-lg font-medium">Subcon Checklist List</h3>
            <Link
              :href="route('subcon-checklists.create')"
              class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
            >
              Add New Checklist
            </Link>
          </div>
          
          <div class="space-y-4">
            <!-- Search input -->
            <div>
              <input
                v-model="search"
                type="text"
                placeholder="Search checklists..."
                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              />
            </div>
            
            <!-- Service Type filter -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Filter by Service Type</label>
              <div class="flex flex-wrap gap-2">
                <button
                  v-for="type in serviceTypes"
                  :key="type"
                  @click="toggleServiceType(type)"
                  class="px-3 py-1 text-xs rounded-full border"
                  :class="isServiceTypeSelected(type) ? 'bg-blue-100 border-blue-500 text-blue-800' : 'bg-gray-100 border-gray-300 text-gray-700 hover:bg-gray-200'"
                >
                  {{ type }}
                </button>
                <button 
                  v-if="selectedServiceTypes.length > 0 || search"
                  @click="clearFilters"
                  class="px-3 py-1 text-xs rounded-full bg-red-100 border border-red-300 text-red-700 hover:bg-red-200"
                >
                  Clear Filters
                </button>
              </div>
            </div>
          </div>

          <table class="min-w-full divide-y divide-gray-200 mt-4">
            <thead>
              <tr>
                <th class="px-3 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                <th class="px-3 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service Type</th>
                <th class="px-3 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Attachment</th>
                <th class="px-3 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-3 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 text-xs">
              <tr v-for="checklist in filteredChecklists" :key="checklist.id">
                <td class="px-3 py-4 whitespace-nowrap">{{ checklist.name }}</td>
                <td class="px-3 py-4 whitespace-nowrap">{{ checklist.service_type }}</td>
                <td class="px-3 py-4 whitespace-nowrap">
                  <Badge :color="checklist.has_attachment ? 'green' : 'gray'" :text="checklist.has_attachment ? 'Required' : 'Not Required'" />
                </td>
                <td class="px-3 py-4 whitespace-nowrap">
                  <Badge :color="checklist.status === 'active' ? 'green' : 'red'" :text="checklist.status" />
                </td>
                <td class="px-3 py-4 whitespace-nowrap">
                  <Link
                    :href="route('subcon-checklists.edit', checklist.id)"
                    class="text-indigo-600 hover:text-indigo-900 mr-3"
                  >
                    Edit
                  </Link>
                  <Link
                    :href="route('subcon-checklists.destroy', checklist.id)"
                    method="delete"
                    as="button"
                    class="text-red-600 hover:text-red-900"
                    @click="confirm('Are you sure you want to delete this checklist?')"
                  >
                    Delete
                  </Link>
                </td>
              </tr>
              <tr v-if="filteredChecklists.length === 0">
                <td colspan="5" class="px-3 py-4 whitespace-nowrap text-sm text-gray-500 text-center">No checklists found</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AppLayout>
</template>