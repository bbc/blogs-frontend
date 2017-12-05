define(['jquery-1.9', 'istats-1'], function ($, istats) {

    var StatsTracking = function (options) {
        this.options = {};
        this.setOptions(options);
    };

    StatsTracking.prototype = {
        initial_options: {
            trackingAttribute: 'data-linktrack',
            labelPrefix: 'programmes_'
        },
        setOptions: function (options) {
            this.options = $.extend(true, {}, this.initial_options, options);
        },
        init: function () {
            this.trackLinks();
            this.hardcodedItems();
        },
        trackLinks: function (context) {
            var _this = this,
                label;
            context = context || $('body');
            var links = context.find('[' + this.options.trackingAttribute + ']');
            links.each(function () {
                label = $(this).attr(_this.options.trackingAttribute);
                istats.track("internal", {
                    region: $(this),
                    linkLocation: _this.options.labelPrefix + label
                });
            });
        },
        hardcodedItems: function () {
            // Because of the nature of these items we can't add the "data-linktrack" attribute inside the HTML so
            // it is required to hardcode a list of custom "istats.track" calls
            istats.track("internal", {
                region: $(".br-masthead .service-brand-logo-master"),
                linkLocation: 'programmes_global_ribbon'
            });
        }
    };
    return StatsTracking;
});
