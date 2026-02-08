<template>
  <Teleport to="body">
    <div
      v-if="modelValue"
      class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-black/50"
      @click.self="$emit('update:modelValue', false)"
    >
      <div class="relative w-full max-w-md mx-4 bg-white dark:bg-[#1a1a18] rounded-lg shadow-xl">
        <div class="p-6">
          <h3 class="text-lg font-semibold text-stone-900 dark:text-stone-100">
            {{ title }}
          </h3>
          <p class="mt-2 text-sm text-stone-600 dark:text-stone-400">
            {{ message }}
          </p>
        </div>
        <div class="flex justify-end gap-2 px-6 py-4 border-t border-stone-200 dark:border-stone-700 bg-stone-50 dark:bg-stone-900/50 rounded-b-lg">
          <button
            type="button"
            @click="$emit('update:modelValue', false)"
            class="px-4 py-2 text-sm font-medium text-stone-700 bg-white border border-stone-300 rounded-md hover:bg-stone-50 dark:bg-stone-800 dark:text-stone-200 dark:border-stone-600"
          >
            Cancel
          </button>
          <button
            type="button"
            :class="[
              'px-4 py-2 text-sm font-medium text-white rounded-md',
              variant === 'danger'
                ? 'bg-red-600 hover:bg-red-700'
                : 'bg-blue-600 hover:bg-blue-700',
            ]"
            @click="onConfirm"
          >
            {{ confirmText }}
          </button>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
defineProps({
  modelValue: { type: Boolean, default: false },
  title: { type: String, default: 'Confirm' },
  message: { type: String, default: '' },
  confirmText: { type: String, default: 'Confirm' },
  variant: { type: String, default: 'danger' }, // 'danger' | 'primary'
});

const emit = defineEmits(['update:modelValue', 'confirm']);

const onConfirm = () => {
  emit('confirm');
  emit('update:modelValue', false);
};
</script>
