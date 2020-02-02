<?php
N2Loader::import('libraries.plugins.N2SliderGeneratorPluginAbstract', 'smartslider');

class N2SSPluginGeneratorJoomShopping extends N2PluginBase
{

    public static $group = 'joomshopping';
    public static $groupLabel = 'JoomShopping';

    function onGeneratorList(&$group, &$list) {
        $installed = N2Filesystem::existsFile(JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_jshopping' . DIRECTORY_SEPARATOR . 'jshopping.php');
        $url       = 'http://extensions.joomla.org/extension/joomshopping';

        $group[self::$group] = self::$groupLabel;

        if (!isset($list[self::$group])) {
            $list[self::$group] = array();
        }

        $list[self::$group]['products'] = N2GeneratorInfo::getInstance(self::$groupLabel, n2_('Products'), $this->getPath() . 'products')
                                                         ->setInstalled($installed)
                                                         ->setUrl($url)
                                                         ->setType('product');
    }

    function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR;
    }

}

N2Plugin::addPlugin('ssgenerator', 'N2SSPluginGeneratorJoomShopping');

