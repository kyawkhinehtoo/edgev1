<script setup>
import { Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import Badge from '@/Components/Badge.vue'

const props = defineProps({
  dnBoxes: Object
})
</script>

<template>
  <AppLayout title="DN Boxes">
    <template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">
        Distribution Node
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
          <div class="flex justify-between mb-6">
            <h3 class="text-lg font-medium">Distribution Node</h3>
            <Link
              :href="route('dn-boxes.create')"
              class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
            >
              Add New Node
            </Link>
          </div>

          <table class="min-w-full divide-y divide-gray-200">
            <thead>
              <tr>
                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Township</th>
                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="dnBox in dnBoxes.data" :key="dnBox.id">
                <td class="px-6 py-4 whitespace-nowrap">{{ dnBox.name }}</td>
                <td class="px-6 py-4 whitespace-nowrap capitalize">{{ dnBox.type || 'dnbox' }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ dnBox.township ? dnBox.township.name : '-' }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ dnBox.location }}</td>
                <td class="px-6 py-4">{{ dnBox.description }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <Badge :color="dnBox.status === 'active' ? 'green' : 'red'" :text="dnBox.status" />
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <Link
                    :href="route('dn-boxes.edit', dnBox.id)"
                    class="text-indigo-600 hover:text-indigo-900 mr-3"
                  >
                    Edit
                  </Link>
                  <Link
                    :href="route('dn-boxes.destroy', dnBox.id)"
                    method="delete"
                    as="button"
                    class="text-red-600 hover:text-red-900"
                    @click="confirm('Are you sure you want to delete this DN Box?')"
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