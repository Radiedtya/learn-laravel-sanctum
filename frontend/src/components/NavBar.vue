<script setup>
import { ref, onMounted, onBeforeUnmount, watch } from "vue";
import { RouterLink, useRoute, useRouter } from "vue-router";
import Swal from "sweetalert2";

const route = useRoute();
const router = useRouter();

const isLoggedIn = ref(false);
const userEmail = ref("");
const userInitial = ref("");
const mobileOpen = ref(false);
const profileOpen = ref(false);
const profileRef = ref(null);

const isActive = (path) => {
  if (path === "/") return route.path === "/";
  return route.path.startsWith(path);
};

const cekAuth = () => {
  const token = localStorage.getItem("token");
  const user = localStorage.getItem("user");
  if (token && user) {
    isLoggedIn.value = true;
    try {
      const parsed = JSON.parse(user);
      userEmail.value = parsed.email;
      userInitial.value = parsed.email?.charAt(0).toUpperCase() || "U";
    } catch {
      userEmail.value = "";
      userInitial.value = "U";
    }
  } else {
    isLoggedIn.value = false;
    userEmail.value = "";
    userInitial.value = "";
  }
};

const toggleProfile = (e) => {
  e.stopPropagation();
  profileOpen.value = !profileOpen.value;
};

const closeProfile = (e) => {
  if (profileRef.value && !profileRef.value.contains(e.target)) {
    profileOpen.value = false;
  }
};

onMounted(() => {
  cekAuth();
  document.addEventListener("click", closeProfile);
});

onBeforeUnmount(() => {
  document.removeEventListener("click", closeProfile);
});

watch(
  () => route.path,
  () => {
    cekAuth();
    mobileOpen.value = false;
    profileOpen.value = false;
  }
);

const logout = async () => {
  profileOpen.value = false;
  const result = await Swal.fire({
    title: "Yakin mau Keluar?",
    text: "Kamu akan keluar dari akun.",
    icon: "question",
    iconColor: "#f08070",
    showCancelButton: true,
    confirmButtonText: "Ya",
    cancelButtonText: "Batal",
    confirmButtonColor: "#e74c3c",
    cancelButtonColor: "rgba(255,255,255,0.08)",
    color: "#d0d0e0",
    background: "#1a1a2e",
    customClass: { popup: "swal-dark" },
  });

  if (!result.isConfirmed) return;

  try {
    const api = (await import("../utils/api")).default;
    await api.post("/logout");
  } catch {
    /* lanjut */
  } finally {
    localStorage.removeItem("token");
    localStorage.removeItem("user");
    mobileOpen.value = false;
    router.push("/");
    cekAuth();
  }
};
</script>

