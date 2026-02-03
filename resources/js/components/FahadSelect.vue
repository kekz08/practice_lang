<template>
    <div>
        <header v-if="title || subtitle" class="mb-6">
            <h1 v-if="title" class="text-2xl font-semibold">{{ title }}</h1>
            <p v-if="subtitle" class="text-[#706f6c] dark:text-[#A1A09A] text-sm">{{ subtitle }}</p>
        </header>

        <FahadSelectInput
            ref="selectRef"
            v-model="selected"
            :searchRoute="searchRoute"
            selectionColor="#e0e0e0"
            optionHoverColor="#2196f3"
            optionSelectedColor="#1976d2"
            v-bind="$attrs"
        />
    </div>
</template>

<script setup>
import { ref } from 'vue';
import FahadSelectInput from 'fahad-select';
import 'fahad-select/dist/style.css';

defineProps({
    title: { type: String, default: '' },
    subtitle: { type: String, default: '' },
    searchRoute: { type: String, default: '/api/users' }
});

const selected = ref(null);
const selectRef = ref(null);

// Optional parameters to send along with the API request
const optionalParams = { key: 'value' };

// Reload the select normally used for cascaded select
const reloadSelect = () => {
    if (selectRef.value) {
        selectRef.value.reload();
    }
};

defineExpose({
    reload: reloadSelect,
    selected
});
</script>
