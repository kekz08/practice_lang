<template>
  <div class="min-h-screen bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
    <nav class="border-b border-stone-200 dark:border-stone-700 bg-white dark:bg-[#161615] px-6 py-3">
      <div class="flex gap-4">
        <router-link
          to="/"
          class="text-[#706f6c] dark:text-[#A1A09A] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC] font-medium"
          active-class="!text-[#1b1b18] dark:!text-[#EDEDEC]"
        >
          Classes
        </router-link>
        <router-link
          to="/students"
          class="text-[#706f6c] dark:text-[#A1A09A] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC] font-medium"
          active-class="!text-[#1b1b18] dark:!text-[#EDEDEC]"
        >
          Students
        </router-link>
        <router-link
          to="/courses"
          class="text-[#706f6c] dark:text-[#A1A09A] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC] font-medium"
          active-class="!text-[#1b1b18] dark:!text-[#EDEDEC]"
        >
          Courses
        </router-link>
      </div>
    </nav>
    <main class="p-6">
      <router-view />
    </main>
  </div>
</template>

<script setup>
// Layout wrapper: nav + slot for the current page (router-view in parent)
</script>
