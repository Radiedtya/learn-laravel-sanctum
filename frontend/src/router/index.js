import { createRouter, createWebHistory } from "vue-router";
import HomeView from "../views/HomeView.vue";
import DetailFilm from "../views/DetailFilm.vue";

import LoginView from "../views/LoginView.vue";
import RegisterView from "../views/RegisterView.vue";

import DashboardView from "../views/DashboardView.vue";
import TambahFilm from "../views/TambahFilm.vue";
import KelolaFilm from "../views/KelolaFilm.vue";

const routes = [
  { path: "/", component: HomeView },
  { path: "/login", component: LoginView },
  { path: "/register", component: RegisterView },

  { path: "/dashboard", component: DashboardView },
  { path: "/tambah-film", component: TambahFilm },
  { path: "/kelola-film", component: KelolaFilm },
  
  { path: "/film/:slug", component: DetailFilm },

  
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;
