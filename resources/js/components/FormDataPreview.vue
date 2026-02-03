<template>
  <div
    v-if="formData && Object.keys(formData).length"
    class="max-w-2xl p-4 rounded-lg bg-stone-100 dark:bg-stone-800 text-sm"
  >
    <strong>Form data:</strong>
    <pre class="mt-2 overflow-auto">{{ JSON.stringify(formData, null, 2) }}</pre>
  </div>
</template>

<script setup>
defineProps({
  formData: {
    type: Object,
    default: () => ({}),
  },
});
</script>
