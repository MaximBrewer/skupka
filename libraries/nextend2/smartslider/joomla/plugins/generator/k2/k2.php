<?php
N2Loader::import('libraries.plugins.N2SliderGeneratorPluginAbstract', 'smartslider');

class N2SSPluginGeneratorK2 extends N2PluginBase
{

    public static $group = 'k2';
    public static $groupLabel = 'K2';

    function onGeneratorList(&$group, &$list) {
        $installed = N2Filesystem::existsFolder(JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_k2');
        $url       = 'http://extensions.joomla.org/extension/authoring-a-content/content-construction/k2';

        $group[self::$group] = self::$groupLabel;

        if (!isset($list[self::$group])) {
            $list[self::$group] = array();
        }

        $list[self::$group]['items'] = N2GeneratorInfo::getInstance(self::$groupLabel, n2_('Items'), $this->getPath() . 'items')
                                                       ->setInstalled($installed)
                                                       ->setUrl($url)
                                                       ->setType('article');
    }

    function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR;
    }

}

N2Plugin::addPlugin('ssgenerator', 'N2SSPluginGeneratorK2');
