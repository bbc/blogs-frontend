define(['jquery', 'istats-tracking', 'lazyload', 'respimg', 'lazysizes'], function ($, IstatsTracking, Lazyload) {
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
});
