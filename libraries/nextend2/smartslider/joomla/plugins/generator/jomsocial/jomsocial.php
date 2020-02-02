<?php
N2Loader::import('libraries.plugins.N2SliderGeneratorPluginAbstract', 'smartslider');

class N2SSPluginGeneratorJomSocial extends N2PluginBase
{

    public static $group = 'jomsocial';
    public static $groupLabel = 'JomSocial';

    function onGeneratorList(&$group, &$list) {
        $installed = N2Filesystem::existsFolder(JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_community');
        $url       = 'http://extensions.joomla.org/extension/jomsocial';

        $group[self::$group] = self::$groupLabel;

        if (!isset($list[self::$group])) {
            $list[self::$group] = array();
        }

        $list[self::$group]['events'] = N2GeneratorInfo::getInstance(self::$groupLabel, n2_('Events'), $this->getPath() . 'events')
                                                       ->setInstalled($installed)
                                                       ->setUrl($url)
                                                       ->setType('event');

        $list[self::$group]['groups'] = N2GeneratorInfo::getInstance(self::$groupLabel, n2_('Groups'), $this->getPath() . 'groups')
                                                       ->setInstalled($installed)
                                                       ->setUrl($url)
                                                       ->setType('article');

        $list[self::$group]['videos'] = N2GeneratorInfo::getInstance(self::$groupLabel, n2_('Videos'), $this->getPath() . 'videos')
                                                       ->setInstalled($installed)
                                                       ->setUrl($url)
                                                       ->setType('youtube');

        $list[self::$group]['activities'] = N2GeneratorInfo::getInstance(self::$groupLabel, n2_('Activities'), $this->getPath() . 'activities')
                                                           ->setInstalled($installed)
                                                           ->setUrl($url)
                                                           ->setType('article');
    }

    function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR;
    }
}

N2Plugin::addPlugin('ssgenerator', 'N2SSPluginGeneratorJomSocial');

