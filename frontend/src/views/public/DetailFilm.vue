<script setup>
import { ref, onMounted } from "vue";
import { useRouter, useRoute } from "vue-router";
import api from "../../utils/api";

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
    film.value = response.data.film;
    actors.value = response.data.actors;
  } catch (err) {
    error.value = "Film tidak ditemukan.";
    console.error(err);
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
    <!-- ── Skeleton ──────────────────────────────────── -->
    <template v-if="loading">
      <div class="skel-hero-wrap">
        <div class="skel skel-hero-bg"></div>
        <div class="container skel-hero-inner">
          <div class="skel skel-btn-back"></div>
          <div class="skel-hero-body">
            <div class="skel skel-hero-poster"></div>
            <div class="skel-hero-text">
              <div class="skel skel-line" style="width: 65%; height: 32px"></div>
              <div class="skel-hero-tags">
                <div class="skel skel-pill"></div>
                <div class="skel skel-pill"></div>
                <div class="skel skel-pill"></div>
              </div>
              <div class="skel skel-line" style="width: 80px; height: 36px; margin: 20px 0"></div>
              <div class="skel skel-line" style="width: 140px; height: 14px"></div>
            </div>
          </div>
        </div>
      </div>

      <div class="container" style="margin-top: 8px">
        <div class="skel skel-card">
          <div class="skel skel-line" style="width: 120px; height: 18px; margin-bottom: 20px"></div>
          <div class="skel skel-line w-100" style="height: 14px; margin-bottom: 12px"></div>
          <div class="skel skel-line w-95" style="height: 14px; margin-bottom: 12px"></div>
          <div class="skel skel-line w-80" style="height: 14px"></div>
        </div>
      </div>

      <div class="container" style="margin-top: 8px">
        <div class="skel skel-card">
          <div class="skel skel-line" style="width: 140px; height: 18px; margin-bottom: 20px"></div>
          <div class="skel-actor-row">
            <div v-for="i in 6" :key="i" class="skel-actor-item">
              <div class="skel skel-circle"></div>
              <div class="skel skel-line" style="width: 70px; height: 12px; margin-top: 10px"></div>
            </div>
          </div>
        </div>
      </div>
    </template>

    <!-- ── Error ─────────────────────────────────────── -->
    <div v-else-if="error" class="detail-error">
      <i class="pi pi-exclamation-circle"></i>
      <p>{{ error }}</p>
      <button class="btn btn-primary" @click="kembali">Kembali</button>
    </div>

    <!-- ── Konten ────────────────────────────────────── -->
    <template v-else-if="film">
      <!-- Hero -->
      <section class="hero">
        <div class="hero-backdrop">
          <img :src="film.poster" :alt="film.judul_film" />
          <div class="hero-backdrop-overlay"></div>
        </div>

        <div class="container hero-content">
          <button class="btn-back" @click="kembali" title="Kembali">
            <i class="pi pi-arrow-left" style="font-size: 13px"></i>
            Kembali
          </button>

          <div class="hero-body">
            <div class="hero-poster">
              <img :src="film.poster" :alt="film.judul_film" />
            </div>

            <div class="hero-info">
              <h1 class="hero-title">{{ film.judul_film }}</h1>

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

              <div v-if="film.rating" class="hero-rating">
                <i class="pi pi-star-fill rating-star-icon"></i>
                <span class="rating-value">{{ film.rating }}</span>
                <span class="rating-max">/ 5</span>
              </div>

              <div v-if="film.sutradara" class="hero-meta">
                <span class="meta-label">Sutradara</span>
                <span class="meta-value">{{ film.sutradara }}</span>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Deskripsi -->
      <section v-if="film.deskripsi" class="detail-section">
        <div class="container">
          <div class="section-card">
            <h2 class="section-title">Deskripsi</h2>
            <p class="deskripsi-text">{{ film.deskripsi }}</p>
          </div>
        </div>
      </section>

      <!-- Pemain -->
      <section v-if="actors.length > 0" class="detail-section">
        <div class="container">
          <div class="section-card">
            <h2 class="section-title">Daftar Pemain</h2>
            <div class="actor-grid">
              <div
                v-for="aktor in actors"
                :key="aktor.id"
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
/* ── Skeleton Base ─────────────────────────────────── */
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

.skel-line {
  border-radius: 6px;
}

.w-100 {
  width: 100%;
}
.w-95 {
  width: 95%;
}
.w-80 {
  width: 80%;
}

/* Skeleton hero */
.skel-hero-wrap {
  position: relative;
  min-height: 520px;
  border-radius: var(--radius-lg);
  overflow: hidden;
}

.skel-hero-bg {
  position: absolute;
  inset: 0;
  border-radius: 0;
  background: var(--color-dark2);
}

.skel-hero-inner {
  position: relative;
  z-index: 1;
  padding-top: 32px;
  padding-bottom: 48px;
}

.skel-btn-back {
  width: 110px;
  height: 36px;
  border-radius: var(--radius);
  margin-bottom: 32px;
}

.skel-hero-body {
  display: flex;
  gap: 40px;
  align-items: flex-start;
}

.skel-hero-poster {
  width: 280px;
  aspect-ratio: 2 / 3;
  border-radius: var(--radius-lg);
  flex-shrink: 0;
}

.skel-hero-text {
  flex: 1;
  padding-top: 8px;
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.skel-hero-tags {
  display: flex;
  gap: 10px;
}

.skel-pill {
  width: 80px;
  height: 28px;
  border-radius: 100px;
}

.skel-card {
  padding: 32px;
  border-radius: var(--radius-lg);
  border: 1px solid var(--color-dark5);
}

.skel-actor-row {
  display: grid;
  grid-template-columns: repeat(6, 1fr);
  gap: 20px;
}

.skel-actor-item {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.skel-circle {
  width: 72px;
  height: 72px;
  border-radius: 50%;
}

/* ── Error ─────────────────────────────────────────── */
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

.detail-error > i {
  font-size: 44px;
  color: var(--color-gray-dark);
}

.detail-error p {
  color: var(--color-gray);
  font-size: 15px;
  max-width: 360px;
  line-height: 1.6;
}

/* ════════════════════════════════════════════════════
   HERO
   ════════════════════════════════════════════════════ */
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
  transition: all 0.2s;
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

/* ── Tags ──────────────────────────────────────────── */
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

/* ── Rating ────────────────────────────────────────── */
.hero-rating {
  display: flex;
  align-items: baseline;
  gap: 8px;
  margin-bottom: 28px;
  padding-bottom: 24px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.06);
}

.rating-star-icon {
  color: #f1c40f;
  font-size: 20px;
}

.rating-value {
  font-size: 28px;
  font-weight: 800;
  color: var(--color-white);
  letter-spacing: -0.02em;
}

.rating-max {
  font-size: 15px;
  color: var(--color-gray-dark);
}

/* ── Meta ──────────────────────────────────────────── */
.hero-meta {
  display: flex;
  flex-direction: column;
  gap: 4px;
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
}

/* ════════════════════════════════════════════════════
   SECTIONS
   ════════════════════════════════════════════════════ */
.detail-section {
  padding: 8px 0 48px;
}

.section-card {
  background: var(--color-dark3);
  border: 1px solid var(--color-dark5);
  border-radius: var(--radius-lg);
  padding: 32px;
}

.section-title {
  font-size: 16px;
  font-weight: 700;
  color: var(--color-white);
  margin-bottom: 18px;
  padding-bottom: 12px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.06);
  letter-spacing: -0.01em;
}

.deskripsi-text {
  font-size: 14px;
  line-height: 1.85;
  color: var(--color-text);
  white-space: pre-line;
}

/* ── Actors ────────────────────────────────────────── */
.actor-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
  gap: 16px;
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
  transition: border-color 0.2s, transform 0.2s;
}

