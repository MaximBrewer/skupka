<?php
N2Loader::import('libraries.settings.settings', 'smartslider');
N2Loader::import('libraries.plugins.N2SliderGeneratorPluginAbstract', 'smartslider');

class N2SSPluginGeneratorYoutube extends N2SliderGeneratorPluginAbstract
{

    public static $group = 'youtube';
    public static $groupLabel = 'YouTube';

    protected $configurationClass = 'N2SliderGeneratorYouTubeConfiguration';

    function onGeneratorList(&$group, &$list) {
        $group[self::$group] = self::$groupLabel;

        if (!isset($list[self::$group])) {
            $list[self::$group] = array();
        }

        $list[self::$group]['bysearch'] = N2GeneratorInfo::getInstance(self::$groupLabel, n2_('By search'), $this->getPath() . 'bysearch')
                                                         ->setType('youtube')
                                                         ->setConfiguration($this->configurationClass);

        $list[self::$group]['byplaylist'] = N2GeneratorInfo::getInstance(self::$groupLabel, n2_('By playlist'), $this->getPath() . 'byplaylist')
                                                           ->setType('youtube')
                                                           ->setConfiguration($this->configurationClass);
    }

    function onSmartSliderConfigurationList(&$list) {
        require_once dirname(__FILE__) . '/configuration.php';

        $class         = $this->configurationClass;
        $configuration = new $class(null);

        $list[self::$group] = array(
            'title'         => n2_('YouTube generator'),
            'configuration' => $configuration,
            'url'           => array(
                'generator/configure',
                array(
                    'group' => 'youtube',
                    'type'  => 'bysearch'
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

N2Plugin::addPlugin('ssgenerator', 'N2SSPluginGeneratorYoutube');
