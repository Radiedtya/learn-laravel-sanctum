<script setup>
import { ref, onMounted } from "vue";
import { useRouter, useRoute } from "vue-router";
import api from "../utils/api";

const router = useRouter();
const route = useRoute();

const film = ref(null);
const actors = ref([]);
const loading = ref(true);
const error = ref(null);

const ambilDetailFilm = async () => {
  try {
    loading.value = true;
    error.value = null;

    const response = await api.get(`/public/films/${route.params.slug}`);

    // Response: { status, message, film: {...}, actors: [...] }
    film.value = response.data.film;
    actors.value = response.data.actors;
  } catch (err) {
    error.value = "Film tidak ditemukan atau server sedang bermasalah.";
    console.error("Error detail:", err);
  } finally {
    loading.value = false;
  }
};

const kembali = () => {
  router.back();
};

onMounted(() => {
  ambilDetailFilm();
  window.scrollTo({ top: 0, behavior: "smooth" });
});
</script>

<template>
  <div class="detail-page">
    <!-- ── Loading ────────────────────────────────────────── -->
    <div v-if="loading" class="detail-loading">
      <div class="spinner"></div>
      <p>Memuat detail film...</p>
    </div>

    <!-- ── Error ──────────────────────────────────────────── -->
    <div v-else-if="error" class="detail-error">
      <div class="error-icon">⚠️</div>
      <p>{{ error }}</p>
      <button class="btn btn-primary" @click="kembali">Kembali</button>
    </div>

    <!-- ── Konten Detail ──────────────────────────────────── -->
    <template v-else-if="film">
      <!-- Hero Section -->
      <section class="hero">
        <div class="hero-backdrop">
          <img :src="film.poster" :alt="film.judul_film" />
          <div class="hero-backdrop-overlay"></div>
        </div>

        <div class="container hero-content">
          <button class="btn-back" @click="kembali" title="Kembali">
            <svg
              width="20"
              height="20"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2.5"
              stroke-linecap="round"
              stroke-linejoin="round"
            >
              <polyline points="15 18 9 12 15 6"></polyline>
            </svg>
            Kembali
          </button>

          <div class="hero-body">
            <!-- Poster -->
            <div class="hero-poster">
              <img :src="film.poster" :alt="film.judul_film" />
            </div>

            <!-- Info Utama -->
            <div class="hero-info">
              <h1 class="hero-title">{{ film.judul_film }}</h1>

              <!-- Tags: genre + tahun + durasi -->
              <div class="hero-tags">
                <span v-if="film.nama_genre" class="tag tag-genre">{{
                  film.nama_genre
                }}</span>
                <span v-if="film.tahun_rilis" class="tag tag-year">{{
                  film.tahun_rilis
                }}</span>
                <span v-if="film.durasi" class="tag tag-durasi"
                  >{{ film.durasi }} menit</span
                >
              </div>

              <!-- Rating -->
              <div v-if="film.rating" class="hero-rating">
                <div class="rating-star">★</div>
                <span class="rating-value">{{ film.rating }}</span>
                <span class="rating-label">/ 10</span>
              </div>

              <!-- Sutradara -->
              <div v-if="film.sutradara" class="meta-grid">
                <div class="meta-item">
                  <span class="meta-label">Sutradara</span>
                  <span class="meta-value">{{ film.sutradara }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Deskripsi Section -->
      <section v-if="film.deskripsi" class="section-deskripsi">
        <div class="container">
          <div class="section-card">
            <h2 class="section-title">Deskripsi</h2>
            <p class="deskripsi-text">{{ film.deskripsi }}</p>
          </div>
        </div>
      </section>

      <!-- Daftar Aktor Section -->
      <section
        v-if="actors.length > 0"
        class="section-aktors"
      >
        <div class="container">
          <div class="section-card">
            <h2 class="section-title">Daftar Pemain</h2>
            <div class="actor-grid">
              <div
                v-for="aktor in actors" :key="aktor.id"
                class="actor-card"
              >
                <div class="actor-avatar">
                  <img
                    v-if="aktor.foto"
                    :src="aktor.foto"
                    :alt="aktor.nama_aktor"
                  />
                  <span v-else class="actor-initial">{{
                    aktor.nama_aktor?.charAt(0)
                  }}</span>
                </div>
                <span class="actor-name">{{ aktor.nama_aktor }}</span>
              </div>
            </div>
          </div>
        </div>
      </section>
    </template>
  </div>
</template>

<style scoped>
/* ── Loading State ──────────────────────────────────────── */
.detail-loading {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 60vh;
  gap: 20px;
  color: var(--color-gray);
  font-size: 15px;
}

.spinner {
  width: 40px;
  height: 40px;
  border: 3px solid var(--color-dark4);
  border-top-color: var(--color-primary);
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

/* ── Error State ────────────────────────────────────────── */
.detail-error {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 60vh;
  gap: 16px;
  text-align: center;
  padding: 24px;
}

.error-icon {
  font-size: 48px;
  opacity: 0.7;
}

.detail-error p {
  color: var(--color-gray);
  font-size: 16px;
  max-width: 400px;
}

/* ══════════════════════════════════════════════════════════
   HERO SECTION
   ══════════════════════════════════════════════════════════ */
.hero {
  position: relative;
  min-height: 520px;
  overflow: hidden;
}

.hero-backdrop {
  position: absolute;
  inset: 0;
  z-index: 0;
}

.hero-backdrop img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  filter: blur(30px) brightness(0.3);
  transform: scale(1.2);
}

.hero-backdrop-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(
    to bottom,
    rgba(10, 10, 18, 0.4) 0%,
    rgba(10, 10, 18, 0.7) 60%,
    rgba(10, 10, 18, 1) 100%
  );
}

