<script setup>
import { ref, onMounted } from "vue";
import { useRouter, RouterLink } from "vue-router";

const router = useRouter();
const user = ref(null);

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
</script>

<template>
  <div class="dash">
    <div class="dash-header">
      <div>
        <h1 class="dash-title">Dashboard</h1>
        <p class="dash-sub">{{ user?.email }}</p>
      </div>
    </div>

    <div class="dash-grid">
      <RouterLink to="/kelola-film" class="dash-card">
        <div class="dash-card-icon">
          <i class="pi pi-video"></i>
        </div>
        <div class="dash-card-body">
          <h3>Kelola Film</h3>
          <p>Edit dan hapus data film</p>
        </div>
        <i class="pi pi-arrow-right dash-card-arrow"></i>
      </RouterLink>

      <RouterLink to="/tambah-film" class="dash-card">
        <div class="dash-card-icon dash-card-icon--green">
          <i class="pi pi-plus"></i>
        </div>
        <div class="dash-card-body">
          <h3>Tambah Film</h3>
          <p>Input film baru ke database</p>
        </div>
        <i class="pi pi-arrow-right dash-card-arrow"></i>
      </RouterLink>

      <RouterLink to="/kelola-genre" class="dash-card">
        <div class="dash-card-icon dash-card-icon--yellow">
          <i class="pi pi-tags"></i>
        </div>
        <div class="dash-card-body">
          <h3>Kelola Genre</h3>
          <p>Tambah, edit, dan hapus genre</p>
        </div>
        <i class="pi pi-arrow-right dash-card-arrow"></i>
      </RouterLink>

      <RouterLink to="/kelola-aktor" class="dash-card">
        <div class="dash-card-icon dash-card-icon--teal">
          <i class="pi pi-user"></i>
        </div>
        <div class="dash-card-body">
          <h3>Kelola Aktor</h3>
          <p>Tambah, edit, dan hapus aktor</p>
        </div>
        <i class="pi pi-arrow-right dash-card-arrow"></i>
      </RouterLink>

      <RouterLink to="/" class="dash-card">
        <div class="dash-card-icon dash-card-icon--blue">
          <i class="pi pi-eye"></i>
        </div>
        <div class="dash-card-body">
          <h3>Lihat Katalog</h3>
          <p>Buka halaman publik</p>
        </div>
        <i class="pi pi-arrow-right dash-card-arrow"></i>
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

.dash-header {
  margin-bottom: 32px;
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

.dash-grid {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.dash-card {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 18px 20px;
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
  width: 42px;
  height: 42px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 10px;
  background: rgba(233, 69, 96, 0.1);
  color: var(--color-primary);
  font-size: 18px;
}

.dash-card-icon--green {
  background: rgba(39, 174, 96, 0.1);
  color: #5ddb9a;
}

.dash-card-icon--yellow {
  background: rgba(241, 196, 15, 0.1);
  color: #f1c40f;
}

.dash-card-icon--teal {
  background: rgba(26, 188, 156, 0.1);
  color: #1abc9c;
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
  font-size: 14px;
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
  font-size: 14px;
  transition: transform var(--transition), color var(--transition);
}

.dash-card:hover .dash-card-arrow {
  transform: translateX(3px);
  color: var(--color-text);
}

@media (max-width: 480px) {
  .dash {
    padding: 20px 16px;
  }

  .dash-card {
    padding: 14px 16px;
    gap: 12px;
  }

  .dash-card-icon {
    width: 36px;
    height: 36px;
    font-size: 15px;
  }
}
</style>