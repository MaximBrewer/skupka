<?php
N2Loader::import('libraries.settings.settings', 'smartslider');

class N2SSPluginGeneratorDribbble extends N2PluginBase
{

    public static $group = 'dribbble';
    public static $groupLabel = 'Dribbble';

    protected $configurationClass = 'N2SliderGeneratorDribbbleConfiguration';

    function onGeneratorList(&$group, &$list) {
        $group[self::$group] = self::$groupLabel;

        if (!isset($list[self::$group])) {
            $list[self::$group] = array();
        }

        $list[self::$group]['shots'] = N2GeneratorInfo::getInstance(self::$groupLabel, n2_x('Shots', "Dribbble"), $this->getPath() . 'shots')
                                                      ->setType('image_extended')
                                                      ->setConfiguration($this->configurationClass);

        $list[self::$group]['project'] = N2GeneratorInfo::getInstance(self::$groupLabel, n2_x('Project', "Dribbble"), $this->getPath() . 'project')
                                                        ->setType('image_extended')
                                                        ->setConfiguration($this->configurationClass);
    }

    function onSmartSliderConfigurationList(&$list) {

        require_once dirname(__FILE__) . '/configuration.php';
        $class         = $this->configurationClass;
        $configuration = new $class(null);

        $list[self::$group] = array(
            'title'         => n2_('Dribbble generator'),
            'configuration' => $configuration,
            'url'           => array(
                'generator/configure',
                array(
                    'group' => 'dribbble'
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

N2Plugin::addPlugin('ssgenerator', 'N2SSPluginGeneratorDribbble');
