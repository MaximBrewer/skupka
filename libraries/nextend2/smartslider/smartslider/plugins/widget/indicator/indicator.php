<?php
class N2SSPluginWidgetIndicator extends N2PluginBase
{

    private static $group = 'indicator';

    function onWidgetList(&$list) {
        $list[self::$group] = array(
            n2_('Indicator'),
            $this->getPath(),
            4
        );
    }

    function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . self::$group . DIRECTORY_SEPARATOR;
    }
}

N2Plugin::addPlugin('sswidget', 'N2SSPluginWidgetIndicator');
