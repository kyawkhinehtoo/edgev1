<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">ODB Details</h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white  shadow-xl sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <div class="flex justify-between mb-4">
              <div class="flex items-center space-x-4 w-1/3">
                <Multiselect
                  v-model="selectedOdf"
                  :options="odfs"
                  :searchable="true"
                  :create-option="false"
                  placeholder="Filter by ODF"
                  label="name"
                  track-by="id"
                  @update:modelValue="filterByOdf"
                  class="w-full"
                />
              </div>
              <div class="flex items-center space-x-3">
                <a :href="route('odnOdbImportView')"
                  class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700" target="_blank">
                  <i class="fas fa-upload mr-2"></i>
                  Import ODN ODBs
                </a>
                <Link :href="route('odb-fiber-cables.create')"
                  class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                  Create New ODB
                </Link>
              </div>
            </div>

            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>

                  <th class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ODF Name</th>
                  <th class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ODB Name</th>
                  <th class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Feeder Cable</th>
                  <th class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cable Color</th>
                  <th class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cable Port</th>
                  <th class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ports Pair</th>
                  <th class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">POP Device(OLT)</th>

                  <th class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                  <th class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200 text-xs">
                <tr v-for="connection in odbFiberCables.data" :key="connection.id">
            
                  <td class="px-2 py-4 whitespace-nowrap">{{ connection.odb?.odf?.name }}</td>
                  <td class="px-2 py-4 whitespace-nowrap">{{ connection.odb?.name }}</td>
                  <td class="px-2 py-4 whitespace-nowrap">{{ connection.fiber_cable?.name }}</td>
                  <td class="px-2 py-4 whitespace-nowrap">
                    <div v-if="connection.fiber_cable_color" class="flex items-center space-x-2">
                      <div
                        class="w-4 h-4 rounded-full border border-gray-300"
                        :style="{ backgroundColor: getColorValue(connection.fiber_cable_color) }"
                        :title="connection.fiber_cable_color"
                      ></div>
                      <span class="text-sm text-gray-600 capitalize">{{ connection.fiber_cable_color }}</span>
                    </div>
                    <span v-else class="text-gray-400 text-sm">No color</span>
                  </td>
                  <td class="px-2 py-4 whitespace-nowrap">
                    <span v-if="connection.fiber_cable_port" class="text-sm text-gray-900">Port {{ connection.fiber_cable_port }}</span>
                    <span v-else class="text-gray-400 text-sm">No port</span>
                  </td>
                  <td class="px-2 py-4 whitespace-nowrap">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mb-1">
                      ODB Port: {{ connection.odb_port }}
                    </span>
                    <i class="fa fas fa-arrow-right text-green-600"></i>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 mr-2 mb-1">
                      OLT Port: {{ connection.olt_port }}
                    </span>
                  
                  </td>
                  <td class="px-2 py-4 whitespace-nowrap">{{ connection.pop_device?.device_name }}</td>

                  <td class="px-2 py-4 whitespace-nowrap">
                    <span :class="{
                      'px-2 py-1 text-xs font-semibold rounded-full': true,
                      'bg-green-100 text-green-800': connection.status === 'active',
                      'bg-red-100 text-red-800': connection.status === 'inactive',
                      'bg-yellow-100 text-yellow-800': connection.status === 'maintenance',
                      'bg-blue-100 text-blue-800': connection.status === 'plan'
                    }">
                      {{ connection.status }}
                    </span>
                  </td>
                  <td class="px-2 py-4 whitespace-nowrap text-sm font-medium">
                    <Link :href="route('odb-fiber-cables.edit', connection.id)" 
                      class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</Link>
                    <button @click="destroy(connection)" 
                      class="text-red-600 hover:text-red-900">Delete</button>
                  </td>
                </tr>
              </tbody>
            </table>

            <pagination class="mt-6" :links="odbFiberCables.links" />
          </div>
        </div>
      </div>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Link } from '@inertiajs/vue3'
import Pagination from '@/Components/Pagination.vue'
import Multiselect from '@suadelabs/vue3-multiselect'

export default {
  components: {
    AppLayout,
    Link,
    Pagination,
    Multiselect
  },
  props: {
    odbFiberCables: Object,
    odfs: Array,
    fiberCables: Array
  },
  data() {
    return {
      selectedOdf: null
    }
  },
  methods: {
    destroy(connection) {
      if (confirm('Are you sure you want to delete this connection?')) {
        this.$inertia.delete(route('odb-fiber-cables.destroy', connection.id))
      }
    },
    filterByOdf() {
      this.$inertia.get(
        route('odb-fiber-cables.index'),
        { odf: this.selectedOdf?.id },
        { preserveState: true }
      )
    },
    getColorValue(colorName) {
      const colorMap = {
        'blue': '#007bff',
        'orange': '#fd7e14',
        'green': '#28a745',
        'brown': '#795548',
        'gray': '#6c757d',
        'white': '#ffffff',
        'red': '#dc3545',
        'black': '#000000',
        'yellow': '#ffc107',
        'purple': '#6f42c1',
        'pink': '#e83e8c',
        'aqua': '#17a2b8'
      }
      return colorMap[colorName?.toLowerCase()] || '#cccccc'
    }
  }
}
</script>