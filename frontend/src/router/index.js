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
  { path: "/", component: HomeView },
  { path: "/genre", component: GenreView },
  { path: "/film/:slug", component: DetailFilm },

  { path: "/login", component: LoginView },
  { path: "/register", component: RegisterView },

  { path: "/dashboard", component: DashboardView, meta: { admin: true } },
  { path: "/kelola-film", component: KelolaFilm, meta: { admin: true } },
  { path: "/tambah-film", component: TambahFilm, meta: { admin: true } },
  { path: "/edit-film/:id", component: EditFilm, meta: { admin: true } },
  { path: "/kelola-genre", component: KelolaGenre, meta: { admin: true } },
  { path: "/kelola-aktor", component: KelolaAktor, meta: { admin: true } },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;