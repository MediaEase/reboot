/**
 * Bundled by jsDelivr using Rollup v2.79.1 and Terser v5.19.2.
 * Original file: /npm/dayjs@1.11.10/plugin/weekOfYear.js
 *
 * Do NOT use SRI with dynamically generated files! More information: https://www.jsdelivr.com/using-sri-with-dynamic-files
 */
"undefined"!=typeof globalThis?globalThis:"undefined"!=typeof window?window:"undefined"!=typeof global?global:"undefined"!=typeof self&&self;var e,t,i={exports:{}},a=i.exports=(e="week",t="year",function(i,a,n){var r=a.prototype;r.week=function(i){if(void 0===i&&(i=null),null!==i)return this.add(7*(i-this.week()),"day");var a=this.$locale().yearStart||1;if(11===this.month()&&this.date()>25){var r=n(this).startOf(t).add(1,t).date(a),s=n(this).endOf(e);if(r.isBefore(s))return 1}var f=n(this).startOf(t).date(a).startOf(e).subtract(1,"millisecond"),d=this.diff(f,e,!0);return d<0?n(this).startOf("week").week():Math.ceil(d)},r.weeks=function(e){return void 0===e&&(e=null),this.week(e)}});export{a as default};
