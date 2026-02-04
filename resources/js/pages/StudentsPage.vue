<template>
  <div class="space-y-6">
    <FahadSelect
        placeholder="Search by Student ID"
        search-route="/api/students"
        @triggerChange="onStudentSelect" />
    <FahadSelect
        placeholder="Search by Program"
        search-route="/api/programs"
        @triggerChange="onProgramSelect" />
      <FahadSelect
          placeholder="Search by Campus"
          search-route="/api/campus"
          @triggerChange="onCampusSelect" />
    <header class="mb-6">
      <h1 class="text-2xl font-semibold">Students</h1>
      <p class="text-[#706f6c] dark:text-[#A1A09A] text-sm">Students table from database</p>
    </header>
    <SimpleTable
      ref="tableRef"
      fetch-url="/api/students"
      :columns="studentColumns"
      :page-sizes="[100, 200, 500, 1000]"
      :per-page="100"
      :query-params="tableQueryParams"
      searchable
      odd-row-color="bg-white dark:bg-[#161615]"
      even-row-color="bg-stone-50 dark:bg-[#1a1a18]"
      hover-color="hover:bg-stone-200 dark:hover:bg-stone-700"
    >
      <template #cell-status="{ row }">
        <span :class="statusClass(row.status)" class="font-semibold capitalize">
          {{ row.status }}
        </span>
      </template>
    </SimpleTable>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import FahadSelect from 'fahad-select';
import 'fahad-select/dist/style.css';
import SimpleTable from '@kikiloaw/simple-table';

const tableRef = ref(null);
tableRef.value = undefined;
const selectedStudentId = ref(null);
const selectedProgramId = ref(null);
const selectedCampusID = ref(null);

const onStudentSelect = (selected) => {
  selectedStudentId.value = selected?.id ?? null;
};

const onProgramSelect = (selected) => {
  selectedProgramId.value = selected?.id ?? null;
};

const onCampusSelect = (selected) => {
    selectedCampusID.value = selected?.id ?? null;
}

watch([selectedStudentId, selectedProgramId, selectedCampusID], () => {
  tableRef.value?.refresh?.();
});

const tableQueryParams = computed(() => {
  const params = {};
  if (selectedStudentId.value != null) params.StudentID = selectedStudentId.value;
  if (selectedProgramId.value != null) params.ProgramID = selectedProgramId.value;
  if (selectedCampusID.value != null) params.CampusID = selectedCampusID.value;
  return params;
});

const studentColumns = [
  { key: '#', label: 'ID', sortable: true, width: '80px', autonumber: true },
  { key: 'StudentYear', label: 'Year', sortable: true, width: '80px' },
  { key: 'FirstName', label: 'First Name', sortable: true, width: '120px' },
  { key: 'MiddleName', label: 'Middle Name', sortable: true, width: '120px' },
  { key: 'LastName', label: 'Last Name', sortable: true, width: '120px' },
  { key: 'Email', label: 'Email', sortable: true, width: '180px' },
  { key: 'PhoneNumber', label: 'Phone', sortable: true, width: '120px' },
  { key: 'Gender', label: 'Gender', sortable: true, width: '90px' },
  { key: 'YearLevel', label: 'Year Level', sortable: true, width: '90px' },
  { key: 'program_name', label: 'Program Name', sortable: true, width: '120px' },
  { key: 'campus_name', label: 'Campus', sortable: true, width: '140px' },
  { key: 'status', label: 'Status', sortable: true, width: '100px' },
  { key: 'created_at', label: 'Created', sortable: true, width: '160px' },
];

const statusClass = (status) => {
  status = status?.toLowerCase();
  return {
    active: 'text-green-500',
    inactive: 'text-gray-500',
    locked: 'text-red-600',
    unlocked: 'text-green-600',
    filled: 'text-red-500',
    'pre-enroll': 'text-yellow-500',
  }[status] || 'text-gray-400';
};
</script>
