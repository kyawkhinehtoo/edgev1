<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import Multiselect from '@suadelabs/vue3-multiselect';


const props = defineProps({
  dnBox: Object,
  townships: Array
})

const form = useForm({
  name: props.dnBox.name,
  location: props.dnBox.location,
  type: props.dnBox.type || 'dnbox',
  township_id: props.dnBox.township_id,
  township: props.dnBox.township,
  description: props.dnBox.description,
  status: props.dnBox.status
})

const submit = () => {
  form.put(route('dn-boxes.update', props.dnBox.id), {
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
  <AppLayout title="Edit DN Box">
    <template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">
        Edit Node
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
                <select
                  v-model="form.type"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                >
                  <option value="dnbox">DN Box</option>
                  <option value="cabinet">Cabinet</option>
                </select>
                <div v-if="form.errors.type" class="text-red-500 text-sm mt-1">{{ form.errors.type }}</div>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">Township</label>
                <Multiselect
                  v-model="form.township"
                  :options="townships"
                  :searchable="true"
                  :create-option="false"
                  placeholder="Select a township"
                  label="name"
                  track-by="id"
                  @update:modelValue="form.township_id = form.township ? form.township.id : null"
                  class="mt-1"
                />
                <div v-if="form.errors.township_id" class="text-red-500 text-sm mt-1">{{ form.errors.township_id }}</div>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">Location</label>
                <input
                  type="text"
                  v-model="form.location"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                />
                <div v-if="form.errors.location" class="text-red-500 text-sm mt-1">{{ form.errors.location }}</div>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">Description</label>
                <textarea
                  v-model="form.description"
                  rows="3"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                  placeholder="Enter description"
                ></textarea>
                <div v-if="form.errors.description" class="text-red-500 text-sm mt-1">{{ form.errors.description }}</div>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">Status</label>
                <select
                  v-model="form.status"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                >
                  <option value="active">Active</option>
                  <option value="inactive">Inactive</option>
                </select>
                <div v-if="form.errors.status" class="text-red-500 text-sm mt-1">{{ form.errors.status }}</div>
              </div>

              <div class="flex justify-end space-x-3">
                <Link
                  :href="route('dn-boxes.index')"
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