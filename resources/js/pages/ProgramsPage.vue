<template>
    <DataTablePage
        title="Classes"
        subtitle="Classes table from database"
        fetch-url="/api/programs"
        :columns="columns"
        :page-sizes="[100, 200, 500, 1000]"
        :per-page="100"
    />
</template>

<script setup>
import DataTablePage from '../components/DataTablePage.vue';

const columns = [
    { key: '#', label: 'ID', sortable: true, width: '80px', autonumber: true, },
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
