/** @type {import('tailwindcss').Config} */
const colors = require('tailwindcss/colors')
module.exports = {
  mode: 'jit',
  content: [
    "./assets/**/*.js",
    "./templates/**/*.html.twig",
    "./node_modules/tw-elements/dist/js/**/*.js"
  ],
  plugins: [
    require("daisyui"),
    require('@tailwindcss/forms'),
    require("tw-elements/dist/plugin.cjs"),
  ],
  darkMode: 'class',
  daisyui: {
    themes: ["winter", "synthwave"],
    darkTheme: "dark",
    base: true,
    styled: true,
    utils: true,
    prefix: "",
    logs: true,
    themeRoot: ":root",
  },
}
