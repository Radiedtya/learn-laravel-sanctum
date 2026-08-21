<script setup>
import { ref, computed, onMounted, watch } from "vue";
import { RouterLink, useRoute, useRouter } from "vue-router";

const route = useRoute();
const router = useRouter();

const isLoggedIn = ref(false);
const userEmail = ref("");
const menuOpen = ref(false);

const cekAuth = () => {
  const token = localStorage.getItem("token");
  const user = localStorage.getItem("user");

  if (token && user) {
    isLoggedIn.value = true;
    try {
      userEmail.value = JSON.parse(user).email;
    } catch {
      userEmail.value = "";
    }
  } else {
    isLoggedIn.value = false;
    userEmail.value = "";
  }
};

// Cek tiap kali route berubah (catch logout dari dashboard)
onMounted(cekAuth);
watch(() => route.path, cekAuth);

const logout = async () => {
  try {
    const api = (await import("./utils/api")).default;
    await api.post("/logout");
  } catch {
    /* lanjut meski error */
  } finally {
    localStorage.removeItem("token");
    localStorage.removeItem("user");
    menuOpen.value = false;
    router.push("/");
    cekAuth();
  }
};

const tutupMenu = () => {
  menuOpen.value = false;
};
</script>

<template>
  <div class="app-shell">
    <!-- Navbar -->
    <nav class="nav">
      <div class="nav-inner">
        <RouterLink to="/" class="nav-logo" @click="tutupMenu"
          >CineVue</RouterLink
        >

        <!-- Desktop nav -->
        <div class="nav-links">
          <RouterLink to="/" class="nav-link">Film</RouterLink>

          <template v-if="!isLoggedIn">
            <RouterLink to="/login" class="nav-link">Login</RouterLink>
            <RouterLink to="/register" class="nav-btn">Daftar</RouterLink>
          </template>

          <template v-else>
            <RouterLink to="/dashboard" class="nav-link">Dashboard</RouterLink>
            <div class="nav-user">
              <span class="nav-email">{{ userEmail }}</span>
              <button @click="logout" class="nav-logout">Keluar</button>
            </div>
          </template>
        </div>

        <!-- Hamburger -->
        <button
          class="nav-hamburger"
          :class="{ active: menuOpen }"
          @click="menuOpen = !menuOpen"
          aria-label="Menu"
        >
          <span></span>
          <span></span>
          <span></span>
        </button>
      </div>

      <!-- Mobile menu -->
      <Transition name="slide">
        <div v-if="menuOpen" class="nav-mobile">
          <RouterLink to="/" class="nav-m-link" @click="tutupMenu"
            >Film</RouterLink
          >

          <template v-if="!isLoggedIn">
            <RouterLink to="/login" class="nav-m-link" @click="tutupMenu"
              >Login</RouterLink
            >
            <RouterLink
              to="/register"
              class="nav-m-link nav-m-link--accent"
              @click="tutupMenu"
              >Daftar</RouterLink
            >
          </template>

          <template v-else>
            <RouterLink to="/dashboard" class="nav-m-link" @click="tutupMenu"
              >Dashboard</RouterLink
            >
            <div class="nav-m-user">
              <span>{{ userEmail }}</span>
              <button @click="logout" class="nav-m-logout">Keluar</button>
            </div>
          </template>
        </div>
      </Transition>
    </nav>

    <!-- Page content -->
    <main>
      <RouterView />
    </main>
  </div>
</template>

