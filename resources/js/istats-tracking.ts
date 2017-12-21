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
            const trackingAttribute = 'data-istats-link-location';
            const links = document.querySelectorAll('[' + trackingAttribute + ']');
            const istats = <any>this.istats; // <any> is a hack as we don't have the Declaration file for istats-1
            for (let i = 0; i < links.length; i++) {
                istats.track('internal', {
                    region: links[i],
                    linkLocation: links[i].getAttribute(trackingAttribute)
                });
            }
        }
    }

    return StatsTracking;
});
