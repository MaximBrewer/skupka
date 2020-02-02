<?php

N2Loader::import('libraries.plugins.N2SliderWidgetAbstract', 'smartslider');
N2Loader::import('libraries.image.color');

class N2SSPluginWidgetIndicatorStripe extends N2SSPluginWidgetAbstract {

    var $_name = 'stripe';

    private static $key = 'widget-indicator-';

    static function getDefaults() {
        return array(
            'widget-indicator-position-mode' => 'simple',
            'widget-indicator-position-area' => 9,
            'widget-indicator-width'         => '100%',
            'widget-indicator-height'        => 6,
            'widget-indicator-overlay'       => 0,
            'widget-indicator-track'         => '000000ab',
            'widget-indicator-bar'           => '00c1c4ff'
        );
    }

    function onIndicatorList(&$list) {
        $list[$this->_name] = $this->getPath();
    }

    function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . 'stripe' . DIRECTORY_SEPARATOR;
    }

    static function getPositions(&$params) {
        $positions                       = array();
        $positions['indicator-position'] = array(
            self::$key . 'position-',
            'indicator'
        );

        return $positions;
    }

    static function render($slider, $id, $params) {

        N2LESS::addFile(N2Filesystem::translate(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'stripe' . DIRECTORY_SEPARATOR . 'style.n2less'), $slider->cacheId, array(
            "sliderid" => $slider->elementId
        ), NEXTEND_SMARTSLIDER_ASSETS . '/less' . NDS);
        N2JS::addFile(N2Filesystem::translate(dirname(__FILE__) . '/stripe/indicator.min.js'), $id);
    

        list($displayClass, $displayAttributes) = self::getDisplayAttributes($params, self::$key);

        list($trackHex, $trackRGBA) = N2Color::colorToCss($params->get(self::$key . 'track'));
        list($barHex, $barRGBA) = N2Color::colorToCss($params->get(self::$key . 'bar'));

        list($style, $attributes) = self::getPosition($params, self::$key);
        $attributes['data-offset'] = $params->get(self::$key . 'position-offset', 0);


        $width = $params->get(self::$key . 'width');
        if (is_numeric($width) || substr($width, -1) == '%' || substr($width, -2) == 'px') {
            $style .= 'width:' . $width . ';';
        } else {
            $attributes['data-sswidth'] = $width;
        }

        $height = intval($params->get(self::$key . 'height'));

        $parameters = array(
            'overlay' => $params->get(self::$key . 'position-mode') != 'simple' || $params->get(self::$key . 'overlay'),
            'area'    => intval($params->get(self::$key . 'position-area'))
        );

        N2JS::addInline('new N2Classes.SmartSliderWidgetIndicatorStripe("' . $id . '", ' . json_encode($parameters) . ');');

        return N2Html::tag('div', $displayAttributes + $attributes + array(
                'class' => $displayClass . "nextend-indicator nextend-indicator-stripe n2-ow",
                'style' => 'background-color:#' . $trackHex . ';background-color:' . $trackRGBA . ';' . $style
            ), N2Html::tag('div', array(
            'class' => "nextend-indicator-track  n2-ow",
            'style' => 'height: ' . $height . 'px;background-color:#' . $barHex . ';background-color:' . $barRGBA . ';'
        ), ''));
    }
}

N2Plugin::addPlugin('sswidgetindicator', 'N2SSPluginWidgetIndicatorStripe');
