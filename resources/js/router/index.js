import { createRouter, createWebHistory } from 'vue-router';
import AppLayout from '../components/AppLayout.vue';
import ClassesPage from '../pages/Classes/ClassesPage.vue';
import CoursesPage from '../pages/Courses/CoursesPage.vue';
import FormPage from '../pages/Forms/FormPage.vue';
import ProgramsPage from '../pages/Programs/ProgramsPage.vue';
import StudentsPage from '../pages/Students/StudentsPage.vue';
import CampusPage from "@/pages/Campus/CampusPage.vue";

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
