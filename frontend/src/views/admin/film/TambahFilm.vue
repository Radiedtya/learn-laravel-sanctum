<script setup>
import { ref, reactive, onMounted, watch } from "vue";
import { RouterLink, useRouter } from "vue-router";
import api from "../../../utils/api";

const router = useRouter();
const loading = ref(false);
const successMsg = ref("");
const errorMsg = ref("");
const genres = ref([]);
const actors = ref([]);
const posterError = ref(false);

const form = reactive({
  judul_film: "",
  slug: "",
  genre_id: "",
  sutradara: "",
  tahun_rilis: "",
  durasi: "",
  rating: "",
  poster: "",
  deskripsi: "",
  aktor_ids: [],
});

watch(
  () => form.judul_film,
  (val) => {
    form.slug = val
      .toLowerCase()
      .trim()
      .replace(/[^a-z0-9\s-]/g, "")
      .replace(/\s+/g, "-")
      .replace(/-+/g, "-");
  },
);

watch(
  () => form.poster,
  () => {
    posterError.value = false;
  },
);

onMounted(async () => {
  if (!localStorage.getItem("token")) {
    router.push("/login");
    return;
  }
  try {
    const [g, a] = await Promise.all([
      api.get("public/genres"),
      api.get("public/actors"),
    ]);
    genres.value = g.data.data.data;
    actors.value = a.data.data;
  } catch {
    errorMsg.value = "Gagal memuat data genre/aktor.";
  }
});

const handleSubmit = async () => {
  if (form.aktor_ids.length === 0) {
    errorMsg.value = "Pilih minimal 1 aktor.";
    return;
  }

  loading.value = true;
  errorMsg.value = "";
  successMsg.value = "";

  try {
    await api.post("/films", form);
    successMsg.value = "Film berhasil ditambahkan.";
    posterError.value = false;

    Object.assign(form, {
      judul_film: "",
      slug: "",
      genre_id: "",
      sutradara: "",
      tahun_rilis: "",
      durasi: "",
      rating: "",
      poster: "",
      deskripsi: "",
      aktor_ids: [],
    });

    window.scrollTo({ top: 0, behavior: "smooth" });
  } catch (err) {
    console.error("Status:", err.response?.status);
    console.error("Response:", err.response?.data);

    if (err.response?.status === 422) {
      const errors = err.response.data.errors;
      errorMsg.value = Object.values(errors)[0][0];
    } else if (err.response?.status === 401) {
      errorMsg.value = "Sesi habis. Silakan login ulang.";
      router.push("/login");
    } else {
      errorMsg.value = err.response?.data?.message || "Gagal menyimpan film.";
    }
  } finally {
    loading.value = false;
  }
};
</script>

<template>
  <div class="container">
    <div class="page-head">
      <RouterLink to="/dashboard" class="back-link">
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
      </RouterLink>
      <h1>Tambah Film</h1>
    </div>

    <div v-if="successMsg" class="msg msg-success">{{ successMsg }}</div>
    <div v-if="errorMsg" class="msg msg-error">{{ errorMsg }}</div>

    <form @submit.prevent="handleSubmit" class="form-card">
      <div class="form-row">
        <div class="field">
          <label for="judul">Judul Film</label>
          <input
            id="judul"
            v-model="form.judul_film"
            type="text"
            placeholder="Avengers: Endgame"
            required
          />
        </div>
        <div class="field">
          <label for="slug">Slug</label>
          <input
            id="slug"
            v-model="form.slug"
            type="text"
            placeholder="avengers-endgame"
            required
          />
        </div>
      </div>

      <div class="form-row">
        <div class="field">
          <label for="genre">Genre</label>
          <select id="genre" v-model="form.genre_id" required>
            <option value="">Pilih genre</option>
            <option v-for="g in genres" :key="g.id" :value="g.id">
              {{ g.nama_genre }}
            </option>
          </select>
        </div>
        <div class="field">
          <label for="sutradara">Sutradara</label>
          <input
            id="sutradara"
            v-model="form.sutradara"
            type="text"
            placeholder="Anthony Russo"
            required
          />
        </div>
      </div>

      <div class="form-row form-row-3">
        <div class="field">
          <label for="tahun">Tahun Rilis</label>
          <input
            id="tahun"
            v-model="form.tahun_rilis"
            type="number"
            min="1900"
            max="2030"
            placeholder="2024"
            required
          />
        </div>
        <div class="field">
          <label for="durasi">Durasi (menit)</label>
          <input
            id="durasi"
            v-model="form.durasi"
            type="number"
            min="1"
            placeholder="120"
            required
          />
        </div>
        <div class="field">
          <label for="rating">Rating (0 - 5)</label>
          <input
            id="rating"
            v-model="form.rating"
            type="number"
            min="0"
            max="5"
            step="0.1"
            placeholder="5"
            required
          />
        </div>
      </div>

      <div class="field">
        <label for="poster">URL Poster</label>
        <input
          id="poster"
          v-model="form.poster"
          type="url"
          placeholder="https://example.com/poster.jpg"
          required
        />
        <div v-if="form.poster && !posterError" class="poster-preview">
          <img
            :src="form.poster"
            alt="Preview poster"
            @error="posterError = true"
          />
        </div>
        <p v-if="posterError" class="poster-err">
          Gambar tidak bisa dimuat. Periksa URL-nya.
        </p>
      </div>

      <div class="field">
        <label for="deskripsi">Deskripsi</label>
        <textarea
          id="deskripsi"
          v-model="form.deskripsi"
          rows="5"
          placeholder="Tulis deskripsi film..."
        ></textarea>
      </div>

      <div class="field">
        <label>Pilih Aktor</label>
        <div class="actor-grid">
          <label
            v-for="a in actors"
            :key="a.id"
            class="actor-chip"
            :class="{ selected: form.aktor_ids.includes(a.id) }"
          >
            <input
              type="checkbox"
              :value="a.id"
              v-model="form.aktor_ids"
              class="sr-only"
            />
            <span>{{ a.nama_aktor }}</span>
          </label>
        </div>
        <p v-if="form.aktor_ids.length === 0" class="hint">
          Belum ada aktor dipilih
        </p>
      </div>

      <button type="submit" :disabled="loading" class="btn-submit">
        <span v-if="loading" class="btn-loader"></span>
        <span>{{ loading ? "Menyimpan..." : "Simpan Film" }}</span>
      </button>
    </form>
  </div>
