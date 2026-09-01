<script setup>
import { computed } from "vue";
import { useRoute } from "vue-router";

const route = useRoute();

const greeting = computed(() => {
  const h = new Date().getHours();
  if (h < 11) return "Selamat pagi";
  if (h < 15) return "Selamat siang";
  if (h < 18) return "Selamat sore";
  return "Selamat malam";
});

const tanggal = computed(() => {
  return new Date().toLocaleDateString("id-ID", {
    weekday: "long",
    day: "numeric",
    month: "long",
    year: "numeric",
  });
});

const crumbs = computed(() => {
  const map = {
    "/dashboard": ["Dashboard"],
    "/kelola-film": ["Dashboard", "Kelola Film"],
    "/tambah-film": ["Dashboard", "Tambah Film"],
    "/edit-film": ["Dashboard", "Kelola Film", "Edit"],
    "/kelola-genre": ["Dashboard", "Kelola Genre"],
    "/kelola-aktor": ["Dashboard", "Kelola Aktor"],
  };

  const path = route.path;

  // Cek exact match dulu
  if (map[path]) return map[path];

  // Fallback: cari prefix yang cocok
  const sorted = Object.keys(map).sort((a, b) => b.length - a.length);
  for (const key of sorted) {
    if (path.startsWith(key)) return map[key];
  }

  return ["Dashboard"];
});
</script>

<template>
  <div class="ah">
    <div class="ah-left">
      <p class="ah-greet">{{ greeting }}</p>
      <div class="ah-bread">
        <template v-for="(c, i) in crumbs" :key="i">
          <span v-if="i > 0" class="ah-sep">/</span>
          <span class="ah-crumb" :class="{ last: i === crumbs.length - 1 }">{{
            c
          }}</span>
        </template>
      </div>
    </div>
    <span class="ah-date">{{ tanggal }}</span>
  </div>
</template>

<style scoped>
.ah {
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
  margin-bottom: 24px;
  padding-bottom: 20px;
  border-bottom: 1px solid var(--color-dark5);
}

.ah-greet {
  font-size: 13px;
  color: var(--color-gray-dark);
  margin-bottom: 4px;
}

.ah-bread {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  gap: 0;
}

.ah-sep {
  margin: 0 8px;
  color: var(--color-gray-dark);
  font-size: 12px;
}

.ah-crumb {
  font-size: 13px;
  color: var(--color-gray-dark);
  font-weight: 500;
}

.ah-crumb.last {
  color: var(--color-white);
  font-weight: 700;
}

.ah-date {
  font-size: 12px;
  color: var(--color-gray-dark);
  white-space: nowrap;
}

@media (max-width: 640px) {
  .ah {
    flex-direction: column;
    align-items: flex-start;
    gap: 4px;
  }

  .ah-date {
    align-self: flex-start;
  }
}
</style>
