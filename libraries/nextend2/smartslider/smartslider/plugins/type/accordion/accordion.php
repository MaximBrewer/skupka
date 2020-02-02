<?php
class N2SSPluginTypeAccordion extends N2PluginBase
{

    private static $name = 'accordion';

    function onTypeList(&$list, &$labels) {
        $list[self::$name]   = $this->getPath();
        $labels[self::$name] = n2_x('Accordion', 'Slider type');
    }

    static function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . self::$name . DIRECTORY_SEPARATOR;
    }
}

N2Plugin::addPlugin('sstype', 'N2SSPluginTypeAccordion');
