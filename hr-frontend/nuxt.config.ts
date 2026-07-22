// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  devtools: {
    enabled: true,
  },

  runtimeConfig: {
    public: {
      apiBase: "http://hr-backend.test/api",
      backendUrl: "http://hr-backend.test",
    },
  },

  modules: ["@nuxtjs/tailwindcss"],
});
