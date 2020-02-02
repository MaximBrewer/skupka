<?php
N2Loader::import('libraries.plugins.N2SliderGeneratorPluginAbstract', 'smartslider');

class N2SSPluginGeneratorImagesInFolder extends N2SliderGeneratorPluginAbstract
{

    public static $group = 'infolder';
    public static $groupLabel = 'Folder';

    function onGeneratorList(&$group, &$list) {
        $group[self::$group] = self::$groupLabel;

        if (!isset($list[self::$group])) {
            $list[self::$group] = array();
        }

        $list[self::$group]['images'] = N2GeneratorInfo::getInstance(self::$groupLabel, n2_('Images in folder'), $this->getPath() . 'images')
                                                       ->setType('image');

		$list[self::$group]['subfolders'] = N2GeneratorInfo::getInstance(self::$groupLabel, n2_('Images in folder and subfolders'), $this->getPath() . 'subfolders')
		                                               ->setType('image');
    }

    function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR;
    }
}

N2Plugin::addPlugin('ssgenerator', 'N2SSPluginGeneratorImagesInFolder');
