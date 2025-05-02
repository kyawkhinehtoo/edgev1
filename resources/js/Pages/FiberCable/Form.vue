<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  fiberCable: {
    type: Object,
    default: () => ({
      name: '',
      core_quantity: '',
      type: '',
      status: ''
    })
  }
})

const form = useForm({
  name: props.fiberCable.name,
  core_quantity: props.fiberCable.core_quantity,
  type: props.fiberCable.type,
  status: props.fiberCable.status
})

const typeOptions = [
  { value: 'feeder', label: 'Feeder' },
  { value: 'sub_feeder', label: 'Sub Feeder' },
  { value: 'destributed_route', label: 'Distributed Route' }
]

const submit = () => {
  if (props.fiberCable.id) {
    form.put(route('fiber-cables.update', props.fiberCable.id))
  } else {
    form.post(route('fiber-cables.store'))
  }
}
</script>

<template>
  <AppLayout :title="fiberCable.id ? 'Edit Fiber Cable' : 'Create Fiber Cable'">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ fiberCable.id ? 'Edit Fiber Cable' : 'Create Fiber Cable' }}
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
                <label class="block text-sm font-medium text-gray-700">Core Quantity</label>
                <input
                  type="number"
                  v-model="form.core_quantity"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                />
                <div v-if="form.errors.core_quantity" class="text-red-500 text-sm mt-1">{{ form.errors.core_quantity }}</div>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">Type</label>
                <select
                  v-model="form.type"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                >
                  <option value="">Select Type</option>
                  <option v-for="option in typeOptions" :key="option.value" :value="option.value">
                    {{ option.label }}
                  </option>
                </select>
                <div v-if="form.errors.type" class="text-red-500 text-sm mt-1">{{ form.errors.type }}</div>
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
                  :href="route('fiber-cables.index')"
                  class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
                >
                  Cancel
                </Link>
                <button
                  type="submit"
                  class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
                  :disabled="form.processing"
                >
                  {{ fiberCable.id ? 'Update' : 'Create' }}
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>