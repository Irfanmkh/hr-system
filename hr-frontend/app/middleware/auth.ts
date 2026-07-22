export default defineNuxtRouteMiddleware((to) => {
  const { token, user } = useAuth();

  // Jika token tidak ada dan mencoba akses halaman selain login
  if (!token.value && to.path !== "/login") {
    return navigateTo("/login");
  }

  if (user.value?.role === "candidate") {
    if (to.path !== "/dashboard") {
      return navigateTo("/dashboard");
    }
  }

  if (user.value?.role === "interviewer") {
    if (!to.path.startsWith("/candidates")) {
      return navigateTo("/candidates");
    }
  }
});
