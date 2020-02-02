<?php
N2Loader::import('libraries.image.color');

class N2SmartSliderCSSCarousel extends N2SmartSliderCSSAbstract
{

    protected function  renderType(&$context) {
        $params = $this->slider->params;

        $width  = intval($context['width']);
        $height = intval($context['height']);


        $backgroundColor           = $params->get('background-color');
        $rgba                      = N2Color::hex2rgba($backgroundColor);
        $context['backgroundrgba'] = 'RGBA(' . $rgba[0] . ',' . $rgba[1] . ',' . $rgba[2] . ',' . round($rgba[3] / 127, 2) . ')';
        if (substr($backgroundColor, 6, 8) != '00') {
            $context['backgroundhex'] = '#' . substr($backgroundColor, 0, 6);
        } else {
            $context['backgroundhex'] = 'transparent';
        }

        $context['backgroundSize']       = $params->get('background-size');
        $context['backgroundAttachment'] = $params->get('background-fixed') ? 'fixed' : 'scroll';


        $backgroundColor                = $params->get('slide-background-color');
        $rgba                           = N2Color::hex2rgba($backgroundColor);
        $context['slideBackgroundrgba'] = 'RGBA(' . $rgba[0] . ',' . $rgba[1] . ',' . $rgba[2] . ',' . round($rgba[3] / 127, 2) . ')';
        if (substr($backgroundColor, 6, 8) != '00') {
            $context['slideBackgroundhex'] = '#' . substr($backgroundColor, 0, 6);
        } else {
            $context['slideBackgroundhex'] = 'transparent';
        }

        $context['slideBorderRadius'] = $params->get('slide-border-radius') . 'px';

        $borderWidth             = $params->get('border-width');
        $backgroundColor         = $params->get('border-color');
        $context['borderRadius'] = $params->get('border-radius') . 'px';


        $context['border'] = $borderWidth . 'px';

        $rgba                  = N2Color::hex2rgba($backgroundColor);
        $context['borderrgba'] = 'RGBA(' . $rgba[0] . ',' . $rgba[1] . ',' . $rgba[2] . ',' . round($rgba[3] / 127, 2) . ')';
        $context['borderhex']  = '#' . substr($backgroundColor, 0, 6);

        $width                   = $width - $borderWidth * 2;
        $height                  = $height - $borderWidth * 2;
        $context['inner1height'] = $height . 'px';

        $context['fullcanvaswidth']  = $width . 'px';
        $context['fullcanvasheight'] = $height . 'px';

        $context['canvaswidth']  = min($width, max(50, intval($params->get('slide-width')))) . 'px';
        $context['canvasheight'] = min($height, max(50, intval($params->get('slide-height')))) . 'px';

        N2LESS::addFile(N2Filesystem::translate(dirname(__FILE__) . NDS . 'style.n2less'), $this->slider->cacheId, $context, NEXTEND_SMARTSLIDER_ASSETS . '/less' . NDS);
    }
}