<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import Multiselect from "@suadelabs/vue3-multiselect";

const props = defineProps({
  dnSplitters: Array
})

const form = useForm({
  name: '',
  dnSplitter: null,
  dn_splitter_id: '',
  location: '',
  status: 'active'
})

const submit = () => {
  form.post(route('sn-boxes.store'), {
    onSuccess: () => {
      form.reset()
    }
  })
}
</script>

<template>
  <AppLayout title="Create SN Box">
    <template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">
        Create SN Box
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
                <label class="block text-sm font-medium text-gray-700">DN Splitter</label>
                <multiselect
                  v-model="form.dnSplitter"
                  :options="dnSplitters"
                  track-by="id"
                  label="name"
                  placeholder="Select DN Splitter"
                  :allow-empty="false"
                  @update:modelValue="form.dn_splitter_id = $event?.id"
                />
                <div v-if="form.errors.dn_splitter_id" class="text-red-500 text-sm mt-1">{{ form.errors.dn_splitter_id }}</div>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">Location (Latitude, Longitude)</label>
                <input
                  type="text"
                  v-model="form.location"
                  placeholder="e.g. 16.8661, 96.1951"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                />
                <div v-if="form.errors.location" class="text-red-500 text-sm mt-1">{{ form.errors.location }}</div>
                <p class="mt-1 text-sm text-gray-500">Enter latitude (-90 to 90) and longitude (-180 to 180) separated by comma</p>
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
                  :href="route('sn-boxes.index')"
                  class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
                >
                  Cancel
                </Link>
                <button
                  type="submit"
                  class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
                  :disabled="form.processing"
                >
                  Create
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>