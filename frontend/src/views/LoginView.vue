<script setup>
import { ref, reactive, onMounted } from "vue";
import { useRouter, RouterLink } from "vue-router";
import api from "../utils/api";

const router = useRouter();

const form = reactive({
  email: "",
  password: "",
});

const loading = ref(false);
const errorMsg = ref("");

onMounted(() => {
  if (localStorage.getItem("token")) {
    router.push("/dashboard");
  }
});

const handleLogin = async () => {
  loading.value = true;
  errorMsg.value = "";

  try {
    const res = await api.post("/login", {
      email: form.email,
      password: form.password,
    });

    const token = res.data.token;
    localStorage.setItem("token", token);

    const profileRes = await api.get("/profile", {
      headers: { Authorization: `Bearer ${token}` },
    });

    localStorage.setItem("user", JSON.stringify(profileRes.data.data));
    router.push("/dashboard");
  } catch (err) {
    const s = err.response?.status;
    if (s === 404) errorMsg.value = "Email tidak terdaftar.";
    else if (s === 401) errorMsg.value = "Password salah.";
    else if (s === 422) errorMsg.value = "Format email tidak valid.";
    else errorMsg.value = "Server tidak merespon. Coba lagi nanti.";
  } finally {
    loading.value = false;
  }
};
</script>

<template>
  <div class="login-page">
    <div class="login-card">
      <div class="login-header">
        <span class="logo">CineVue</span>
        <p class="login-sub">Masuk ke panel admin</p>
      </div>

      <div v-if="errorMsg" class="error-bar">{{ errorMsg }}</div>

      <form @submit.prevent="handleLogin" class="login-form">
        <div class="field">
          <label for="email">Email</label>
          <input
            id="email"
            v-model="form.email"
            type="email"
            placeholder="admin@cinevue.com"
            autocomplete="email"
            required
          />
        </div>

        <div class="field">
          <label for="password">Password</label>
          <input
            id="password"
            v-model="form.password"
            type="password"
            placeholder="Masukkan password"
            autocomplete="current-password"
            required
          />
        </div>

        <button type="submit" :disabled="loading" class="btn-submit">
          <span v-if="loading" class="btn-loader"></span>
          <span>{{ loading ? "Memproses" : "Masuk" }}</span>
        </button>
      </form>

      <div class="login-back">
        <RouterLink to="/">&larr; Kembali ke beranda</RouterLink>
      </div>
    </div>
  </div>
</template>

<style scoped>
.login-page {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 24px;
  background: var(--color-dark);
}

/* Card */
.login-card {
  width: 100%;
  max-width: 380px;
  background: var(--color-dark3);
  border: 1px solid var(--color-dark5);
  border-radius: var(--radius-lg);
  padding: 40px 32px 32px;
}

/* Header */
.login-header {
  text-align: center;
  margin-bottom: 32px;
}

.logo {
  font-size: 26px;
  font-weight: 800;
  color: var(--color-primary);
  letter-spacing: -0.03em;
}

.login-sub {
  margin-top: 6px;
  font-size: 14px;
  color: var(--color-gray-dark);
}

/* Error */
.error-bar {
  padding: 10px 14px;
  margin-bottom: 20px;
  background: rgba(231, 76, 60, 0.1);
  border: 1px solid rgba(231, 76, 60, 0.25);
  border-radius: var(--radius);
  color: #f08070;
  font-size: 13px;
  font-weight: 500;
}

/* Form */
.login-form {
  display: flex;
  flex-direction: column;
  gap: 20px;
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

.field input {
  padding: 12px 14px;
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

.field input::placeholder {
  color: var(--color-gray-dark);
}

.field input:focus {
  border-color: var(--color-primary);
  box-shadow: 0 0 0 3px rgba(233, 69, 96, 0.12);
}

/* Tombol */
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

/* Spinner kecil di dalam tombol */
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

/* Footer */
.login-back {
  text-align: center;
  margin-top: 24px;
  font-size: 13px;
}

.login-back a {
  color: var(--color-gray-dark);
  transition: color var(--transition);
}

.login-back a:hover {
  color: var(--color-text);
}
</style>
