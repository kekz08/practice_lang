<template>
  <div
    :class="gridClass"
    class="gap-5.5 p-6.5 grid-auto-rows-[minmax(100px,_auto)]"
  >
    <FormFormatter
      :sampledata="sampledata"
      :form="form"
      :parameters="parameters"
      :position="position"
      @selectRefsReady="$emit('selectRefsReady', $event)"
      @triggerCallback="$emit('triggerCallback', $event)"
    />
  </div>
</template>

<script setup>
import { computed } from 'vue';
import FormFormatter from 'form-formatter';

const props = defineProps({
  /** Field configs: array of { type, model, label, ... } (see form-formatter README) */
  sampledata: {
    type: Array,
    required: true,
  },
  /** Reactive form data object (v-model / ref) */
  form: {
    type: Object,
    required: true,
  },
  /** Optional parameters for selects */
  parameters: {
    type: Object,
    default: () => ({}),
  },
  /** Grid columns: 1, 2, or 3 (matches form-formatter README layout) */
  gridCols: {
    type: Number,
    default: 1,
  },
  /** FormFormatter position prop */
  position: {
    type: Boolean,
    default: false,
  },
});

defineEmits(['selectRefsReady', 'triggerCallback']);

const gridClass = computed(() => {
  const n = props.gridCols;
  if (n === 2) return 'grid grid-cols-2';
  if (n === 3) return 'grid grid-cols-3';
  return 'grid grid-cols-1';
});
</script>