<template>
  <nav class="nav">
    <div class="nav-inner">
      <RouterLink to="/" class="nav-logo">CineVue</RouterLink>

      <!-- Tengah -->
      <div class="nav-center">
        <RouterLink
          to="/"
          class="nav-link"
          :class="{ on: isActive('/') }"
          >Film</RouterLink
        >
        <RouterLink
          to="/genre"
          class="nav-link"
          :class="{ on: isActive('/genre') }"
          >Genre</RouterLink
        >
      </div>

      <!-- Kanan -->
      <div class="nav-right">
        <template v-if="!isLoggedIn">
          <RouterLink
            to="/login"
            class="nav-link"
            :class="{ on: isActive('/login') }"
            >Login</RouterLink
          >
          <RouterLink to="/register" class="nav-cta">Daftar</RouterLink>
        </template>

        <template v-else>
          <div class="profile-wrap" ref="profileRef">
            <button class="profile-btn" @click="toggleProfile">
              <span class="profile-initial">{{ userInitial }}</span>
            </button>

            <Transition name="drop">
              <div v-if="profileOpen" class="profile-drop">
                <div class="profile-drop-head">{{ userEmail }}</div>
                <RouterLink
                  to="/dashboard"
                  class="profile-drop-item"
                  @click="profileOpen = false"
                >
                  <i class="pi pi-th-large"></i>
                  Dashboard
                </RouterLink>
                <button
                  class="profile-drop-item profile-drop-item--red"
                  @click="logout"
                >
                  <i class="pi pi-sign-out"></i>
                  Keluar
                </button>
              </div>
            </Transition>
          </div>
        </template>
      </div>

      <!-- Burger -->
      <button
        class="burger"
        :class="{ on: mobileOpen }"
        @click="mobileOpen = !mobileOpen"
      >
        <span></span>
        <span></span>
        <span></span>
      </button>
    </div>

    <!-- Mobile -->
    <Transition name="mob">
      <div v-if="mobileOpen" class="mob">
        <RouterLink
          to="/"
          class="mob-link"
          :class="{ on: isActive('/') }"
          @click="mobileOpen = false"
          >Film</RouterLink
        >
        <RouterLink
          to="/genre"
          class="mob-link"
          :class="{ on: isActive('/genre') }"
          @click="mobileOpen = false"
          >Genre</RouterLink
        >

        <template v-if="!isLoggedIn">
          <RouterLink
            to="/login"
            class="mob-link"
            :class="{ on: isActive('/login') }"
            @click="mobileOpen = false"
            >Login</RouterLink
          >
          <RouterLink
            to="/register"
            class="mob-link mob-link--cta"
            @click="mobileOpen = false"
            >Daftar</RouterLink
          >
        </template>

        <template v-else>
          <RouterLink
            to="/dashboard"
            class="mob-link"
            :class="{ on: isActive('/dashboard') }"
            @click="mobileOpen = false"
            >Dashboard</RouterLink
          >
          <div class="mob-foot">
            <span>{{ userEmail }}</span>
            <button @click="logout" class="mob-out">
              <i class="pi pi-sign-out"></i> Keluar
            </button>
          </div>
        </template>
      </div>
    </Transition>
  </nav>
</template>

<style scoped>
.nav {
  position: sticky;
  top: 0;
  z-index: 100;
  background: var(--color-dark2);
  border-bottom: 1px solid var(--color-dark5);
}

.nav-inner {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 24px;
  height: 56px;
  display: flex;
  align-items: center;
}

/* Logo */
.nav-logo {
  flex-shrink: 0;
  font-size: 18px;
  font-weight: 800;
  color: var(--color-primary);
  letter-spacing: -0.04em;
  text-decoration: none;
}

/* Center links */
.nav-center {
  flex: 1;
  display: flex;
  justify-content: center;
  gap: 4px;
}

/* Right */
.nav-right {
  flex-shrink: 0;
  display: flex;
  align-items: center;
  gap: 4px;
}

/* Links */
.nav-link {
  position: relative;
  padding: 6px 14px;
  font-size: 13px;
  font-weight: 500;
  color: var(--color-gray-dark);
  border-radius: 6px;
  text-decoration: none;
  transition: color 0.15s;
}

.nav-link:hover {
  color: var(--color-text);
}

.nav-link.on {
  color: var(--color-white);
}

.nav-link.on::after {
  content: "";
  position: absolute;
  bottom: -12px;
  left: 14px;
  right: 14px;
  height: 2px;
  background: var(--color-primary);
  border-radius: 1px;
}

/* CTA */
.nav-cta {
  padding: 6px 16px;
  font-size: 12px;
  font-weight: 600;
  color: white;
  background: var(--color-primary);
  border-radius: 6px;
  text-decoration: none;
  margin-left: 4px;
  transition: opacity 0.15s;
}

.nav-cta:hover {
  opacity: 0.85;
  color: white;
}

/* ── Profile ──────────────────────────────────────── */
.profile-wrap {
  position: relative;
}

.profile-btn {
  width: 34px;
  height: 34px;
  border-radius: 50%;
  border: 2px solid var(--color-dark5);
  background: var(--color-dark4);
  color: var(--color-primary);
  font-weight: 700;
  font-size: 13px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: border-color 0.2s;
  padding: 0;
}

.profile-btn:hover {
  border-color: var(--color-primary);
}

