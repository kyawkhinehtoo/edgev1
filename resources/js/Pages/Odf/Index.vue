<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">ODF Management</h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <div class="flex justify-between mb-4">
              <Link :href="route('odfs.create')"
                class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                Create New ODF
              </Link>
            </div>

            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">POP Site</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">POP Devices (OLT)</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="odf in odfs.data" :key="odf.id">
                  <td class="px-6 py-4 whitespace-nowrap">{{ odf.name }}</td>
                  <td class="px-6 py-4 whitespace-normal">
                    <div class="flex flex-wrap gap-2">
                      <span v-for="site in getUniquePOPSitesArray(odf.pop_device_id)" 
                            :key="site" 
                            class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                        {{ site }}
                      </span>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-normal">
                    <div class="flex flex-wrap gap-2">
                      <span v-for="deviceId in odf.pop_device_id" 
                            :key="deviceId" 
                            class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                        {{ getDeviceName(deviceId) }}
                      </span>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">{{ odf.location }}</td>
                  <td class="px-6 py-4 whitespace-nowrap">{{ odf.description }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <Link :href="route('odfs.edit', odf.id)" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</Link>
                    <button @click="destroy(odf)" class="text-red-600 hover:text-red-900">Delete</button>
                  </td>
                </tr>
              </tbody>
            </table>

            <pagination class="mt-6" :links="odfs.links" />
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

export default {
  components: {
    AppLayout,
    Link,
    Pagination
  },
  props: {
    odfs: Object,
    popDevices: {
      type: Array,
      required: true
    }
  },
  methods: {
    destroy(odf) {
      if (confirm('Are you sure you want to delete this ODF?')) {
        this.$inertia.delete(route('odfs.destroy', odf.id))
      }
    },
    getDeviceName(deviceId) {
      const device = this.popDevices.find(d => d.id === deviceId)
      return device ? device.device_name : 'Unknown Device'
    },
    getUniquePOPSitesArray(deviceIds) {
      if (!Array.isArray(deviceIds)) return []
      
      return deviceIds
        .map(id => {
          const device = this.popDevices.find(d => d.id === id)
          return device?.pop?.site_name
        })
        .filter((site, index, self) => site && self.indexOf(site) === index)
    }
  }
}
</script>