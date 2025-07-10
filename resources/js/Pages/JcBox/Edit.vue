<script setup>
import { useForm,Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  jcBox: Object
})

const form = useForm({
  name: props.jcBox.name,
  location: props.jcBox.location,
  type: props.jcBox.type,
  status: props.jcBox.status
})

const submit = () => {
  form.put(route('jc-boxes.update', props.jcBox.id), {
    onSuccess: (page) => {
      Toast.fire({
        icon: 'success',
        title: page.props.flash.message, // Access the message property from the page props
      })
    }
  })
}
</script>

<template>
  <AppLayout title="Edit JC Box">
    <template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">
        Edit Connection Node
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
          <form @submit.prevent="submit">
            <div class="grid grid-cols-1 gap-6">
              <div>
                <label class="block text-sm font-medium text-gray-700">Name</label>
                <input
                  type="text"
                  v-model="form.name"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                />
                <div v-if="form.errors.name" class="text-red-500 text-sm mt-1">{{ form.errors.name }}</div>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Node Type</label>
                <div class="mt-1 space-y-2">
                  <div class="flex items-center">
                    <input
                      type="radio"
                      id="jc"
                      value="jc"
                      v-model="form.type"
                      class="h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-500"
                    />
                    <label for="jc" class="ml-2 text-sm text-gray-700">JC Box</label>
                  </div>
                  <div class="flex items-center">
                    <input
                      type="radio"
                      id="cabinet"
                      value="cabinet"
                      v-model="form.type"
                      class="h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-500"
                    />
                    <label for="cabinet" class="ml-2 text-sm text-gray-700">Cabinet</label>
                  </div>
                </div>
                <div v-if="form.errors.type" class="text-red-500 text-sm mt-1">{{ form.errors.type }}</div>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Location (Latitude,Longitude)</label>
                <input
                  type="text"
                  v-model="form.location"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                  placeholder="Example: 16.8661,96.1951"
                />
                <div v-if="form.errors.location" class="text-red-500 text-sm mt-1">{{ form.errors.location }}</div>
                <p class="text-sm text-gray-500 mt-1">Enter coordinates in format: latitude,longitude (e.g., 16.8661,96.1951)</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">Status</label>
                <input
                  type="text"
                  v-model="form.status"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                />
                <div v-if="form.errors.status" class="text-red-500 text-sm mt-1">{{ form.errors.status }}</div>
              </div>

              <div class="flex justify-end space-x-3">
                <Link
                  :href="route('jc-boxes.index')"
                  class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
                >
                  Cancel
                </Link>
                <button
                  type="submit"
                  class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
                  :disabled="form.processing"
                >
                  Update
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>