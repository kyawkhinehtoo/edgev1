<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">Activities Triggered Definition</h2>
    </template>
    <div class="py-2">
      <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
         <div class="flex justify-end mb-4">
            <button @click="openModal()" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">
              Add Activity
            </button>
          </div>
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
         
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email?</th>
                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SMS?</th>
                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email Template</th>
                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 text-sm">
              <tr v-for="activity in activities" :key="activity.id">
                <td class="px-3 py-3">{{ activity.code }}</td>
                <td class="px-3 py-3">{{ activity.name }}</td>
                <td class="px-3 py-3">{{ activity.description }}</td>
                <td class="px-3 py-3">{{ activity.notify_by_email ? 'Yes' : 'No' }}</td>
                <td class="px-3 py-3">{{ activity.notify_by_sms ? 'Yes' : 'No' }}</td>
                <td class="px-3 py-3">{{ activity.email_template?.name || '-' }}</td>
                <td class="px-3 py-3 text-right">
                  <button @click="edit(activity)" class="text-yellow-600 hover:text-yellow-900 mr-2">Edit</button>
                  <button @click="destroy(activity.id)" class="text-red-600 hover:text-red-900">Delete</button>
                </td>
              </tr>
              <tr v-if="!activities.length">
                <td colspan="7" class="text-center text-gray-500 py-4">No activities found.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Modal for Create/Edit -->
    <div v-if="isOpen" class="fixed z-10 inset-0 overflow-y-auto ease-out duration-400">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" @click="closeModal()">
          <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full" role="dialog" aria-modal="true" aria-labelledby="modal-headline">
          <form @submit.prevent="submit">
            <div class="shadow sm:rounded-md sm:overflow-hidden">
              <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
                <div>
                  <label for="code" class="block text-sm font-medium text-gray-700 mb-1">Code</label>
                  <select v-model="form.code" id="code" class="form-input w-full border-gray-300 rounded-md" required>
                    <option value="">Select Code</option>
                    <option v-for="item in predefinedCodes" :key="item.value" :value="item.value">{{ item.label }}</option>
                  </select>
                </div>
                <div>
                  <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                  <input v-model="form.name" id="name" type="text" class="form-input w-full border-gray-300 rounded-md" required />
                </div>
                <div>
                  <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                  <textarea v-model="form.description" id="description" class="form-input w-full border-gray-300 rounded-md"></textarea>
                </div>
                <div class="flex gap-4">
                  <div>
                    <label for="notify_by_email" class="block text-sm font-medium text-gray-700 mb-1">Notify by Email</label>
                    <input type="checkbox" v-model="form.notify_by_email" id="notify_by_email" class="border-gray-300 rounded-sm" />
                  </div>
                  <div>
                    <label for="notify_by_sms" class="block text-sm font-medium text-gray-700 mb-1">Notify by SMS</label>
                    <input type="checkbox" v-model="form.notify_by_sms" id="notify_by_sms" class="border-gray-300 rounded-sm" />
                  </div>
                </div>
                <div>
                  <label for="email_template_id" class="block text-sm font-medium text-gray-700 mb-1">Email Template</label>
                  <select v-model="form.email_template_id" id="email_template_id" class="form-input w-full border-gray-300 rounded-md">
                    <option value="">Select Template</option>
                    <option v-for="template in emailTemplates" :key="template.id" :value="template.id">{{ template.name }}</option>
                  </select>
                </div>
              </div>
              <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                <button type="button" @click="closeModal()" class="inline-flex justify-center rounded-md border border-gray-300 px-4 py-2 bg-white text-base leading-6 font-medium text-gray-700 shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue transition ease-in-out duration-150 sm:text-sm sm:leading-5 mr-2">Cancel</button>
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">
                  {{ editMode ? 'Update' : 'Create' }}
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </app-layout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { router } from '@inertiajs/vue3';
import { ref, reactive } from 'vue';
import { Link } from '@inertiajs/vue3';

defineProps({
  activities: Array,
  emailTemplates: Array
});

const isOpen = ref(false);
const editMode = ref(false);
const form = reactive({
  id: null,
  code: '',
  name: '',
  description: '',
  notify_by_email: false,
  notify_by_sms: false,
  email_template_id: ''
});

const predefinedCodes = [
  { value: 'USER_REGISTER', label: 'User Register' },
  { value: 'ORDER_PLACED', label: 'Order Placed' },
  { value: 'PAYMENT_RECEIVED', label: 'Payment Received' },
  { value: 'ACCOUNT_SUSPENDED', label: 'Account Suspended' },
  { value: 'PASSWORD_RESET', label: 'Password Reset' },
  // Add more as needed
];

function openModal() {
  resetForm();
  isOpen.value = true;
  editMode.value = false;
}

function edit(activity) {
  form.id = activity.id;
  form.code = activity.code;
  form.name = activity.name;
  form.description = activity.description;
  form.notify_by_email = activity.notify_by_email;
  form.notify_by_sms = activity.notify_by_sms;
  form.email_template_id = activity.email_template_id;
  isOpen.value = true;
  editMode.value = true;
}

function closeModal() {
  isOpen.value = false;
  resetForm();
}

function resetForm() {
  form.id = null;
  form.code = '';
  form.name = '';
  form.description = '';
  form.notify_by_email = false;
  form.notify_by_sms = false;
  form.email_template_id = '';
}

function submit() {
  if (editMode.value) {
    router.put(route('activities.update', form.id), form, {
      onSuccess: () => closeModal()
    });
  } else {
    router.post(route('activities.store'), form, {
      onSuccess: () => closeModal()
    });
  }
}

function destroy(id) {
  if (confirm('Are you sure?')) {
    router.delete(route('activities.destroy', id));
  }
}
</script>
