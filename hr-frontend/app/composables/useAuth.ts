// composables/useAuth.ts
export const useAuth = () => {
  // 💡 Gunakan useCookie agar token tersimpan di browser & bertahan saat refresh
  const token = useCookie<string | null>("auth_token", {
    maxAge: 60 * 60 * 24 * 7, // Token bertahan 7 hari
    path: "/",
  });

  const user = useCookie<any>("auth_user", {
    maxAge: 60 * 60 * 24 * 7,
    path: "/",
  });

  // Fungsi simpan auth saat login berhasil
  const saveAuth = (accessToken: string, userData: any) => {
    token.value = accessToken;
    user.value = userData;
  };
  const loadAuth = () => {
    // Dengan useCookie, token otomatis ter-sync,
    // tapi fungsi ini bisa kamu pakai untuk cek apakah token valid
    return {
      token: token.value,
      user: user.value,
    };
  };
  // Fungsi logout
  const logout = () => {
    token.value = null;
    user.value = null;
    navigateTo("/login");
  };

  return {
    token,
    user,
    saveAuth,
    logout,
    loadAuth,
  };
};
