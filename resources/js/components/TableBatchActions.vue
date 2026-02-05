<template>
    <div class="flex gap-2">
        <button
            @click="$emit('export', 'csv', rows)"
            class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium transition-colors bg-white border rounded-md shadow-sm border-stone-200 hover:bg-stone-50 focus:outline-none focus:ring-2 focus:ring-stone-500 focus:ring-offset-2 dark:bg-[#161615] dark:border-stone-700 dark:hover:bg-stone-800 dark:text-[#EDEDEC]"
        >
            <Download class="mr-2 h-4 w-4" />
            Export ({{ rows.length }})
        </button>

        <button
            @click="$emit('export', 'excel', rows)"
            class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium transition-colors bg-white border rounded-md shadow-sm border-stone-200 hover:bg-stone-50 focus:outline-none focus:ring-2 focus:ring-stone-500 focus:ring-offset-2 dark:bg-[#161615] dark:border-stone-700 dark:hover:bg-stone-800 dark:text-[#EDEDEC]"
        >
            <Download class="mr-2 h-4 w-4" />
            Excel
        </button>

        <button
            @click="$emit('bulk-delete', rows)"
            class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white transition-colors bg-red-600 rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:bg-red-700 dark:hover:bg-red-800"
        >
            <Trash class="mr-2 h-4 w-4" />
            Bulk Delete
        </button>
    </div>
</template>

<script setup>
import { Download, Trash } from 'lucide-vue-next';

defineProps({
    rows: {
        type: Array,
        required: true
    }
});

defineEmits(['export', 'bulk-delete']);
</script>
