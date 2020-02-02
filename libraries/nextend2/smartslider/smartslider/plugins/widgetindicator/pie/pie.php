<?php

N2Loader::import('libraries.plugins.N2SliderWidgetAbstract', 'smartslider');
N2Loader::import('libraries.image.color');

class N2SSPluginWidgetIndicatorPie extends N2SSPluginWidgetAbstract {

    var $_name = 'pie';

    private static $key = 'widget-indicator-';

    static function getDefaults() {
        return array(
            'widget-indicator-position-mode'   => 'simple',
            'widget-indicator-position-area'   => 4,
            'widget-indicator-position-offset' => 15,
            'widget-indicator-size'            => 25,
            'widget-indicator-thickness'       => 30,
            'widget-indicator-track'           => '000000ab',
            'widget-indicator-bar'             => 'ffffffff',
            'widget-indicator-skin'            => 'default'
        );
    }

    function onIndicatorList(&$list) {
        $list[$this->_name] = $this->getPath();
    }

    function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . 'pie' . DIRECTORY_SEPARATOR;
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
        N2JS::addFile(N2Filesystem::translate(dirname(__FILE__) . '/pie/indicator.min.js'), $id);
    

        list($displayClass, $displayAttributes) = self::getDisplayAttributes($params, self::$key);

        list($style, $attributes) = self::getPosition($params, self::$key);

        $parameters = array(
            'min'       => 0,
            'max'       => 100,
            'readOnly'  => 1,
            'step'      => 0.1,
            'bgColor'   => N2Color::colorToRGBA($params->get(self::$key . 'track')),
            'fgColor'   => N2Color::colorToRGBA($params->get(self::$key . 'bar')),
            'width'     => intval($params->get(self::$key . 'size')),
            'height'    => intval($params->get(self::$key . 'size')),
            'thickness' => $params->get(self::$key . 'thickness') / 100,
            'lineCap'   => 'butt',
            'skin'      => $params->get(self::$key . 'skin'),
            'inline'    => 0
        );

        N2JS::addInline('new N2Classes.SmartSliderWidgetIndicatorPie("' . $id . '", ' . json_encode($parameters) . ');');

        return N2Html::tag('div', $displayAttributes + $attributes + array(
                'class' => $displayClass . "nextend-indicator nextend-indicator-pie n2-ow",
                'style' => $style
            ));
    }
}

N2Plugin::addPlugin('sswidgetindicator', 'N2SSPluginWidgetIndicatorPie');

class N2SSPluginWidgetIndicatorPieFull extends N2SSPluginWidgetIndicatorPie {

    var $_name = 'pieFull';

    static function getDefaults() {
        return array_merge(N2SSPluginWidgetIndicatorPie::getDefaults(), array(
            'widget-indicator-thickness' => 100,
            'widget-indicator-track'     => 'ffffff00',
            'widget-indicator-bar'       => 'ffffff80',
        ));
    }
}

N2Plugin::addPlugin('sswidgetindicator', 'N2SSPluginWidgetIndicatorPieFull');