</template>

<style scoped>
.page-head {
  margin-bottom: 28px;
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

.page-head h1 {
  font-size: 1.6rem;
  font-weight: 800;
  color: var(--color-white);
  letter-spacing: -0.02em;
}

.msg {
  padding: 10px 14px;
  border-radius: var(--radius);
  font-size: 13px;
  font-weight: 500;
  margin-bottom: 20px;
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

.form-card {
  max-width: 720px;
  background: var(--color-dark3);
  border: 1px solid var(--color-dark5);
  border-radius: var(--radius-lg);
  padding: 32px;
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
}

.form-row-3 {
  grid-template-columns: 1fr 1fr 1fr;
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

input,
select,
textarea {
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

input::placeholder,
textarea::placeholder {
  color: var(--color-gray-dark);
}

input:focus,
select:focus,
textarea:focus {
  border-color: var(--color-primary);
  box-shadow: 0 0 0 3px rgba(233, 69, 96, 0.12);
}

select {
  appearance: none;
  background-image: url("data:image/svg+xml,%3Csvg width='12' height='8' viewBox='0 0 12 8' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1 1.5L6 6.5L11 1.5' stroke='%2355556a' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 14px center;
  padding-right: 36px;
}

select option {
  background: var(--color-dark3);
  color: var(--color-white);
}

textarea {
  resize: vertical;
  min-height: 100px;
}

/* Poster preview */
.poster-preview {
  margin-top: 8px;
  width: 120px;
  height: 170px;
  border-radius: var(--radius);
  overflow: hidden;
  border: 1px solid var(--color-dark5);
  background: var(--color-dark2);
}

.poster-preview img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}

.poster-err {
  font-size: 12px;
  color: #f08070;
  margin-top: 6px;
}

/* Actor chips */
.actor-grid {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  margin-top: 2px;
}

.actor-chip {
  display: inline-flex;
  align-items: center;
  padding: 6px 14px;
  background: var(--color-dark2);
  border: 1px solid var(--color-dark5);
  border-radius: 100px;
  font-size: 13px;
  font-weight: 500;
  color: var(--color-text-dim);
  cursor: pointer;
  transition: all var(--transition);
  user-select: none;
}

.actor-chip:hover {
  border-color: var(--color-gray-dark);
  color: var(--color-text);
}

.actor-chip.selected {
  background: rgba(233, 69, 96, 0.12);
  border-color: rgba(233, 69, 96, 0.35);
  color: var(--color-primary);
  font-weight: 600;
}

.sr-only {
  position: absolute;
  width: 1px;
  height: 1px;
  padding: 0;
  margin: -1px;
  overflow: hidden;
  clip: rect(0, 0, 0, 0);
  border: 0;
}

.hint {
  font-size: 12px;
  color: var(--color-gray-dark);
  margin-top: 4px;
}

.btn-submit {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  margin-top: 4px;
  padding: 13px;
  background: var(--color-primary);
  color: white;
  border: none;
  border-radius: var(--radius);
  font-size: 14px;
  font-weight: 700;
  font-family: inherit;
  letter-spacing: 0.02em;
  cursor: pointer;
  transition:
    background var(--transition),
    box-shadow var(--transition);
  align-self: flex-start;
}

.btn-submit:hover:not(:disabled) {
  background: var(--color-primary-dim);
  box-shadow: 0 4px 20px rgba(233, 69, 96, 0.3);
}

.btn-submit:active:not(:disabled) {
  transform: scale(0.98);
}

.btn-submit:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.btn-loader {
  width: 16px;
  height: 16px;
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

@media (max-width: 640px) {
  .form-card {
    padding: 24px 20px;
  }

  .form-row,
  .form-row-3 {
    grid-template-columns: 1fr;
    gap: 0;
  }
}
</style>