.actor-card:hover {
  border-color: rgba(233, 69, 96, 0.25);
  transform: translateY(-2px);
}

.actor-avatar {
  width: 64px;
  height: 64px;
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
}

.actor-initial {
  font-size: 24px;
  font-weight: 700;
  color: var(--color-primary);
  text-transform: uppercase;
}

.actor-name {
  font-size: 12px;
  font-weight: 600;
  color: var(--color-text);
  text-align: center;
  line-height: 1.3;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* ════════════════════════════════════════════════════
   RESPONSIVE
   ════════════════════════════════════════════════════ */
@media (max-width: 768px) {
  .skel-hero-wrap {
    min-height: auto;
  }

  .skel-hero-body {
    flex-direction: column;
    align-items: center;
  }

  .skel-hero-poster {
    width: 180px;
  }

  .skel-hero-text {
    align-items: center;
  }

  .skel-actor-row {
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
  }

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

  .hero-meta {
    align-items: center;
  }

  .section-card {
    padding: 24px 20px;
  }

  .actor-grid {
    grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
    gap: 12px;
  }

  .actor-avatar {
    width: 52px;
    height: 52px;
  }

  .actor-initial {
    font-size: 20px;
  }

  .actor-card {
    padding: 16px 8px;
  }
}

@media (min-width: 769px) and (max-width: 1024px) {
  .skel-hero-poster {
    width: 220px;
  }

  .skel-actor-row {
    grid-template-columns: repeat(5, 1fr);
  }

  .hero-poster {
    width: 220px;
  }

  .hero-title {
    font-size: 2rem;
  }
}
</style>