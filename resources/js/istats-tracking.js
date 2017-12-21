define(['jquery-1.9', 'istats-1'], function ($, istats) {

    var StatsTracking = function () {
        this.options = {
            trackingAttribute: 'data-istats-link-location'
        };
    };

    StatsTracking.prototype = {
        init: function () {
            this.trackLinks();
        },
        trackLinks: function () {
            var trackingAttribute = this.options.trackingAttribute;
            var links = $('body').find('[' + trackingAttribute + ']');
            links.each(function () {
                istats.track("internal", {
                    region: $(this),
                    linkLocation: $(this).attr(trackingAttribute)
                });
            });
        }
    };
    return StatsTracking;
});
