<?php
class N2SSPluginResponsiveAdaptive extends N2PluginBase
{

    private static $name = 'adaptive';

    function onResponsiveList(&$list, &$labels) {
        $list[self::$name]   = $this->getPath();
        $labels[self::$name] = n2_x('Fixed', 'Slider responsive mode');
    }

    static function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . self::$name . DIRECTORY_SEPARATOR;
    }
}

N2Plugin::addPlugin('ssresponsive', 'N2SSPluginResponsiveAdaptive');

class N2SSResponsiveAdaptive
{

    private $params, $responsive;

    public function __construct($params, $responsive, $features) {
        $this->params     = $params;
        $this->responsive = $responsive;
    }
}
