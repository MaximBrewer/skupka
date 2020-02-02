<?php
N2Loader::import('libraries.plugins.N2SliderGeneratorPluginAbstract', 'smartslider');

class N2SSPluginGeneratorODude extends N2SliderGeneratorPluginAbstract
{

    public static $group = 'odude';
    public static $groupLabel = 'ODude';

    function onGeneratorList(&$group, &$list) {
        $installed = N2Filesystem::existsFolder(JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_odudecard');
        $url       = 'http://extensions.joomla.org/extensions/extension/photos-a-images/ecards/odude-ecards';

        $group[self::$group] = self::$groupLabel;

        if (!isset($list[self::$group])) {
            $list[self::$group] = array();
        }

        $list[self::$group]['ecard'] = N2GeneratorInfo::getInstance(self::$groupLabel, n2_('E-card'), $this->getPath() . 'ecard')
                                                      ->setInstalled($installed)
                                                      ->setUrl($url)
                                                      ->setType('image_extended');
    }

    function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR;
    }

}

N2Plugin::addPlugin('ssgenerator', 'N2SSPluginGeneratorODude');
