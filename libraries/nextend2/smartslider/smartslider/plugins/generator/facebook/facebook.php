<?php
N2Loader::import('libraries.settings.settings', 'smartslider');
N2Loader::import('libraries.plugins.N2SliderGeneratorPluginAbstract', 'smartslider');

class N2SSPluginGeneratorFacebook extends N2SliderGeneratorPluginAbstract
{

    public static $group = 'facebook';
    public static $groupLabel = 'Facebook';

    protected $configurationClass = 'N2SliderGeneratorFacebookConfiguration';

    function onGeneratorList(&$group, &$list) {
        $group[self::$group] = self::$groupLabel;

        if (!isset($list[self::$group])) {
            $list[self::$group] = array();
        }

        $list[self::$group]['albums'] = N2GeneratorInfo::getInstance(self::$groupLabel, n2_('Photos by album'), $this->getPath() . 'albums')
                                                       ->setType('image_extended')
                                                       ->setConfiguration($this->configurationClass);

        $list[self::$group]['postsbypage'] = N2GeneratorInfo::getInstance(self::$groupLabel, n2_('Posts by page'), $this->getPath() . 'postsbypage')
                                                            ->setType('image')
                                                            ->setConfiguration($this->configurationClass);
    }

    function onSmartSliderConfigurationList(&$list) {

        require_once dirname(__FILE__) . '/configuration.php';
        $class         = $this->configurationClass;
        $configuration = new $class(null);

        $list[self::$group] = array(
            'title'         => n2_('Facebook generator'),
            'configuration' => $configuration,
            'url'           => array(
                'generator/configure',
                array(
                    'group' => 'facebook',
                    'type'  => 'postsbypage'
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

N2Plugin::addPlugin('ssgenerator', 'N2SSPluginGeneratorFacebook');
