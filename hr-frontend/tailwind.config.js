/** @type {import('tailwindcss').Config} */
module.exports = {
  theme: {
    extend: {
      // Font Family
      fontFamily: {
        sans: ["Open Sans", "sans-serif"], // Default Font
        headline: ["K2D", "sans-serif"], // Headline Font
      },
      // 2. Palette Warna Key Value
      colors: {
        primary: {
          DEFAULT: "#162D5D",
          light: "#C7A670",
          soft: "#EFCD9C",
        },
        accent: {
          red: "#82161A",
          "red-bright": "#BD242A",
          blue: "#164A82",
          "blue-light": "#2B6A8C",
          teal: "#045B62",
          green: "#17695D",
          gold: "#D4A674",
          sand: "#E8D1B7",
        },
      },
    },
  },
};
