<?php
class N2SSPluginWidgetFullScreen extends N2PluginBase
{

    private static $group = 'fullscreen';

    function onWidgetList(&$list) {
        $list[self::$group] = array(
            n2_('Full screen'),
            $this->getPath(),
            9
        );
    }

    function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . self::$group . DIRECTORY_SEPARATOR;
    }
}

N2Plugin::addPlugin('sswidget', 'N2SSPluginWidgetFullScreen');
