<?php
N2Loader::import('libraries.slider.slides.slide.item.itemFactoryAbstract', 'smartslider');

class N2SSPluginItemFactoryProgressBar extends N2SSPluginItemFactoryAbstract {

    var $type = 'progressbar';

    protected $priority = 10;

    protected $layerProperties = array(
        "desktopportraitwidth" => 300
    );

    protected $group = 'Content';

    protected $class = 'N2SSItemProgressBar';

    private static $font = '{"name":"Static","data":[{"extra":"","color":"ffffffff","size":"14||px","tshadow":"0|*|0|*|0|*|000000ff","lineheight":"1.5","bold":0,"italic":0,"underline":0,"align":"center","letterspacing":"normal","wordspacing":"normal","texttransform":"none"}]}';

    private static $fontLabel = '{"name":"Static","data":[{"extra":"","color":"ffffffff","size":"14||px","tshadow":"0|*|0|*|0|*|000000ff","lineheight":"1.5","bold":0,"italic":0,"underline":0,"align":"left","letterspacing":"normal","wordspacing":"normal","texttransform":"none"}]}';


    public function __construct() {
        $this->_title = n2_x('Progress Bar', 'Slide item');
    }

    public function loadResources($slider) {
        parent::loadResources($slider);

        N2LESS::addFile($this->getPath() . "/progressbar.n2less", $slider->cacheId, array(
            "sliderid" => $slider->elementId
        ), NEXTEND_SMARTSLIDER_ASSETS . '/less' . NDS);
    }

    function getValues() {
        self::initDefault();

        return array(
            'value'             => 50,
            'startvalue'        => 0,
            'total'             => 100,
            'color'             => '00000080',
            'color2'            => '64c133ff',
            'pre'               => '',
            'post'              => '%',
            'label'             => 'Progress',
            'font'              => self::$font,
            'fontlabel'         => self::$fontLabel,
            'labelplacement'    => 'before',
            'animationduration' => 1000,
            'animationdelay'    => 0
        );
    }

    function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . $this->type . DIRECTORY_SEPARATOR;
    }

    public function getFilled($slide, $data) {
        $data->set('label', $slide->fill($data->get('label', '')));

        return $data;
    }

    public function prepareExport($export, $data) {
        $export->addVisual($data->get('font'));
        $export->addVisual($data->get('fontlabel'));
    }

    public function prepareImport($import, $data) {
        $data->set('font', $import->fixSection($data->get('font')));
        $data->set('fontlabel', $import->fixSection($data->get('fontlabel')));

        return $data;
    }

    private static function initDefault() {
        static $inited = false;
        if (!$inited) {
            $res = N2StorageSectionAdmin::get('smartslider', 'default', 'item-progressbar-font');
            if (is_array($res)) {
                self::$font = $res['value'];
            }
            if (is_numeric(self::$font)) {
                N2FontRenderer::preLoad(self::$font);
            }

            $res = N2StorageSectionAdmin::get('smartslider', 'default', 'item-progressbar-fontlabel');
            if (is_array($res)) {
                self::$fontLabel = $res['value'];
            }
            if (is_numeric(self::$fontLabel)) {
                N2FontRenderer::preLoad(self::$fontLabel);
            }
            $inited = true;
        }
    }

    public static function onSmartsliderDefaultSettings(&$settings) {
        self::initDefault();
        $settings['font'][] = '<param name="item-progressbar-font" type="font" previewmode="simple" label="' . n2_('Item') . ' - ' . n2_('Progress bar') . '" default="' . htmlspecialchars(self::$font, ENT_QUOTES) . '" />';
        $settings['font'][] = '<param name="item-progressbar-fontlabel" type="font" previewmode="simple" label="' . n2_('Item') . ' - ' . n2_('Progress bar label') . '" default="' . htmlspecialchars(self::$fontLabel, ENT_QUOTES) . '" />';
    }

}

N2Plugin::addPlugin('ssitem', 'N2SSPluginItemFactoryProgressBar');

N2Pluggable::addAction('smartsliderDefault', 'N2SSPluginItemFactoryProgressBar::onSmartsliderDefaultSettings');