<style scoped>
/* ── Shell ──────────────────────────────────────────────── */
.app-shell {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

main {
  flex: 1;
}

/* ── Navbar ─────────────────────────────────────────────── */
.nav {
  position: sticky;
  top: 0;
  z-index: 100;
  background: rgba(10, 10, 18, 0.85);
  backdrop-filter: blur(16px);
  border-bottom: 1px solid var(--color-dark5);
}

.nav-inner {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 24px;
  height: 60px;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

/* Logo */
.nav-logo {
  font-size: 20px;
  font-weight: 800;
  color: var(--color-primary);
  letter-spacing: -0.03em;
  text-decoration: none;
  transition: opacity var(--transition);
}

.nav-logo:hover {
  opacity: 0.8;
  color: var(--color-primary);
}

/* Desktop links */
.nav-links {
  display: flex;
  align-items: center;
  gap: 8px;
}

.nav-link {
  padding: 8px 14px;
  font-size: 13px;
  font-weight: 500;
  color: var(--color-text-dim);
  border-radius: var(--radius);
  text-decoration: none;
  transition:
    color var(--transition),
    background var(--transition);
}

.nav-link:hover {
  color: var(--color-white);
  background: rgba(255, 255, 255, 0.05);
}

/* Tombol Daftar */
.nav-btn {
  padding: 7px 18px;
  font-size: 13px;
  font-weight: 600;
  color: white;
  background: var(--color-primary);
  border-radius: var(--radius);
  text-decoration: none;
  transition:
    background var(--transition),
    box-shadow var(--transition);
}

.nav-btn:hover {
  background: var(--color-primary-dim);
  box-shadow: 0 2px 12px rgba(233, 69, 96, 0.25);
  color: white;
}

/* User area (logged in) */
.nav-user {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-left: 8px;
  padding-left: 16px;
  border-left: 1px solid var(--color-dark5);
}

.nav-email {
  font-size: 12px;
  color: var(--color-gray-dark);
  max-width: 160px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.nav-logout {
  padding: 6px 14px;
  font-size: 12px;
  font-weight: 600;
  font-family: inherit;
  color: #f08070;
  background: rgba(231, 76, 60, 0.08);
  border: 1px solid rgba(231, 76, 60, 0.15);
  border-radius: var(--radius);
  cursor: pointer;
  transition: all var(--transition);
}

.nav-logout:hover {
  background: rgba(231, 76, 60, 0.15);
  border-color: rgba(231, 76, 60, 0.3);
}

/* ── Hamburger ──────────────────────────────────────────── */
.nav-hamburger {
  display: none;
  flex-direction: column;
  justify-content: center;
  gap: 5px;
  width: 36px;
  height: 36px;
  padding: 8px 6px;
  background: none;
  border: none;
  cursor: pointer;
}

.nav-hamburger span {
  display: block;
  width: 100%;
  height: 2px;
  background: var(--color-text);
  border-radius: 2px;
  transition: all 0.3s ease;
  transform-origin: center;
}

.nav-hamburger.active span:nth-child(1) {
  transform: translateY(7px) rotate(45deg);
}

.nav-hamburger.active span:nth-child(2) {
  opacity: 0;
}

.nav-hamburger.active span:nth-child(3) {
  transform: translateY(-7px) rotate(-45deg);
}

/* ── Mobile menu ────────────────────────────────────────── */
.nav-mobile {
  display: none;
  flex-direction: column;
  padding: 8px 24px 20px;
  border-top: 1px solid var(--color-dark5);
  background: rgba(10, 10, 18, 0.95);
  backdrop-filter: blur(16px);
}

.nav-m-link {
  display: block;
  padding: 12px 0;
  font-size: 14px;
  font-weight: 500;
  color: var(--color-text-dim);
  text-decoration: none;
  border-bottom: 1px solid var(--color-dark5);
  transition: color var(--transition);
}

.nav-m-link:hover {
  color: var(--color-white);
}

.nav-m-link--accent {
  color: var(--color-primary);
  font-weight: 600;
}

.nav-m-user {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 16px 0 4px;
  font-size: 13px;
  color: var(--color-gray-dark);
}

.nav-m-logout {
  padding: 6px 14px;
  font-size: 12px;
  font-weight: 600;
  font-family: inherit;
  color: #f08070;
  background: rgba(231, 76, 60, 0.08);
  border: 1px solid rgba(231, 76, 60, 0.15);
  border-radius: var(--radius);
  cursor: pointer;
}

/* Slide transition */
.slide-enter-active,
.slide-leave-active {
  transition: all 0.25s ease;
}

.slide-enter-from,
.slide-leave-to {
  opacity: 0;
  transform: translateY(-8px);
}

/* ── Responsive ─────────────────────────────────────────── */
@media (max-width: 640px) {
  .nav-links {
    display: none;
  }

  .nav-hamburger {
    display: flex;
  }

  .nav-mobile {
    display: flex;
  }
}
</style>
