define(['jquery-1.9', 'istats-1'], function ($, istats) {

    var StatsTracking = function (options) {
        this.options = {};
        this.setOptions(options);
    };

    StatsTracking.prototype = {
        initial_options: {
            trackingAttribute: 'data-istats-link-location'
        },
        setOptions: function (options) {
            this.options = $.extend(true, {}, this.initial_options, options);
        },
        init: function () {
            this.trackLinks();
            this.hardcodedItems();
        },
        trackLinks: function (context) {
            var _this = this;
            context = context || $('body');
            var links = context.find('[' + this.options.trackingAttribute + ']');
            links.each(function () {
                istats.track("internal", {
                    region: $(this),
                    linkLocation: $(this).attr(_this.options.trackingAttribute)
                });
            });
        },
        hardcodedItems: function () {
            // Because of the nature of these items we can't add the "data-istats-link-location" attribute inside the HTML so
            // it is required to hardcode a list of custom "istats.track" calls
            istats.track("internal", {
                region: $(".br-masthead .service-brand-logo-master"),
                linkLocation: 'blogs_global_ribbon'
            });
        }
    };
    return StatsTracking;
});
