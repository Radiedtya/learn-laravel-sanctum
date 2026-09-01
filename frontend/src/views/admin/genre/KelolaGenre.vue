<script setup>
import { ref, reactive, onMounted, watch } from "vue";
import { RouterLink, useRouter } from "vue-router";
import api from "../../../utils/api";

const router = useRouter();
const genres = ref([]);
const loading = ref(true);
const successMsg = ref("");
const errorMsg = ref("");
const deletingId = ref(null);
const showModal = ref(false);
const genreToDelete = ref(null);
const showForm = ref(false);
const saving = ref(false);
const editingId = ref(null);

const form = reactive({
  nama_genre: "",
  slug: "",
});

watch(
  () => form.nama_genre,
  (val) => {
    form.slug = val
      .toLowerCase()
      .trim()
      .replace(/[^a-z0-9\s-]/g, "")
      .replace(/\s+/g, "-")
      .replace(/-+/g, "-");
  },
);

onMounted(async () => {
  if (!localStorage.getItem("token")) {
    router.push("/login");
    return;
  }
  await ambilGenre();
});

const ambilGenre = async () => {
  try {
    loading.value = true;
    const res = await api.get("/genres");
    genres.value = res.data.data.data || res.data.data || [];
  } catch {
    errorMsg.value = "Gagal memuat data genre.";
  } finally {
    loading.value = false;
  }
};

const bukaFormTambah = () => {
  editingId.value = null;
  form.nama_genre = "";
  form.slug = "";
  errorMsg.value = "";
  showForm.value = true;
};

const bukaFormEdit = (g) => {
  editingId.value = g.id;
  form.nama_genre = g.nama_genre;
  form.slug = g.slug;
  errorMsg.value = "";
  showForm.value = true;
};

const tutupForm = () => {
  showForm.value = false;
  editingId.value = null;
  form.nama_genre = "";
  form.slug = "";
  errorMsg.value = "";
};

const handleSubmit = async () => {
  if (!form.nama_genre.trim()) {
    errorMsg.value = "Nama genre wajib diisi.";
    return;
  }

  saving.value = true;
  errorMsg.value = "";

  try {
    if (editingId.value) {
      await api.put(`/genres/${editingId.value}`, { ...form });
      successMsg.value = `Genre "${form.nama_genre}" diperbarui.`;
    } else {
      await api.post("/genres", { ...form });
      successMsg.value = `Genre "${form.nama_genre}" ditambahkan.`;
    }

    tutupForm();
    await ambilGenre();
    setTimeout(() => (successMsg.value = ""), 3000);
  } catch (err) {
    if (err.response?.status === 422) {
      errorMsg.value = Object.values(err.response.data.errors)[0][0];
    } else {
      errorMsg.value = "Gagal menyimpan genre.";
    }
  } finally {
    saving.value = false;
  }
};

const hapusGenre = (id, nama) => {
  genreToDelete.value = { id, nama };
  showModal.value = true;
};

