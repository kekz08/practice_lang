<template>
  <div>
    <header class="mb-6">
      <h1 class="text-2xl font-semibold">{{ title }}</h1>
      <p class="text-[#706f6c] dark:text-[#A1A09A] text-sm">{{ subtitle }}</p>
    </header>

    <SimpleTable
      :fetch-url="fetchUrl"
      :columns="columns"
      :page-sizes="pageSizes"
      :per-page="perPage"
      searchable
      odd-row-color="bg-white dark:bg-[#161615]"
      even-row-color="bg-stone-50 dark:bg-[#1a1a18]"
      hover-color="hover:bg-stone-200 dark:hover:bg-stone-700"
    >
      <template v-for="(_, name) in $slots" #[name]="slotData">
        <slot :name="name" v-bind="slotData || {}" />
      </template>
    </SimpleTable>
  </div>
</template>

<script setup>
import SimpleTable from '@kikiloaw/simple-table';

defineProps({
  title: { type: String, required: true },
  subtitle: { type: String, default: '' },
  fetchUrl: { type: String, required: true },
  columns: { type: Array, required: true },
  pageSizes: { type: Array, default: () => [100, 200, 500, 1000] },
  perPage: { type: Number, default: 100 },
});
</script>
