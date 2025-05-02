<script setup>
import { ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import Pagination from "@/Components/Pagination";
const props = defineProps({
  fiberCables: Object,
  filters: {
    type: Object,
    default: () => ({
      name: '',
      type: '',
      cable_layout: '',
      status: ''
    })
  }
})

const typeLabels = {
  feeder: 'Feeder',
  sub_feeder: 'Sub Feeder',
  destributed_route: 'Distributed Route'
}

const form = ref({
  name: props.filters.name,
  type: props.filters.type,
  cable_layout: props.filters.cable_layout,
  status: props.filters.status
})

const search = () => {
  router.get(route('fiber-cables.index'), form.value, {
    preserveState: true,
    preserveScroll: true
  })
}
</script>

<template>
  <AppLayout title="Fiber Cables">
    <template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">
        Fiber Cables
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
          <!-- Search Filters -->
          <div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700">Name</label>
              <input
                type="text"
                v-model="form.name"
                @input="search"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                placeholder="Search by name"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Layout</label>
              <select
                v-model="form.cable_layout"
                @change="search"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
              >
                <option value="">All Layouts</option>
                <option value="UG">Underground</option>
                <option value="OH">Overhead</option>
             
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Type</label>
              <select
                v-model="form.type"
                @change="search"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
              >
                <option value="">All Types</option>
                <option v-for="(label, value) in typeLabels" :key="value" :value="value">
                  {{ label }}
                </option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700">Status</label>
              <input
                type="text"
                v-model="form.status"
                @input="search"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                placeholder="Search by status"
              />
            </div>
          </div>

          <div class="flex justify-between mb-6">
            <h3 class="text-lg font-medium">Fiber Cable List</h3>
            <Link
              :href="route('fiber-cables.create')"
              class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
            >
              Add New Fiber Cable
            </Link>
          </div>

          <table class="min-w-full divide-y divide-gray-200 text-xs">
            <thead>
              <tr>
                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Length</th>
                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Layouts</th>
                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Core Quantity</th>
                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="fiberCable in fiberCables.data" :key="fiberCable.id">
                <td class="px-6 py-4 whitespace-nowrap">{{ fiberCable.name }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ fiberCable.cable_length??'-'  }} km</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ fiberCable.cable_layout }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ fiberCable.core_quantity }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ typeLabels[fiberCable.type] }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ fiberCable.status }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <Link
                    :href="route('fiber-cables.edit', fiberCable.id)"
                    class="text-indigo-600 hover:text-indigo-900 mr-3"
                  >
                    Edit
                  </Link>
                  <Link
                    :href="route('fiber-cables.destroy', fiberCable.id)"
                    method="delete"
                    as="button"
                    class="text-red-600 hover:text-red-900"
                    @click="confirm('Are you sure you want to delete this fiber cable?')"
                  >
                    Delete
                  </Link>
                </td>
              </tr>
            </tbody>
          </table>
          <span v-if="fiberCables.total" class="block mt-4 text-xs text-gray-600">
            {{ fiberCables.data.length }} Fiber Cable in Current Page. Total Number of Fiber Cable: {{ fiberCables.total }}
          </span>
  
          <pagination class="mt-6" v-if="fiberCables.links" :links="fiberCables.links" />
        </div>
      </div>
    </div>
  </AppLayout>
</template>