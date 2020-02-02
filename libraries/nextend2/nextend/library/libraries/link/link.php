<?php

class N2LinkParser {

    public static function parse($url, &$attributes, $isEditor = false) {
        if ($url == '#' || $isEditor) {
            $attributes['onclick'] = "return false;";
        }

        preg_match('/^([a-zA-Z]+)\[(.*)]$/', $url, $matches);
        if (!empty($matches)) {
            $class = 'N2Link' . $matches[1];
            if (class_exists($class, false)) {
                $url = call_user_func_array(array(
                    $class,
                    'parse'
                ), array(
                    $matches[2],
                    &$attributes,
                    $isEditor
                ));
            }
        } else {
            $url = N2ImageHelper::fixed($url);
        }

        return $url;
    }
}
class N2LinkLightbox {

    private static function init() {
        static $inited = false;
        if (!$inited) {

            N2JS::addInline('
        var n2Lightbox = function(container){
            container.find("a[n2-lightbox-urls], div[n2-lightbox-urls]").css("cursor", "zoom-in").each(function(){
                var el = $(this);
                var group = el.data("litebox-group"),
                    parts = el.attr("n2-lightbox-urls").split(","),
                    titles = el.attr("n2-lightbox-titles").split("|||");
                for(var i = 0; i < parts.length; i++){
                    var a = $("<a href=\""+parts[i]+"\" data-litebox-group=\""+group+"\" style=\"display:none;\"></a>").appendTo("body");
                    if(!!titles[i]){
                        a.data("title", titles[i]);
                    }
                }
            });
            container.find("a[n2-lightbox], div[n2-lightbox]").css("cursor", "zoom-in").liteBox({
                callbackBeforeOpen: function(e){
                    this.$element.trigger("mediaStarted", this);
                },
                callbackAfterClose: function(){
                    this.$element.trigger("mediaEnded", this);
                }
            });
        };
        n2Lightbox($("body"));
        n2(window).on("n2Rocket", function(e, container){
            n2Lightbox(container);
        });');
            $inited = true;
        }
    }

    public static function parse($argument, &$attributes, $isEditor = false) {
        if (!$isEditor) {
            self::init();
            $attributes['onclick']     = "return false;";
            $attributes['n2-lightbox'] = '';

            if (!isset($attributes['class'])) $attributes['class'] = '';
            $attributes['class'] .= " n2-lightbox-trigger";

            N2AssetsPredefined::loadLiteBox();

            $urls     = explode(',', $argument);
            $realUrls = array();
            $titles   = array();
            $parts    = explode(';', array_shift($urls), 2);
            $argument = N2ImageHelper::fixed($parts[0]);
            if (!empty($parts[1])) {
                $attributes['data-title'] = $parts[1];
            }
            if (count($urls)) {
                if (intval($urls[count($urls) - 1]) > 0) {
                    $attributes['data-autoplay'] = intval(array_pop($urls));
                }
                for ($i = 0; $i < count($urls); $i++) {
                    if (!empty($urls[$i])) {
                        $parts      = explode(';', $urls[$i], 2);
                        $realUrls[] = N2ImageHelper::fixed($parts[0]);
                        $titles[]   = !empty($parts[1]) ? $parts[1] : '';
                    }
                }
                $attributes['n2-lightbox-urls']   = implode(',', $realUrls);
                $attributes['n2-lightbox-titles'] = implode('|||', $titles);
                $attributes['data-litebox-group'] = md5(uniqid(mt_rand(), true));
            }
        }

        return $argument;
    }
}


class N2LinkScrollTo {

    private static function init() {
        static $inited = false;
        if (!$inited) {
            N2JS::addInline('
            window.n2Scroll = {
                to: function(top){
                    n2("html, body").animate({ scrollTop: top }, 400);
                },
                top: function(){
                    n2Scroll.to(0);
                },
                bottom: function(){
                    n2Scroll.to(n2(document).height() - n2(window).height());
                },
                before: function(el){
                    n2Scroll.to(el.offset().top - n2(window).height());
                },
                after: function(el){
                    n2Scroll.to(el.offset().top + el.height());
                },
                next: function(el, selector){
                    var els = n2(selector),
                        nextI = -1;
                    els.each(function(i, slider){
                        if(n2(el).is(slider) || n2.contains(slider, el)){
                            nextI = i + 1;
                            return false;
                        }
                    });
                    if(nextI != -1 && nextI <= els.length){
                        n2Scroll.element(els.eq(nextI));
                    }
                },
                previous: function(el, selector){
                    var els = n2(selector),
                        prevI = -1;
                    els.each(function(i, slider){
                        if(n2(el).is(slider) || n2.contains(slider, el)){
                            prevI = i - 1;
                            return false;
                        }
                    });
                    if(prevI >= 0){
                        n2Scroll.element(els.eq(prevI));
                    }
                },
                element: function(selector){
                    var offsetTop = 0;
                    if(typeof n2ScrollOffsetSelector !== "undefined"){
                        offsetTop = n2(n2ScrollOffsetSelector).outerHeight();
                    }
                    n2Scroll.to(n2(selector).offset().top - offsetTop);
                }
            };');
            $inited = true;
        }
    }

    public static function parse($argument, &$attributes, $isEditor = false) {
        if (!$isEditor) {
            self::init();
            switch ($argument) {
                case 'top':
                case 'bottom':
                    $onclick = 'n2Scroll.' . $argument . '();';
                    break;
                case 'beforeSlider':
                    $onclick = 'n2Scroll.before(n2(this).closest(".n2-ss-slider").addBack());';
                    break;
                case 'afterSlider':
                    $onclick = 'n2Scroll.after(n2(this).closest(".n2-ss-slider").addBack());';
                    break;
                case 'nextSlider':
                    $onclick = 'n2Scroll.next(this, ".n2-ss-slider");';
                    break;
                case 'previousSlider':
                    $onclick = 'n2Scroll.previous(this, ".n2-ss-slider");';
                    break;
                default:
                    $onclick = 'n2Scroll.element("' . $argument . '");';
                    break;
            }
            $attributes['onclick'] = $onclick . "return false;";
        }

        return '#';
    }
}