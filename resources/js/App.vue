<template>
  <div class="min-h-screen bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC] p-6">
    <header class="mb-6">
      <h1 class="text-2xl font-semibold">Classes</h1>
      <p class="text-[#706f6c] dark:text-[#A1A09A] text-sm">Classes table from database</p>
    </header>

    <SimpleTable
      fetch-url="/api/classes"
      :columns="columns"
      :page-sizes="[100, 200, 500, 1000]"
      :per-page="100"
      searchable
      odd-row-color="bg-white dark:bg-[#161615]"
      even-row-color="bg-stone-50 dark:bg-[#1a1a18]"
      hover-color="hover:bg-stone-200 dark:hover:bg-stone-700"
    />
  </div>
</template>

<script setup>
import SimpleTable from '@kikiloaw/simple-table'

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
]
</script>
