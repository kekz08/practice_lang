<template>
  <div class="space-y-6">
    <FahadSelect search-route="/api/courses" @trigger-change="onCourseSelect" />
    <DataTablePage
      title="Courses"
      subtitle="Courses table from database"
      fetch-url="/api/courses"
      :columns="courseColumns"
      :page-sizes="[10, 25, 50, 100]"
      :per-page="25"
      :query-params="tableQueryParams"
    />
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import DataTablePage from '../components/DataTablePage.vue';
import FahadSelect from '../components/FahadSelect.vue';

const selectedCourseId = ref(null);

const onCourseSelect = (selected) => {
  selectedCourseId.value = selected?.id ?? null;
};

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