const konfirmasiHapus = async () => {
  const { id, nama } = genreToDelete.value;
  showModal.value = false;

  try {
    deletingId.value = id;
    await api.delete(`/genres/${id}`);
    genres.value = genres.value.filter((g) => g.id !== id);
    successMsg.value = `Genre "${nama}" dihapus.`;
    setTimeout(() => (successMsg.value = ""), 3000);
  } catch (err) {
    errorMsg.value = err.response?.data?.message || "Gagal menghapus genre.";
  } finally {
    deletingId.value = null;
    genreToDelete.value = null;
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
        <!-- <h1>Kelola Genre</h1> -->
        <button @click="bukaFormTambah" class="btn-add">
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
          Tambah Genre
        </button>
      </div>
    </div>

    <div v-if="successMsg" class="msg msg-success">{{ successMsg }}</div>
    <div v-if="errorMsg" class="msg msg-error">{{ errorMsg }}</div>

    <!-- Form tambah/edit -->
    <Transition name="slide-down">
      <form v-if="showForm" @submit.prevent="handleSubmit" class="add-card">
        <div class="add-row">
          <div class="field">
            <label for="nama">Nama Genre</label>
            <input
              id="nama"
              v-model="form.nama_genre"
              type="text"
              placeholder="Horror"
              required
            />
          </div>
          <div class="field">
            <label for="slug">Slug</label>
            <input
              id="slug"
              v-model="form.slug"
              type="text"
              placeholder="horror"
              required
            />
          </div>
        </div>
        <div class="add-btns">
          <button type="button" @click="tutupForm" class="btn-cancel-sm">
            Batal
          </button>
          <button type="submit" :disabled="saving" class="btn-save">
            <span v-if="saving" class="btn-spin"></span>
            <span>{{
              saving ? "Menyimpan..." : editingId ? "Update" : "Simpan"
            }}</span>
          </button>
        </div>
      </form>
    </Transition>

    <!-- Tabel -->
    <p v-if="loading" class="loading-text">Memuat data genre...</p>

    <div v-else class="table-wrap">
      <table class="genre-table">
        <thead>
          <tr>
            <th class="col-no">No</th>
            <th>Nama Genre</th>
            <th>Slug</th>
            <th class="col-aksi">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="genres.length === 0">
            <td colspan="4" class="empty-row">Belum ada data genre.</td>
          </tr>
          <tr v-for="(g, i) in genres" :key="g.id">
            <td class="col-no">{{ i + 1 }}</td>
            <td class="td-name">{{ g.nama_genre }}</td>
            <td class="td-slug">{{ g.slug }}</td>
            <td class="col-aksi">
              <div class="actions">
                <button @click="bukaFormEdit(g)" class="act-btn act-edit">
                  Edit
                </button>
                <button
                  @click="hapusGenre(g.id, g.nama_genre)"
                  :disabled="deletingId === g.id"
                  class="act-btn act-del"
                >
                  <span v-if="deletingId === g.id" class="act-spin"></span>
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
            <h3>Hapus Genre</h3>
            <p>Yakin ingin menghapus genre berikut?</p>
            <p class="modal-name">{{ genreToDelete?.nama }}</p>
            <p class="modal-warn">
              Film yang terhubung ke genre ini akan terpengaruh.
            </p>
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
  border: none;
  border-radius: var(--radius);
  font-size: 13px;
  font-weight: 600;
  font-family: inherit;
  cursor: pointer;
  transition:
    background var(--transition),
    box-shadow var(--transition);
}
.btn-add:hover {
  background: var(--color-primary-dim);
  box-shadow: 0 2px 12px rgba(233, 69, 96, 0.25);
}

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

.add-card {
  background: var(--color-dark3);
  border: 1px solid var(--color-dark5);
  border-radius: var(--radius-lg);
  padding: 24px;
  margin-bottom: 20px;
}
.add-row {
  display: flex;
  gap: 16px;
  margin-bottom: 16px;
}
.add-row .field {
  flex: 1;
  min-width: 0;
}
.add-btns {
  display: flex;
  gap: 10px;
  justify-content: flex-end;
}

.field {
  display: flex;
  flex-direction: column;
  gap: 6px;
}
.field label {
  font-size: 12px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  color: var(--color-gray-dark);
}
input {
  padding: 11px 14px;
  background: var(--color-dark2);
  border: 1.5px solid var(--color-dark5);
  border-radius: var(--radius);
  color: var(--color-white);
  font-size: 14px;
  font-family: inherit;
  outline: none;
  transition:
    border-color var(--transition),
    box-shadow var(--transition);
}
input::placeholder {
  color: var(--color-gray-dark);
}
input:focus {
  border-color: var(--color-primary);
  box-shadow: 0 0 0 3px rgba(233, 69, 96, 0.12);
}

.btn-cancel-sm {
  padding: 10px 20px;
  background: var(--color-dark4);
  border: 1px solid var(--color-dark5);
  border-radius: var(--radius);
  color: var(--color-text-dim);
  font-size: 13px;
  font-weight: 600;
  font-family: inherit;
  cursor: pointer;
  transition: all var(--transition);
}
.btn-cancel-sm:hover {
  background: var(--color-dark5);
  color: var(--color-text);
}

.btn-save {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 10px 24px;
  background: var(--color-primary);
  color: white;
  border: none;
  border-radius: var(--radius);
  font-size: 13px;
  font-weight: 700;
  font-family: inherit;
  cursor: pointer;
  transition: background var(--transition);
  white-space: nowrap;
}
.btn-save:hover:not(:disabled) {
  background: var(--color-primary-dim);
}
.btn-save:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.btn-spin {
  width: 14px;
  height: 14px;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-top-color: white;
  border-radius: 50%;
  animation: spin 0.6s linear infinite;
  flex-shrink: 0;
}
@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

.table-wrap {
  background: var(--color-dark3);
  border: 1px solid var(--color-dark5);
  border-radius: var(--radius-lg);
  overflow-x: auto;
}
.genre-table {
  width: 100%;
  border-collapse: collapse;
}
.genre-table th {
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
.genre-table td {
  padding: 14px 16px;
  font-size: 14px;
  color: var(--color-text);
  border-bottom: 1px solid var(--color-dark5);
  vertical-align: middle;
}
.genre-table tbody tr:last-child td {
  border-bottom: none;
}
.genre-table tbody tr:hover td {
  background: rgba(255, 255, 255, 0.02);
}

.col-no {
  width: 50px;
  color: var(--color-gray-dark);
  font-size: 13px;
}
.td-name {
  font-weight: 600;
  color: var(--color-white);
}
.td-slug {
  color: var(--color-text-dim);
  font-size: 13px;
  font-family: monospace;
}
.col-aksi {
  width: 160px;
}
.empty-row {
  text-align: center;
  color: var(--color-gray-dark);
  padding: 48px 16px !important;
  font-size: 14px;
}

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

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
.slide-down-enter-active,
.slide-down-leave-active {
  transition: all 0.25s ease;
}
.slide-down-enter-from,
.slide-down-leave-to {
  opacity: 0;
  transform: translateY(-8px);
}
</style>
