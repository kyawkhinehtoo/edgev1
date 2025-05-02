<script setup>
defineProps({
  color: {
    type: String,
    required: true
  },
  text: {
    type: String,
    required: true
  }
})
</script>

<template>
  <span :class="`bg-${color}-100 text-${color}-800 text-sm font-medium px-2.5 py-0.5 rounded-sm dark:bg-${color}-700 dark:text-${color}-300 capitalize`">
    {{ text }}
  </span>
</template>