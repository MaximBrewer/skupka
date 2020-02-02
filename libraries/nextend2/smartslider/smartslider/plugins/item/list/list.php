<?php
N2Loader::import('libraries.slider.slides.slide.item.itemFactoryAbstract', 'smartslider');

class N2SSPluginItemFactoryList extends N2SSPluginItemFactoryAbstract {

    var $type = 'list';

    protected $priority = 6;

    protected $layerProperties = array(
        "desktopportraitleft"   => 0,
        "desktopportraittop"    => 0,
        "desktopportraitwidth"  => 400,
        "desktopportraitalign"  => "left",
        "desktopportraitvalign" => "top"
    );

    protected $group = 'Content';

    private static $font = 1304;

    protected $class = 'N2SSItemList';

    public function __construct() {
        $this->_title = n2_x('List', 'Slide item');
    }

    private static function initDefaultFont() {
        static $inited = false;
        if (!$inited) {
            $res = N2StorageSectionAdmin::get('smartslider', 'default', 'item-list-font');
            if (is_array($res)) {
                self::$font = $res['value'];
            }
            if (is_numeric(self::$font)) {
                N2FontRenderer::preLoad(self::$font);
            }
            $inited = true;
        }
    }

    private static $listStyle = 1801;
    private static $itemStyle = '';

    private static function initDefaultStyle() {
        static $inited = false;
        if (!$inited) {
            $res = N2StorageSectionAdmin::get('smartslider', 'default', 'item-list-liststyle');
            if (is_array($res)) {
                self::$listStyle = $res['value'];
            }
            if (is_numeric(self::$listStyle)) {
                N2StyleRenderer::preLoad(self::$listStyle);
            }
            $res = N2StorageSectionAdmin::get('smartslider', 'default', 'item-list-itemstyle');
            if (is_array($res)) {
                self::$itemStyle = $res['value'];
            }
            if (is_numeric(self::$itemStyle)) {
                N2StyleRenderer::preLoad(self::$itemStyle);
            }
            $inited = true;
        }
    }

    public static function onSmartsliderDefaultSettings(&$settings) {
        self::initDefaultFont();
        $settings['font'][] = '<param name="item-list-font" type="font" previewmode="list" label="' . n2_('Item') . ' - ' . n2_('List') . '" default="' . self::$font . '" />';

        self::initDefaultStyle();
        $settings['style'][] = '<param name="item-list-liststyle" type="style" previewmode="heading" label="' . n2_('Item') . ' - ' . n2_('List') . '" default="' . self::$listStyle . '" />';
        $settings['style'][] = '<param name="item-list-itemstyle" type="style" previewmode="heading" label="' . n2_('Item') . ' - ' . n2_('List') . ' - ' . n2_('Item') . '" default="' . self::$itemStyle . '" />';
    }

    function getValues() {
        self::initDefaultFont();
        self::initDefaultStyle();

        return array(
            'content'   => n2_("Item 1\nItem 2\nItem 3"),
            'font'      => self::$font,
            'liststyle' => self::$listStyle,
            'itemstyle' => self::$itemStyle,
            'type'      => 'disc'
        );
    }

    function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . $this->type . DIRECTORY_SEPARATOR;
    }

    public function getFilled($slide, $data) {
        $data->set('content', $slide->fill($data->get('content', '')));

        return $data;
    }

    public function prepareExport($export, $data) {
        $export->addVisual($data->get('font'));
        $export->addVisual($data->get('liststyle'));
        $export->addVisual($data->get('itemstyle'));
    }

    public function prepareImport($import, $data) {
        $data->set('font', $import->fixSection($data->get('font')));
        $data->set('liststyle', $import->fixSection($data->get('liststyle')));
        $data->set('itemstyle', $import->fixSection($data->get('itemstyle')));

        return $data;
    }
}

N2Plugin::addPlugin('ssitem', 'N2SSPluginItemFactoryList');

N2Pluggable::addAction('smartsliderDefault', 'N2SSPluginItemFactoryList::onSmartsliderDefaultSettings');
