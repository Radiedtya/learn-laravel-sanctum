<script setup>
import { ref, onMounted } from "vue";
import { RouterLink, useRouter } from "vue-router";
import api from "../../../utils/api";

const router = useRouter();
const films = ref([]);
const loading = ref(true);
const successMsg = ref("");
const errorMsg = ref("");
const deletingId = ref(null);
const showModal = ref(false);
const filmToDelete = ref(null);

onMounted(async () => {
  if (!localStorage.getItem("token")) {
    router.push("/login");
    return;
  }
  await ambilFilm();
});

const ambilFilm = async () => {
  try {
    loading.value = true;
    const res = await api.get("/films");
    // Sesuaikan: kalau paginated → res.data.data.data, kalau bukan → res.data.data
    films.value = res.data.data.data || res.data.data || [];
  } catch (err) {
    errorMsg.value = "Gagal memuat data film.";
    console.error(err);
  } finally {
    loading.value = false;
  }
};

const hapusFilm = (id, judul) => {
  filmToDelete.value = { id, judul };
  showModal.value = true;
};

const konfirmasiHapus = async () => {
  const { id, judul } = filmToDelete.value;
  showModal.value = false;

  try {
    deletingId.value = id;
    await api.delete(`/films/${id}`);
    films.value = films.value.filter((f) => f.id !== id);
    successMsg.value = `"${judul}" berhasil dihapus.`;
    setTimeout(() => (successMsg.value = ""), 3000);
  } catch (err) {
    errorMsg.value = err.response?.data?.message || "Gagal menghapus film.";
  } finally {
    deletingId.value = null;
    filmToDelete.value = null;
  }
};
</script>

<template>
  <div class="container">
    <div class="page-head">
      <!-- <RouterLink to="/dashboard" class="back-link">
        <svg
          width="16"
          height="16"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="2.5"
          stroke-linecap="round"
          stroke-linejoin="round"
        >
          <polyline points="15 18 9 12 15 6" />
        </svg>
        Dashboard
      </RouterLink> -->
      <div class="title-row">
        <!-- <h1>Kelola Film</h1> -->
        <RouterLink to="/tambah-film" class="btn-add">
          <svg
            width="14"
            height="14"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2.5"
            stroke-linecap="round"
            stroke-linejoin="round"
          >
            <line x1="12" y1="5" x2="12" y2="19" />
            <line x1="5" y1="12" x2="19" y2="12" />
          </svg>
          Tambah Film
        </RouterLink>
      </div>
    </div>

    <div v-if="successMsg" class="msg msg-success">{{ successMsg }}</div>
    <div v-if="errorMsg" class="msg msg-error">{{ errorMsg }}</div>

    <p v-if="loading" class="loading-text">Memuat data film...</p>

    <div v-else class="table-wrap">
      <table class="film-table">
        <thead>
          <tr>
            <th class="col-no">No</th>
            <th class="col-poster"></th>
            <th>Judul</th>
            <th>Genre</th>
            <th>Sutradara</th>
            <th class="col-durasi">Durasi</th>
            <th class="col-aksi">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="films.length === 0">
            <td colspan="7" class="empty-row">Belum ada data film.</td>
          </tr>
          <tr v-for="(film, i) in films" :key="film.id">
            <td class="col-no">{{ i + 1 }}</td>
            <td class="col-poster">
              <img :src="film.poster" :alt="film.judul_film" />
            </td>
            <td class="td-title">{{ film.judul_film }}</td>
            <td>
              <span class="badge">{{ film.nama_genre }}</span>
            </td>
            <td>{{ film.sutradara }}</td>
            <td class="col-durasi">{{ film.durasi }} mnt</td>
            <td class="col-aksi">
              <div class="actions">
                <RouterLink
                  :to="'/edit-film/' + film.id"
                  class="act-btn act-edit"
                >
                  Edit
                </RouterLink>
                <button
                  @click="hapusFilm(film.id, film.judul_film)"
                  :disabled="deletingId === film.id"
                  class="act-btn act-del"
                >
                  <span v-if="deletingId === film.id" class="act-spin"></span>
                  <span v-else>Hapus</span>
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal -->
    <Teleport to="body">
      <Transition name="fade">
        <div v-if="showModal" class="modal-bg" @click.self="showModal = false">
          <div class="modal-card">
            <h3>Hapus Film</h3>
            <p>Yakin ingin menghapus film berikut?</p>
            <p class="modal-name">{{ filmToDelete?.judul }}</p>
            <p class="modal-warn">Tindakan ini tidak bisa dibatalkan.</p>
            <div class="modal-btns">
              <button @click="showModal = false" class="mbtn mbtn-cancel">
                Batal
              </button>
              <button @click="konfirmasiHapus" class="mbtn mbtn-del">
                Hapus
              </button>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>
  </div>
</template>

<style scoped>
.page-head {
  margin-bottom: 24px;
}

.back-link {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-size: 13px;
  color: var(--color-gray-dark);
  margin-bottom: 8px;
  transition: color var(--transition);
}

.back-link:hover {
  color: var(--color-text);
}

.title-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 12px;
}

.title-row h1 {
  font-size: 1.6rem;
  font-weight: 800;
  color: var(--color-white);
  letter-spacing: -0.02em;
}

