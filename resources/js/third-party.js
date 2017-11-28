define(['jquery-1.9'], function($) {
    var Provider, Embed;

    /*
     * Provider class
     *
     * @param  {String}    name             The name of the provider
     * @param  {String}    type             The type of the provider can be "file", "photo", "video", "rich"
     * @param  {String}    urlshemesarray   Array of url of the provider
     * @param  {String}    apiendpoint      The endpoint of the provider
     */
    Provider = function(name, type, urlschemesarray, apiendpoint, extraSettings) {
        this.name = name;
        this.type = type; // "photo", "video", "link", "rich", null
        this.urlschemes = urlschemesarray;
        this.apiendpoint = apiendpoint;
        this.maxWidth = 500;
        this.maxHeight = 400;
        extraSettings =extraSettings ||{};

        for (var property in extraSettings) {
            this[property] = extraSettings[property];
        }

        this.format = this.format || 'json';
        this.callbackparameter = this.callbackparameter || "callback";
        this.embedtag = this.embedtag || {tag:""};
    };

    Embed = function(options) {
        this.container = options.container;
        this.url = options.url;

        this.start();
    };
    Embed.prototype = {
        doEmbed : function(code, provider) {
            var wrap = $('<div class="third-party__embed"></div>');
            wrap.html(code);
            this.container.addClass('third-party--' + provider.name).html(wrap);
        },
        start : function() {
            var matched_provider = this.getOEmbedProvider(this.url);
            if (!matched_provider) return null;

            return this.generateCode(matched_provider, this.url);

        },
        getOEmbedProvider : function (url) {
            var providers_length = this.providers.length,
                schemes_length = 0;
            for (var i = 0; i < providers_length; i++) {
                schemes_length = this.providers[i].urlschemes.length;
                for (var j = 0; j < schemes_length; j++) {
                    var regExp = new RegExp(this.providers[i].urlschemes[j], "i");
                    if (url.match(regExp) !== null) return this.providers[i];
                }
            }
            return null;
        },
        getRequestUrl : function (provider, externalUrl) {
            var url = provider.apiendpoint,
                qs = "",
                i;
            url += (url.indexOf("?") <= 0) ? "?" : "&";
            url = url.replace('#','%23');

            url += "format=" + provider.format + "&url=" + escape(externalUrl) + qs;
            if(provider.dataType!='json') url += "&" + provider.callbackparameter + "=?";

            return url;
        },
        generateCode : function (embedProvider, externalUrl) {
            if (embedProvider.templateRegex) {
                return this.generateCodeFromTemplate(embedProvider, externalUrl);
            }
            return this.generateCodeFromRequest(embedProvider, externalUrl);
        },
        generateCodeFromRequest : function(embedProvider, externalUrl) {
            var _this = this,
                requestUrl = this.getRequestUrl(embedProvider, externalUrl),
                ajaxopts = {
                    url: requestUrl,
                    dataType: embedProvider.dataType || 'jsonp',
                    success: function(data) {
                        var code;
                        switch (data.type) {
                            case "photo":
                                code = _this.getPhotoCode(externalUrl, data);
                                break;
                            case "video":
                            case "rich":
                                code = _this.getRichCode(externalUrl, data);
                                break;
                            default:
                                code = _this.getGenericCode(externalUrl, data);
                                break;
                        }
                        if (code) {
                            _this.doEmbed(code, embedProvider);
                        }
                    }
                };
            $.ajax(ajaxopts);
        },
        generateCodeFromTemplate : function(embedProvider, externalUrl) {
            var _this = this,
                tag, src, ajaxopts;
            if (embedProvider.embedtag.tag!==''){
                tag = embedProvider.embedtag.tag || 'embed';
                src = externalUrl.replace(embedProvider.templateRegex,embedProvider.apiendpoint);

                var code = $('<'+tag+'/>')
                    .attr('src',src)
                    .attr('allowfullscreen', embedProvider.embedtag.allowfullscreen || 'true')
                    .attr('allowscriptaccess', embedProvider.embedtag.allowfullscreen || 'always')
                    .css('max-height', 'auto' )
                    .css('max-width', 'auto' );
                this.doEmbed(code, embedProvider);
            } else if (embedProvider.apiendpoint) {
                ajaxopts = {
                    url: externalUrl.replace(embedProvider.templateRegex, embedProvider.apiendpoint),
                    dataType: 'jsonp',
                    success: function(data) {
                        _this.doEmbed(embedProvider.templateData(data), embedProvider);
                    }
                };
                $.ajax(ajaxopts);
            } else {
                this.doEmbed(externalUrl.replace(embedProvider.templateRegex,embedProvider.template), embedProvider);
            }
        },
        getPhotoCode : function(url, oembedData) {
            console.log(oembedData.html);
            return oembedData.html;
        },

        getRichCode : function(url, oembedData) {
            var code = oembedData.html;
            code = code.replace(/(width|height)="\d*"\s/g,"");
            code = code.replace(/marginmarginscrolling/g, "scrolling");
            return code;
        },

        getGenericCode : function(url, oembedData) {
            var title = (oembedData.title !== null) ? oembedData.title : url,
                code = '<a href="' + url + '">' + title + '</a>';
            if (oembedData.html) code += "<div>" + oembedData.html + "</div>";
            return code;
        },
        providers : [
            /* Flickr have currently broken their oEmbed API in Chrome and IE */
            new Provider("slideshare", "rich", ["slideshare\\.net"], "https://www.slideshare.net/api/oembed/2", {
                format: 'jsonp',
            }),
            new Provider("flickr", "photo", ["flickr\\.com/photos/.+","flic\\.kr/p/.+"], "https://flickr.com/services/oembed",{callbackparameter:'jsoncallback'}),
            new Provider("instagram", "rich", ["instagr\\.?am(\\.com)?/.+"], "//instagram.com/p/$1/embed/", {
                templateRegex: /.*(?:\/p\/)([\w\-]+)&?.*/,
                embedtag: {
                    tag: 'iframe'
                }
            }),
            new Provider("soundcloud", "rich", ["soundcloud.com/.+","snd.sc/.+"], "https://soundcloud.com/oembed?maxheight=166",{format:'js'}),
            new Provider("twitter", "rich", ["twitter.com/.+"], "https://api.twitter.com/1/statuses/oembed.json"),
            new Provider("vimeo", "video", ["www\\.vimeo\\.com\/groups\/.*\/videos\/.*", "www\\.vimeo\\.com\/.*", "vimeo\\.com\/groups\/.*\/videos\/.*", "vimeo\\.com\/.*"], "https://vimeo.com/api/oembed.json"),
            new Provider("youtube", "video", ["youtube\\.com/watch.+v=[\\w-]+&?", "youtu\\.be/[\\w-]+","youtube.com/embed"], 'https://www.youtube.com/embed/$1?wmode=transparent', {
                templateRegex: /.*(?:v\=|be\/|embed\/)([\w\-]+)&?.*/,
                embedtag: {
                    tag: 'iframe'
                }
            }),
            new Provider("vine", "video", ["vine\\.co\/v\/.+"], 'https://vine.co/v/$1/embed/simple', {
                templateRegex: /.*(?:\/v\/)([\w\-]+)&?.*/,
                embedtag: {
                    tag: 'iframe'
                }
            }),
            new Provider("viddler", "video", ["viddler.com\/v\/.+"], "https://viddler.com/embed/$1", {
                templateRegex: /.*(?:\/v\/)([\w\-]+)&?.*/,
                embedtag: {
                    tag: 'iframe'
                }
            })
        ]
    };

    return Embed;
});
