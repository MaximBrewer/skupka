N2Require('GSAP', [], [], function ($, scope, undefined) {
    if (typeof TimelineLite == 'undefined') {
        alert('GSAP - TimelineLite missing');
    }

    window.NextendTimeline = TimelineLite;

    if (typeof TweenLite == 'undefined') {
        alert('GSAP - TweenLite missing');
    }
    window.NextendTween = TweenLite;


    if (typeof SplitText == 'undefined') {
        alert('GSAP - SplitText missing');
    }
    window.NextendSplitText = SplitText;


    if (window.n2FilterProperty) {
        CSSPlugin.registerSpecialProp(
            "n2blur",
            function (target, value, tween) {
                var start = 0,
                    match;
                if ((match = target.style[window.n2FilterProperty].match(/blur\((.+)?px\)/))) {
                    start = parseFloat(match[1]);
                }
                if (start == value) {
                    return function () {

                    };
                }
                var diff = value - start;
                return function (ratio) {
                    target.style[window.n2FilterProperty] = "blur(" + (start + diff * ratio) + "px)";
                };
            },
            0
        );
    }
});