<script setup>
import { ref } from 'vue'
import { router,Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import Pagination from "@/Components/Pagination";
const props = defineProps({
  jcBoxes: Object,
  filters: {
    type: Object,
    default: () => ({
      name: '',
      status: ''
    })
  }
})

const form = ref({
  name: props.filters.name,
  status: props.filters.status
})

const search = () => {
  router.get(route('jc-boxes.index'), form.value, {
    preserveState: true,
    preserveScroll: true
  })
}
</script>

<template>
  <AppLayout title="JC Boxes">
    <template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">
        JC Boxes
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
          <!-- Search Filters -->
          <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
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
            <h3 class="text-lg font-medium">JC Box List</h3>
            <Link
              :href="route('jc-boxes.create')"
              class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
            >
              Add New JC Box
            </Link>
          </div>

          <table class="min-w-full divide-y divide-gray-200">
            <thead>
              <tr>
                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="jcBox in jcBoxes.data" :key="jcBox.id">
                <td class="px-6 py-4 whitespace-nowrap">{{ jcBox.name }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ jcBox.location }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ jcBox.status }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <Link
                    :href="route('jc-boxes.edit', jcBox.id)"
                    class="text-indigo-600 hover:text-indigo-900 mr-3"
                  >
                    Edit
                  </Link>
                  <Link
                    :href="route('jc-boxes.destroy', jcBox.id)"
                    method="delete"
                    as="button"
                    class="text-red-600 hover:text-red-900"
                    @click="confirm('Are you sure you want to delete this JC Box?')"
                  >
                    Delete
                  </Link>
                </td>
              </tr>
            </tbody>
          </table>
          <span v-if="jcBoxes.total" class="block mt-4 text-xs text-gray-600">
            {{ jcBoxes.data.length }} JC Boxs in Current Page. Total Number of JC Boxs: {{ jcBoxes.total }}
          </span>
  
          <pagination class="mt-6" v-if="jcBoxes.links" :links="jcBoxes.links" />
        </div>
      </div>
    </div>
  </AppLayout>
</template>