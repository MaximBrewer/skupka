<?php
N2Loader::import('libraries.plugins.N2SliderGeneratorPluginAbstract', 'smartslider');

class N2SSPluginGeneratorEasyDiscuss extends N2PluginBase
{

    public static $group = 'easydiscuss';
    public static $groupLabel = 'EasyDiscuss';

    function onGeneratorList(&$group, &$list) {
        $installed = N2Filesystem::existsFolder(JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easydiscuss');
        $url       = 'http://extensions.joomla.org/extensions/extension/communication/question-a-answers/easydiscuss';

        $group[self::$group] = self::$groupLabel;

        if (!isset($list[self::$group])) {
            $list[self::$group] = array();
        }

        $list[self::$group]['discussions'] = N2GeneratorInfo::getInstance(self::$groupLabel, n2_('Discussions'), $this->getPath() . 'discussions')
                                                            ->setInstalled($installed)
                                                            ->setUrl($url)
                                                            ->setType('article');
    }

    function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR;
    }
}

N2Plugin::addPlugin('ssgenerator', 'N2SSPluginGeneratorEasyDiscuss');

