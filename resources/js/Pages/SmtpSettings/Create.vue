<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">Add SMTP Setting</h2>
    </template>
    <div class="py-2">
      <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
          <form @submit.prevent="submit">
            <SmtpSettingForm v-model="form" />
            <div class="flex justify-end mt-4">
              <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">
                Save
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </app-layout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import SmtpSettingForm from './Partials/SmtpSettingForm.vue';
import { useForm } from '@inertiajs/vue3';

const form = useForm({
  host: '',
  port: '',
  encryption: '',
  username: '',
  password: '',
  from_address: '',
  from_name: '',
  is_active: true,
});

function submit() {
  form.post(route('smtp-settings.store'));
}
</script>
