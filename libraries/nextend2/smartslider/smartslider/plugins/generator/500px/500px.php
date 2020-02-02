<?php
N2Loader::import('libraries.settings.settings', 'smartslider');

class N2SSPluginGenerator500px extends N2PluginBase
{

    public static $group = '500px';
    public static $groupLabel = '500px';

    protected $configurationClass = 'N2SliderGenerator500pxConfiguration';

    function onGeneratorList(&$group, &$list) {
        $group[self::$group] = self::$groupLabel;

        if (!isset($list[self::$group])) {
            $list[self::$group] = array();
        }

        $list[self::$group]['collection'] = N2GeneratorInfo::getInstance(self::$groupLabel, n2_x('Collection', "500px"), $this->getPath() . 'collection')
                                                            ->setType('image_extended')
                                                            ->setConfiguration($this->configurationClass);
    }

    function onSmartSliderConfigurationList(&$list) {

        require_once dirname(__FILE__) . '/configuration.php';
        $class         = $this->configurationClass;
        $configuration = new $class(null);

        $list[self::$group] = array(
            'title'         => n2_('500px generator'),
            'configuration' => $configuration,
            'url'           => array(
                'generator/configure',
                array(
                    'group' => '500px'
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

N2Plugin::addPlugin('ssgenerator', 'N2SSPluginGenerator500px');
