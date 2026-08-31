<script setup>
import { ref, onMounted } from "vue";
import { RouterLink } from "vue-router";
import api from "../../utils/api";

const genres = ref([]);
const genrePage = ref(1);
const genreLastPage = ref(1);
const genreLoading = ref(false);
const selectedGenre = ref(null);
const selectedGenreName = ref("");

const films = ref([]);
const filmPage = ref(1);
const filmLastPage = ref(1);
const filmLoading = ref(false);
const filmInit = ref(false);

const ambilGenre = async () => {
  if (genreLoading.value || genrePage.value > genreLastPage.value) return;

  genreLoading.value = true;
  try {
    const res = await api.get("/public/genres", {
      params: { page: genrePage.value },
    });
    const paginated = res.data.data;
    genres.value = [...genres.value, ...paginated.data];
    genreLastPage.value = paginated.last_page;
    genrePage.value++;
  } catch (err) {
    console.error(err);
  } finally {
    genreLoading.value = false;
  }
};

const pilihGenre = async (genre) => {
  selectedGenre.value = genre.id;
  selectedGenreName.value = genre.nama_genre;
  films.value = [];
  filmPage.value = 1;
  filmLastPage.value = 1;
  filmInit.value = true;
  await ambilFilm();
};

const ambilFilm = async () => {
  if (filmLoading.value || filmPage.value > filmLastPage.value) return;

  filmLoading.value = true;
  try {
    const res = await api.get(`/public/genres/${selectedGenre.value}/films`, {
      params: { page: filmPage.value },
    });
    const paginated = res.data.data;
    films.value = [...films.value, ...paginated.data];
    filmLastPage.value = paginated.last_page;
    filmPage.value++;
  } catch (err) {
    console.error(err);
  } finally {
    filmLoading.value = false;
  }
};

onMounted(ambilGenre);
</script>

<template>
  <div class="container">
    <div class="page-head">
      <h1>Genre</h1>
      <p class="subtitle">Pilih genre untuk melihat daftar film</p>
    </div>

    <!-- Daftar Genre -->
    <div class="genre-section">
      <div class="genre-chips">
        <button
          v-for="g in genres"
          :key="g.id"
          class="chip"
          :class="{ active: selectedGenre === g.id }"
          @click="pilihGenre(g)"
        >
          {{ g.nama_genre }}
        </button>
      </div>

      <button
        v-if="genrePage <= genreLastPage && genres.length > 0"
        @click="ambilGenre"
        :disabled="genreLoading"
        class="btn-more"
      >
        <span v-if="genreLoading" class="btn-spin"></span>
        <span>{{ genreLoading ? "Memuat..." : "Muat Lebih Banyak" }}</span>
      </button>
    </div>

    <!-- Film per Genre -->
    <div v-if="selectedGenre" class="film-section">
      <div class="film-section-head">
        <h2>{{ selectedGenreName }}</h2>
        <span v-if="films.length > 0" class="film-count"
          >{{ films.length }} film</span
        >
      </div>

      <p
        v-if="filmInit && films.length === 0 && !filmLoading"
        class="empty-text"
      >
        Belum ada film untuk genre ini.
      </p>

      <div v-if="films.length > 0" class="film-grid">
        <RouterLink
          v-for="film in films"
          :key="film.id"
          :to="'/film/' + film.slug"
          class="film-card"
        >
          <div class="film-poster">
            <img :src="film.poster" :alt="film.judul_film" />
          </div>
          <div class="film-info">
            <h3>{{ film.judul_film }}</h3>
            <span class="film-meta"
              >{{ film.tahun_rilis }} &middot; {{ film.durasi }} mnt</span
            >
          </div>
        </RouterLink>
      </div>

      <button
        v-if="filmPage <= filmLastPage && films.length > 0"
        @click="ambilFilm"
        :disabled="filmLoading"
        class="btn-more"
      >
        <span v-if="filmLoading" class="btn-spin"></span>
        <span>{{ filmLoading ? "Memuat..." : "Muat Lebih Banyak" }}</span>
      </button>
    </div>

    <!-- Placeholder jika belum pilih genre -->
    <div v-else-if="genres.length > 0" class="placeholder">
      <svg
        width="48"
        height="48"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="1.5"
        stroke-linecap="round"
        stroke-linejoin="round"
        class="placeholder-icon"
      >
        <polygon points="23 7 16 12 23 17 23 7" />
        <rect x="1" y="5" width="15" height="14" rx="2" ry="2" />
      </svg>
      <p>Pilih genre di atas untuk melihat film</p>
    </div>
  </div>
