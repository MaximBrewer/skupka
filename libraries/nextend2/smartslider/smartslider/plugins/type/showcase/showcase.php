<?php
class N2SSPluginTypeShowcase extends N2PluginBase
{

    private static $name = 'showcase';

    function onTypeList(&$list, &$labels) {
        $list[self::$name]   = $this->getPath();
        $labels[self::$name] = n2_x('Showcase', 'Slider type');
    }

    static function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . self::$name . DIRECTORY_SEPARATOR;
    }
}

N2Plugin::addPlugin('sstype', 'N2SSPluginTypeShowcase');
