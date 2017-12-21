define(['istats-1', 'istats-tracking', 'respimg', 'lazysizes'], function (istats, IstatsTracking) {
    // cut the mustard
    if ('querySelector' in document && 'addEventListener' in window) {
        var tracking = new IstatsTracking(istats);
        tracking.init();
    }
});
