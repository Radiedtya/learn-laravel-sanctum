<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";
import { RouterLink } from "vue-router";
import api from "../../utils/api";

const films = ref([]);
const loading = ref(true);
const error = ref(null);
const keyword = ref("");
const currentSlide = ref(0);
const isPaused = ref(false);
let autoPlayInterval = null;
let searchTimeout = null;

const latestFilms = computed(() => {
  if (!films.value.length) return [];
  return [...films.value]
    .sort((a, b) => b.tahun_rilis - a.tahun_rilis)
    .slice(0, 3);
});

const showHero = computed(() => {
  return latestFilms.value.length > 0 && keyword.value.trim() === "";
});

const nextSlide = () => {
  if (latestFilms.value.length <= 1) return;
  currentSlide.value = (currentSlide.value + 1) % latestFilms.value.length;
};

const prevSlide = () => {
  if (latestFilms.value.length <= 1) return;
  currentSlide.value =
    (currentSlide.value - 1 + latestFilms.value.length) %
    latestFilms.value.length;
};

const goToSlide = (index) => {
  currentSlide.value = index;
  resetAutoPlay();
};

const startAutoPlay = () => {
  stopAutoPlay();
  autoPlayInterval = setInterval(nextSlide, 5000);
};

const stopAutoPlay = () => {
  if (autoPlayInterval) {
    clearInterval(autoPlayInterval);
    autoPlayInterval = null;
  }
};

const resetAutoPlay = () => {
  startAutoPlay();
};

const pauseHero = () => {
  isPaused.value = true;
  stopAutoPlay();
};

const resumeHero = () => {
  isPaused.value = false;
  startAutoPlay();
};

const ambilDataFilm = async () => {
  try {
    loading.value = true;
    error.value = null;
    const res = await api.get("/public/films");
    films.value = res.data.data.data;
    currentSlide.value = 0;
    if (keyword.value.trim() === "") {
      startAutoPlay();
    }
  } catch (err) {
    error.value = "Gagal memuat data.";
    console.error(err);
  } finally {
    loading.value = false;
  }
};

const cariFilm = () => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(async () => {
    if (keyword.value.trim() === "") {
      stopAutoPlay();
      ambilDataFilm();
      return;
    }
    try {
      loading.value = true;
      error.value = null;
      stopAutoPlay();
      const res = await api.get("/public/search", {
        params: { keyword: keyword.value },
      });
      films.value = res.data.data.data;
    } catch (err) {
      error.value = "Pencarian gagal. Coba lagi nanti.";
      console.error(err);
    } finally {
      loading.value = false;
    }
  }, 500);
};

const clearSearch = () => {
  keyword.value = "";
  cariFilm();
};

onMounted(ambilDataFilm);

onUnmounted(() => {
  stopAutoPlay();
  clearTimeout(searchTimeout);
});
</script>

