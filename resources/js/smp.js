define('smp',['jquery-1.9'], function ($) {

    var SMP = function (options) {
        this.resume = null;
        this.xhrResult = null;
        this.options = {};
        this.setOptions(options);
        this.init();
    };

    SMP.prototype = {
        current : {
            player : null
        },
        initial_options : {
            container : null,
            playerSettings : {
                product : "iplayer",
                siteID : 'iPlayer',
                appName: "blogs",
                appType: "web",
                // counterName : window.bbcBlogs.counterName || null,
                counterName : 'test',
                playerProfile: 'smp',
                responsive: true,
                superResponsive: true,
                playlistObject : null,
                delayEmbed : false,
                requestWMP : true,
                statsObject : {
                    deviceId : null
                },
                ui : {},
                locale : {
                    lang : 'en-gb'
                },
                embed : {
                    enabled : true
                },
                muted: null,
                volume: null,
                autoplay: false
            },
            rememberResume : false,
            messages : {
                loading : 'Loading player...',
                error : 'An error occurred',
                noVersions : 'Currently unavailable'
            }
        },
        setOptions : function (options) {
            this.options = $.extend(true, {}, this.initial_options, options);
        },
        init : function() {
            var _this = this,
                url,
                message_container = $(this.options.container + ' .js-loading-message'),
                spinner_class = 'loading-spinner';
            message_container.addClass(spinner_class).html(this.options.messages.loading);

            if (this.options.pid) {
                if (location.protocol === "https:") {
                    // Temporary url, we will use www. once programmes is on https
                    url = 'https://ssl.bbc.co.uk/programmes/' + this.options.pid + '/playlist.json?callback=?';
                } else {
                    url = 'http://www.bbc.co.uk/programmes/' + this.options.pid + '/playlist.json';
                    // url = 'http://' + window.location.host + '/programmes/' + this.options.pid + '/playlist.json';
                }
            } else if (this.options.xml) {
                url = this.options.xml;
                this.loadXMLPlayer(url);
                return;
            }

            $.getJSON(url, function(data) {
                _this.xhrResult = data;
                _this.options.playerSettings.statsObject = $.extend(
                    true,
                    {},
                    _this.options.playerSettings.statsObject,
                    data.statsObject
                );
                if (data.defaultAvailableVersion) {
                    _this.loadPlayer(data.defaultAvailableVersion);
                } else {
                    message_container.removeClass(spinner_class).html(_this.options.messages.noVersions);
                    /* if there is no image, add one */
                    if (data.holdingImage &&
                        $(_this.options.container + ' .smp_holding').length === 0
                    ) {
                        $(_this.options.container).prepend(
                            '<img src="' + data.holdingImage + '" class="rsp-img smp_holding" alt="" />'
                        );
                    }
                }
            }).error(function() {
                message_container.removeClass(spinner_class).html(_this.options.messages.error);
            });
        },
        loadXMLPlayer : function(url) {
            var _this = this;
            this.options.playerSettings.playlist = url;
            require(['bump-3'], function($) {
                _this.current.player = $(_this.options.container).player(_this.options.playerSettings);
                _this.current.player.load();

            });
        },
        loadPlayer : function(data) {
            var _this = this,
                smpConfig = data.smpConfig,
                markers = data.markers,
                hasMarkers = (markers.length > 0);
            this.options.playerSettings.playlistObject = $.extend(
                true,
                {},
                this.options.playerSettings.playlistObject,
                smpConfig
            );

            require(['bump-3'], function($) {
                if (hasMarkers) {
                    _this.options.playerSettings.ui.markers = {
                        enabled : true,
                        hideBelowWidth : 480
                    };
                }
                _this.current.player = $(_this.options.container).player(_this.options.playerSettings);
                if (hasMarkers) {
                    _this.current.player.bind('initialised', function() {
                        _this.current.player.setData( { name: 'SMP.markers', data: markers } );
                    });
                }
                _this.current.player.load();

            });
        }
    };
    return SMP;
});
