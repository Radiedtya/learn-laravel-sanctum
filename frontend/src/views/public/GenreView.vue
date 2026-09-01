<script setup>
import { ref, onMounted } from "vue";
import { RouterLink } from "vue-router";
import api from "../../utils/api";

const genres = ref([]);
const genrePage = ref(1);
const genreLastPage = ref(1);
const genreLoading = ref(false);
const genreInit = ref(true);
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
    genreInit.value = false;
  }
};

const pilihGenre = async (genre) => {
  selectedGenre.value = genre.id;
  selectedGenreName.value = genre.nama_genre;
  films.value = [];
  filmPage.value = 1;
  filmLastPage.value = 1;
  filmInit.value = true;
  // filmLoading.value = true; // bikin looping jir loadingnya
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

    <!-- Genre Chips -->
    <div class="genre-section">
      <!-- Skeleton chips -->
      <div v-if="genreInit" class="genre-chips">
        <div v-for="i in 8" :key="i" class="skel skel-chip"></div>
      </div>

      <!-- Real chips -->
      <template v-else>
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
      </template>
    </div>

    <!-- Film per Genre -->
    <div v-if="selectedGenre" class="film-section">
      <div class="film-section-head">
        <h2>{{ selectedGenreName }}</h2>
        <span v-if="films.length > 0" class="film-count"
          >{{ films.length }} film</span
        >
      </div>

      <!-- Skeleton grid -->
      <div
        v-if="filmLoading && films.length === 0"
        class="film-grid"
      >
        <div v-for="i in 8" :key="i" class="film-card">
          <div class="skel skel-poster"></div>
          <div class="film-info">
            <div class="skel skel-line" style="width: 80%; height: 14px"></div>
            <div class="skel skel-line" style="width: 50%; height: 12px"></div>
          </div>
        </div>
      </div>

      <!-- Empty -->
      <p
        v-else-if="!filmLoading && films.length === 0"
        class="empty-text"
      >
        Belum ada film untuk genre ini.
      </p>

      <!-- Real grid -->
      <div v-if="films.length > 0" class="film-grid">
        <RouterLink
          v-for="film in films"
          :key="film.id"
          :to="'/film/' + film.slug"
          class="film-card"
        >
          <div class="film-poster">
            <img :src="film.poster" :alt="film.judul_film" />
            <div class="film-overlay">
              <span class="film-overlay-text">Lihat Detail</span>
            </div>
          </div>
          <div class="film-info">
            <h3>{{ film.judul_film }}</h3>
            <span class="film-meta"
              >{{ film.tahun_rilis }} &middot; {{ film.durasi }} mnt</span
            >
          </div>
        </RouterLink>
      </div>

      <!-- Load more films -->
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

    <!-- Placeholder -->
    <div v-else-if="!genreInit && genres.length > 0" class="placeholder">
      <i class="pi pi-film" style="font-size: 44px; opacity: 0.15"></i>
      <p>Pilih genre di atas untuk melihat film</p>
    </div>
  </div>
</template>

<style scoped>
/* ── Skeleton ──────────────────────────────────────── */
.skel {
  position: relative;
  overflow: hidden;
  background: var(--color-dark3);
  border-radius: var(--radius);
}

.skel::after {
  content: "";
  position: absolute;
  inset: 0;
  background: linear-gradient(
    90deg,
    transparent 0%,
    rgba(255, 255, 255, 0.035) 35%,
    rgba(255, 255, 255, 0.07) 50%,
    rgba(255, 255, 255, 0.035) 65%,
    transparent 100%
  );
  animation: shimmer 1.6s ease-in-out infinite;
}

@keyframes shimmer {
  0% {
    transform: translateX(-100%);
  }
  100% {
    transform: translateX(100%);
  }
}

.skel-chip {
  width: 90px;
  height: 36px;
  border-radius: 100px;
}

.skel-poster {
  aspect-ratio: 2 / 3;
  border-radius: 0;
}

.skel-line {
  border-radius: 6px;
}

/* ── Page Head ─────────────────────────────────────── */
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

/* ── Genre Chips ───────────────────────────────────── */
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
  transition: all 0.2s;
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

/* ── Load More ─────────────────────────────────────── */
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
  transition: all 0.2s;
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

/* ── Film Section ──────────────────────────────────── */
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

/* ── Film Grid ─────────────────────────────────────── */
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
  transition: all 0.2s;
}

.film-card:hover {
  transform: translateY(-4px);
  box-shadow: var(--shadow-card), var(--shadow-glow);
  border-color: rgba(233, 69, 96, 0.25);
}

.film-poster {
  position: relative;
  aspect-ratio: 2 / 3;
  overflow: hidden;
  background: var(--color-dark2);
}

.film-poster img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.4s ease, filter 0.4s ease;
}

.film-card:hover .film-poster img {
  transform: scale(1.06);
  filter: brightness(0.45);
}

.film-overlay {
  position: absolute;
  inset: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  transition: opacity 0.2s;
}

.film-card:hover .film-overlay {
  opacity: 1;
}

.film-overlay-text {
  padding: 9px 18px;
  font-size: 12px;
  font-weight: 600;
  color: white;
  background: rgba(233, 69, 96, 0.9);
  border-radius: var(--radius);
  backdrop-filter: blur(8px);
  border: 1px solid rgba(255, 255, 255, 0.12);
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

/* ── Placeholder ──────────────────────────────────── */
.placeholder {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 16px;
  padding: 80px 20px;
  color: var(--color-gray-dark);
  font-size: 15px;
}

/* ── Responsive ────────────────────────────────────── */
@media (max-width: 640px) {
  .skel-chip {
    width: 72px;
    height: 32px;
  }

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