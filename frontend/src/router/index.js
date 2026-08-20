import { createRouter, createWebHistory } from "vue-router";
import HomeView from "../views/HomeView.vue";
import DetailFilm from "../views/DetailFilm.vue";

const routes = [
  { path: "/", component: HomeView },
  { path: "/film/:slug", component: DetailFilm },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;
