<script setup>
import { ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import Badge from '@/Components/Badge.vue'
import Multiselect from "@suadelabs/vue3-multiselect";
import Pagination from "@/Components/Pagination";
const props = defineProps({
  coreAssignments: Object,
  fiberCables: Array,
  jcBoxes: Array,
  filters: {
    type: Object,
    default: () => ({
      source : '',
      source_id: '',
      dest : '',
      dest_id: '',
      jc : '',
      jc_id: ''
    })
  }
})

const form = ref({
  source_id: props.filters.source_id,
  dest_id: props.filters.dest_id,
  jc_id: props.filters.jc_id
})

const search = () => {
  router.get(route('core-assignments.index'), form.value, {
    preserveState: true,
    preserveScroll: true
  })
}
const deleteCoreAssignment = (id) => {
  Toast.fire({
    icon: 'warning',
    title: 'Are you sure you want to delete this core assignment?'
  })
  if(!confirm('Are you sure you want to delete this core assignment?')) {
    return;
  }
  router.delete(route('core-assignments.destroy', id), {
    preserveState: true,
    preserveScroll: true
  })
}
</script>

<template>
  <AppLayout title="Core Assignments">
    <template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">
        Core Assignments
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
          <!-- Search Filters -->
          <div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
              <label class="block text-xs font-medium text-gray-700">Source Cable</label>
              <multiselect deselect-label="Selected already" :options="fiberCables" track-by="id"
              label="name" v-model="form.source" :allow-empty="true" :multiple="false" tabindex="1"
              @update:modelValue="form.source_id = $event?.id" >
            </multiselect>
            </div>

            <div>
              <label class="block text-xs font-medium text-gray-700">Destination Cable</label>
              <multiselect deselect-label="Selected already" :options="fiberCables" track-by="id"
              label="name" v-model="form.dest" :allow-empty="true" :multiple="false" tabindex="2"
              @update:modelValue="form.dest_id = $event?.id" >
              </multiselect>
            </div>

            <div>
              <label class="block text-xs font-medium text-gray-700">Connection Node</label>
              <multiselect deselect-label="Selected already" :options="jcBoxes" track-by="id"
              label="name" v-model="form.jc" :allow-empty="true" :multiple="false" tabindex="2"
              @update:modelValue="form.jc_id = $event?.id" >
              </multiselect>
            </div>
            <div>
              <button class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 mt-4" @click="search">Search</button>
               
              <button class="ml-2 px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 mt-4" @click="() => { form.source_id = ''; form.dest_id = ''; form.jc_id = ''; search(); }">Reset</button>
            </div>
          </div>

          <div class="flex justify-between mb-6">
            <h3 class="text-lg font-medium">Core Assignment List</h3>
            <div class="flex space-x-3">
              <a
                :href="route('odnJcImportView')"
                class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700"
                target="_blank"
              >
                Import Core Assignment
              </a>
              <Link
                :href="route('core-assignments.create')"
                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
              >
                Add New Core Assignment
              </Link>
            </div>
          </div>

          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Source Cable</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Source Details</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Destination Cable</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Destination Details</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Connection Node</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="assignment in coreAssignments.data" :key="assignment.id" class="hover:bg-gray-50">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-xs font-medium text-gray-900">
                      {{ assignment.source_fiber_cable?.name }}
                    </div>
                  </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex flex-col space-y-1">
                    <div class="flex items-center space-x-2">
                      <span class="text-xs text-gray-500">Color:</span>
                      <div class="flex items-center space-x-1">
                        <div 
                          class="w-4 h-4 rounded-full border border-gray-300" 
                          :style="{ backgroundColor: assignment.source_color }"
                        ></div>
                        <span class="text-xs font-medium">{{ assignment.source_color }}</span>
                      </div>
                    </div>
                    <div class="flex items-center space-x-2">
                      <span class="text-xs text-gray-500">Port:</span>
                      <span class="text-xs font-medium">{{ assignment.source_port }}</span>
                    </div>
                  </div>
                </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-xs font-medium text-gray-900">
                      {{ assignment.destination_fiber_cable?.name }}
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex flex-col space-y-1">
                      <div class="flex items-center space-x-2">
                        <span class="text-xs text-gray-500">Color:</span>
                        <div class="flex items-center space-x-1">
                          <div 
                            class="w-4 h-4 rounded-full border border-gray-300" 
                            :style="{ backgroundColor: assignment.dest_color }"
                          ></div>
                          <span class="text-xs font-medium">{{ assignment.dest_color }}</span>
                        </div>
                      </div>
                      <div class="flex items-center space-x-2">
                        <span class="text-xs text-gray-500">Port:</span>
                        <span class="text-xs font-medium">{{ assignment.dest_port }}</span>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-xs text-gray-900">
                      {{ assignment.jc_box?.name }}
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <Badge :color="assignment.status === 'active' ? 'green' : 'red'" :text="assignment.status" />
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-xs font-medium">
                    <div class="flex space-x-2">
                      <Link
                        :href="route('core-assignments.edit', assignment.id)"
                        class="text-indigo-600 hover:text-indigo-900"
                      >
                        Edit
                      </Link>
                      <button
                        class="text-red-600 hover:text-red-900"
                        @click="deleteCoreAssignment(assignment.id)"
                      >
                        Delete
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <span v-if="coreAssignments.total" class="block mt-4 text-xs text-gray-600">
            {{ coreAssignments.data.length }} Core Assignment in Current Page. Total Number of Core Assignment: {{ coreAssignments.total }}
          </span>

          <pagination class="mt-6" v-if="coreAssignments.links" :links="coreAssignments.links" />
        </div>
      </div>
    </div>
  </AppLayout>
</template>