<?php
N2Loader::import('libraries.plugins.N2SliderGeneratorPluginAbstract', 'smartslider');

class N2SSPluginGeneratorPinterest extends N2SliderGeneratorPluginAbstract
{

    public static $group = 'pinterest';
    public static $groupLabel = 'Pinterest';

    function onGeneratorList(&$group, &$list) {
        $group[self::$group] = self::$groupLabel;

        if (!isset($list[self::$group])) {
            $list[self::$group] = array();
        }

        $list[self::$group]['images'] = N2GeneratorInfo::getInstance(self::$groupLabel, n2_('Images'), $this->getPath() . 'images')
                                                       ->setType('image_extended');
    }

    function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR;
    }
}

N2Plugin::addPlugin('ssgenerator', 'N2SSPluginGeneratorPinterest');

