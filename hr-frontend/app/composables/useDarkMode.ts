// composables/useDarkMode.ts
export const useDarkMode = () => {
  const isDark = useState<boolean>("isDark", () => false);

  const toggleDarkMode = () => {
    isDark.value = !isDark.value;
    if (import.meta.client) {
      if (isDark.value) {
        document.documentElement.classList.add("dark");
        localStorage.setItem("theme", "dark");
      } else {
        document.documentElement.classList.remove("dark");
        localStorage.setItem("theme", "light");
      }
    }
  };

  const initTheme = () => {
    if (import.meta.client) {
      const savedTheme = localStorage.getItem("theme");
      if (
        savedTheme === "dark" ||
        (!savedTheme &&
          window.matchMedia("(prefers-color-scheme: dark)").matches)
      ) {
        isDark.value = true;
        document.documentElement.classList.add("dark");
      } else {
        isDark.value = false;
        document.documentElement.classList.remove("dark");
      }
    }
  };

  return { isDark, toggleDarkMode, initTheme };
};
