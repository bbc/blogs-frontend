define(['istats-tracking', 'respimg', 'lazysizes'], function (IstatsTracking) {
    function init() {
        var tracking = new IstatsTracking();
        tracking.init();
    }

    // cut the mustard
    if ('querySelector' in document && 'addEventListener' in window) {
        init();
    }
});
