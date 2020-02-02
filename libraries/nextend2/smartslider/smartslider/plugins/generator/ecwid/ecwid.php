<?php
N2Loader::import('libraries.settings.settings', 'smartslider');
N2Loader::import('libraries.plugins.N2SliderGeneratorPluginAbstract', 'smartslider');

class N2SSPluginGeneratorEcwid extends N2SliderGeneratorPluginAbstract
{

    public static $group = 'ecwid';
    public static $groupLabel = 'Ecwid';

    protected $configurationClass = 'N2SliderGeneratorEcwidConfiguration';

    function onGeneratorList(&$group, &$list) {

        $group[self::$group] = 'Ecwid';

        if (!isset($list[self::$group])) {
            $list[self::$group] = array();
        }

        $list[self::$group]['products'] = N2GeneratorInfo::getInstance(self::$groupLabel, n2_('Products'), $this->getPath() . 'products')
                                                         ->setType('product')
                                                         ->setConfiguration($this->configurationClass);

        $list[self::$group]['random_products'] = N2GeneratorInfo::getInstance(self::$groupLabel, n2_('Random products'), $this->getPath() . 'random_products')
                                                                ->setType('product')
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
            'title'         => 'Ecwid ' . n2_('generator'),
            'configuration' => $configuration,
            'url'           => array(
                'generator/configure',
                array(
                    'group' => 'ecwid',
                    'type'  => 'products'
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

N2Plugin::addPlugin('ssgenerator', 'N2SSPluginGeneratorEcwid');



