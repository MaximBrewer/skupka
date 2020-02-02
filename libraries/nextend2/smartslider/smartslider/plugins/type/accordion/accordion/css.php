<?php
N2Loader::import('libraries.image.color');
N2Loader::import('libraries.parse.font');

class N2SmartSliderCSSAccordion extends N2SmartSliderCSSAbstract
{

    protected function  renderType(&$context) {
        $params = $this->slider->params;

        $orientation = $params->get('orientation');

        $width  = intval($context['width']);
        $height = intval($context['height']);


        $border1            = $params->get('outer-border');
        $context['border1'] = $border1 . 'px';

        $borderOuterColor       = $params->get('outer-border-color');
        $rgba                   = N2Color::hex2rgba($borderOuterColor);
        $context['border1rgba'] = 'RGBA(' . $rgba[0] . ',' . $rgba[1] . ',' . $rgba[2] . ',' . round($rgba[3] / 127, 2) . ')';
        $context['border1hex']  = '#' . substr($borderOuterColor, 0, 6);

        $border2            = $params->get('inner-border');
        $context['border2'] = $border2 . 'px';

        $borderInnerColor       = $params->get('inner-border-color');
        $rgba                   = N2Color::hex2rgba($borderInnerColor);
        $context['border2rgba'] = 'RGBA(' . $rgba[0] . ',' . $rgba[1] . ',' . $rgba[2] . ',' . round($rgba[3] / 127, 2) . ')';
        $context['border2hex']  = '#' . substr($borderInnerColor, 0, 6);

        $context['borderRadius'] = intval($params->get('border-radius')) . 'px';

        $orientationMargin            = intval($params->get('title-margin'));
        $context['orientationmargin'] = $orientationMargin . 'px';

        $context['tabbg']       = '#' . $params->get('tab-normal-color');
        $context['tabbgactive'] = '#' . $params->get('tab-active-color');

        $slideMargin = max(0, $params->get('slide-margin'));

        $context['slidemargin']    = $slideMargin . 'px';
        $context['slidemarginneg'] = -$slideMargin . 'px';


        $title      = max(10, $params->get('title-size'));
        $titleSizes = $title * $context['count'];

        $context['titleBorderRadius'] = max(0, intval($params->get('title-border-radius'))) . 'px';

        $context['inner1margin'] = '0';

        if ($context['canvas']) {

            switch ($orientation) {
                case 'vertical':
                    $width += 2 * ($border1 + $border2) + $slideMargin * 2;
                    $height += 2 * ($border1 + $border2 + $slideMargin * $context['count']) + $titleSizes;
                    break;
                default:
                    $width += 2 * ($border1 + $border2) + $slideMargin * 2 * $context['count'] + $titleSizes;
                    $height += 2 * ($border1 + $border2 + $slideMargin);
            }

            $context['width']  = $width . "px";
            $context['height'] = $height . "px";
        }

        $width                    = $width - 2 * $border1;
        $height                   = $height - 2 * $border1;
        $context['border1width']  = $width . 'px';
        $context['border1height'] = $height . 'px';

        $width                    = $width - 2 * $border2;
        $height                   = $height - 2 * $border2;
        $context['border2width']  = $width . 'px';
        $context['border2height'] = $height . 'px';


        switch ($orientation) {
            case 'vertical':
                $width  = $width - 2 * $slideMargin;
                $height = $height - 2 * $context['count'] * $slideMargin;

                $context['titlewidth']   = $width . "px";
                $context['titleheight']  = $title . "px";
                $context['canvaswidth']  = $width . "px";
                $context['canvasheight'] = $height - $titleSizes . "px";
                break;
            default:
                $width  = $width - 2 * ($context['count']) * $slideMargin;
                $height = $height - 2 * $slideMargin;

                $context['titlewidth']   = $title . "px";
                $context['titleheight']  = $height . "px";
                $context['canvaswidth']  = $width - $titleSizes . "px";
                $context['canvasheight'] = $height . "px";
        }

        N2LESS::addFile(N2Filesystem::translate(dirname(__FILE__) . NDS . 'style.n2less'), $this->slider->cacheId, $context, NEXTEND_SMARTSLIDER_ASSETS . '/less' . NDS);
    }
}