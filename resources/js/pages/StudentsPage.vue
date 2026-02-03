<template>
  <div class="space-y-6">
    <FahadSelect search-route="/api/students" @trigger-change="onStudentSelect" />
    <DataTablePage
      title="Students"
      subtitle="Students table from database"
      fetch-url="/api/students"
      :columns="studentColumns"
      :page-sizes="[100, 200, 500, 1000]"
      :per-page="100"
      :query-params="tableQueryParams"
    >
      <template #cell-status="{ row }">
        <span
          :class="statusClass(row.status)"
          class="font-semibold capitalize"
        >
          {{ row.status }}
        </span>
      </template>
    </DataTablePage>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import DataTablePage from '../components/DataTablePage.vue';
import FahadSelect from '../components/FahadSelect.vue';

const selectedStudentId = ref(null);

const onStudentSelect = (selected) => {
  selectedStudentId.value = selected?.id ?? null;
};

const tableQueryParams = computed(() => {
  if (selectedStudentId.value == null) return {};
  return { StudentID: selectedStudentId.value };
});

const studentColumns = [
  { key: '#', label: 'ID', sortable: true, width: '80px', autonumber: true, },
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