<template>
  <div class="home">
    <!-- ── Loading Skeleton ──────────────────────────── -->
    <template v-if="loading">
      <div class="skel-hero"></div>

      <div class="search-section">
        <div class="skel-search"></div>
      </div>

      <div class="film-grid">
        <div v-for="i in 10" :key="i" class="film-card">
          <div class="skel-poster"></div>
          <div class="film-info">
            <div class="skel-line w-90"></div>
            <div class="skel-line w-60"></div>
            <div class="skel-line w-45"></div>
          </div>
        </div>
      </div>
    </template>

    <!-- ── Error ─────────────────────────────────────── -->
    <div v-else-if="error" class="alert alert-error">{{ error }}</div>

    <!-- ── Content ───────────────────────────────────── -->
    <template v-else>
      <!-- Hero Carousel -->
      <section
        v-if="showHero"
        class="hero"
        @mouseenter="pauseHero"
        @mouseleave="resumeHero"
      >
        <div class="hero-slides">
          <div
            v-for="(film, index) in latestFilms"
            :key="film.id"
            class="hero-slide"
            :class="{ active: index === currentSlide }"
          >
            <div class="hero-bg">
              <img :src="film.poster" :alt="film.judul_film" />
              <div class="hero-grad"></div>
            </div>
            <div class="hero-content">
              <span class="hero-genre">{{ film.nama_genre }}</span>
              <h1>{{ film.judul_film }}</h1>
              <p>
                {{ film.sutradara }} &middot; {{ film.tahun_rilis }} &middot;
                {{ film.durasi }} mnt
              </p>
              <RouterLink :to="'/film/' + film.slug" class="hero-btn">
                Lihat Detail
                <i class="pi pi-arrow-right" style="font-size: 11px"></i>
              </RouterLink>
            </div>
          </div>
        </div>

        <button
          class="hero-nav hero-nav--prev"
          @click="prevSlide"
          aria-label="Sebelumnya"
        >
          <i class="pi pi-chevron-left"></i>
        </button>
        <button
          class="hero-nav hero-nav--next"
          @click="nextSlide"
          aria-label="Selanjutnya"
        >
          <i class="pi pi-chevron-right"></i>
        </button>

        <div class="hero-dots">
          <button
            v-for="(_, index) in latestFilms"
            :key="index"
            class="hero-dot"
            :class="{ active: index === currentSlide }"
            @click="goToSlide(index)"
            :aria-label="'Slide ' + (index + 1)"
          />
        </div>

        <div
          class="hero-progress"
          :key="currentSlide"
          :style="{ animationPlayState: isPaused ? 'paused' : 'running' }"
        ></div>
      </section>

      <!-- Search -->
      <div class="search-section">
        <div class="search-wrap">
          <i class="pi pi-search"></i>
          <input
            v-model="keyword"
            type="text"
            placeholder="Cari judul film..."
            @input="cariFilm"
          />
          <button
            v-if="keyword"
            class="search-clear"
            @click="clearSearch"
            aria-label="Hapus pencarian"
          >
            <i class="pi pi-times"></i>
          </button>
        </div>
      </div>

      <!-- Search result info -->
      <p
        v-if="keyword.trim() && films.length > 0"
        class="search-result-info"
      >
        Hasil pencarian untuk "<strong>{{ keyword }}</strong>"
      </p>

      <!-- Grid -->
      <div v-if="films.length > 0" class="film-grid">
        <div v-for="film in films" :key="film.id" class="film-card">
          <div class="film-poster">
            <img :src="film.poster" :alt="film.judul_film" />
            <div class="film-overlay">
              <RouterLink :to="'/film/' + film.slug" class="btn btn-primary"
                >Lihat Detail</RouterLink
              >
            </div>
          </div>
          <div class="film-info">
            <h3 class="film-title">{{ film.judul_film }}</h3>
            <div class="film-meta">
              <span class="badge">{{ film.nama_genre }}</span>
              <span>{{ film.tahun_rilis }}</span>
            </div>
            <p>{{ film.sutradara }}</p>
            <p>{{ film.durasi }} menit</p>
          </div>
        </div>
      </div>

      <!-- Empty -->
      <div v-if="films.length === 0" class="empty-state">
        <i class="pi pi-search" style="font-size: 40px; opacity: 0.2"></i>
        <p>Tidak ada film yang ditemukan.</p>
      </div>
    </template>
  </div>
</template>

<style scoped>
/* ── Skeleton Shimmer ──────────────────────────────── */
.skel-hero,
.skel-search,
.skel-poster,
.skel-line {
  position: relative;
  overflow: hidden;
  background: var(--color-dark3);
  border-radius: var(--radius);
}

.skel-hero::after,
.skel-search::after,
.skel-poster::after,
.skel-line::after {
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

.skel-hero {
  height: 600px;
  /* border-radius: var(--radius-lg); */
  border-bottom-left-radius: 20px;
  border-bottom-right-radius: 10px;
  margin-top: -31px;
  margin-bottom: 48px;
}

.skel-search {
  height: 46px;
  border-radius: var(--radius);
  max-width: 480px;
  margin: 0 auto 36px;
  display: block;
}

.skel-poster {
  aspect-ratio: 2 / 3;
  border-radius: 0;
}

.skel-line {
  height: 12px;
  border-radius: 6px;
  margin-bottom: 8px;
}

.skel-line:last-child {
  margin-bottom: 0;
}

.w-90 {
  width: 90%;
}
.w-60 {
  width: 60%;
}
.w-45 {
  width: 45%;
}

/* ── Hero Carousel ─────────────────────────────────── */
.hero {
  position: relative;
  height: 600px;
  overflow: hidden;
  margin-top: -31px;
  margin-bottom: 48px;
  /* border-radius: var(--radius-lg); */
  border-bottom-left-radius: 20px;
  border-bottom-right-radius: 10px;
  background: var(--color-dark2);
}

.hero-slides {
  position: absolute;
  inset: 0;
}

.hero-slide {
  position: absolute;
  inset: 0;
  opacity: 0;
  pointer-events: none;
  transition: opacity 0.9s ease;
}

.hero-slide.active {
  opacity: 1;
  pointer-events: auto;
}

.hero-bg {
  position: absolute;
  inset: 0;
}

.hero-bg img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  filter: brightness(0.7);
  transition: transform 6s ease-out;
}

