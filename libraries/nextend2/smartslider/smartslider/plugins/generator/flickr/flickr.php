<?php
N2Loader::import('libraries.settings.settings', 'smartslider');

class N2SSPluginGeneratorFlickr extends N2PluginBase
{

    public static $group = 'flickr';
    public static $groupLabel = 'Flickr';

    protected $configurationClass = 'N2SliderGeneratorFlickrConfiguration';

    function onGeneratorList(&$group, &$list) {
        $group[self::$group] = self::$groupLabel;

        if (!isset($list[self::$group])) {
            $list[self::$group] = array();
        }
        $list[self::$group]['peoplephotostream'] = N2GeneratorInfo::getInstance(self::$groupLabel, n2_('My photostream'), $this->getPath() . 'peoplephotostream')
                                                                  ->setType('image_extended')
                                                                  ->setConfiguration($this->configurationClass);

        $list[self::$group]['peoplealbum'] = N2GeneratorInfo::getInstance(self::$groupLabel, n2_('My album'), $this->getPath() . 'peoplealbum')
                                                            ->setType('image_extended')
                                                            ->setConfiguration($this->configurationClass);

        $list[self::$group]['peoplephotogallery'] = N2GeneratorInfo::getInstance(self::$groupLabel, n2_('My photogallery'), $this->getPath() . 'peoplephotogallery')
                                                                   ->setType('image_extended')
                                                                   ->setConfiguration($this->configurationClass);

        $list[self::$group]['photossearch'] = N2GeneratorInfo::getInstance(self::$groupLabel, n2_('Search'), $this->getPath() . 'photossearch')
                                                            ->setType('image')
                                                            ->setConfiguration($this->configurationClass);
    }

    function onSmartSliderConfigurationList(&$list) {

        require_once dirname(__FILE__) . '/configuration.php';
        $class         = $this->configurationClass;
        $configuration = new $class(null);

        $list[self::$group] = array(
            'title'         => n2_('Flickr generator'),
            'configuration' => $configuration,
            'url'           => array(
                'generator/configure',
                array(
                    'group' => 'flickr',
                    'type'  => 'peoplephotoset'
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

N2Plugin::addPlugin('ssgenerator', 'N2SSPluginGeneratorFlickr');
