<template>
    <div class="space-y-6">
        <FahadSelect
            class="flex-1 min-w-[200px]"
            placeholder="Search by Campus"
            search-route="/api/campus"
            @triggerChange="onCampusSelect" />
        <header class="mb-6">
            <h1 class="text-2xl font-semibold">Campus</h1>
            <p class="text-[#706f6c] dark:text-[#A1A09A] text-sm">Campus table from database</p>
        </header>
        <SimpleTable
            ref="tableRef"
            fetch-url="/api/campus"
            :columns="columns"
            :page-sizes="[100, 200, 500, 1000]"
            :per-page="100"
            :query-params="tableQueryParams"
            searchable
            odd-row-color="bg-white dark:bg-[#161615]"
            even-row-color="bg-stone-50 dark:bg-[#1a1a18]"
            hover-color="hover:bg-stone-200 dark:hover:bg-stone-700"
        />
    </div>
</template>

<script setup>
import {ref, computed, watch} from 'vue';
import FahadSelect from 'fahad-select';
import 'fahad-select/dist/style.css';
import SimpleTable from '@kikiloaw/simple-table';

const tableRef = ref(null);
const selectedCampusID = ref(null);
const onCampusSelect = (selected) => {
    selectedCampusID.value = selected?.id ?? null;
};

watch([selectedCampusID], () => {
    tableRef.value?.refresh?.();
})

const tableQueryParams = computed(() => {
    const params = {};
    if (selectedCampusID.value != null) params.CampusID = selectedCampusID.value;
    return params;
});

const columns = [
    { key: '#', label: 'ID', sortable: true, width: '80px', autonumber: true },
    { key: 'CampusCode', label: 'Campus Code', sortable: true, width: '100px' },
    { key: 'CampusName', label: 'Campus Name', sortable: true, width: '120px' },
    { key: 'Location', label: 'Location', sortable: true, width: '100px' },
    { key: 'CampusHead', label: 'CampusHead', sortable: true, width: '100px' },
    { key: 'OfficeCode', label: 'Office Code', sortable: true, width: '100px' },
    { key: 'status', label: 'Status', sortable: true, width: '100px' },
    { key: 'created_at', label: 'Created', sortable: true, width: '160px' },
    { key: 'action', label: 'Action', sortable: false, width: '100px' },
];
</script>
