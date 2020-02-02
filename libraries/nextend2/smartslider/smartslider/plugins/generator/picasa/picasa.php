<?php
N2Loader::import('libraries.settings.settings', 'smartslider');
N2Loader::import('libraries.plugins.N2SliderGeneratorPluginAbstract', 'smartslider');

class N2SSPluginGeneratorPicasa extends N2SliderGeneratorPluginAbstract
{

    public static $group = 'picasa';
    public static $groupLabel = 'Picasa';

    protected $configurationClass = 'N2SliderGeneratorPicasaConfiguration';

    function onGeneratorList(&$group, &$list) {

        $group[self::$group] = 'Picasa';

        if (!isset($list[self::$group])) {
            $list[self::$group] = array();
        }

        $list[self::$group]['images'] = N2GeneratorInfo::getInstance(self::$groupLabel, n2_('Images'), $this->getPath() . 'images')
                                                       ->setType('image')
                                                       ->setConfiguration($this->configurationClass);
    }

    function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR;
    }

    function onSmartSliderConfigurationList(&$list) {

        require_once dirname(__FILE__) . '/configuration.php';
        $class         = $this->configurationClass;
        $configuration = new $class(null);

        $list[self::$group] = array(
            'title'         => 'Picasa ' . n2_('generator'),
            'configuration' => $configuration,
            'url'           => array(
                'generator/configure',
                array(
                    'group' => 'picasa',
                    'type'  => 'images'
                )
            )
        );
    }

    function onNextendGeneratorConfiguration(&$group, &$path) {
        if ($group == self::$group) {
            $path = $this->getPath();
        }
    }
}

N2Plugin::addPlugin('ssgenerator', 'N2SSPluginGeneratorPicasa');
