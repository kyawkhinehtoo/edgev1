<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">Edit SMTP Setting</h2>
    </template>
    <div class="py-2">
      <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
          <form @submit.prevent="submit">
            <SmtpSettingForm v-model="form" />
            <div class="flex justify-end mt-4">
              <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">
                Update
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
import { defineProps } from 'vue';

const props = defineProps({
  smtpSetting: Object
});

const form = useForm({
  host: props.smtpSetting.host,
  port: props.smtpSetting.port,
  encryption: props.smtpSetting.encryption,
  username: props.smtpSetting.username,
  password: props.smtpSetting.password,
  from_address: props.smtpSetting.from_address,
  from_name: props.smtpSetting.from_name,
  is_active: props.smtpSetting.is_active?true:false,
});

function submit() {
  form.put(route('smtp-settings.update', props.smtpSetting.id));
}
</script>
