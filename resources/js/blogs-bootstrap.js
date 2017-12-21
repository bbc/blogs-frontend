define(['istats-tracking', 'respimg', 'lazysizes'], function (IstatsTracking) {
    // cut the mustard
    if ('querySelector' in document && 'addEventListener' in window) {
        var tracking = new IstatsTracking();
        tracking.init();
    }
});
