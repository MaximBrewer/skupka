<?php
N2Loader::import('libraries.plugins.N2SliderGeneratorPluginAbstract', 'smartslider');

class N2SSPluginGeneratorIgniteGallery extends N2PluginBase
{

    public static $group = 'ignitegallery';
    public static $groupLabel = 'Ignite Gallery';

    function onGeneratorList(&$group, &$list) {
        $installed = N2Filesystem::existsFolder(JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_igallery');
        $url       = 'http://extensions.joomla.org/profile/extension/photos-a-images/galleries/ignite-gallery';

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

N2Plugin::addPlugin('ssgenerator', 'N2SSPluginGeneratorIgniteGallery');

