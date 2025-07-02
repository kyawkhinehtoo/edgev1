<template>
  <div class="space-y-4">
    <div>
      <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
      <input v-model="modelValue.name" id="name" type="text" class="form-input w-full" required />
    </div>
    <div>
      <label for="port_sharing_service_id" class="block text-sm font-medium text-gray-700 mb-1">Port Sharing Service ID</label>
      <input v-model="modelValue.port_sharing_service_id" id="port_sharing_service_id" type="number" class="form-input w-full" required />
    </div>
    <div>
      <label for="isp_id" class="block text-sm font-medium text-gray-700 mb-1">ISP ID</label>
      <input v-model="modelValue.isp_id" id="isp_id" type="number" class="form-input w-full" required />
    </div>
    <div>
      <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
      <input v-model="modelValue.start_date" id="start_date" type="datetime-local" class="form-input w-full" required />
    </div>
    <div>
      <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
      <input v-model="modelValue.end_date" id="end_date" type="datetime-local" class="form-input w-full" required />
    </div>
    <div>
      <label for="fix_rate" class="block text-sm font-medium text-gray-700 mb-1">Fix Rate</label>
      <input v-model="modelValue.fix_rate" id="fix_rate" type="text" class="form-input w-full" />
    </div>
    <div>
      <label for="rate_type" class="block text-sm font-medium text-gray-700 mb-1">Rate Type</label>
      <select v-model="modelValue.rate_type" id="rate_type" class="form-input w-full" required>
        <option value="fixed">Fixed</option>
        <option value="percentage">Percentage</option>
      </select>
    </div>
    <div>
      <label for="discount_percentage" class="block text-sm font-medium text-gray-700 mb-1">Discount Percentage</label>
      <input v-model="modelValue.discount_percentage" id="discount_percentage" type="number" step="0.01" class="form-input w-full" required />
    </div>
    <div>
      <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
      <textarea v-model="modelValue.description" id="description" class="form-input w-full"></textarea>
    </div>
    <div>
      <label for="is_active" class="block text-sm font-medium text-gray-700 mb-1">Active</label>
      <select v-model="modelValue.is_active" id="is_active" class="form-input w-full">
        <option :value="1">Yes</option>
        <option :value="0">No</option>
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
