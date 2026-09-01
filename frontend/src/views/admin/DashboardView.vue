<script setup>
import { ref, onMounted } from "vue";
import { useRouter, RouterLink } from "vue-router";
import api from "../../utils/api";

const router = useRouter();
const stats = ref({ film: 0, genre: 0, aktor: 0 });
const recentFilms = ref([]);
const loading = ref(true);

onMounted(async () => {
  if (!localStorage.getItem("token")) {
    router.push("/login");
    return;
  }

  try {
    const [filmRes, genreRes, aktorRes] = await Promise.all([
      api.get("/films"),
      api.get("/genres"),
      api.get("/aktors"),
    ]);

    const filmData = filmRes.data.data;
    const filmArray = Array.isArray(filmData) ? filmData : (filmData.data || []);

    stats.value.film = filmArray.length;
    stats.value.genre = Array.isArray(genreRes.data.data) ? genreRes.data.data.length : 0;
    stats.value.aktor = Array.isArray(aktorRes.data.data) ? aktorRes.data.data.length : 0;
    recentFilms.value = filmArray.slice(0, 5);
  } catch (err) {
    console.error(err);
  } finally {
    loading.value = false;
  }
});
</script>

<template>
  <div>
    <div v-if="!loading" class="stats">
      <div class="stat">
        <div class="stat-icon stat-icon--red">
          <i class="pi pi-video"></i>
        </div>
        <div class="stat-body">
          <span class="stat-num">{{ stats.film }}</span>
          <span class="stat-label">Total Film</span>
        </div>
      </div>

      <div class="stat">
        <div class="stat-icon stat-icon--yellow">
          <i class="pi pi-tags"></i>
        </div>
        <div class="stat-body">
          <span class="stat-num">{{ stats.genre }}</span>
          <span class="stat-label">Total Genre</span>
        </div>
      </div>

      <div class="stat">
        <div class="stat-icon stat-icon--teal">
          <i class="pi pi-user"></i>
        </div>
        <div class="stat-body">
          <span class="stat-num">{{ stats.aktor }}</span>
          <span class="stat-label">Total Aktor</span>
        </div>
      </div>
    </div>

    <p v-if="loading" class="load-text">Memuat data...</p>

    <div v-if="!loading && recentFilms.length > 0" class="recent">
      <div class="recent-head">
        <h2>Film Terbaru</h2>
        <RouterLink to="/kelola-film" class="recent-see">Lihat semua</RouterLink>
      </div>
      <div class="recent-grid">
        <RouterLink
          v-for="film in recentFilms"
          :key="film.id"
          :to="'/film/' + film.slug"
          class="recent-card"
        >
          <div class="recent-poster">
            <img :src="film.poster" :alt="film.judul_film" />
          </div>
          <div class="recent-info">
            <h3>{{ film.judul_film }}</h3>
            <span>{{ film.nama_genre }} &middot; {{ film.tahun_rilis }}</span>
          </div>
        </RouterLink>
      </div>
    </div>
  </div>
</template>

<style scoped>
.stats {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 16px;
  margin-bottom: 36px;
}

.stat {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 20px;
  background: var(--color-dark3);
  border: 1px solid var(--color-dark5);
  border-radius: var(--radius-lg);
}

.stat-icon {
  width: 48px; height: 48px; border-radius: 12px;
  display: flex; align-items: center; justify-content: center;
  font-size: 20px; flex-shrink: 0;
}
.stat-icon--red { background: rgba(233,69,96,0.1); color: var(--color-primary); }
.stat-icon--yellow { background: rgba(241,196,15,0.1); color: #f1c40f; }
.stat-icon--teal { background: rgba(26,188,156,0.1); color: #1abc9c; }

.stat-body { display: flex; flex-direction: column; gap: 2px; }
.stat-num { font-size: 28px; font-weight: 800; color: var(--color-white); letter-spacing: -0.03em; line-height: 1; }
.stat-label { font-size: 12px; color: var(--color-gray-dark); font-weight: 500; text-transform: uppercase; letter-spacing: 0.05em; }

.load-text { color: var(--color-gray-dark); font-size: 14px; padding: 40px 0; }

.recent { margin-top: 4px; }
.recent-head { display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 16px; }
.recent-head h2 { font-size: 15px; font-weight: 700; color: var(--color-white); }
.recent-see { font-size: 12px; font-weight: 600; color: var(--color-primary); text-decoration: none; transition: opacity 0.15s; }
.recent-see:hover { opacity: 0.7; }

.recent-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 14px; }
.recent-card { display: flex; flex-direction: column; background: var(--color-dark3); border: 1px solid var(--color-dark5); border-radius: var(--radius); overflow: hidden; text-decoration: none; transition: all 0.2s; }
.recent-card:hover { transform: translateY(-4px); box-shadow: 0 8px 24px rgba(0,0,0,0.3); border-color: rgba(233,69,96,0.2); }
.recent-poster { aspect-ratio: 2/3; overflow: hidden; background: var(--color-dark2); }
.recent-poster img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.4s ease; }
.recent-card:hover .recent-poster img { transform: scale(1.06); }
.recent-info { padding: 12px; display: flex; flex-direction: column; gap: 4px; }
.recent-info h3 { font-size: 13px; font-weight: 700; color: var(--color-white); line-height: 1.3; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
.recent-info span { font-size: 11px; color: var(--color-gray-dark); }

@media (max-width: 900px) { .recent-grid { grid-template-columns: repeat(3, 1fr); } }
@media (max-width: 640px) {
  .stats { grid-template-columns: 1fr; gap: 10px; }
  .stat { padding: 16px; }
  .stat-num { font-size: 24px; }
  .recent-grid { grid-template-columns: repeat(2, 1fr); gap: 10px; }
  .recent-info { padding: 10px; }
  .recent-info h3 { font-size: 12px; }
}
</style>