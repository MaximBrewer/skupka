<?php
N2Loader::import('libraries.plugins.N2SliderGeneratorPluginAbstract', 'smartslider');

class N2SSPluginGeneratorText extends N2SliderGeneratorPluginAbstract
{

    public static $group = 'text';
    public static $groupLabel = 'Text';

    function onGeneratorList(&$group, &$list) {
        $group[self::$group] = self::$groupLabel;

        if (!isset($list[self::$group])) {
            $list[self::$group] = array();
        }

        $list[self::$group]['text'] = N2GeneratorInfo::getInstance(self::$groupLabel, n2_('Text from file'), $this->getPath() . 'text')
                                                       ->setType('image');
        $list[self::$group]['input'] = N2GeneratorInfo::getInstance(self::$groupLabel, n2_('Text from input'), $this->getPath() . 'input')
                                                       ->setType('image');
    }

    function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR;
    }
}

N2Plugin::addPlugin('ssgenerator', 'N2SSPluginGeneratorText');
