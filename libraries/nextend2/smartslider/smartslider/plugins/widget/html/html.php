<?php
class N2SSPluginWidgetHTML extends N2PluginBase
{

    private static $group = 'html';

    function onWidgetList(&$list) {
        $list[self::$group] = array(
            n2_('HTML'),
            $this->getPath(),
            10
        );
    }

    function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . self::$group . DIRECTORY_SEPARATOR;
    }
}

N2Plugin::addPlugin('sswidget', 'N2SSPluginWidgetHTML');
