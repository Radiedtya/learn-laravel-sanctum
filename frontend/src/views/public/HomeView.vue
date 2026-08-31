<script setup>
import { ref, onMounted } from "vue";
import { RouterLink } from "vue-router";
import api from "../../utils/api";

const films = ref([]);
const loading = ref(true);
const error = ref(null);
const keyword = ref("");

const ambilDataFilm = async () => {
  try {
    loading.value = true;
    error.value = null;
    const res = await api.get("/public/films");
    films.value = res.data.data.data;
  } catch (err) {
    error.value = "Gagal memuat data. Coba lagi nanti.";
    console.error(err);
  } finally {
    loading.value = false;
  }
};

let searchTimeout = null;

const cariFilm = () => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(async () => {
    if (keyword.value.trim() === "") {
      ambilDataFilm();
      return;
    }
    try {
      loading.value = true;
      error.value = null;
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

onMounted(ambilDataFilm);
</script>

<template>
  <div class="container">
    <div class="page-header">
      <h1>Daftar Film</h1>
      <p class="subtitle">Temukan film favoritmu</p>
    </div>

    <div class="search-box">
      <input
        v-model="keyword"
        type="text"
        placeholder="Cari judul film..."
        @input="cariFilm"
      />
    </div>

    <p v-if="loading" class="loading-text">Memuat data film...</p>

    <div v-else-if="error" class="alert alert-error">{{ error }}</div>

    <div v-else class="film-grid">
      <div v-for="film in films" :key="film.id" class="film-card">
        <div class="film-poster">
          <img :src="film.poster" :alt="film.judul_film" />
          <div class="film-overlay">
            <RouterLink :to="'/film/' + film.slug" class="btn btn-primary">
              Lihat Detail
            </RouterLink>
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

    <div v-if="!loading && films.length === 0 && !error" class="empty-state">
      <p>Tidak ada film yang ditemukan.</p>
    </div>
  </div>
</template>

<style scoped>
.page-header {
  margin-bottom: 36px;
}

.page-header h1 {
  font-size: 2rem;
  font-weight: 800;
  color: var(--color-white);
  letter-spacing: -0.02em;
  line-height: 1.2;
}

.subtitle {
  margin-top: 6px;
  font-size: 15px;
  color: var(--color-gray);
}

.search-box {
  position: relative;
  margin-bottom: 36px;
  max-width: 500px;
}

.search-box input {
  width: 100%;
  padding: 14px 20px 14px 46px;
  background: var(--color-dark3);
  border: 1.5px solid var(--color-dark5);
  border-radius: var(--radius);
  color: var(--color-white);
  font-size: 15px;
  font-family: inherit;
  outline: none;
  transition: all var(--transition);
}

.search-box input::placeholder {
  color: var(--color-gray-dark);
}

.search-box input:focus {
  border-color: var(--color-primary);
  box-shadow: 0 0 0 3px rgba(233, 69, 96, 0.15);
  background: var(--color-dark2);
}

.search-box::before {
  /* content: <Search />; */
  position: absolute;
  left: 16px;
  top: 50%;
  transform: translateY(-50%);
  font-size: 16px;
  pointer-events: none;
}

.film-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 28px;
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
  transition:
    transform 0.5s cubic-bezier(0.4, 0, 0.2, 1),
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
  padding: 16px;
  display: flex;
  flex-direction: column;
  gap: 6px;
  flex: 1;
}

.film-title {
  font-size: 15px;
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
  gap: 10px;
  flex-wrap: wrap;
  margin-top: 2px;
}

.badge {
  display: inline-block;
  padding: 3px 10px;
  font-size: 11px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  border-radius: 100px;
  background: rgba(233, 69, 96, 0.15);
  color: var(--color-primary);
  border: 1px solid rgba(233, 69, 96, 0.25);
}

.film-meta span:not(.badge) {
  font-size: 13px;
  color: var(--color-gray);
}

.film-info p {
  font-size: 13px;
  color: var(--color-text-dim);
  line-height: 1.4;
}

.empty-state {
  text-align: center;
  padding: 80px 20px;
}

.empty-state p {
  font-size: 17px;
  color: var(--color-gray);
  font-weight: 500;
}

@media (max-width: 640px) {
  .container {
    padding: 20px 16px 48px;
  }

  .page-header h1 {
    font-size: 1.5rem;
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

  .badge {
    font-size: 10px;
    padding: 2px 8px;
  }

  .film-info p {
    font-size: 12px;
  }

  .search-box {
    max-width: 100%;
  }
}

@media (min-width: 641px) and (max-width: 900px) {
  .film-grid {
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
  }
}

@media (min-width: 901px) {
  .film-grid {
    grid-template-columns: repeat(4, 1fr);
  }
}

@media (min-width: 1100px) {
  .film-grid {
    grid-template-columns: repeat(5, 1fr);
  }
}
</style>
