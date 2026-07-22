<script setup lang="ts">
const { user, logout } = useAuth();
const isSidebarOpen = ref(false);
const { isDark, toggleDarkMode, initTheme } = useDarkMode();

onMounted(() => {
  initTheme();
});

const handleLogout = async () => {
  await logout();
  await navigateTo("/login");
};
</script>

<template>
  <!-- 🔴 1. Tambahkan dark:bg-gray-900 & dark:text-gray-100 di container paling luar -->
  <div
    class="min-h-screen bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 flex font-sans transition-colors duration-200"
  >
    <!-- Sidebar Mobile Overlay -->
    <div
      v-if="isSidebarOpen"
      @click="isSidebarOpen = false"
      class="fixed inset-0 bg-black/50 z-40 lg:hidden"
    ></div>

    <!-- Sidebar Main -->
    <aside
      :class="[
        'fixed top-0 bottom-0 left-0 z-50 w-64 bg-primary dark:bg-gray-950 text-white flex flex-col transition-transform duration-300 lg:translate-x-0 lg:static border-r border-transparent dark:border-gray-800',
        isSidebarOpen ? 'translate-x-0' : '-translate-x-full',
      ]"
    >
      <!-- Sidebar Brand Header -->
      <div
        class="p-6 border-b border-primary-light/20 flex justify-between items-center"
      >
        <h1
          class="font-headline text-2xl font-bold text-primary-light tracking-wide"
        >
          {{ user?.role === "candidate" ? "Portal Pelamar" : "HR System" }}
        </h1>
        <button
          @click="isSidebarOpen = false"
          class="lg:hidden text-gray-400 hover:text-white"
        >
          ✕
        </button>
      </div>

      <!-- Navigation Links -->
      <nav class="flex-1 p-4 space-y-1.5 overflow-y-auto">
        <!-- 1. Jika Role Candidate -->
        <template v-if="user?.role === 'candidate'">
          <NuxtLink
            to="/dashboard"
            @click="isSidebarOpen = false"
            class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition hover:bg-white/10"
            active-class="bg-primary-light text-primary font-bold shadow-sm"
          >
            <span>👤</span> Data Diri
          </NuxtLink>
        </template>

        <!-- 2. Jika Role Interviewer (Hanya Candidate List) -->
        <template v-else-if="user?.role === 'interviewer'">
          <NuxtLink
            to="/candidates"
            @click="isSidebarOpen = false"
            class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition hover:bg-white/10"
            active-class="bg-primary-light text-primary font-bold shadow-sm"
          >
            <span>👥</span> Candidate List
          </NuxtLink>
        </template>

        <!-- 3. Jika Role Admin / HR / Lainnya -->
        <template v-else>
          <NuxtLink
            to="/dashboard"
            @click="isSidebarOpen = false"
            class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition hover:bg-white/10"
            active-class="bg-primary-light text-primary font-bold shadow-sm"
          >
            <span>📊</span> Dashboard
          </NuxtLink>

          <NuxtLink
            to="/candidates"
            @click="isSidebarOpen = false"
            class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition hover:bg-white/10"
            active-class="bg-primary-light text-primary font-bold shadow-sm"
          >
            <span>👥</span> Candidate List
          </NuxtLink>

          <NuxtLink
            to="/jobs"
            @click="isSidebarOpen = false"
            class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition hover:bg-white/10"
            active-class="bg-primary-light text-primary font-bold shadow-sm"
          >
            <span>💼</span> List Lowongan Kerja
          </NuxtLink>
        </template>
      </nav>

      <!-- Sidebar Footer / Logout -->
      <div class="p-4 border-t border-primary-light/20">
        <button
          @click="handleLogout"
          class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-primary-light text-accent-red hover:bg-accent-red hover:text-white font-semibold text-xs transition"
        >
          LOGOUT
        </button>
      </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0">
      <!-- 🔴 2. Top Navbar disesuaikan warna dark-nya -->
      <header
        class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-4 py-3 flex justify-between items-center shadow-sm transition-colors duration-200"
      >
        <div class="flex items-center gap-3">
          <button
            @click="isSidebarOpen = true"
            class="lg:hidden p-2 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 text-sm"
          >
            🍔 Menu
          </button>

          <!-- Tombol Dark Mode Toggle -->
          <button
            @click="toggleDarkMode"
            class="p-2 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-sm flex items-center gap-1.5 transition font-medium"
          >
            <span>{{ isDark ? "☀️ Light Mode" : "🌙 Dark Mode" }}</span>
          </button>
        </div>

        <div class="flex items-center gap-3">
          <div class="text-right">
            <p class="text-xs font-bold text-gray-800 dark:text-gray-100">
              {{ user?.name || "User" }}
            </p>
            <p class="text-[10px] text-gray-500 dark:text-gray-400 capitalize">
              {{ user?.role || user?.email || "user@company.com" }}
            </p>
          </div>
          <div
            class="w-8 h-8 rounded-full bg-primary dark:bg-primary-light text-primary-light dark:text-primary flex items-center justify-center font-bold text-xs shadow-sm"
          >
            {{ (user?.name || "U").charAt(0).toUpperCase() }}
          </div>
        </div>
      </header>

      <!-- Page Content Slot -->
      <main class="flex-1 overflow-y-auto p-4 md:p-6">
        <slot />
      </main>
    </div>
  </div>
</template>
