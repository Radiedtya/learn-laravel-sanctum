<script setup>
import { computed } from "vue";
import { useRoute } from "vue-router";
import NavBar from "./components/NavBar.vue";
import AdminLayout from "./components/AdminLayout.vue";
import Footer from "./components/Footer.vue";

const route = useRoute();
const isAdmin = computed(() => route.meta.admin === true);
</script>

<template>
  <AdminLayout v-if="isAdmin" />
  <div v-else class="pub">
    <NavBar />
    <main class="pub-main">
      <RouterView />
    </main>
    <Footer />
  </div>
</template>

<style scoped>
.pub {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

.pub-main {
  flex: 1;
  margin-top: 32px;
}
</style>
