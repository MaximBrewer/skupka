<?php
N2Loader::import('libraries.plugins.N2SliderGeneratorPluginAbstract', 'smartslider');

class N2SSPluginGeneratorEasySocial extends N2PluginBase
{

    public static $group = 'easysocial';
    public static $groupLabel = 'EasySocial';

    function onGeneratorList(&$group, &$list) {
        $installed = N2Filesystem::existsFolder(JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easysocial');
        $url       = 'http://extensions.joomla.org/extension/easysocial';

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

        $list[self::$group]['albums'] = N2GeneratorInfo::getInstance(self::$groupLabel, n2_('Albums'), $this->getPath() . 'albums')
                                                       ->setInstalled($installed)
                                                       ->setUrl($url)
                                                       ->setType('image');

        $list[self::$group]['videos'] = N2GeneratorInfo::getInstance(self::$groupLabel, n2_('Videos'), $this->getPath() . 'videos')
                                                       ->setInstalled($installed)
                                                       ->setUrl($url)
                                                       ->setType('article');
    }

    function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR;
    }
}

N2Plugin::addPlugin('ssgenerator', 'N2SSPluginGeneratorEasySocial');

