<?php
N2Loader::import('libraries.plugins.N2SliderGeneratorPluginAbstract', 'smartslider');

class N2SSPluginGeneratorEasyBlog extends N2PluginBase
{

    public static $group = 'easyblog';
    public static $groupLabel = 'EasyBlog';

    function onGeneratorList(&$group, &$list) {
        $installed = N2Filesystem::existsFolder(JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easyblog');
        $url       = 'http://extensions.joomla.org/extension/easyblog';

        $group[self::$group] = self::$groupLabel;

        if (!isset($list[self::$group])) {
            $list[self::$group] = array();
        }

        $list[self::$group]['posts'] = N2GeneratorInfo::getInstance(self::$groupLabel, n2_('Posts'), $this->getPath() . 'posts')
                                                       ->setInstalled($installed)
                                                       ->setUrl($url)
                                                       ->setType('article');
    }

    function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR;
    }
}

N2Plugin::addPlugin('ssgenerator', 'N2SSPluginGeneratorEasyBlog');

