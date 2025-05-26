<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const form = useForm({
  name: '',
  description: '',
  status: 'active',
  is_installation: false,
  is_maintenance: true,
  is_pending: false,
});

const submit = () => {
  form.post(route('root-causes.store'), {
    onSuccess: () => {
      form.reset()
    }
  });
};
</script>

<template>
  <AppLayout title="Create Root Cause">
    <template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">
        Create Root Cause
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
                <label class="block text-sm font-medium text-gray-700">Description</label>
                <textarea
                  v-model="form.description"
                  rows="3"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                ></textarea>
                <div v-if="form.errors.description" class="text-red-500 text-sm mt-1">{{ form.errors.description }}</div>
              </div>
          
              <div class="grid grid-cols-3">
                <div>
                  <label class="block text-sm font-medium text-gray-700">For Installation</label>
                  <input
                    type="checkbox"
                    v-model="form.is_installation"
                    class="mt-1 p-2.5 rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                  />
                  <div v-if="form.errors.is_installation" class="text-red-500 text-sm mt-1">{{ form.errors.is_installation }}</div>
                </div>
                
                <div>
                  <label class="block text-sm font-medium text-gray-700">For Maintenance</label>
                  <input
                    type="checkbox"
                    v-model="form.is_maintenance"
                    class="mt-1 p-2.5 rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                  />
                  <div v-if="form.errors.is_maintenance" class="text-red-500 text-sm mt-1">{{ form.errors.is_maintenance }}</div>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">For Pending Case</label>
                  <input
                    type="checkbox"
                    v-model="form.is_pending"
                    class="mt-1 p-2.5 rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                  />
                  <div v-if="form.errors.is_pending" class="text-red-500 text-sm mt-1">{{ form.errors.is_pending }}</div>
                </div>
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
                  :href="route('root-causes.index')"
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