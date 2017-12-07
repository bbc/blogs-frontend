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
        }
    };
    return StatsTracking;
});
