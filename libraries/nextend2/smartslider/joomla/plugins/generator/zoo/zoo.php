<?php
N2Loader::import('libraries.plugins.N2SliderGeneratorPluginAbstract', 'smartslider');

class N2SSPluginGeneratorZoo extends N2PluginBase
{

    public static $group = 'zoo';
    public static $groupLabel = 'Zoo';

    function onGeneratorList(&$group, &$list) {

        $installed = N2Filesystem::existsFolder(JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_zoo');
        $url       = "http://extensions.joomla.org/extension/zoo";

        $group[self::$group] = self::$groupLabel;

        if (!isset($list[self::$group])) {
            $list[self::$group] = array();
        }

        if ($installed) {

            require_once(JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_zoo' . DIRECTORY_SEPARATOR . 'config.php');
            $zoo = App::getInstance('zoo');

            $apps = $zoo->table->application->all(array('order' => 'name'));

            foreach ($apps AS $app) {
                foreach ($app->getTypes() AS $type) {
                    //Make them class name safe
                    $appId      = preg_replace('/[^a-zA-Z0-9_\x7f-\xff]*/', '', $app->id);
                    $identifier = preg_replace('/[^a-zA-Z0-9_\x7f-\xff]*/', '', $type->identifier);

                    $list[self::$group][$appId . $identifier] = N2GeneratorInfo::getInstance(self::$groupLabel, self::$groupLabel . ': ' . $app->name . ' (' . $identifier . ')', $this->getPath() . 'items')
                                                                               ->setInstalled($installed)
                                                                               ->setUrl($url)
                                                                               ->setType('article')
                                                                               ->setData('appid', $app->id)
                                                                               ->setData('identifier', $type->identifier);


                    if (!class_exists('N2GeneratorZoo' . $appId . $identifier)) {
                        require_once($this->getPath() . 'items' . DIRECTORY_SEPARATOR . 'generator.php');
                        eval('class N2GeneratorZoo' . $appId . $identifier . ' extends N2GeneratorZooItems{}');
                    }
                }
            }
        } else {
            $list[self::$group]['items'] = N2GeneratorInfo::getInstance(self::$groupLabel, 'Items', $this->getPath() . 'items')
                                                          ->setInstalled($installed)
                                                          ->setUrl($url);
        }
    }

    function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR;
    }

}

N2Plugin::addPlugin('ssgenerator', 'N2SSPluginGeneratorZoo');
