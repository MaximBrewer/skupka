/**
 * sh404SEF - SEO extension for Joomla!
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier - Weeblr llc - 2016
 * @package     sh404SEF
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     4.7.3.3292
 * @date        2016-03-15
 */

// get Google Analytics queue
var _gaq = _gaq || [];

// our own tracker
var _sh404sefSocialTrack = _sh404sefSocialTrack || [];
// which analytics type is in use?
var sh404SEFAnalyticsType = sh404SEFAnalyticsType || [];

_sh404sefSocialTrack.setup = function () {

    FB.init({
        appId: _sh404sefSocialTrack.options.FBAppId ? _sh404sefSocialTrack.options.FBAppId : "",
        version: "v2.3",
        status: true, // check login status
        cookie: true, // enable cookies to allow the server to access the session
        oauth: true, // enable OAuth 2.0
        xfbml: true  // parse XFBML
    });

    if (_sh404sefSocialTrack.options.enableAnalytics) {
        // compute tracker name
        _sh404sefSocialTrack.trackerName = _sh404sefSocialTrack.options.trackerName ? _sh404sefSocialTrack.options.trackerName
            : "sh404SEF_social_tracker";

        // enable tracking, either sync. or async.
        _sh404sefSocialTrack.setupFBTracking();
        _sh404sefSocialTrack.setupTweeterTracking();
    }
};

/*
 * Facebook tracking : Call directly if SDK loaded synchroneously or assign to
 * window.fbAsyncInit if using asynchronous loading
 */

_sh404sefSocialTrack.setupFBTracking = function () {

    try {
        if (FB && FB.Event && FB.Event.subscribe) {
            if (sh404SEFAnalyticsType && sh404SEFAnalyticsType.classic) {
                FB.Event.subscribe('edge.create', function (targetUrl) {
                    _gaq.push([ '_trackEvent', _sh404sefSocialTrack.trackerName + '_facebook', 'like', targetUrl, 1, true ]);
                    // Google tracking
                    if (_sh404sefSocialTrack.options.enableGoogleTracking) {
                        _gaq.push([ '_trackSocial', 'facebook', 'like', targetUrl ]);
                    }
                });
                FB.Event.subscribe('edge.remove', function (targetUrl) {
                    _gaq.push([ '_trackEvent', _sh404sefSocialTrack.trackerName + '_facebook', 'unlike', targetUrl, 1, true ]);
                    // Google tracking
                    if (_sh404sefSocialTrack.options.enableGoogleTracking) {
                        _gaq.push([ '_trackSocial', 'facebook', 'unlike', targetUrl ]);
                    }
                });
                FB.Event.subscribe('message.send', function (targetUrl) {
                    _gaq.push([ '_trackEvent', _sh404sefSocialTrack.trackerName + '_facebook', 'send', targetUrl, 1, true ]);
                    // Google tracking
                    if (_sh404sefSocialTrack.options.enableGoogleTracking) {
                        _gaq.push([ '_trackSocial', 'facebook', 'send', targetUrl ]);
                    }
                });
            }
            if (sh404SEFAnalyticsType && sh404SEFAnalyticsType.universal) {
                FB.Event.subscribe('edge.create', function (targetUrl) {
                    ga('send', 'event', _sh404sefSocialTrack.trackerName + '_facebook', 'like', targetUrl, 1);
                    // Google tracking
                    if (_sh404sefSocialTrack.options.enableGoogleTracking) {
                        ga('send', 'social', 'facebook', 'like', targetUrl);
                    }
                });
                FB.Event.subscribe('edge.remove', function (targetUrl) {
                    ga('send', 'event', _sh404sefSocialTrack.trackerName + '_facebook', 'unlike', targetUrl, 1);
                    // Google tracking
                    if (_sh404sefSocialTrack.options.enableGoogleTracking) {
                        ga('send', 'social', 'facebook', 'unlike', targetUrl);
                    }
                });
                FB.Event.subscribe('message.send', function (targetUrl) {
                    ga('send', 'event', _sh404sefSocialTrack.trackerName + '_facebook', 'send', targetUrl, 1);
                    // Google tracking
                    if (_sh404sefSocialTrack.options.enableGoogleTracking) {
                        ga('send', 'social', 'facebook', 'send', targetUrl);
                    }
                });
            }


        }
    } catch (e) {
        console.log(e.message);
    }
};

