<?php
N2Loader::import('libraries.plugins.N2SliderGeneratorPluginAbstract', 'smartslider');

class N2SSPluginGeneratorCobalt extends N2PluginBase
{

    public static $group = 'cobalt';
    public static $groupLabel = 'Cobalt';

    function onGeneratorList(&$group, &$list) {
        $installed = N2Filesystem::existsFolder(JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_cobalt');
        $url       = 'http://extensions.joomla.org/extension/cobalt';

        $group[self::$group] = self::$groupLabel;

        if (!isset($list[self::$group])) {
            $list[self::$group] = array();
        }

        if ($installed) {

            $db = new N2Model("js_res_sections");

            $query = "SELECT id, name FROM #__js_res_sections ORDER BY ordering ASC";

            $sections = $db->db->queryAll($query, array("id"), "assoc", "id");

            require_once($this->getPath() . 'records' . DIRECTORY_SEPARATOR . 'generator.php');

            foreach ($sections AS $section) {
                $list[self::$group]['section' . $section['id']] = N2GeneratorInfo::getInstance(self::$groupLabel, $section['name'], $this->getPath() . 'records')
                                                                                 ->setInstalled($installed)
                                                                                 ->setUrl($url)
                                                                                 ->setType('article')
                                                                                 ->setData('section_id', $section['id']);

                if (!class_exists('N2GeneratorCobaltSection' . $section['id'])) {
                    eval('class N2GeneratorCobaltSection' . $section['id'] . ' extends N2GeneratorCobaltRecords{}');
                }
            }
        } else {
            $list[self::$group]['section'] = N2GeneratorInfo::getInstance(self::$groupLabel, 'Records', $this->getPath() . 'records')
                                                            ->setInstalled($installed)
                                                            ->setUrl($url);
        }
    }

    function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR;
    }
}

N2Plugin::addPlugin('ssgenerator', 'N2SSPluginGeneratorCobalt');

