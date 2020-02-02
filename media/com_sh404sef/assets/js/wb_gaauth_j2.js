/**
 * Handles authenticating to Google with oauth
 *
 * @author       ant_author_ant
 * @copyright    ant_copyright_ant
 * @package      ant_package_ant
 * @license      ant_license_ant
 * @version      ant_version_ant
 *
 * ant_current_date_ant
 */

;
(function (_s, window, document, $) {
    "use strict";

    var _request = {};
    _request.windowName = 'wb_sh404sef_ga_auth_window';
    _request.windowFeatures = 'menubar=no,location=yes,resizable=yes,scrollbars=yes,status=yes,toolbar=no,alwaysRaised=yes';

    var _authButton;
    var _clearButton;
    var _hint;
    var _input;
    var _authWindow;

    function authenticate() {
        _input.setProperty('disabled', false);
        _input.show();
        _hint.show();
        _authButton.hide();

        // open auth window
        _authWindow = openWindow(_request);
    }

    function openWindow(request) {
        var height = window.innerHeight * 0.75;
        var width = window.innerWidth * 0.75;
        var w = window.open(request.targetUrl, request.windowName, request.windowFeatures + ',width=' + width + ',height=' + height);
        if (window.focus) {
            w.focus();
        }
        return w;
    }

    function clearAuth() {
        _authButton.hide();
        _clearButton.hide();
        _input.hide();
        _hint.hide();
        $$('.wbga_auth_good').hide();
        $('jform_wbga_clearauthorization').set('value', 1);
        $$('.wbga_authclearhint').show();
    }

    function onReady() {
        try {
            if (_request) {
                _input = $$('.wbga_authinput');
                _hint = $$('.wbga_authinputhint');
                _authButton = $$('.wbga_authbutton');
                _authButton.addEvent('click', authenticate);
                _clearButton = $$('.wbga_clearauthbutton');
                _clearButton.addEvent('click', clearAuth);
            }
        }
        catch (e) {
            console.log('Error setting up Google authentication: ' + e.message);
        }
    }

    $(window).addEvent('domready', onReady);

    // interface
    _s.gaAuth = {
        add: function (request) {
            _request = request;
        }
    }
    return _s;
})
(window.sh404sefApp = window.sh404sefApp || {}, window, document, $);

