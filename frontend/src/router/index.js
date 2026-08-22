import { createRouter, createWebHistory } from "vue-router";
import HomeView from "../views/HomeView.vue";
import GenreView from "../views/GenreView.vue";
import DetailFilm from "../views/DetailFilm.vue";

import LoginView from "../views/LoginView.vue";
import RegisterView from "../views/RegisterView.vue";

import DashboardView from "../views/DashboardView.vue";
import TambahFilm from "../views/TambahFilm.vue";
import EditFilm from "../views/EditFilm.vue";
import KelolaFilm from "../views/KelolaFilm.vue";


const routes = [
  { path: "/", component: HomeView },
  { path: "/genre", component: GenreView },
  { path: "/login", component: LoginView },
  { path: "/register", component: RegisterView },

  { path: "/dashboard", component: DashboardView },

  { path: "/kelola-film", component: KelolaFilm },
  { path: "/tambah-film", component: TambahFilm },
  { path: "/film/:slug", component: DetailFilm },
  { path: "/edit-film/:id", component: EditFilm },

  
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;
