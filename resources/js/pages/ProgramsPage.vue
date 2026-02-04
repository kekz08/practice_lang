<template>
  <div class="space-y-6">
    <FahadSelect
        placeholder="Select Program"
        search-route="/api/programs"
        @triggerChange="onProgramSelect" />
    <header class="mb-6">
      <h1 class="text-2xl font-semibold">Programs</h1>
      <p class="text-[#706f6c] dark:text-[#A1A09A] text-sm">Programs table from database</p>
    </header>
    <SimpleTable
        ref="tableRef"
      fetch-url="/api/programs"
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

const selectedProgramId = ref(null);
const tableRef = ref(null);
tableRef.value = undefined;
const onProgramSelect = (selected) => {
  selectedProgramId.value = selected?.id ?? null;
};

watch ([selectedProgramId], () => {
    tableRef.value?.refresh?.();
})

const tableQueryParams = computed(() => {
  if (selectedProgramId.value == null) return {};
  return { ProgramID: selectedProgramId.value };
});

const columns = [
  { key: '#', label: 'ID', sortable: true, width: '80px', autonumber: true },
  { key: 'ProgramCode', label: 'Program Code', sortable: true, width: '100px' },
  { key: 'ProgramName', label: 'Program Name', sortable: true, width: '90px' },
  { key: 'UnitID', label: 'Unit ID', sortable: true, width: '90px' },
  { key: 'Major', label: 'Major', sortable: true, width: '90px' },
  { key: 'Minor', label: 'Minor', sortable: true, width: '100px' },
  { key: 'ProgramTypeID', label: 'Program TypeID', sortable: true, width: '90px' },
  { key: 'ProgramChair', label: 'Program Chair', sortable: true, width: '80px' },
  { key: 'C1', label: 'C1', sortable: true, width: '100px' },
  { key: 'C2', label: 'C2', sortable: true, width: '90px' },
  { key: 'C3', label: 'C3', sortable: true, width: '110px' },
  { key: 'parent', label: 'Parent', sortable: true, width: '100px' },
  { key: 'child', label: 'Child', sortable: true, width: '120px' },
  { key: 'order', label: 'Order', sortable: true, width: '100px' },
  { key: 'YearOffered', label: 'Year Offered', sortable: true, width: '90px' },
  { key: 'created_by', label: 'Created By', sortable: true, width: '160px' },
  { key: 'created_at', label: 'Created', sortable: true, width: '160px' },
];
</script>
