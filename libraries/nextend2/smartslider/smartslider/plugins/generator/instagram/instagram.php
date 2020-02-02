<?php
N2Loader::import('libraries.settings.settings', 'smartslider');

class N2SSPluginGeneratorInstagram extends N2PluginBase
{

    public static $group = 'instagram';
    public static $groupLabel = 'Instagram';

    protected $configurationClass = 'N2SliderGeneratorInstagramConfiguration';

    function onGeneratorList(&$group, &$list) {
        $group[self::$group] = self::$groupLabel;

        if (!isset($list[self::$group])) {
            $list[self::$group] = array();
        }

        $list[self::$group]['myfeed'] = N2GeneratorInfo::getInstance(self::$groupLabel, n2_('My feed'), $this->getPath() . 'myfeed')
                                                       ->setType('image_extended')
                                                       ->setConfiguration($this->configurationClass);

        $list[self::$group]['tagsearch'] = N2GeneratorInfo::getInstance(self::$groupLabel, n2_('Search by tag'), $this->getPath() . 'tagsearch')
                                                          ->setType('image_extended')
                                                          ->setConfiguration($this->configurationClass);

        $list[self::$group]['myphotos'] = N2GeneratorInfo::getInstance(self::$groupLabel, n2_('Photos by user'), $this->getPath() . 'myphotos')
                                                         ->setType('image_extended')
                                                         ->setConfiguration($this->configurationClass);

    }

    function onNextendInstagram(&$instagram) {
        $config = new N2Data();
        $config->loadJson(N2Base::getApplication('smartslider')->storage->get(self::$group));

        require_once($this->getPath() . "api/Instagram.php");
        $c = array(
            'client_id'     => $config->get('apikey', ''),
            'client_secret' => $config->get('apisecret', ''),
            'redirect_uri'  => '',
            'grant_type'    => 'authorization_code',
        );

        $instagram = new Instagram($c);

        $instagram->setAccessToken($config->get('token', ''));
    }

    function onSmartSliderConfigurationList(&$list) {

        require_once dirname(__FILE__) . '/configuration.php';
        $class         = $this->configurationClass;
        $configuration = new $class(null);

        $list[self::$group] = array(
            'title'         => n2_('Instagram generator'),
            'configuration' => $configuration,
            'url'           => array(
                'generator/configure',
                array(
                    'group' => 'instagram'
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

N2Plugin::addPlugin('ssgenerator', 'N2SSPluginGeneratorInstagram');
