import { createRouter, createWebHistory } from 'vue-router';
import AppLayout from '../components/AppLayout.vue';
import ClassesPage from '../pages/ClassesPage.vue';
import CoursesPage from '../pages/CoursesPage.vue';
import FormPage from '../pages/FormPage.vue';
import ProgramsPage from '../pages/ProgramsPage.vue';
import StudentsPage from '../pages/StudentsPage.vue';
import CampusPage from "@/pages/CampusPage.vue";

const routes = [
  {
    path: '/',
    component: AppLayout,
    children: [
      { path: '', name: 'classes', component: ClassesPage },
      { path: 'students', name: 'students', component: StudentsPage },
      { path: 'courses', name: 'courses', component: CoursesPage },
      { path: 'programs', name: 'programs', component: ProgramsPage },
      { path: 'form', name: 'form', component: FormPage },
        { path: 'campus', name: 'campus', component: CampusPage },
    ],
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;
