<script setup>
import { Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import Badge from '@/Components/Badge.vue'

const props = defineProps({
  snSplitters: Object
})
</script>

<template>
  <AppLayout title="SN Splitters">
    <template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">
        SN Splitters
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-xl sm:rounded-lg p-6">
          <div class="flex justify-between mb-6">
            <h3 class="text-lg font-medium">SN Splitter List</h3>
            <Link
              :href="route('sn-splitters.create')"
              class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
            >
              Add New SN Splitter
            </Link>
          </div>

          <table class="min-w-full divide-y divide-gray-200">
            <thead>
              <tr>
                <th class="px-3 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                <th class="px-3 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SN Box</th>
                <th class="px-3 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fiber Cable</th>
                <th class="px-3 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Core Number</th>
                <th class="px-3 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                <th class="px-3 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-3 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 text-xs">
              <tr v-for="snSplitter in snSplitters.data" :key="snSplitter.id">
                <td class="px-3 py-4 whitespace-nowrap">{{ snSplitter.name }}</td>
                <td class="px-3 py-4 whitespace-nowrap">{{ snSplitter.sn_box?.name }}</td>
                <td class="px-3 py-4 whitespace-nowrap">{{ snSplitter.fiber_type=='distributed_route'?snSplitter.fiber_cable?.name:'Patch Chord' }}</td>
                <td class="px-3 py-4 whitespace-nowrap">{{ snSplitter.core_number??'-' }}</td>
                <td class="px-3 py-4 whitespace-nowrap">{{ snSplitter.location }}</td>
                <td class="px-3 py-4 whitespace-nowrap">
                  <Badge :color="snSplitter.status === 'active' ? 'green' : 'red'" :text="snSplitter.status" />
                </td>
                <td class="px-3 py-4 whitespace-nowrap">
                  <Link
                    :href="route('sn-splitters.edit', snSplitter.id)"
                    class="text-indigo-600 hover:text-indigo-900 mr-3"
                  >
                    Edit
                  </Link>
                  <Link
                    :href="route('sn-splitters.destroy', snSplitter.id)"
                    method="delete"
                    as="button"
                    class="text-red-600 hover:text-red-900"
                    @click="confirm('Are you sure you want to delete this SN Splitter?')"
                  >
                    Delete
                  </Link>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AppLayout>
</template>