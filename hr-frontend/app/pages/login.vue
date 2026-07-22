<script setup lang="ts">
const config = useRuntimeConfig();
const email = ref("");
const password = ref("");
const errorMessage = ref("");
const loading = ref(false);

const { saveAuth } = useAuth();

definePageMeta({
  layout: false,
});
const login = async () => {
  errorMessage.value = "";
  loading.value = true;

  try {
    const response: any = await $fetch("/login", {
      baseURL: config.public.apiBase,
      method: "POST",
      body: {
        email: email.value,
        password: password.value,
      },
    });

    saveAuth(response.access_token, response.user);
    await navigateTo("/dashboard");
  } catch (error: any) {
    errorMessage.value =
      error.data?.message || "Login gagal, periksa email & password Anda.";
  } finally {
    loading.value = false;
  }
};
</script>

<template>
  <div
    class="min-h-screen bg-primary flex items-center justify-center p-4 font-sans"
  >
    <div
      class="max-w-md w-full bg-white rounded-2xl shadow-2xl overflow-hidden border border-primary-light/20"
    >
      <!-- Header Card -->
      <div
        class="bg-primary p-8 text-center text-white border-b border-primary-light/30"
      >
        <h1
          class="font-headline text-4xl font-bold tracking-wide text-primary-soft"
        >
          HR System
        </h1>

        <p class="text-xs text-gray-300 mt-1">Silahkan Login ...</p>
      </div>

      <!-- Form -->
      <form @submit.prevent="login" class="p-8 space-y-5">
        <div
          v-if="errorMessage"
          class="p-3 bg-accent-red/10 border border-accent-red/30 rounded-lg text-accent-red text-xs font-medium"
        >
          {{ errorMessage }}
        </div>

        <div>
          <label
            for="email"
            class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1"
          >
            Email Address
          </label>
          <input
            id="email"
            v-model="email"
            type="email"
            placeholder="admin@company.com"
            required
            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:bg-white transition"
          />
        </div>

        <div>
          <label
            for="password"
            class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1"
          >
            Password
          </label>
          <input
            id="password"
            v-model="password"
            type="password"
            placeholder="••••••••"
            required
            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:bg-white transition"
          />
        </div>

        <button
          type="submit"
          :disabled="loading"
          class="w-full py-3 bg-primary text-white font-medium text-sm rounded-lg hover:bg-primary-light hover:text-primary transition shadow-md disabled:opacity-50"
        >
          {{ loading ? "Memproses..." : "Sign In" }}
        </button>
      </form>
    </div>
  </div>
</template>
