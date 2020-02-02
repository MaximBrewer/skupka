<?php

class N2SmartSliderCSSBlock extends N2SmartSliderCSSAbstract
{

    protected function  renderType(&$context) {
        $params = $this->slider->params;
        N2Loader::import('libraries.image.color');

        $width  = intval($context['width']);
        $height = intval($context['height']);

        $context['backgroundSize']       = $params->get('background-size');
        $context['backgroundAttachment'] = $params->get('background-fixed') ? 'fixed' : 'scroll';

        $context['canvaswidth']  = $width . "px";
        $context['canvasheight'] = $height . "px";

        N2LESS::addFile(N2Filesystem::translate(dirname(__FILE__) . NDS . 'style.n2less'), $this->slider->cacheId, $context, NEXTEND_SMARTSLIDER_ASSETS . '/less' . NDS);
    }

}