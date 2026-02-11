<template>
  <div class="space-y-6">
    <FahadSelect search-route="/api/courses" @triggerChange="onCourseSelect" />
    <header class="mb-6">
      <h1 class="text-2xl font-semibold">Courses</h1>
      <p class="text-[#706f6c] dark:text-[#A1A09A] text-sm">Courses table from database</p>
    </header>
    <SimpleTable ref="tableRef"
      fetch-url="/api/courses"
      :columns="courseColumns"
      :page-sizes="[10, 25, 50, 100]"
      :per-page="25"
      :query-params="tableQueryParams"
      searchable
      odd-row-color="bg-white dark:bg-[#161615]"
      even-row-color="bg-stone-50 dark:bg-[#1a1a18]"
      hover-color="hover:bg-stone-200 dark:hover:bg-stone-700"
    />
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import FahadSelect from 'fahad-select';
import 'fahad-select/dist/style.css';
import SimpleTable from '@kikiloaw/simple-table';

const tableRef = ref(null);
const selectedCourseId = ref(null);

const onCourseSelect = (selected) => {
  selectedCourseId.value = selected?.id ?? null;
};

watch ([selectedCourseId], () => {
    tableRef.value.refresh?.();
})

const tableQueryParams = computed(() => {
  if (selectedCourseId.value == null) return {};
  return { CourseID: selectedCourseId.value };
});

const courseColumns = [
  { key: 'CourseID', label: 'ID', sortable: true, width: '80px' },
  { key: 'CourseCode', label: 'Course Code', sortable: true, width: '120px' },
  { key: 'Description', label: 'Description', sortable: true, width: '200px' },
  { key: 'Units', label: 'Units', sortable: true, width: '80px' },
  { key: 'LectureUnits', label: 'Lecture Units', sortable: true, width: '110px' },
  { key: 'LectureHours', label: 'Lecture Hours', sortable: true, width: '110px' },
  { key: 'LaboratoryHours', label: 'Lab Hours', sortable: true, width: '100px' },
  { key: 'LaboratoryUnits', label: 'Lab Units', sortable: true, width: '90px' },
  { key: 'CourseTypeID', label: 'Course Type', sortable: true, width: '100px' },
  { key: 'SchoolFeeTypeID', label: 'School Fee Type', sortable: true, width: '130px' },
  { key: 'CampusID', label: 'Campus ID', sortable: true, width: '90px' },
  { key: 'campus_name', label: 'Campus Name', sortable: true, width: '140px' },
  { key: 'status', label: 'Status', sortable: true, width: '100px' },
  { key: 'created_at', label: 'Created', sortable: true, width: '160px' },
];
</script>
