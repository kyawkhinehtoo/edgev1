<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">Partners</h2>
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
                  placeholder="Search Partners..."
                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                >
              </div>
            </div>
            <Link :href="route('partner.create')" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
              Add Partner
            </Link>
          </div>

          <table class="min-w-full divide-y divide-gray-200">
            <thead>
              <tr>
                <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Name</th>
                <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Contact Person</th>
                <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Email</th>
                <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="partner in partners.data" :key="partner.id">
                <td class="px-6 py-4 whitespace-nowrap">{{ partner.name }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ partner.contact_person }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ partner.phone }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ partner.email }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <Link :href="route('partner.show', partner.id)" class="text-indigo-600 hover:text-indigo-900 mr-3">View</Link>
                  <Link :href="route('partner.edit', partner.id)" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</Link>
                  <button @click="deletePartner(partner)" class="text-red-600 hover:text-red-900">Delete</button>
                </td>
              </tr>
            </tbody>
          </table>

          <Pagination :links="partners.links" class="mt-6" />
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
    partners: Object,
    filters: Object
  },
  
  setup(props) {
    const search = ref(props.filters.search)

    watch(search, debounce((value) => {
      router.get(route('partner.index'), 
        { search: value }, 
        { preserveState: true, preserveScroll: true }
      )
    }, 300))

    return { search }
  },
  methods: {
    deletePartner(partner) {
      if (confirm('Are you sure you want to delete this partner?')) {
        this.$inertia.delete(route('partner.destroy', partner.id))
      }
    }
  }
}
</script>