.hero-slide.active .hero-bg img {
  transform: scale(1.04);
}

.hero-grad {
  position: absolute;
  inset: 0;
  background: linear-gradient(
    to right,
    rgba(10, 10, 18, 0.15) 0%,
    rgba(10, 10, 18, 0.7) 40%,
    rgba(10, 10, 18, 0.97) 100%
  );
}

.hero-content {
  position: absolute;
  inset: 0;
  display: flex;
  flex-direction: column;
  justify-content: flex-end;
  padding: 44px;
  opacity: 0;
  transform: translateY(14px);
  transition: opacity 0.5s ease 0.25s, transform 0.5s ease 0.25s;
}

.hero-slide.active .hero-content {
  opacity: 1;
  transform: translateY(0);
}

.hero-genre {
  padding: 3px 10px;
  font-size: 11px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  color: var(--color-primary);
  margin-bottom: 12px;
  width: fit-content;
}

.hero-content h1 {
  font-size: 2.4rem;
  font-weight: 800;
  color: white;
  letter-spacing: -0.03em;
  line-height: 1.1;
  margin-bottom: 10px;
  text-shadow: 0 2px 20px rgba(0, 0, 0, 0.4);
}

.hero-content p {
  font-size: 14px;
  color: rgba(255, 255, 255, 0.65);
  margin-bottom: 22px;
}

.hero-btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 11px 26px;
  background: white;
  color: #111;
  border: none;
  border-radius: var(--radius);
  font-size: 13px;
  font-weight: 700;
  font-family: inherit;
  text-decoration: none;
  transition: all 0.2s;
  width: fit-content;
}

.hero-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 24px rgba(0, 0, 0, 0.35);
  color: #111;
}

/* ── Hero Nav Arrows ──────────────────────────────── */
.hero-nav {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  width: 38px;
  height: 38px;
  border-radius: 50%;
  border: 1px solid rgba(255, 255, 255, 0.12);
  background: rgba(0, 0, 0, 0.35);
  backdrop-filter: blur(6px);
  color: rgba(255, 255, 255, 0.8);
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 13px;
  transition: all 0.2s;
  z-index: 5;
}

.hero-nav:hover {
  background: rgba(255, 255, 255, 0.12);
  border-color: rgba(255, 255, 255, 0.25);
  color: white;
}

.hero-nav--prev {
  left: 16px;
}

.hero-nav--next {
  right: 16px;
}

/* ── Hero Dots ────────────────────────────────────── */
.hero-dots {
  position: absolute;
  bottom: 22px;
  left: 44px;
  display: flex;
  gap: 6px;
  z-index: 5;
}

.hero-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  border: none;
  background: rgba(255, 255, 255, 0.25);
  cursor: pointer;
  padding: 0;
  transition: all 0.35s ease;
}

.hero-dot:hover {
  background: rgba(255, 255, 255, 0.45);
}

.hero-dot.active {
  background: var(--color-primary);
  width: 22px;
  border-radius: 4px;
}

/* ── Hero Progress Bar ────────────────────────────── */
.hero-progress {
  position: absolute;
  bottom: 0;
  left: 0;
  height: 3px;
  background: var(--color-primary);
  z-index: 6;
  animation: progress-fill 5s linear forwards;
  border-radius: 0 2px 2px 0;
}

@keyframes progress-fill {
  from {
    width: 0;
  }
  to {
    width: 100%;
  }
}

/* ── Search ───────────────────────────────────────── */
.search-section {
  display: flex;
  justify-content: center;
  padding: 0 0 36px;
}

.search-wrap {
  position: relative;
  width: 100%;
  max-width: 480px;
}

.search-wrap i.pi-search {
  position: absolute;
  left: 16px;
  top: 50%;
  transform: translateY(-50%);
  color: var(--color-gray-dark);
  font-size: 14px;
  pointer-events: none;
}

.search-wrap input {
  width: 100%;
  padding: 12px 44px 12px 44px;
  background: var(--color-dark3);
  border: 1.5px solid var(--color-dark5);
  border-radius: var(--radius);
  color: var(--color-white);
  font-size: 14px;
  font-family: inherit;
  outline: none;
  transition: all var(--transition);
}

