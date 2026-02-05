<template>
  <div class="space-y-6">
      <header class="mb-6">
          <h1 class="text-2xl font-semibold">Students</h1>
          <p class="text-[#706f6c] dark:text-[#A1A09A] text-sm">Students table from database</p>
      </header>
    <div v-show="showFilters" class="flex flex-wrap items-end gap-4">
        <FahadSelect
            class="flex-1 min-w-[200px]"
            placeholder="Search by Student"
            search-route="/api/students"
            @triggerChange="onStudentSelect" />
        <FahadSelect
            class="flex-1 min-w-[200px]"
            placeholder="Search by Program"
            search-route="/api/programs"
            @triggerChange="onProgramSelect" />
        <FahadSelect
            class="flex-1 min-w-[200px]"
            placeholder="Search by Campus"
            search-route="/api/campus"
            @triggerChange="onCampusSelect" />
    </div>
    <SimpleTable
      ref="tableRef"
      fetch-url="/api/students"
      :columns="studentColumns"
      :page-sizes="[10, 20, 50, 100]"
      :per-page="10"
      :query-params="tableQueryParams"
      selectable
      searchable
      odd-row-color="bg-white dark:bg-[#161615]"
      even-row-color="bg-stone-50 dark:bg-[#1a1a18]"
      hover-color="hover:bg-stone-200 dark:hover:bg-stone-700"
    >
        <template #actions="{ rows }">
            <TableBatchActions :rows="rows" @export="handleExport" @bulk-delete="handleBulkDelete" @toggle-filters="showFilters = !showFilters" />
        </template>
        <template #cell-actions="{ row }">
            <TableActions
                :row="row"
                @edit="handleEdit"
                @delete="handleDelete"
                @view="handleView"
            />
        </template>
      <template #cell-status="{ row }">
        <span :class="statusClass(row.status)" class="font-semibo
        ld capitalize">
          {{ row.status }}
        </span>
      </template>
    </SimpleTable>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import axios from 'axios';
import FahadSelect from 'fahad-select';
import 'fahad-select/dist/style.css';
import SimpleTable from '@kikiloaw/simple-table';
import TableBatchActions from '../components/TableBatchActions.vue';
import TableActions from "@/components/TableActions.vue";

const tableRef = ref(null);
const showFilters = ref(false);
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
    { key: 'actions', label: 'Actions', sortable: false, width: '120px'}
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

const handleExport = (type, rows) => {
    console.log(`Exporting ${rows.length} rows as ${type}`)

    if (type === 'csv') {
        const csv = rows.map(row =>
            `${row.StudentID},${row.FirstName},${row.LastName},${row.Email},${row.campus_name},${row.program_name}`
        ).join('\n')

        downloadCSV(csv, 'export.csv')
    }
}

const handleBulkDelete = (rows) => {
    const ids = rows.map(r => r.StudentID)
    if (confirm(`Delete ${ids.length} items?`)) {
        axios.delete('/api/bulk-delete', { data: { ids } })
            .then(() => {
                tableRef.value?.refresh?.();
            })
            .catch(error => {
                console.error('Bulk delete failed:', error);
                alert('Failed to delete items');
            });
    }
}

const handleEdit = (row) => {
    console.log('Editing student:', row);
    alert(`Edit student: ${row.FirstName} ${row.LastName}`);
};

const handleDelete = (row) => {
    if (confirm(`Are you sure you want to delete ${row.FirstName} ${row.LastName}?`)) {
        axios.delete(`/api/students/${row.StudentID}`)
            .then(() => {
                tableRef.value?.refresh?.();
            })
            .catch(error => {
                console.error('Delete failed:', error);
                alert('Failed to delete student');
            });
    }
};

const handleView = (row) => {
    console.log('Viewing student:', row);
    alert(`Viewing student: ${row.FirstName} ${row.LastName}`);
};

const downloadCSV = (content, filename) => {
    const blob = new Blob([content], { type: 'text/csv' })
    const url = URL.createObjectURL(blob)
    const a = document.createElement('a')
    a.href = url
    a.download = filename
    a.click()
}
</script>
