// This file is generated.
// It is an intermediary file, we should update the Gulp file to use pipes correctly instead of using intermediary files
define([], function () {
    'use strict';
    var StatsTracking = /** @class */ (function () {
        function StatsTracking(istats) {
            this.istats = istats;
        }
        StatsTracking.prototype.init = function () {
            this.trackLinks();
        };
        StatsTracking.prototype.trackLinks = function () {
            var trackingAttribute = 'data-istats-link-location';
            var links = document.querySelectorAll('[' + trackingAttribute + ']');
            var istats = this.istats; // <any> is a hack as we don't have the Declaration file for istats-1
            for (var i = 0; i < links.length; i++) {
                istats.track('internal', {
                    region: links[i],
                    linkLocation: links[i].getAttribute(trackingAttribute)
                });
            }
        };
        return StatsTracking;
    }());
    return StatsTracking;
});