.search-wrap input::placeholder {
  color: var(--color-gray-dark);
}

.search-wrap input:focus {
  border-color: var(--color-primary);
  box-shadow: 0 0 0 3px rgba(233, 69, 96, 0.15);
  background: var(--color-dark2);
}

.search-clear {
  position: absolute;
  right: 10px;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  color: var(--color-gray-dark);
  cursor: pointer;
  padding: 6px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 4px;
  transition: color 0.2s;
  font-size: 13px;
}

.search-clear:hover {
  color: var(--color-white);
}

/* ── Search Result Info ───────────────────────────── */
.search-result-info {
  font-size: 13px;
  color: var(--color-gray-dark);
  margin-bottom: 22px;
  padding-left: 2px;
}

.search-result-info strong {
  color: var(--color-text);
  font-weight: 600;
}

/* ── Film Grid ────────────────────────────────────── */
.film-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 24px;
}

.film-card {
  background: var(--color-dark3);
  border-radius: var(--radius-lg);
  overflow: hidden;
  border: 1px solid var(--color-dark5);
  transition: all var(--transition);
  display: flex;
  flex-direction: column;
}

.film-card:hover {
  transform: translateY(-6px);
  box-shadow: var(--shadow-card), var(--shadow-glow);
  border-color: rgba(233, 69, 96, 0.3);
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
  transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1),
    filter 0.5s ease;
}

.film-card:hover .film-poster img {
  transform: scale(1.08);
  filter: brightness(0.5);
}

.film-overlay {
  position: absolute;
  inset: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  transition: opacity var(--transition);
  padding: 16px;
}

.film-card:hover .film-overlay {
  opacity: 1;
}

.film-overlay .btn {
  padding: 10px 20px;
  font-size: 12px;
  backdrop-filter: blur(8px);
  background: rgba(233, 69, 96, 0.9);
  border: 1px solid rgba(255, 255, 255, 0.15);
}

.film-overlay .btn:hover {
  background: rgba(233, 69, 96, 1);
  box-shadow: 0 4px 20px rgba(233, 69, 96, 0.5);
}

.film-info {
  padding: 14px;
  display: flex;
  flex-direction: column;
  gap: 5px;
  flex: 1;
}

.film-title {
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
  display: flex;
  align-items: center;
  gap: 8px;
  flex-wrap: wrap;
  margin-top: 2px;
}

.badge {
  display: inline-block;
  padding: 2px 9px;
  font-size: 10px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  border-radius: 100px;
  background: rgba(233, 69, 96, 0.12);
  color: var(--color-primary);
  border: 1px solid rgba(233, 69, 96, 0.2);
}

.film-meta span:not(.badge) {
  font-size: 12px;
  color: var(--color-gray-dark);
}

.film-info p {
  font-size: 12px;
  color: var(--color-text-dim);
  line-height: 1.4;
}

/* ── Empty ────────────────────────────────────────── */
.empty-state {
  text-align: center;
  padding: 80px 20px;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 12px;
}

.empty-state p {
  font-size: 16px;
  color: var(--color-gray);
  font-weight: 500;
}

/* ── Responsive ──────────────────────────────────── */
@media (max-width: 640px) {
  .skel-hero {
    height: 360px;
    margin-bottom: 32px;
  }

  .hero {
    height: 360px;
    margin-bottom: 32px;
  }

  .hero-content {
    padding: 20px 20px 40px;
  }

  .hero-content h1 {
    font-size: 1.5rem;
  }

  .hero-btn {
    padding: 9px 20px;
    font-size: 12px;
  }

  .hero-nav {
    width: 32px;
    height: 32px;
    font-size: 11px;
  }

  .hero-nav--prev {
    left: 10px;
  }

  .hero-nav--next {
    right: 10px;
  }

  .hero-dots {
    left: 20px;
    bottom: 18px;
  }

  .search-section {
    padding: 0 0 28px;
  }

  .film-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 14px;
  }

  .film-info {
    padding: 12px;
  }

  .film-title {
    font-size: 13px;
  }
}

@media (min-width: 641px) and (max-width: 1024px) {
  .film-grid {
    grid-template-columns: repeat(3, 1fr);
  }
}

@media (min-width: 1025px) {
  .film-grid {
    grid-template-columns: repeat(4, 1fr);
  }
}

@media (min-width: 1200px) {
  .film-grid {
    grid-template-columns: repeat(5, 1fr);
  }
}
</style>