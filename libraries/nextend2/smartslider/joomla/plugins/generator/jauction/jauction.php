<?php
N2Loader::import('libraries.plugins.N2SliderGeneratorPluginAbstract', 'smartslider');

class N2SSPluginGeneratorJAuction extends N2PluginBase
{

    public static $group = 'jauction';
    public static $groupLabel = 'JAuction';

    function onGeneratorList(&$group, &$list) {
        $installed = N2Filesystem::existsFile(JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_jauction' . DIRECTORY_SEPARATOR . 'jauction.php');
        $url       = 'https://joobi.co/jauction.html';

        $group[self::$group] = self::$groupLabel;

        if (!isset($list[self::$group])) {
            $list[self::$group] = array();
        }

        $list[self::$group]['auctions'] = N2GeneratorInfo::getInstance(self::$groupLabel, n2_('Auctions'), $this->getPath() . 'auctions')
                                                         ->setInstalled($installed)
                                                         ->setUrl($url)
                                                         ->setType('article');
    }

    function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR;
    }

}

N2Plugin::addPlugin('ssgenerator', 'N2SSPluginGeneratorJAuction');
