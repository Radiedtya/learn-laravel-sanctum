<script setup>
import { ref, onMounted, watch } from "vue";
import { RouterLink, useRoute, useRouter } from "vue-router";
import Swal from "sweetalert2";
import AdminHeader from "./AdminHeader.vue";

const route = useRoute();
const router = useRouter();
const open = ref(false);
const email = ref("");

const menu = [
  { to: "/dashboard", label: "Dashboard", icon: "pi-th-large" },
  { to: "/kelola-film", label: "Film", icon: "pi-video" },
  // { to: "/tambah-film", label: "Tambah Film", icon: "pi-plus-circle" },
  { to: "/kelola-genre", label: "Genre", icon: "pi-tags" },
  { to: "/kelola-aktor", label: "Aktor", icon: "pi-user" },
];

const isActive = (p) => {
  if (p === "/dashboard") return route.path === "/dashboard";
  return route.path.startsWith(p);
};

onMounted(() => {
  if (!localStorage.getItem("token")) {
    router.push("/login");
    return;
  }
  try {
    email.value = JSON.parse(localStorage.getItem("user")).email;
  } catch {}
});

watch(
  () => route.path,
  () => {
    open.value = false;
  },
);

const logout = async () => {
  const r = await Swal.fire({
    title: "Keluar?",
    text: "Kamu akan keluar dari akun.",
    icon: "question",
    iconColor: "#f08070",
    showCancelButton: true,
    confirmButtonText: "Keluar",
    cancelButtonText: "Batal",
    confirmButtonColor: "#e74c3c",
    cancelButtonColor: "rgba(255,255,255,0.08)",
    color: "#d0d0e0",
    background: "#1a1a2e",
    customClass: { popup: "swal-dark" },
  });
  if (!r.isConfirmed) return;
  try {
    const api = (await import("../utils/api")).default;
    await api.post("/logout");
  } catch {
  } finally {
    localStorage.removeItem("token");
    localStorage.removeItem("user");
    router.push("/login");
  }
};
</script>

<template>
  <div class="a">
    <div class="a-bar">
      <button class="a-burger" :class="{ on: open }" @click="open = !open">
        <span></span><span></span><span></span>
      </button>
      <span class="a-bar-title">Admin Panel</span>
      <RouterLink to="/" class="a-bar-home"
        ><i class="pi pi-globe"></i
      ></RouterLink>
    </div>

    <aside class="a-side" :class="{ open }">
      <div class="a-side-in">
        <RouterLink to="/dashboard" class="a-logo" @click="open = false">
          CineVue<span class="a-tag">Admin</span>
        </RouterLink>

        <nav class="a-nav">
          <RouterLink
            v-for="m in menu"
            :key="m.to"
            :to="m.to"
            class="a-link"
            :class="{ on: isActive(m.to) }"
            @click="open = false"
          >
            <i :class="'pi ' + m.icon"></i>
            <span>{{ m.label }}</span>
          </RouterLink>
        </nav>

        <div class="a-foot">
          <div class="a-user">
            <div class="a-avatar">{{ email?.charAt(0)?.toUpperCase() }}</div>
            <div class="a-info">
              <span class="a-name">{{ email }}</span>
              <span class="a-role">Administrator</span>
            </div>
          </div>
          <div class="a-acts">
            <RouterLink to="/" class="a-act" @click="open = false">
              <i class="pi pi-globe"></i> Buka Situs
            </RouterLink>
            <button @click="logout" class="a-act a-act--red">
              <i class="pi pi-sign-out"></i> Keluar
            </button>
          </div>
        </div>
      </div>
    </aside>

    <Transition name="f">
      <div v-if="open" class="a-overlay" @click="open = false"></div>
    </Transition>

    <main class="a-main">
      <AdminHeader />
      <RouterView />
    </main>
  </div>
</template>

<style scoped>
.a {
  display: flex;
  min-height: 100vh;
}

/* Mobile bar */
.a-bar {
  display: none;
  align-items: center;
  gap: 12px;
  height: 52px;
  padding: 0 16px;
  background: var(--color-dark2);
  border-bottom: 1px solid var(--color-dark5);
}
.a-bar-title {
  font-size: 14px;
  font-weight: 700;
  color: var(--color-white);
  flex: 1;
}
.a-bar-home {
  width: 34px;
  height: 34px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--color-gray-dark);
  text-decoration: none;
  border-radius: 6px;
  transition: all 0.15s;
}
.a-bar-home:hover {
  color: var(--color-text);
  background: var(--color-dark4);
}