</template>

<style scoped>
.page-head {
  margin-bottom: 28px;
}

.page-head h1 {
  font-size: 1.6rem;
  font-weight: 800;
  color: var(--color-white);
  letter-spacing: -0.02em;
}

.subtitle {
  margin-top: 4px;
  font-size: 14px;
  color: var(--color-gray-dark);
}

/* ── Genre Chips ─────────────────────────────────────────── */
.genre-section {
  margin-bottom: 36px;
}

.genre-chips {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
}

.chip {
  padding: 8px 18px;
  background: var(--color-dark3);
  border: 1px solid var(--color-dark5);
  border-radius: 100px;
  color: var(--color-text-dim);
  font-size: 13px;
  font-weight: 500;
  font-family: inherit;
  cursor: pointer;
  transition: all var(--transition);
  user-select: none;
}

.chip:hover {
  border-color: var(--color-gray-dark);
  color: var(--color-text);
}

.chip.active {
  background: rgba(233, 69, 96, 0.12);
  border-color: rgba(233, 69, 96, 0.35);
  color: var(--color-primary);
  font-weight: 600;
}

/* ── Muat Lebih Banyak ──────────────────────────────────── */
.btn-more {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  margin-top: 16px;
  padding: 10px 22px;
  background: var(--color-dark3);
  border: 1px solid var(--color-dark5);
  border-radius: var(--radius);
  color: var(--color-text-dim);
  font-size: 13px;
  font-weight: 600;
  font-family: inherit;
  cursor: pointer;
  transition: all var(--transition);
}

.btn-more:hover:not(:disabled) {
  border-color: var(--color-gray-dark);
  color: var(--color-text);
}

.btn-more:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.btn-spin {
  width: 14px;
  height: 14px;
  border: 2px solid rgba(136, 136, 160, 0.3);
  border-top-color: var(--color-gray);
  border-radius: 50%;
  animation: spin 0.6s linear infinite;
  flex-shrink: 0;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

/* ── Film Section ────────────────────────────────────────── */
.film-section {
  border-top: 1px solid var(--color-dark5);
  padding-top: 28px;
}

.film-section-head {
  display: flex;
  align-items: baseline;
  gap: 12px;
  margin-bottom: 24px;
}

.film-section-head h2 {
  font-size: 1.3rem;
  font-weight: 700;
  color: var(--color-white);
}

.film-count {
  font-size: 13px;
  color: var(--color-gray-dark);
}

.empty-text {
  color: var(--color-gray-dark);
  font-size: 14px;
  padding: 32px 0;
}

/* ── Film Grid ───────────────────────────────────────────── */
.film-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
  gap: 24px;
}

.film-card {
  display: flex;
  flex-direction: column;
  background: var(--color-dark3);
  border: 1px solid var(--color-dark5);
  border-radius: var(--radius-lg);
  overflow: hidden;
  text-decoration: none;
  transition: all var(--transition);
}

.film-card:hover {
  transform: translateY(-4px);
  box-shadow: var(--shadow-card);
  border-color: rgba(233, 69, 96, 0.25);
}

.film-poster {
  aspect-ratio: 2 / 3;
  overflow: hidden;
  background: var(--color-dark2);
}

.film-poster img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.4s ease;
}

.film-card:hover .film-poster img {
  transform: scale(1.06);
}

.film-info {
  padding: 14px;
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.film-info h3 {
  font-size: 14px;
  font-weight: 700;
  color: var(--color-white);
  line-height: 1.35;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.film-meta {
  font-size: 12px;
  color: var(--color-gray-dark);
}

/* ── Placeholder ────────────────────────────────────────── */
.placeholder {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 16px;
  padding: 80px 20px;
  color: var(--color-gray-dark);
  font-size: 15px;
}

.placeholder-icon {
  opacity: 0.25;
}

/* ── Responsive ──────────────────────────────────────────── */
@media (max-width: 640px) {
  .film-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 14px;
  }

  .film-info {
    padding: 10px;
  }

  .film-info h3 {
    font-size: 13px;
  }

  .film-meta {
    font-size: 11px;
  }
}
</style>
