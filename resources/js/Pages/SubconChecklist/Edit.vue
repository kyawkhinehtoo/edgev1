<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { computed } from 'vue'

const props = defineProps({
  checklist: Object,
  checkListSummary: Object,
  groups: Array
});

const form = useForm({
  name: props.checklist.name,
  has_attachment: props.checklist.has_attachment?true:false,
  service_type: props.checklist.service_type,
  remarks: props.checklist.remarks || '',
  status: props.checklist.status,
  group_id: props.checklist.group_id || ''
});

const submit = () => {
  form.put(route('subcon-checklists.update', props.checklist.id), {
    onSuccess: (page) => {
      Toast.fire({
        icon: 'success',
        title: page.props.flash.message, // Access the message property from the page props
      })
    }
  });
};
</script>

<template>
  <AppLayout title="Edit Subcon Checklist">
    <template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">
        Edit Subcon Checklist
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
          <form @submit.prevent="submit">
            <div class="grid grid-cols-1 gap-6">
              <div>
                <label class="block text-sm font-medium text-gray-700">Group</label>
                <select
                  v-model="form.group_id"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                  required
                >
                  <option value="" disabled>Select Group</option>
                  <option v-for="group in props.groups" :key="group.id" :value="group.id">
                    {{ group.name }}
                  </option>
                </select>
                <div v-if="form.errors.group_id" class="text-red-500 text-sm mt-1">{{ form.errors.group_id }}</div>
              </div>

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
                <label class="block text-sm font-medium text-gray-700">Service Type</label>
                <select
                  v-model="form.service_type"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                >
                  <option value="installation">Installation</option>
                  <option value="maintenance">Maintenance</option>
                  <option value="termination">Termination</option>
                </select>
                <div v-if="form.errors.service_type" class="text-red-500 text-sm mt-1">{{ form.errors.service_type }}</div>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">Requires Attachment</label>
                <div class="flex gap-4">
                  <label class="inline-flex items-center">
                    <input
                      type="checkbox"
                      v-model="form.has_attachment"
                      class="form-checkbox text-indigo-600 border-gray-300 focus:ring-indigo-500"
                    />
                    <span class="ml-2">Required</span>
                  </label>
                </div>
                <div v-if="form.errors.has_attachment" class="text-red-500 text-sm mt-1">{{ form.errors.has_attachment }}</div>
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

              <div>
                <label class="block text-sm font-medium text-gray-700">Remarks</label>
                <textarea
                  v-model="form.remarks"
                  rows="3"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                ></textarea>
                <div v-if="form.errors.remarks" class="text-red-500 text-sm mt-1">{{ form.errors.remarks }}</div>
              </div>

              <div class="flex justify-end space-x-3">
                <Link
                  :href="route('subcon-checklists.index')"
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