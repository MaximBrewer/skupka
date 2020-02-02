<?php
N2Loader::import('libraries.settings.settings', 'smartslider');

class N2SSPluginGeneratorTwitter extends N2PluginBase
{

    public static $group = 'twitter';
    public static $groupLabel = 'Twitter';

    protected $configurationClass = 'N2SliderGeneratorTwitterConfiguration';

    function onGeneratorList(&$group, &$list) {
        $group[self::$group] = self::$groupLabel;

        if (!isset($list[self::$group])) {
            $list[self::$group] = array();
        }

        $list[self::$group]['timeline'] = N2GeneratorInfo::getInstance(self::$groupLabel, n2_x('Timeline', "twitter"), $this->getPath() . 'timeline')
                                                         ->setType('social_post')
                                                         ->setConfiguration($this->configurationClass);
    }

    function onSmartSliderConfigurationList(&$list) {

        require_once dirname(__FILE__) . '/configuration.php';
        $class         = $this->configurationClass;
        $configuration = new $class(null);

        $list[self::$group] = array(
            'title'         => n2_('Twitter generator'),
            'configuration' => $configuration,
            'url'           => array(
                'generator/configure',
                array(
                    'group' => 'twitter'
                )
            )
        );
    }

    function onNextendGeneratorConfiguration(&$group, &$path) {
        if ($group == self::$group) {
            $path = $this->getPath();
        }
    }

    function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR;
    }
}

N2Plugin::addPlugin('ssgenerator', 'N2SSPluginGeneratorTwitter');