.btn-add {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 9px 18px;
  background: var(--color-primary);
  color: white;
  border-radius: var(--radius);
  font-size: 13px;
  font-weight: 600;
  text-decoration: none;
  transition:
    background var(--transition),
    box-shadow var(--transition);
}

.btn-add:hover {
  background: var(--color-primary-dim);
  box-shadow: 0 2px 12px rgba(233, 69, 96, 0.25);
  color: white;
}

/* Messages */
.msg {
  padding: 10px 14px;
  border-radius: var(--radius);
  font-size: 13px;
  font-weight: 500;
  margin-bottom: 16px;
}

.msg-success {
  background: rgba(39, 174, 96, 0.1);
  border: 1px solid rgba(39, 174, 96, 0.25);
  color: #5ddb9a;
}

.msg-error {
  background: rgba(231, 76, 60, 0.1);
  border: 1px solid rgba(231, 76, 60, 0.25);
  color: #f08070;
}

/* Table */
.table-wrap {
  background: var(--color-dark3);
  border: 1px solid var(--color-dark5);
  border-radius: var(--radius-lg);
  overflow-x: auto;
}

.film-table {
  width: 100%;
  border-collapse: collapse;
  min-width: 600px;
}

.film-table th {
  padding: 14px 16px;
  text-align: left;
  font-size: 11px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  color: var(--color-gray-dark);
  border-bottom: 1px solid var(--color-dark5);
  background: var(--color-dark2);
}

.film-table td {
  padding: 12px 16px;
  font-size: 14px;
  color: var(--color-text);
  border-bottom: 1px solid var(--color-dark5);
  vertical-align: middle;
}

.film-table tbody tr:last-child td {
  border-bottom: none;
}

.film-table tbody tr:hover td {
  background: rgba(255, 255, 255, 0.02);
}

.col-no {
  width: 40px;
  color: var(--color-gray-dark);
  font-size: 13px;
}

.col-poster {
  width: 56px;
}

.col-poster img {
  width: 40px;
  height: 56px;
  object-fit: cover;
  border-radius: 6px;
}

.td-title {
  font-weight: 600;
  color: var(--color-white);
  max-width: 220px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.badge {
  display: inline-block;
  padding: 3px 10px;
  font-size: 11px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  border-radius: 100px;
  background: rgba(233, 69, 96, 0.12);
  color: var(--color-primary);
  border: 1px solid rgba(233, 69, 96, 0.2);
  white-space: nowrap;
}

.col-durasi {
  width: 80px;
  color: var(--color-text-dim);
  font-size: 13px;
}

.col-aksi {
  width: 140px;
}

.empty-row {
  text-align: center;
  color: var(--color-gray-dark);
  padding: 48px 16px !important;
  font-size: 14px;
}

/* Action buttons */
.actions {
  display: flex;
  gap: 6px;
}

.act-btn {
  padding: 5px 12px;
  border-radius: 6px;
  font-size: 12px;
  font-weight: 600;
  font-family: inherit;
  cursor: pointer;
  border: none;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 6px;
  transition: all var(--transition);
}

.act-edit {
  background: rgba(41, 128, 185, 0.1);
  color: #5dade2;
  border: 1px solid rgba(41, 128, 185, 0.2);
}

.act-edit:hover {
  background: rgba(41, 128, 185, 0.18);
  color: #5dade2;
}

.act-del {
  background: rgba(231, 76, 60, 0.1);
  color: #f08070;
  border: 1px solid rgba(231, 76, 60, 0.2);
}

.act-del:hover:not(:disabled) {
  background: rgba(231, 76, 60, 0.18);
}

.act-del:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.act-spin {
  width: 12px;
  height: 12px;
  border: 2px solid rgba(240, 128, 112, 0.3);
  border-top-color: #f08070;
  border-radius: 50%;
  animation: spin 0.6s linear infinite;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

/* Modal */
.modal-bg {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.6);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 999;
  padding: 24px;
}

.modal-card {
  background: var(--color-dark3);
  border: 1px solid var(--color-dark5);
  border-radius: var(--radius-lg);
  padding: 32px;
  max-width: 400px;
  width: 100%;
  text-align: center;
}

.modal-card h3 {
  font-size: 18px;
  font-weight: 700;
  color: var(--color-white);
  margin-bottom: 12px;
}

.modal-card p {
  font-size: 14px;
  color: var(--color-text-dim);
}

.modal-name {
  font-weight: 700;
  color: var(--color-white);
  font-size: 15px;
  margin: 12px 0 !important;
}

.modal-warn {
  color: #f08070 !important;
  font-size: 13px;
  margin-bottom: 24px !important;
}

.modal-btns {
  display: flex;
  gap: 10px;
  justify-content: center;
}

.mbtn {
  padding: 10px 24px;
  border-radius: var(--radius);
  font-size: 13px;
  font-weight: 600;
  font-family: inherit;
  cursor: pointer;
  border: none;
  transition: all var(--transition);
}

.mbtn-cancel {
  background: var(--color-dark4);
  color: var(--color-text-dim);
  border: 1px solid var(--color-dark5);
}

.mbtn-cancel:hover {
  background: var(--color-dark5);
  color: var(--color-text);
}

.mbtn-del {
  background: #e74c3c;
  color: white;
}

.mbtn-del:hover {
  background: #c0392b;
}

/* Fade transition */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

@media (max-width: 640px) {
  .title-row {
    flex-direction: column;
    align-items: flex-start;
  }
}
</style>
