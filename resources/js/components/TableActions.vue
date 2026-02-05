<template>
    <div class="flex gap-2">
        <button
            @click="$emit('edit', row)"
            class="p-1.5 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors"
            title="Edit"
        >
            <Pencil class="w-4 h-4" />
        </button>
        <button
            @click="$emit('delete', row)"
            class="p-1.5 bg-red-500 text-white rounded hover:bg-red-600 transition-colors"
            title="Delete"
        >
            <Trash class="w-4 h-4" />
        </button>
        <button
            @click="$emit('view', row)"
            class="p-1.5 bg-green-500 text-white rounded hover:bg-green-600 transition-colors"
            title="View"
        >
            <Eye class="w-4 h-4" />
        </button>
    </div>
</template>

<script setup>
import { Pencil, Trash, Eye } from 'lucide-vue-next';

defineProps({
    row: {
        type: Object,
        required: true
    }
});

defineEmits(['edit', 'delete', 'view']);
</script>
