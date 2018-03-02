define(['jquery-1.9'], function ($) {

    var Lazyload = function (options) {
        this.setOptions(options);
    };

    // contains jQuery objects: the elements that are lazy but yet to be loaded
    var toAppear = [];

    function debounce(func, wait) {
        var timeout;
        return function() {
            var later = function() {
                timeout = null;
            };
            var callNow = !timeout;
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
            if (callNow) func.apply(this, arguments);
        };
    };

    Lazyload.prototype = {
        options: {
            context: $('body'),
            lazy_module_css: 'lazy-module',
            lazy_css_state: {
                loading: 'lazy-module--loading lazy-module--loading--loader blogs-loading-spinner',
                complete: 'lazy-module--complete',
                error: 'lazy-module--error'
            },
            data_threshold : 'data-lazyload-threshold',
            data_always_lazyload: 'data-lazyload-always',
            data_inc_path: 'data-lazyload-inc',
            data_delay_lazyload : 'data-lazyload-delay'
        },
        setOptions: function (options) {
            this.options = $.extend({}, this.options, options);
        },
        init: function () {
            var _this = this;

            this.loadContents();

            $('body').on('lazyload-refresh', function (event) {
                _this.loadContents();
            });
        },
        getLazyContents: function (context) {
            var context = (context) ? context : this.options.context,
                lazy_contents = $('.' + this.options.lazy_module_css, context).not('.' + this.options.lazy_css_state.loading);

            return (lazy_contents.length) ? lazy_contents : false;
        },
        isOverThreshold : function (lazyloaded_object) {
            var minimum_render_threshold = lazyloaded_object.getAttribute(this.options.data_threshold);
            return (minimum_render_threshold) ? ($(window).width() >= minimum_render_threshold) : true;
        },
        alwaysLoadContent: function (context) {
            return (context.getAttribute(this.options.data_always_lazyload) === "true");
        },
        isDelayed : function (lazyloaded_object) {
            return (lazyloaded_object.getAttribute(this.options.data_delay_lazyload) === "true");
        },
        shouldBeRenderedNow : function (element) {
            return ((this.isOverThreshold(element) || this.alwaysLoadContent(element)) && (!this.isDelayed(element) || this.isInViewport(element)));
        },
        loadContents: function (context) {
            var contents = this.getLazyContents(context);
            if (!contents) {
                return;
            }
            var _this = this;

            contents.each(function (i) {
                if (_this.shouldBeRenderedNow(this)) {
                    _this.loadAjaxContent(this);
                } else {
                    toAppear.push(this);
                }
            });

            this.addOnScrollOrResizeHandler();
        },
        isInViewport : function (element) {
            var $element = $(element);
            var $window = $(window);
            var window_left = $window.scrollLeft();
            var window_top = $window.scrollTop();
            var offset = $element.offset();
            var element_left = offset.left;
            var element_top = offset.top;

            // load just before in viewport
            var lazy_top_offset = 500;
            var lazy_left_offset = 500;

            var below_window_top = (element_top + $element.height() >= window_top);
            var above_window_bottom = (element_top - lazy_top_offset <= window_top + $window.height());
            var right_of_lhs = (element_left + $element.width() >= window_left);
            var left_of_rhs = (element_left - lazy_left_offset <= window_left + $window.width());

            return below_window_top && above_window_bottom && right_of_lhs && left_of_rhs;
        },
        checkInView : function () {
            return debounce(function () {
                var _this = this;
                toAppear.forEach(function (element, index) {
                    if (_this.shouldBeRenderedNow(element)) {
                        _this.loadAjaxContent(element);
                        toAppear.splice(index, 1);
                    }
                });
            }, 75);
        },
        addOnScrollOrResizeHandler : function () {
            $(window).scroll(this.checkInView().bind(this)).resize(this.checkInView().bind(this));
        },
        loadAjaxContent: function (content) {
            var _this = this,
                content = $(content);
            content.addClass(this.options.lazy_css_state.loading);
            return $.ajax({
                url: content.attr(_this.options.data_inc_path),
                dataType: 'html',
                success: function (data) {
                    var loaded_content = $($.parseHTML(data, document, true)).addClass(_this.options.lazy_css_state.loading);
                    content.replaceWith(loaded_content);
                    loaded_content.addClass(_this.options.lazy_css_state.complete);
                    loaded_content.removeClass(_this.options.lazy_css_state.loading);
                    setTimeout(function () {
                        loaded_content.trigger("lazyloadComplete", {
                            content: loaded_content
                        });
                        $('body').trigger("lazyloadComplete");
                    }, 1);
                },
                error: function (request, status, error) {
                    content.removeClass(_this.options.lazy_css_state.loading);
                    content.addClass(_this.options.lazy_css_state.error);
                }
            });
        }
    };
    return Lazyload;
});
