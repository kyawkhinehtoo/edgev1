<template>
  <div class="space-y-4">
    <div>
      <label for="host" class="block text-sm font-medium text-gray-700">Host</label>
      <input v-model="modelValue.host" id="host" type="text" class="mb-1 border-gray-300 rounded-md w-full shadow-sm" required />
    </div>
    <div>
      <label for="port" class="block text-sm font-medium text-gray-700">Port</label>
      <input v-model="modelValue.port" id="port" type="number" class="mb-1 border-gray-300 rounded-md w-full shadow-sm" required />
    </div>
    <div>
      <label for="encryption" class="block text-sm font-medium text-gray-700">Encryption</label>
    <div class="flex items-center space-x-4">
      <label class="inline-flex items-center">
        <input
        type="radio"
        class="form-radio"
        value="tls"
        v-model="modelValue.encryption"
        id="encryption-tls"
        />
        <span class="ml-2">TLS</span>
      </label>
      <label class="inline-flex items-center">
        <input
        type="radio"
        class="form-radio"
        value="ssl"
        v-model="modelValue.encryption"
        id="encryption-ssl"
        />
        <span class="ml-2">SSL</span>
      </label>
    </div>
    </div>
    <div>
      <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
      <input v-model="modelValue.username" id="username" type="text" class="mb-1 border-gray-300 rounded-md w-full shadow-sm" required />
    </div>
    <div>
      <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
      <input v-model="modelValue.password" id="password" type="password" class="mb-1 border-gray-300 rounded-md w-full shadow-sm" required />
    </div>
    <div>
      <label for="from_address" class="block text-sm font-medium text-gray-700">From Address</label>
      <input v-model="modelValue.from_address" id="from_address" type="email" class="mb-1 border-gray-300 rounded-md w-full shadow-sm" required />
    </div>
    <div>
      <label for="from_name" class="block text-sm font-medium text-gray-700">From Name</label>
      <input v-model="modelValue.from_name" id="from_name" type="text" class="mb-1 border-gray-300 rounded-md w-full shadow-sm" />
    </div>
    <div>
      <label for="is_active" class="block text-sm font-medium text-gray-700">Active</label>
      <select v-model="modelValue.is_active" id="is_active" class="mb-1 border-gray-300 rounded-md w-full shadow-sm">
        <option :value="true">Yes</option>
        <option :value="false">No</option>
      </select>
    </div>
  </div>
</template>

<script setup>
import { defineProps, defineEmits } from 'vue';
const props = defineProps({
  modelValue: Object
});
const emit = defineEmits(['update:modelValue']);
</script>
