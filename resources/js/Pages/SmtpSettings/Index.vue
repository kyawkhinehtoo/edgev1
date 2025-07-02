<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">SMTP Settings</h2>
    </template>
    <div class="py-2">
      <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
          <div class="flex justify-end mb-4">
            <Link :href="route('smtp-settings.create')" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition"  v-if="smtpSettings.length==0">
              Add SMTP Setting
            </Link >
          </div>
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Host</th>
                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Port</th>
                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Encryption</th>
                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Username</th>
                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">From Address</th>
                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Active</th>
                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 text-sm">
              <tr v-for="smtp in smtpSettings" :key="smtp.id">
                <td class="px-3 py-3">{{ smtp.host }}</td>
                <td class="px-3 py-3">{{ smtp.port }}</td>
                <td class="px-3 py-3">{{ smtp.encryption || '-' }}</td>
                <td class="px-3 py-3">{{ smtp.username }}</td>
                <td class="px-3 py-3">{{ smtp.from_address }}</td>
                <td class="px-3 py-3">{{ smtp.is_active ? 'Yes' : 'No' }}</td>
                <td class="px-3 py-3 text-right">
                  <Link :href="route('smtp-settings.edit', smtp.id)" class="text-yellow-600 hover:text-yellow-900 mr-2">Edit</Link>
                  <button @click="destroy(smtp.id)" class="text-red-600 hover:text-red-900">Delete</button>
                </td>
              </tr>
              <tr v-if="!smtpSettings.length">
                <td colspan="7" class="text-center text-gray-500 py-4">No SMTP settings found.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </app-layout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { defineProps } from 'vue';

defineProps({
  smtpSettings: Array
});

function destroy(id) {
  if (confirm('Are you sure?')) {
    router.delete(route('smtp-settings.destroy', id));
  }
}
</script>