.profile-initial {
  line-height: 1;
}

.profile-drop {
  position: absolute;
  top: calc(100% + 8px);
  right: 0;
  width: 210px;
  background: var(--color-dark3);
  border: 1px solid var(--color-dark5);
  border-radius: var(--radius);
  overflow: hidden;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.5);
}

.profile-drop-head {
  padding: 12px 14px;
  font-size: 12px;
  color: var(--color-gray-dark);
  border-bottom: 1px solid var(--color-dark5);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.profile-drop-item {
  display: flex;
  align-items: center;
  gap: 10px;
  width: 100%;
  padding: 10px 14px;
  font-size: 13px;
  font-weight: 500;
  color: var(--color-text);
  background: none;
  border: none;
  cursor: pointer;
  text-decoration: none;
  font-family: inherit;
  transition: background 0.15s;
}

.profile-drop-item:hover {
  background: var(--color-dark4);
}

.profile-drop-item i {
  font-size: 14px;
  width: 18px;
  text-align: center;
  color: var(--color-gray-dark);
}

.profile-drop-item--red {
  color: #f08070;
}

.profile-drop-item--red i {
  color: #f08070;
}

.profile-drop-item--red:hover {
  background: rgba(231, 76, 60, 0.08);
}

/* Dropdown transition */
.drop-enter-active,
.drop-leave-active {
  transition: all 0.15s ease;
}

.drop-enter-from,
.drop-leave-to {
  opacity: 0;
  transform: translateY(-4px);
}

/* ── Burger ───────────────────────────────────────── */
.burger {
  display: none;
  flex-direction: column;
  justify-content: center;
  gap: 4px;
  width: 32px;
  height: 32px;
  padding: 7px 5px;
  background: none;
  border: none;
  cursor: pointer;
}

.burger span {
  display: block;
  width: 100%;
  height: 1.5px;
  background: var(--color-gray-dark);
  border-radius: 1px;
  transition: all 0.25s ease;
  transform-origin: center;
}

.burger.on span:nth-child(1) {
  transform: translateY(5.5px) rotate(45deg);
}
.burger.on span:nth-child(2) {
  opacity: 0;
}
.burger.on span:nth-child(3) {
  transform: translateY(-5.5px) rotate(-45deg);
}

/* ── Mobile Menu ──────────────────────────────────── */
.mob {
  display: none;
  flex-direction: column;
  padding: 4px 24px 20px;
  background: var(--color-dark2);
  border-bottom: 1px solid var(--color-dark5);
}

.mob-link {
  padding: 10px 8px;
  font-size: 14px;
  font-weight: 500;
  color: var(--color-gray-dark);
  text-decoration: none;
  border-radius: 6px;
  transition: color 0.15s, background 0.15s;
}

.mob-link:hover {
  color: var(--color-text);
}

.mob-link.on {
  color: var(--color-primary);
  font-weight: 600;
}

.mob-link--cta {
  color: var(--color-primary);
  font-weight: 600;
}

.mob-foot {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding-top: 12px;
  margin-top: 4px;
  border-top: 1px solid var(--color-dark5);
  font-size: 13px;
  color: var(--color-gray-dark);
}

.mob-out {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 6px 12px;
  font-size: 12px;
  font-weight: 600;
  font-family: inherit;
  color: #f08070;
  background: none;
  border: 1px solid rgba(231, 76, 60, 0.15);
  border-radius: 6px;
  cursor: pointer;
  transition: background 0.15s;
}

.mob-out:hover {
  background: rgba(231, 76, 60, 0.1);
}

/* Mobile transition */
.mob-enter-active,
.mob-leave-active {
  transition: all 0.2s ease;
}

.mob-enter-from,
.mob-leave-to {
  opacity: 0;
}

/* ── Responsive ───────────────────────────────────── */
@media (max-width: 640px) {
  .nav-center,
  .nav-right {
    display: none;
  }

  .burger {
    display: flex;
    margin-left: auto;
  }

  .mob {
    display: flex;
  }
}
</style>