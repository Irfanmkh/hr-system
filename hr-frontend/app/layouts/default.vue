<script setup lang="ts">
const { user, logout } = useAuth();
const isSidebarOpen = ref(false);

const handleLogout = async () => {
  await logout();
  await navigateTo("/login");
};
</script>

<template>
  <div class="min-h-screen bg-gray-50 flex font-sans">
    <!-- Sidebar Mobile Overlay -->
    <div
      v-if="isSidebarOpen"
      @click="isSidebarOpen = false"
      class="fixed inset-0 bg-black/50 z-40 lg:hidden"
    ></div>

    <!-- Sidebar Main -->
    <aside
      :class="[
        'fixed top-0 bottom-0 left-0 z-50 w-64 bg-primary text-white flex flex-col transition-transform duration-300 lg:translate-x-0 lg:static',
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
          HR System
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
          <span>💼</span> Job List CRUD
        </NuxtLink>
      </nav>

      <!-- Sidebar Footer / Logout -->
      <div class="p-4 border-t border-primary-light/20">
        <button
          @click="handleLogout"
          class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-accent-red/20 text-accent-red hover:bg-accent-red hover:text-white font-semibold text-xs transition"
        >
          <span>🚪</span> Logout
        </button>
      </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0">
      <!-- Top Navbar (Mobile Hamburger & User Profile) -->
      <header
        class="bg-white border-b border-gray-200 px-4 py-3 flex justify-between items-center shadow-sm"
      >
        <div class="flex items-center gap-3">
          <button
            @click="isSidebarOpen = true"
            class="lg:hidden p-2 rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200 text-sm"
          >
            🍔 Menu
          </button>
          <span
            class="font-headline font-semibold text-primary hidden sm:inline text-sm"
          >
            Recruitment Portal
          </span>
        </div>

        <div class="flex items-center gap-3">
          <div class="text-right">
            <p class="text-xs font-bold text-gray-800">
              {{ user?.name || "Administrator" }}
            </p>
            <p class="text-[10px] text-gray-500">
              {{ user?.email || "admin@company.com" }}
            </p>
          </div>
          <div
            class="w-8 h-8 rounded-full bg-primary text-primary-light flex items-center justify-center font-bold text-xs shadow-sm"
          >
            {{ (user?.name || "A").charAt(0).toUpperCase() }}
          </div>
        </div>
      </header>

      <!-- Page Content Slot -->
      <main class="flex-1 overflow-y-auto">
        <slot />
      </main>
    </div>
  </div>
</template>
