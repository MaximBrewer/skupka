<?php
class N2SSPluginTypeBlock extends N2PluginBase
{

    private static $name = 'block';

    function onTypeList(&$list, &$labels) {
        $list[self::$name]   = $this->getPath();
        $labels[self::$name] = n2_x('Block', 'Slider type');
    }

    static function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . self::$name . DIRECTORY_SEPARATOR;
    }
}

N2Plugin::addPlugin('sstype', 'N2SSPluginTypeBlock');