// Tweeter tracking
_sh404sefSocialTrack.setupTweeterTracking = function () {
    try {
        if (twttr && twttr.events && twttr.events.bind) {
            twttr.events.bind('tweet', function (event) {
                if (event) {
                    var targetUrl; // Default value is undefined.
                    if (event.target && event.target.nodeName == 'IFRAME') {
                        targetUrl = _sh404sefSocialTrack.extractParamFromUri(event.target.src, 'url');
                    }
                    if (sh404SEFAnalyticsType && sh404SEFAnalyticsType.classic) {
                        _gaq.push([ '_trackEvent', _sh404sefSocialTrack.trackerName + '_tweeter', 'tweet', targetUrl, 1, true ]);
                        // Google tracking
                        if (_sh404sefSocialTrack.options.enableGoogleTracking) {
                            _gaq.push([ '_trackSocial', 'twitter', 'tweet', targetUrl ]);
                        }
                    }
                    if (sh404SEFAnalyticsType && sh404SEFAnalyticsType.universal) {
                        ga('send', 'event', _sh404sefSocialTrack.trackerName + '_tweeter', 'tweet', targetUrl, 1);
                        // Google tracking
                        if (_sh404sefSocialTrack.options.enableGoogleTracking) {
                            ga('send', 'social', 'tweeter', 'tweet', targetUrl);
                        }
                    }
                }
            });
        }
    } catch (e) {
        console.log(e.message);
    }
};

/*
 * Pinterest tracking, through a callback
 */
_sh404sefSocialTrackPinterestTracking = function (msg, url) {
    try {
        if (msg == "pinned") {
            var targetMedia = _sh404sefSocialTrack.extractParamFromUri(url, 'url') + ' ('
                + _sh404sefSocialTrack.extractParamFromUri(url, 'media') + ')';
            if (sh404SEFAnalyticsType && sh404SEFAnalyticsType.classic) {
                _gaq.push([ '_trackEvent', _sh404sefSocialTrack.trackerName + '_pinterest', msg, targetMedia, 1, true ]);
            }
            if (sh404SEFAnalyticsType && sh404SEFAnalyticsType.universal) {
                ga('send', 'event', _sh404sefSocialTrack.trackerName + '_pinterest', msg, targetUrl, 1);
            }
        }
    } catch (e) {
        console.log(e.message);
    }
};

/*
 * G+ requires a callback function for each click
 */
_sh404sefSocialTrackGPlusTracking = function (data) {
    try {
        if (sh404SEFAnalyticsType && sh404SEFAnalyticsType.classic) {
            if (data.state == "on" || data.state == "off") {
                _gaq.push([ '_trackEvent', _sh404sefSocialTrack.trackerName + '_gplus', data.state, data.href, 1, true ]);
            }
        }
        if (sh404SEFAnalyticsType && sh404SEFAnalyticsType.universal) {
            if (data.state == "on" || data.state == "off") {
                ga('send', 'event', _sh404sefSocialTrack.trackerName + '_gplus', data.state, data.href, 1);
            }
        }
    } catch (e) {
        console.log(e.message);
    }
};

// Google page click tracking
_sh404sefSocialTrack.GPageTracking = function (target, source) {
    try {
        if (sh404SEFAnalyticsType && sh404SEFAnalyticsType.classic) {
            _gaq.push([ '_trackEvent', _sh404sefSocialTrack.trackerName + '_gplus_page', target, source, 1, true ]);
        }
        if (sh404SEFAnalyticsType && sh404SEFAnalyticsType.universal) {
            ga('send', 'event', _sh404sefSocialTrack.trackerName + '_gplus_page', target, source, 1);
        }
    } catch (e) {
        console.log(e.message);
    }
};

/**
 * Extracts a query parameter value from a URI. (c) Google - 2011
 *
 * @param {string}
 *          uri The URI from which to extract the parameter.
 * @param {string}
 *          paramName The name of the query paramater to extract.
 * @return {string} The un-encoded value of the query paramater. underfined if
 *         there is no URI parameter.
 * @private
 */
_sh404sefSocialTrack.extractParamFromUri = function (uri, paramName) {
    if (!uri || !uri.indexOf('#')) {
        return;
    }
    var uri = uri.indexOf('#') > 0 ? uri.split('#')[1] : uri; // Remove anchor.

    // Find url param.
    paramName += '=';
    uri = uri.split('?');
    uri = uri[1] ? uri[1] : uri[0];
    var params = uri.split('&');
    for (var i = 0, param; param = params[i]; ++i) {
        if (param.indexOf(paramName) === 0) {
            return unescape(decodeURI(param.split('=')[1]));
        }
    }
    return;
};
