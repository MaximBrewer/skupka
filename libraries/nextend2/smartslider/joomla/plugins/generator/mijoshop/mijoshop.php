<?php
N2Loader::import('libraries.plugins.N2SliderGeneratorPluginAbstract', 'smartslider');

class N2SSPluginGeneratorMijoShop extends N2PluginBase
{

    public static $group = 'mijoshop';
    public static $groupLabel = 'MijoShop';

    function onGeneratorList(&$group, &$list) {
        $installed = N2Filesystem::existsFile(JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_mijoshop' . DIRECTORY_SEPARATOR . 'mijoshop.php');
        $url       = 'http://miwisoft.com/joomla-extensions/mijoshop-joomla-shopping-cart';

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

N2Plugin::addPlugin('ssgenerator', 'N2SSPluginGeneratorMijoShop');
