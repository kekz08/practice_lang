import { createRouter, createWebHistory } from 'vue-router';
import AppLayout from '../components/AppLayout.vue';
import ClassesPage from '../pages/ClassesPage.vue';
import CoursesPage from '../pages/CoursesPage.vue';
import StudentsPage from '../pages/StudentsPage.vue';

const routes = [
  {
    path: '/',
    component: AppLayout,
    children: [
      { path: '', name: 'classes', component: ClassesPage },
      { path: 'students', name: 'students', component: StudentsPage },
      { path: 'courses', name: 'courses', component: CoursesPage },
    ],
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;
