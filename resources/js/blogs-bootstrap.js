define(['jquery', 'istats-tracking', 'lazyload', 'lazysizes'], function ($, IstatsTracking, Lazyload, lazySizes) {
    function init() {
        var tracking = new IstatsTracking();
        tracking.init();
        var responsiveLazyload = new Lazyload();

        responsiveLazyload.init();

        return {
            $: $,
            Lazyload: Lazyload
        }
    }

    // cut the mustard
    if ('querySelector' in document && 'addEventListener' in window) {
        init();
    }
    // Lazy sizes (as of v4.0.2) breaks in IE11 without this hack
    window.lazySizes = lazySizes;
    // Load responsive image polyfill if needed
    var image = document.createElement( "img" );
    if (!("srcset" in image) || !("sizes" in image) || !(window.HTMLPictureElement)) {
        require(['picturefill'], function (picturefill) {})
    }
});
