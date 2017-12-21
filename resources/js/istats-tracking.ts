define([], function () {

    'use strict';

    class StatsTracking {
        istats: object;

        constructor(istats: object) {
            this.istats = istats;
        }

        init() {
            this.trackLinks();
        }

        trackLinks() {
            var trackingAttribute = 'data-istats-link-location';
            var links = document.querySelectorAll('[' + trackingAttribute + ']');
            var istats = <any>this.istats; // <any> is a hack as we don't have the Declaration file for istats-1
            for (var i = 0; i < links.length; i++) {
                istats.track('internal', {
                    region: links[i],
                    linkLocation: links[i].getAttribute(trackingAttribute)
                });
            }
        }
    }

    return StatsTracking;
});
