<script setup>
import { ref, reactive } from "vue";
import { useRouter, RouterLink } from "vue-router";
import api from "../utils/api";

const router = useRouter();

const form = reactive({
  name: "",
  email: "",
  password: "",
  password_confirmation: "",
});

const loading = ref(false);
const errorMsg = ref("");
const success = ref(false);

const handleRegister = async () => {
  loading.value = true;
  errorMsg.value = "";
  success.value = false;

  if (form.password !== form.password_confirmation) {
    errorMsg.value = "Password tidak cocok.";
    loading.value = false;
    return;
  }

  try {
    await api.post("/register", {
      name: form.name,
      email: form.email,
      password: form.password,
      password_confirmation: form.password_confirmation,
    });

    success.value = true;

    setTimeout(() => {
      router.push("/login");
    }, 1500);
  } catch (err) {
    const s = err.response?.status;
    if (s === 422) {
      const errors = err.response?.data?.errors;
      if (errors?.email) errorMsg.value = errors.email[0];
      else if (errors?.name) errorMsg.value = errors.name[0];
      else if (errors?.password) errorMsg.value = errors.password[0];
      else errorMsg.value = "Data yang dikirim tidak valid.";
    } else {
      errorMsg.value = "Server tidak merespon. Coba lagi nanti.";
    }
  } finally {
    loading.value = false;
  }
};
</script>

<template>
  <div class="register-page">
    <div class="register-card">
      <div class="register-header">
        <span class="logo">CineVue</span>
        <p class="register-sub">Buat akun admin baru</p>
      </div>

      <!-- Sukses -->
      <div v-if="success" class="success-bar">
        Akun berhasil dibuat. Mengalihkan ke login...
      </div>

      <!-- Error -->
      <div v-if="errorMsg" class="error-bar">{{ errorMsg }}</div>

      <!-- Form -->
      <form
        v-if="!success"
        @submit.prevent="handleRegister"
        class="register-form"
      >
        <div class="field">
          <label for="name">Nama</label>
          <input
            id="name"
            v-model="form.name"
            type="text"
            placeholder="Nama lengkap"
            autocomplete="name"
            required
          />
        </div>

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
            placeholder="Minimal 8 karakter"
            autocomplete="new-password"
            required
          />
        </div>

        <div class="field">
          <label for="password_confirmation">Konfirmasi Password</label>
          <input
            id="password_confirmation"
            v-model="form.password_confirmation"
            type="password"
            placeholder="Ulangi password"
            autocomplete="new-password"
            required
          />
        </div>

        <button type="submit" :disabled="loading" class="btn-submit">
          <span v-if="loading" class="btn-loader"></span>
          <span>{{ loading ? "Memproses" : "Daftar" }}</span>
        </button>
      </form>

      <div class="register-back">
        <RouterLink to="/login">Sudah punya akun? Masuk</RouterLink>
      </div>
    </div>
  </div>
</template>

<style scoped>
.register-page {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 24px;
  background: var(--color-dark);
}

.register-card {
  width: 100%;
  max-width: 380px;
  background: var(--color-dark3);
  border: 1px solid var(--color-dark5);
  border-radius: var(--radius-lg);
  padding: 40px 32px 32px;
}

.register-header {
  text-align: center;
  margin-bottom: 32px;
}

.logo {
  font-size: 26px;
  font-weight: 800;
  color: var(--color-primary);
  letter-spacing: -0.03em;
}

.register-sub {
  margin-top: 6px;
  font-size: 14px;
  color: var(--color-gray-dark);
}

.success-bar {
  padding: 10px 14px;
  margin-bottom: 20px;
  background: rgba(39, 174, 96, 0.1);
  border: 1px solid rgba(39, 174, 96, 0.25);
  border-radius: var(--radius);
  color: #5ddb9a;
  font-size: 13px;
  font-weight: 500;
  text-align: center;
}

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

.register-form {
  display: flex;
  flex-direction: column;
  gap: 18px;
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

.register-back {
  text-align: center;
  margin-top: 24px;
  font-size: 13px;
}

.register-back a {
  color: var(--color-gray-dark);
  transition: color var(--transition);
}

.register-back a:hover {
  color: var(--color-text);
}

@media (max-width: 480px) {
  .register-card {
    padding: 32px 20px 24px;
  }
}
</style>
