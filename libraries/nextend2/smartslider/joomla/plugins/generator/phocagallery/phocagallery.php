<?php
N2Loader::import('libraries.plugins.N2SliderGeneratorPluginAbstract', 'smartslider');

class N2SSPluginGeneratorPhocaGallery extends N2SliderGeneratorPluginAbstract
{

    public static $group = 'phocagallery';
    public static $groupLabel = 'Phoca Gallery';

    function onGeneratorList(&$group, &$list) {
        $installed = N2Filesystem::existsFolder(JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_phocagallery');
        $url       = 'http://extensions.joomla.org/extension/phoca-gallery';

        $group[self::$group] = self::$groupLabel;

        if (!isset($list[self::$group])) {
            $list[self::$group] = array();
        }

        $list[self::$group]['images'] = N2GeneratorInfo::getInstance(self::$groupLabel, n2_('Images'), $this->getPath() . 'images')
                                                       ->setInstalled($installed)
                                                       ->setUrl($url)
                                                       ->setType('image_extended');
    }

    function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR;
    }

}

N2Plugin::addPlugin('ssgenerator', 'N2SSPluginGeneratorPhocaGallery');
