<?php
N2Loader::import('libraries.plugins.N2SliderGeneratorPluginAbstract', 'smartslider');

class N2SSPluginGeneratorVirtueMart extends N2PluginBase
{

    public static $group = 'virtuemart';
    public static $groupLabel = 'VirtueMart';

    function onGeneratorList(&$group, &$list) {
        $installed = N2Filesystem::existsFile(JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_virtuemart' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'config.php');
        $url       = 'http://extensions.joomla.org/extension/virtuemart';

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

N2Plugin::addPlugin('ssgenerator', 'N2SSPluginGeneratorVirtueMart');