.a-burger {
  width: 32px;
  height: 32px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  gap: 4px;
  padding: 7px 5px;
  background: none;
  border: none;
  cursor: pointer;
}
.a-burger span {
  display: block;
  width: 100%;
  height: 1.5px;
  background: var(--color-gray-dark);
  border-radius: 1px;
  transition: all 0.25s ease;
  transform-origin: center;
}
.a-burger.on span:nth-child(1) {
  transform: translateY(5.5px) rotate(45deg);
}
.a-burger.on span:nth-child(2) {
  opacity: 0;
}
.a-burger.on span:nth-child(3) {
  transform: translateY(-5.5px) rotate(-45deg);
}

/* Sidebar */
.a-side {
  width: 220px;
  flex-shrink: 0;
  background: var(--color-dark2);
  border-right: 1px solid var(--color-dark5);
  position: sticky;
  top: 0;
  height: 100vh;
}
.a-side-in {
  display: flex;
  flex-direction: column;
  height: 100%;
  padding: 20px 0;
}
.a-logo {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 0 20px;
  margin-bottom: 24px;
  font-size: 18px;
  font-weight: 800;
  color: var(--color-primary);
  letter-spacing: -0.04em;
  text-decoration: none;
}
.a-tag {
  font-size: 10px;
  font-weight: 600;
  padding: 2px 7px;
  background: rgba(233, 69, 96, 0.12);
  color: var(--color-primary);
  border-radius: 4px;
  letter-spacing: 0.04em;
  text-transform: uppercase;
}

/* Nav */
.a-nav {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 2px;
  padding: 0 10px;
}
.a-link {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 9px 12px;
  font-size: 13px;
  font-weight: 500;
  color: var(--color-gray-dark);
  border-radius: 8px;
  text-decoration: none;
  transition: all 0.15s;
  border-left: 2px solid transparent;
}
.a-link:hover {
  color: var(--color-text);
  background: rgba(255, 255, 255, 0.02);
}
.a-link.on {
  color: var(--color-white);
  background: rgba(233, 69, 96, 0.08);
  border-left-color: var(--color-primary);
  font-weight: 600;
}
.a-link i {
  width: 18px;
  text-align: center;
  font-size: 14px;
}

/* Foot */
.a-foot {
  padding: 16px 10px 0;
  border-top: 1px solid var(--color-dark5);
  margin-top: 12px;
}
.a-user {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 0 12px;
  margin-bottom: 16px;
}
.a-avatar {
  width: 34px;
  height: 34px;
  border-radius: 8px;
  background: rgba(233, 69, 96, 0.1);
  color: var(--color-primary);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 14px;
  font-weight: 700;
  flex-shrink: 0;
}
.a-info {
  min-width: 0;
}
.a-name {
  display: block;
  font-size: 12px;
  font-weight: 600;
  color: var(--color-white);
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}
.a-role {
  font-size: 11px;
  color: var(--color-gray-dark);
}

.a-acts {
  display: flex;
  flex-direction: column;
  gap: 2px;
}
.a-act {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 8px 12px;
  font-size: 12px;
  font-weight: 500;
  color: var(--color-gray-dark);
  border-radius: 6px;
  text-decoration: none;
  border: none;
  background: none;
  cursor: pointer;
  font-family: inherit;
  width: 100%;
  transition: all 0.15s;
}
.a-act:hover {
  color: var(--color-text);
  background: rgba(255, 255, 255, 0.02);
}
.a-act i {
  width: 18px;
  text-align: center;
  font-size: 13px;
}
.a-act--red {
  color: #f08070;
}
.a-act--red:hover {
  color: #f5a090;
  background: rgba(231, 76, 60, 0.06);
}

/* Overlay */
.a-overlay {
  display: none;
}

/* Content */
.a-main {
  flex: 1;
  padding: 28px 28px 48px;
  min-width: 0;
  background: var(--color-dark);
}

.f-enter-active,
.f-leave-active {
  transition: opacity 0.15s;
}
.f-enter-from,
.f-leave-to {
  opacity: 0;
}

/* Responsive */
@media (max-width: 768px) {
  .a-bar {
    display: flex;
  }
  .a-side {
    position: fixed;
    top: 0;
    left: 0;
    z-index: 200;
    transform: translateX(-100%);
    transition: transform 0.25s ease;
  }
  .a-side.open {
    transform: translateX(0);
  }
  .a-overlay {
    display: block;
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 199;
  }
  .a-main {
    padding: 20px 16px 40px;
  }
}
</style>
