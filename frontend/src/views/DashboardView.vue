<script setup>
import { ref, onMounted } from "vue";
import { useRouter, RouterLink } from "vue-router";
import api from "../utils/api";

const router = useRouter();
const user = ref(null);
const loadingLogout = ref(false);

onMounted(() => {
  const token = localStorage.getItem("token");
  const userData = localStorage.getItem("user");

  if (!token) {
    router.push("/login");
    return;
  }

  try {
    user.value = JSON.parse(userData);
  } catch (e) {
    user.value = null;
  }
});

const handleLogout = async () => {
  loadingLogout.value = true;
  try {
    await api.post("/logout");
  } catch (err) {
    console.warn("Logout API error:", err);
  } finally {
    localStorage.removeItem("token");
    localStorage.removeItem("user");
    router.push("/login");
  }
};
</script>

<template>
  <div class="dash">
    <!-- Header -->
    <div class="dash-header">
      <div>
        <h1 class="dash-title">Dashboard</h1>
        <p class="dash-sub">{{ user?.email }}</p>
      </div>
      <button
        @click="handleLogout"
        :disabled="loadingLogout"
        class="btn-logout"
      >
        <svg
          v-if="!loadingLogout"
          width="16"
          height="16"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="2"
          stroke-linecap="round"
          stroke-linejoin="round"
        >
          <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
          <polyline points="16 17 21 12 16 7" />
          <line x1="21" y1="12" x2="9" y2="12" />
        </svg>
        <span v-if="loadingLogout" class="btn-spin"></span>
        <span>{{ loadingLogout ? "Keluar..." : "Keluar" }}</span>
      </button>
    </div>

    <!-- Menu -->
    <div class="dash-grid">
      <RouterLink to="/kelola-film" class="dash-card">
        <div class="dash-card-icon">
          <svg
            width="22"
            height="22"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
          >
            <rect x="2" y="2" width="20" height="20" rx="2.18" ry="2.18" />
            <line x1="7" y1="2" x2="7" y2="22" />
            <line x1="17" y1="2" x2="17" y2="22" />
            <line x1="2" y1="12" x2="22" y2="12" />
            <line x1="2" y1="7" x2="7" y2="7" />
            <line x1="2" y1="17" x2="7" y2="17" />
            <line x1="17" y1="7" x2="22" y2="7" />
            <line x1="17" y1="17" x2="22" y2="17" />
          </svg>
        </div>
        <div class="dash-card-body">
          <h3>Kelola Film</h3>
          <p>Edit dan hapus data film</p>
        </div>
        <svg
          class="dash-card-arrow"
          width="18"
          height="18"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="2"
          stroke-linecap="round"
          stroke-linejoin="round"
        >
          <polyline points="9 18 15 12 9 6" />
        </svg>
      </RouterLink>

      <RouterLink to="/tambah-film" class="dash-card">
        <div class="dash-card-icon dash-card-icon--green">
          <svg
            width="22"
            height="22"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
          >
            <circle cx="12" cy="12" r="10" />
            <line x1="12" y1="8" x2="12" y2="16" />
            <line x1="8" y1="12" x2="16" y2="12" />
          </svg>
        </div>
        <div class="dash-card-body">
          <h3>Tambah Film</h3>
          <p>Input film baru ke database</p>
        </div>
        <svg
          class="dash-card-arrow"
          width="18"
          height="18"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="2"
          stroke-linecap="round"
          stroke-linejoin="round"
        >
          <polyline points="9 18 15 12 9 6" />
        </svg>
      </RouterLink>

      <RouterLink to="/" class="dash-card">
        <div class="dash-card-icon dash-card-icon--blue">
          <svg
            width="22"
            height="22"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
          >
            <polygon points="23 7 16 12 23 17 23 7" />
            <rect x="1" y="5" width="15" height="14" rx="2" ry="2" />
          </svg>
        </div>
        <div class="dash-card-body">
          <h3>Lihat Katalog</h3>
          <p>Buka halaman publik</p>
        </div>
        <svg
          class="dash-card-arrow"
          width="18"
          height="18"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="2"
          stroke-linecap="round"
          stroke-linejoin="round"
        >
          <polyline points="9 18 15 12 9 6" />
        </svg>
      </RouterLink>
    </div>
  </div>
</template>

<style scoped>
.dash {
  max-width: 720px;
  margin: 0 auto;
  padding: 32px 24px;
}

/* ── Header ─────────────────────────────────────────────── */
.dash-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 36px;
  padding-bottom: 24px;
  border-bottom: 1px solid var(--color-dark5);
}

.dash-title {
  font-size: 1.6rem;
  font-weight: 800;
  color: var(--color-white);
  letter-spacing: -0.02em;
}

.dash-sub {
  margin-top: 4px;
  font-size: 13px;
  color: var(--color-gray-dark);
}

.btn-logout {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 9px 18px;
  background: rgba(231, 76, 60, 0.1);
  border: 1px solid rgba(231, 76, 60, 0.2);
  border-radius: var(--radius);
  color: #f08070;
  font-size: 13px;
  font-weight: 600;
  font-family: inherit;
  cursor: pointer;
  transition: all var(--transition);
}

.btn-logout:hover:not(:disabled) {
  background: rgba(231, 76, 60, 0.18);
  border-color: rgba(231, 76, 60, 0.35);
  color: #f5a090;
}

.btn-logout:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.btn-spin {
  width: 14px;
  height: 14px;
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

/* ── Grid ───────────────────────────────────────────────── */
.dash-grid {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

/* ── Card ───────────────────────────────────────────────── */
.dash-card {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 20px 24px;
  background: var(--color-dark3);
  border: 1px solid var(--color-dark5);
  border-radius: var(--radius);
  color: var(--color-text);
  text-decoration: none;
  transition: all var(--transition);
}

.dash-card:hover {
  background: var(--color-dark4);
  border-color: var(--color-gray-dark);
  text-decoration: none;
  transform: translateX(4px);
}

.dash-card-icon {
  flex-shrink: 0;
  width: 44px;
  height: 44px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 10px;
  background: rgba(233, 69, 96, 0.1);
  color: var(--color-primary);
}

.dash-card-icon--green {
  background: rgba(39, 174, 96, 0.1);
  color: #5ddb9a;
}

.dash-card-icon--blue {
  background: rgba(41, 128, 185, 0.1);
  color: #5dade2;
}

.dash-card-body {
  flex: 1;
  min-width: 0;
}

.dash-card-body h3 {
  font-size: 15px;
  font-weight: 700;
  color: var(--color-white);
  margin-bottom: 2px;
}

.dash-card-body p {
  font-size: 13px;
  color: var(--color-gray-dark);
}

.dash-card-arrow {
  flex-shrink: 0;
  color: var(--color-gray-dark);
  transition:
    transform var(--transition),
    color var(--transition);
}

.dash-card:hover .dash-card-arrow {
  transform: translateX(3px);
  color: var(--color-text);
}

/* ── Responsive ─────────────────────────────────────────── */
@media (max-width: 480px) {
  .dash {
    padding: 20px 16px;
  }

  .dash-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 16px;
  }

  .dash-card {
    padding: 16px;
    gap: 12px;
  }

  .dash-card-icon {
    width: 38px;
    height: 38px;
  }

  .dash-card-icon svg {
    width: 18px;
    height: 18px;
  }
}
</style>
