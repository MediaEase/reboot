/**
 * Bundled by jsDelivr using Rollup v2.79.1 and Terser v5.19.2.
 * Original file: /npm/dayjs@1.11.10/plugin/weekYear.js
 *
 * Do NOT use SRI with dynamically generated files! More information: https://www.jsdelivr.com/using-sri-with-dynamic-files
 */
"undefined"!=typeof globalThis?globalThis:"undefined"!=typeof window?window:"undefined"!=typeof global?global:"undefined"!=typeof self&&self;var e={exports:{}},o=e.exports=function(e,o){o.prototype.weekYear=function(){var e=this.month(),o=this.week(),t=this.year();return 1===o&&11===e?t+1:0===e&&o>=52?t-1:t}};export{o as default};