.hero-content {
  position: relative;
  z-index: 1;
  padding-top: 32px;
  padding-bottom: 48px;
}

.btn-back {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 8px 16px;
  margin-bottom: 32px;
  background: rgba(255, 255, 255, 0.06);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: var(--radius);
  color: var(--color-text);
  font-size: 14px;
  font-weight: 500;
  font-family: inherit;
  cursor: pointer;
  transition: all var(--transition);
  backdrop-filter: blur(8px);
}

.btn-back:hover {
  background: rgba(255, 255, 255, 0.12);
  color: var(--color-white);
  border-color: rgba(255, 255, 255, 0.2);
  transform: translateX(-2px);
}

.hero-body {
  display: flex;
  gap: 40px;
  align-items: flex-start;
}

.hero-poster {
  flex-shrink: 0;
  width: 280px;
  border-radius: var(--radius-lg);
  overflow: hidden;
  box-shadow: 0 12px 48px rgba(0, 0, 0, 0.6);
  border: 1px solid rgba(255, 255, 255, 0.08);
  aspect-ratio: 2 / 3;
}

.hero-poster img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.6s ease;
}

.hero-poster:hover img {
  transform: scale(1.05);
}

.hero-info {
  flex: 1;
  padding-top: 8px;
}

.hero-title {
  font-size: 2.4rem;
  font-weight: 800;
  color: var(--color-white);
  line-height: 1.2;
  letter-spacing: -0.03em;
  margin-bottom: 16px;
}

/* ── Tags ───────────────────────────────────────────────── */
.hero-tags {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  margin-bottom: 20px;
}

.tag {
  display: inline-block;
  padding: 5px 14px;
  font-size: 12px;
  font-weight: 600;
  border-radius: 100px;
  letter-spacing: 0.03em;
}

.tag-genre {
  background: rgba(233, 69, 96, 0.2);
  color: var(--color-primary);
  border: 1px solid rgba(233, 69, 96, 0.35);
}

.tag-year {
  background: rgba(255, 255, 255, 0.08);
  color: var(--color-white);
  border: 1px solid rgba(255, 255, 255, 0.12);
}

.tag-durasi {
  background: rgba(255, 255, 255, 0.06);
  color: var(--color-text-dim);
  border: 1px solid rgba(255, 255, 255, 0.08);
}

/* ── Rating ─────────────────────────────────────────────── */
.hero-rating {
  display: flex;
  align-items: baseline;
  gap: 6px;
  margin-bottom: 28px;
  padding-bottom: 24px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.06);
}

