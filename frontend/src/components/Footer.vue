<script setup>
import { ref, onMounted } from "vue";
import api from "../utils/api";

const genres = ref([]);
const year = new Date().getFullYear();

onMounted(async () => {
  try {
    const res = await api.get("/public/films");
    const films = res.data.data.data;
    genres.value = [...new Set(films.map((f) => f.nama_genre))].sort();
  } catch (err) {
    console.error(err);
  }
});
</script>

<template>
  <footer class="footer">
    <div class="footer-inner">
      <div class="footer-brand">
        <span class="footer-logo">CineVue</span>
        <p>Temukan dan jelajahi film favoritmu.</p>
      </div>

      <div v-if="genres.length" class="footer-genres">
        <span class="footer-label">Genre</span>
        <div class="genre-list">
          <span v-for="genre in genres" :key="genre" class="genre-item">{{
            genre
          }}</span>
        </div>
      </div>
    </div>

    <div class="footer-bottom">
      <p>&copy; {{ year }} CineVue</p>
    </div>
  </footer>
</template>

<style scoped>
.footer {
  margin-top: 80px;
  border-top: 1px solid var(--color-dark5);
  background: var(--color-dark2);
}

.footer-inner {
  max-width: 1200px;
  margin: 0 auto;
  padding: 44px 24px 32px;
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 48px;
}

.footer-brand {
  flex-shrink: 0;
}

.footer-logo {
  font-size: 1.2rem;
  font-weight: 800;
  color: var(--color-white);
  letter-spacing: -0.02em;
}

.footer-brand p {
  margin-top: 8px;
  font-size: 13px;
  color: var(--color-gray-dark);
  line-height: 1.5;
  max-width: 240px;
}

.footer-genres {
  text-align: right;
}

.footer-label {
  display: block;
  font-size: 10px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  color: var(--color-gray);
  margin-bottom: 12px;
}

.genre-list {
  display: flex;
  flex-wrap: wrap;
  justify-content: flex-end;
  gap: 0;
  line-height: 1;
}

.genre-item {
  font-size: 13px;
  color: var(--color-gray-dark);
  padding: 4px 0;
}

.genre-item:not(:last-child)::after {
  content: "·";
  margin: 0 10px;
  color: var(--color-dark5);
}

.footer-bottom {
  border-top: 1px solid var(--color-dark5);
  padding: 18px 24px;
  text-align: center;
}

.footer-bottom p {
  font-size: 12px;
  color: var(--color-gray-dark);
  letter-spacing: 0.01em;
}

@media (max-width: 640px) {
  .footer {
    margin-top: 56px;
  }

  .footer-inner {
    flex-direction: column;
    gap: 28px;
    padding: 32px 20px 24px;
  }

  .footer-genres {
    text-align: left;
  }

  .genre-list {
    justify-content: flex-start;
  }

  .footer-brand p {
    max-width: none;
  }
}
</style>