import { createRouter, createWebHistory } from "vue-router";

import HomeView from "../views/public/HomeView.vue";
import GenreView from "../views/public/GenreView.vue";
import DetailFilm from "../views/public/DetailFilm.vue";

import LoginView from "../views/auth/LoginView.vue";
import RegisterView from "../views/auth/RegisterView.vue";

import DashboardView from "../views/admin/DashboardView.vue";

import KelolaFilm from "../views/admin/film/KelolaFilm.vue";
import TambahFilm from "../views/admin/film/TambahFilm.vue";
import EditFilm from "../views/admin/film/EditFilm.vue";

import KelolaGenre from "../views/admin/genre/KelolaGenre.vue";
import KelolaAktor from "../views/admin/aktor/KelolaAktor.vue";

const routes = [
  // public
  { path: "/", component: HomeView },
  { path: "/genre", component: GenreView },
  { path: "/film/:slug", component: DetailFilm },
  
  { path: "/login", component: LoginView },
  { path: "/register", component: RegisterView },

  // admin
  { path: "/dashboard", component: DashboardView },
  { path: "/kelola-film", component: KelolaFilm },
  { path: "/tambah-film", component: TambahFilm },
  { path: "/edit-film/:id", component: EditFilm },
  { path: "/kelola-genre", component: KelolaGenre },
  { path: "/kelola-aktor", component: KelolaAktor },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;