.rating-star {
  color: #f1c40f;
  font-size: 28px;
  line-height: 1;
  filter: drop-shadow(0 0 6px rgba(241, 196, 15, 0.4));
}

.rating-value {
  font-size: 28px;
  font-weight: 800;
  color: var(--color-white);
  letter-spacing: -0.02em;
}

.rating-label {
  font-size: 15px;
  color: var(--color-gray-dark);
  font-weight: 400;
}

/* ── Meta (Sutradara) ───────────────────────────────────── */
.meta-grid {
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.meta-item {
  display: flex;
  flex-direction: column;
  gap: 3px;
}

.meta-label {
  font-size: 11px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  color: var(--color-gray-dark);
}

.meta-value {
  font-size: 15px;
  color: var(--color-text);
  font-weight: 500;
  line-height: 1.5;
}

/* ══════════════════════════════════════════════════════════
   SECTION: Deskripsi
   ══════════════════════════════════════════════════════════ */
.section-deskripsi,
.section-aktors {
  padding: 8px 0 48px;
}

.section-card {
  background: var(--color-dark3);
  border: 1px solid var(--color-dark5);
  border-radius: var(--radius-lg);
  padding: 32px;
}

.section-title {
  font-size: 18px;
  font-weight: 700;
  color: var(--color-white);
  margin-bottom: 20px;
  padding-bottom: 12px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.06);
  letter-spacing: -0.01em;
}

.deskripsi-text {
  font-size: 15px;
  line-height: 1.85;
  color: var(--color-text);
  white-space: pre-line;
}

/* ══════════════════════════════════════════════════════════
   SECTION: Daftar Pemain (Aktor)
   ══════════════════════════════════════════════════════════ */
.actor-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
  gap: 20px;
}

.actor-card {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 10px;
  padding: 20px 12px;
  background: var(--color-dark2);
  border: 1px solid var(--color-dark5);
  border-radius: var(--radius);
  transition: all var(--transition);
}

.actor-card:hover {
  border-color: rgba(233, 69, 96, 0.3);
  transform: translateY(-3px);
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
}

.actor-avatar {
  width: 72px;
  height: 72px;
  border-radius: 50%;
  overflow: hidden;
  border: 2px solid var(--color-dark5);
  flex-shrink: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  background: var(--color-dark4);
}

.actor-avatar img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.actor-card:hover .actor-avatar img {
  transform: scale(1.1);
}

.actor-initial {
  font-size: 28px;
  font-weight: 700;
  color: var(--color-primary);
  text-transform: uppercase;
}

.actor-name {
  font-size: 13px;
  font-weight: 600;
  color: var(--color-text);
  text-align: center;
  line-height: 1.3;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* ══════════════════════════════════════════════════════════
   RESPONSIVE
   ══════════════════════════════════════════════════════════ */
@media (max-width: 768px) {
  .hero {
    min-height: auto;
  }

  .hero-content {
    padding-top: 20px;
    padding-bottom: 32px;
  }

  .btn-back {
    margin-bottom: 24px;
  }

  .hero-body {
    flex-direction: column;
    gap: 24px;
    align-items: center;
    text-align: center;
  }

  .hero-poster {
    width: 180px;
  }

  .hero-title {
    font-size: 1.6rem;
  }

  .hero-tags {
    justify-content: center;
  }

  .hero-rating {
    justify-content: center;
    margin-bottom: 20px;
    padding-bottom: 20px;
  }

  .meta-grid {
    align-items: center;
  }

  .meta-item {
    align-items: center;
  }

  .section-card {
    padding: 24px 20px;
  }

  .actor-grid {
    grid-template-columns: repeat(auto-fill, minmax(110px, 1fr));
    gap: 12px;
  }

  .actor-avatar {
    width: 56px;
    height: 56px;
  }

  .actor-initial {
    font-size: 22px;
  }

  .actor-name {
    font-size: 12px;
  }

  .actor-card {
    padding: 16px 8px;
  }
}

@media (min-width: 769px) and (max-width: 1024px) {
  .hero-poster {
    width: 220px;
  }

  .hero-title {
    font-size: 2rem;
  }
}
</style>
