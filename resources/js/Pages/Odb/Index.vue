<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">ODB Management</h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
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
              <Link :href="route('odbs.create')"
                class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                Create New ODB
              </Link>
            </div>

            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ODB Name</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ODF</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Ports</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200 text-sm">
                <tr v-for="odb in odbs.data" :key="odb.id">
                  <td class="px-6 py-4 whitespace-nowrap">{{ odb.name }}</td>
                  <td class="px-6 py-4 whitespace-nowrap">{{ odb.odf?.name }}</td>
                  <td class="px-6 py-4 whitespace-nowrap">{{ odb.total_ports }}</td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="{
                      'px-2 py-1 text-xs font-semibold rounded-full': true,
                      'bg-green-100 text-green-800': odb.status === 'active',
                      'bg-red-100 text-red-800': odb.status === 'inactive',
                      'bg-yellow-100 text-yellow-800': odb.status === 'maintenance'
                    }">
                      {{ odb.status }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <Link :href="route('odbs.edit', odb.id)" 
                      class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</Link>
                    <button @click="destroy(odb)" 
                      class="text-red-600 hover:text-red-900">Delete</button>
                  </td>
                </tr>
              </tbody>
            </table>

            <pagination class="mt-6" :links="odbs.links" />
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
    odbs: Object,
    odfs: Array
  },
  data() {
    return {
      selectedOdf: null
    }
  },
  methods: {
    destroy(odb) {
      if (confirm('Are you sure you want to delete this ODB?')) {
        this.$inertia.delete(route('odbs.destroy', odb.id))
      }
    },
    filterByOdf() {
      this.$inertia.get(
        route('odbs.index'),
        { odf: this.selectedOdf?.id },
        { preserveState: true }
      )
    }
  }
}
</script>