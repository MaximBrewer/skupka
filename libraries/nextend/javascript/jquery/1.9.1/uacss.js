
(function($) {
    var uaMatch = '', 
        prefix = '',
        html = $('html'),
        dir = $(document.documentElement).attr('dir');
    if(!dir) dir = 'ltr';
    html.addClass('x-'+dir);
    window.nextendDir = dir;

    if (navigator.userAgent.match(/Windows/))
    {
        html.addClass('x-win');
    }
    else if (navigator.userAgent.match(/Mac OS X/))
    {
        html.addClass('x-mac');
    }
    else if (navigator.userAgent.match(/X11/))
    {
        html.addClass('x-x11');
    }

    if (navigator.userAgent.match(/Chrome/))
    {
        uaMatch = ' Chrome/';
        prefix = 'x-chrome';
    }
    else if (navigator.userAgent.match(/Safari/))
    {
        uaMatch = ' Version/';
        prefix = 'x-safari';
    }
    else if (navigator.userAgent.match(/Firefox/))
    {
        uaMatch = ' Firefox/';
        prefix = 'x-firefox';
    }
    else if (navigator.userAgent.match(/MSIE/))
    {
        uaMatch = ' MSIE ';
        prefix = 'x-msie';
    }else if(!!navigator.userAgent.match(/Trident/)){
        uaMatch = ' rv:';
        prefix = 'x-msie';
    }
    if (prefix)
    {
        html.addClass(prefix);

        uaMatch = new RegExp(uaMatch + '(\\d+)\.(\\d+)');
        var uaMatch = navigator.userAgent.match(uaMatch);
        if (uaMatch && uaMatch[1])
        {
            html.addClass(prefix + '-' + uaMatch[1]);
            html.addClass(prefix + '-' + uaMatch[1] + '-' + uaMatch[2]);
        }
    }
    $(window).load(function(){
        setTimeout(function(){
            html.addClass('x-ready');
        }, 500);
    });
})(njQuery);