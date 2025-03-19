<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">Zones</h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
          <div class="flex justify-between mb-6">
            <div class="flex items-center flex-1">
              <div class="w-1/3">
                <input
                  v-model="search"
                  type="text"
                  placeholder="Search Zones..."
                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                >
              </div>
            </div>
            <Link :href="route('zone.create')" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
              Add Zone
            </Link>
          </div>

          <table class="min-w-full divide-y divide-gray-200">
            <thead>
              <tr>
                <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Name</th>
                <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Description</th>
                <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Townships</th>
                <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="zone in zones.data" :key="zone.id">
                <td class="px-6 py-4 whitespace-nowrap">{{ zone.name }}</td>
                <td class="px-6 py-4">{{ zone.description }}</td>
                <td class="px-6 py-4">
                  <div class="flex flex-wrap gap-1">
                    <span v-for="township in zone.townships" :key="township.id"
                          class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                      {{ township.name }}
                    </span>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="[
                    'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                    zone.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                  ]">
                    {{ zone.is_active ? 'Active' : 'Inactive' }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <Link :href="route('zone.edit', zone.id)" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</Link>
                  <button @click="deleteZone(zone)" class="text-red-600 hover:text-red-900">Delete</button>
                </td>
              </tr>
            </tbody>
          </table>

          <Pagination :links="zones.links" class="mt-6" />
        </div>
      </div>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Link } from '@inertiajs/vue3'
import Pagination from '@/Components/Pagination.vue'
import { ref, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import debounce from 'lodash/debounce'

export default {
  components: {
    AppLayout,
    Link,
    Pagination
  },
  props: {
    zones: Object,
    filters: Object
  },
  setup(props) {
    const search = ref(props.filters.search)

    watch(search, debounce((value) => {
      router.get(route('zone.index'), 
        { search: value }, 
        { preserveState: true, preserveScroll: true }
      )
    }, 300))

    return { search }
  },
  methods: {
    deleteZone(zone) {
      if (confirm('Are you sure you want to delete this zone?')) {
        this.$inertia.delete(route('zone.destroy', zone.id))
      }
    }
  }
}
</script>