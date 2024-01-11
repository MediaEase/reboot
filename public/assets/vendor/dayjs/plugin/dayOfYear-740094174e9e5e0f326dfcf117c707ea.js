/**
 * Bundled by jsDelivr using Rollup v2.79.1 and Terser v5.19.2.
 * Original file: /npm/dayjs@1.11.10/plugin/dayOfYear.js
 *
 * Do NOT use SRI with dynamically generated files! More information: https://www.jsdelivr.com/using-sri-with-dynamic-files
 */
"undefined"!=typeof globalThis?globalThis:"undefined"!=typeof window?window:"undefined"!=typeof global?global:"undefined"!=typeof self&&self;var e={exports:{}},t=e.exports=function(e,t,o){t.prototype.dayOfYear=function(e){var t=Math.round((o(this).startOf("day")-o(this).startOf("year"))/864e5)+1;return null==e?t:this.add(e-t,"day")}};export{t as default};
