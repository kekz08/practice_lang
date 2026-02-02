<template>
  <DataTablePage
    title="Students"
    subtitle="Students table from database"
    fetch-url="/api/students"
    :columns="studentColumns"
    :page-sizes="[10, 25, 50, 100]"
    :per-page="25"
  />
</template>

<script setup>
import DataTablePage from '../components/DataTablePage.vue';

const studentColumns = [
  { key: 'StudentID', label: 'ID', sortable: true, width: '80px' },
  { key: 'StudentYear', label: 'Year', sortable: true, width: '80px' },
  { key: 'FirstName', label: 'First Name', sortable: true, width: '120px' },
  { key: 'MiddleName', label: 'Middle Name', sortable: true, width: '120px' },
  { key: 'LastName', label: 'Last Name', sortable: true, width: '120px' },
  { key: 'Email', label: 'Email', sortable: true, width: '180px' },
  { key: 'PhoneNumber', label: 'Phone', sortable: true, width: '120px' },
  { key: 'Gender', label: 'Gender', sortable: true, width: '90px' },
  { key: 'YearLevel', label: 'Year Level', sortable: true, width: '90px' },
  { key: 'course_code', label: 'Course', sortable: true, width: '120px' },
  { key: 'campus_name', label: 'Campus', sortable: true, width: '140px' },
  { key: 'status', label: 'Status', sortable: true, width: '100px' },
  { key: 'created_at', label: 'Created', sortable: true, width: '160px' },
];
</script>
