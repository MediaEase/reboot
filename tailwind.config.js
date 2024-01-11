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
}
