<template>
  <DataTablePage
    title="Classes"
    subtitle="Classes table from database"
    fetch-url="/api/classes"
    :columns="columns"
    :page-sizes="[100, 200, 500, 1000]"
    :per-page="100"
  />
</template>

<script setup>
import DataTablePage from '../components/DataTablePage.vue';

const columns = [
  { key: 'ClassID', label: 'ID', sortable: true, width: '80px' },
  { key: 'ClassCode', label: 'Class Code', sortable: true, width: '100px' },
  { key: 'AYFrom', label: 'AY From', sortable: true, width: '90px' },
  { key: 'AYTo', label: 'AY To', sortable: true, width: '90px' },
  { key: 'Semester', label: 'Semester', sortable: true, width: '90px' },
  { key: 'SectionCode', label: 'Section', sortable: true, width: '100px' },
  { key: 'MaxSize', label: 'Max Size', sortable: true, width: '90px' },
  { key: 'Size', label: 'Size', sortable: true, width: '80px' },
  { key: 'status', label: 'Status', sortable: true, width: '100px' },
  { key: 'CourseID', label: 'Course ID', sortable: true, width: '90px' },
  { key: 'EmployeeID', label: 'Employee ID', sortable: true, width: '110px' },
  { key: 'ProgramID', label: 'Program ID', sortable: true, width: '100px' },
  { key: 'CurriculumID', label: 'Curriculum ID', sortable: true, width: '120px' },
  { key: 'ClassTypeID', label: 'Class Type', sortable: true, width: '100px' },
  { key: 'CampusID', label: 'Campus', sortable: true, width: '90px' },
  { key: 'created_at', label: 'Created', sortable: true, width: '160px' },
];
</script>
