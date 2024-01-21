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
    themes: [
      {
        light: {
          ...require("daisyui/src/theming/themes")["[data-theme=light]"],
          "primary": "#00b6ff",
          "window-header": "#f5fff3",
          "secondary": "#3e00ff",
          "accent": "#f50000",
          "neutral": "#091802",
          "base-100": "#ffffff",
          "info": "#0074e3",
          "success": "#00bd54",
          "warning": "#ffa400",
          "error": "#c92036",
        },
        'dark': {
          ...require("daisyui/src/theming/themes")["[data-theme=dark]"],
          "primary": "#ff00d1",
          "window-header": "#0c1710",
          "secondary": "#007c9b",
          "accent": "#0079cd",
          "neutral": "#0c1710",
          "base-100": "#202529",
          "info": "#0089c7",
          "success": "#009749",
          "warning": "#cd4600",
          "error": "#ff7f7f",
        }
      },
    ],
  },
}
