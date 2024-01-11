/**
 * Bundled by jsDelivr using Rollup v2.79.1 and Terser v5.19.2.
 * Original file: /npm/normalize-wheel-es@1.2.0/dist/index.mjs
 *
 * Do NOT use SRI with dynamically generated files! More information: https://www.jsdelivr.com/using-sri-with-dynamic-files
 */
var e=!1,n,t,i,r,a,o,d,u,c,l,s,f,p,w,m;function v(){if(!e){e=!0;var v=navigator.userAgent,x=/(?:MSIE.(\d+\.\d+))|(?:(?:Firefox|GranParadiso|Iceweasel).(\d+\.\d+))|(?:Opera(?:.+Version.|.)(\d+\.\d+))|(?:AppleWebKit.(\d+(?:\.\d+)?))|(?:Trident\/\d+\.\d+.*rv:(\d+\.\d+))/.exec(v),h=/(Mac OS X)|(Windows)|(Linux)/.exec(v);if(f=/\b(iPhone|iP[ao]d)/.exec(v),p=/\b(iP[ao]d)/.exec(v),l=/Android/i.exec(v),w=/FBAN\/\w+;/i.exec(v),m=/Mobile/i.exec(v),s=!!/Win64/.exec(v),x){n=x[1]?parseFloat(x[1]):x[5]?parseFloat(x[5]):NaN,n&&document&&document.documentMode&&(n=document.documentMode);var N=/(?:Trident\/(\d+.\d+))/.exec(v);o=N?parseFloat(N[1])+4:n,t=x[2]?parseFloat(x[2]):NaN,i=x[3]?parseFloat(x[3]):NaN,r=x[4]?parseFloat(x[4]):NaN,r?(x=/(?:Chrome\/(\d+\.\d+))/.exec(v),a=x&&x[1]?parseFloat(x[1]):NaN):a=NaN}else n=t=i=a=r=NaN;if(h){if(h[1]){var M=/(?:Mac OS X (\d+(?:[._]\d+)?))/.exec(v);d=M?parseFloat(M[1].replace("_",".")):!0}else d=!1;u=!!h[2],c=!!h[3]}else d=u=c=!1}}var x={ie:function(){return v()||n},ieCompatibilityMode:function(){return v()||o>n},ie64:function(){return x.ie()&&s},firefox:function(){return v()||t},opera:function(){return v()||i},webkit:function(){return v()||r},safari:function(){return x.webkit()},chrome:function(){return v()||a},windows:function(){return v()||u},osx:function(){return v()||d},linux:function(){return v()||c},iphone:function(){return v()||f},mobile:function(){return v()||f||p||l||m},nativeApp:function(){return v()||w},android:function(){return v()||l},ipad:function(){return v()||p}},h=x;var N=!!(typeof window<"u"&&window.document&&window.document.createElement),M={canUseDOM:N,canUseWorkers:typeof Worker<"u",canUseEventListeners:N&&!!(window.addEventListener||window.attachEvent),canUseViewport:N&&!!window.screen,isInWorker:!N},F=M;var D;F.canUseDOM&&(D=document.implementation&&document.implementation.hasFeature&&document.implementation.hasFeature("","")!==!0);function b(e,n){if(!F.canUseDOM||n&&!("addEventListener"in document))return!1;var t="on"+e,i=t in document;if(!i){var r=document.createElement("div");r.setAttribute(t,"return;"),i=typeof r[t]=="function"}return!i&&D&&e==="wheel"&&(i=document.implementation.hasFeature("Events.wheel","3.0")),i}var E=b;var O=10,X=40,A=800;function U(e){var n=0,t=0,i=0,r=0;return"detail"in e&&(t=e.detail),"wheelDelta"in e&&(t=-e.wheelDelta/120),"wheelDeltaY"in e&&(t=-e.wheelDeltaY/120),"wheelDeltaX"in e&&(n=-e.wheelDeltaX/120),"axis"in e&&e.axis===e.HORIZONTAL_AXIS&&(n=t,t=0),i=n*O,r=t*O,"deltaY"in e&&(r=e.deltaY),"deltaX"in e&&(i=e.deltaX),(i||r)&&e.deltaMode&&(e.deltaMode==1?(i*=X,r*=X):(i*=A,r*=A)),i&&!n&&(n=i<1?-1:1),r&&!t&&(t=r<1?-1:1),{spinX:n,spinY:t,pixelX:i,pixelY:r}}U.getEventType=function(){return h.firefox()?"DOMMouseScroll":E("wheel")?"wheel":"mousewheel"};var W=U;
/**
 * Checks if an event is supported in the current execution environment.
 *
 * NOTE: This will not work correctly for non-generic events such as `change`,
 * `reset`, `load`, `error`, and `select`.
 *
 * Borrows from Modernizr.
 *
 * @param {string} eventNameSuffix Event name, e.g. "click".
 * @param {?boolean} capture Check if the capture phase is supported.
 * @return {boolean} True if the event is supported.
 * @internal
 * @license Modernizr 3.0.0pre (Custom Build) | MIT
 */export{W as default};
