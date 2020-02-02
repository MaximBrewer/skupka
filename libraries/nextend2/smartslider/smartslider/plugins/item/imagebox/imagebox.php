<?php
N2Loader::import('libraries.slider.slides.slide.item.itemFactoryAbstract', 'smartslider');
N2Loader::import('libraries.image.color');

class N2SSPluginItemFactoryImageBox extends N2SSPluginItemFactoryAbstract {

    var $type = 'imagebox';

    protected $priority = 5;

    protected $layerProperties = array();

    protected $group = 'Basic';

    protected $class = 'N2SSItemImageBox';

    private static $fontTitle = '{"name":"Static","data":[{"extra":"","color":"ffffffff","size":"32||px","tshadow":"0|*|0|*|0|*|000000ff","lineheight":"1.5","bold":0,"italic":0,"underline":0,"align":"inherit","letterspacing":"normal","wordspacing":"normal","texttransform":"none"}]}';

    private static $fontDescription = '{"name":"Static","data":[{"extra":"","color":"ffffffff","size":"16||px","tshadow":"0|*|0|*|0|*|000000ff","lineheight":"2","bold":0,"italic":0,"underline":0,"align":"inherit","letterspacing":"normal","wordspacing":"normal","texttransform":"none"}]}';

    private static $style = '';


    public function __construct() {
        $this->_title = n2_x('Image box', 'Slide item');
    }

    public function loadResources($slider) {
        parent::loadResources($slider);

        N2LESS::addFile($this->getPath() . "/imagebox.n2less", $slider->cacheId, array(
            "sliderid" => $slider->elementId
        ), NEXTEND_SMARTSLIDER_ASSETS . '/less' . NDS);
    }

    function getValues() {
        self::initDefault();

        return array(
            'layout'          => 'top',
            'spacing'         => 10,
            'inneralign'      => 'center',
            'verticalalign'   => 'flex-start',
            'image'           => '$system$/images/placeholder/image.png',
            'imagewidth'      => 100,
            'imagealt'        => n2_('Image is not available'),
            'icon'            => '',
            'iconsize'        => 64,
            'iconcolor'       => 'ffffffff',
            'heading'         => n2_('Heading layer'),
            'headingpriority' => 'div',
            'fonttitle'       => self::$fontTitle,
            'description'     => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
            'fontdescription' => self::$fontDescription,
            'style'           => self::$style,
            'link'            => '#|*|_self'
        );
    }

    function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . $this->type . DIRECTORY_SEPARATOR;
    }

    public function getFilled($slide, $data) {
        $data->set('heading', $slide->fill($data->get('heading', '')));
        $data->set('description', $slide->fill($data->get('description', '')));
        $data->set('link', $slide->fill($data->get('link', '#|*|')));
        $data->set('image', $slide->fill($data->get('image', '')));
        $data->set('imagealt', $slide->fill($data->get('imagealt', '')));

        return $data;
    }

    public function prepareExport($export, $data) {
        $export->addImage($data->get('image'));
        $export->addVisual($data->get('fonttitle'));
        $export->addVisual($data->get('fontdescription'));
        $export->addVisual($data->get('style'));
        $export->addLightbox($data->get('link'));
    }

    public function prepareImport($import, $data) {
        $data->set('image', $import->fixImage($data->get('image')));
        $data->set('fonttitle', $import->fixSection($data->get('fonttitle')));
        $data->set('fontdescription', $import->fixSection($data->get('fontdescription')));
        $data->set('style', $import->fixSection($data->get('style')));
        $data->set('link', $import->fixLightbox($data->get('link')));

        return $data;
    }

    private static function initDefault() {
        static $inited = false;
        if (!$inited) {
            $res = N2StorageSectionAdmin::get('smartslider', 'default', 'item-imagebox-fonttitle');
            if (is_array($res)) {
                self::$fontTitle = $res['value'];
            }
            if (is_numeric(self::$fontTitle)) {
                N2FontRenderer::preLoad(self::$fontTitle);
            }

            $res = N2StorageSectionAdmin::get('smartslider', 'default', 'item-imagebox-fontdescription');
            if (is_array($res)) {
                self::$fontDescription = $res['value'];
            }
            if (is_numeric(self::$fontDescription)) {
                N2FontRenderer::preLoad(self::$fontDescription);
            }

            $res = N2StorageSectionAdmin::get('smartslider', 'default', 'item-heading-style');
            if (is_array($res)) {
                self::$style = $res['value'];
            }
            if (is_numeric(self::$style)) {
                N2StyleRenderer::preLoad(self::$style);
            }
            $inited = true;
        }
    }

    public static function onSmartsliderDefaultSettings(&$settings) {
        self::initDefault();

        $settings['font'][] = '<param name="item-imagebox-fonttitle" type="font" previewmode="hover" label="' . n2_('Item') . ' - ' . n2_('Image box') . ' ' . n2_('Title') . '" default="' . htmlspecialchars(self::$fontTitle, ENT_QUOTES) . '" />';

        $settings['font'][] = '<param name="item-imagebox-fontdescription" type="font" previewmode="paragraph" label="' . n2_('Item') . ' - ' . n2_('Image box') . ' ' . n2_('Description') . '" default="' . htmlspecialchars(self::$fontDescription, ENT_QUOTES) . '" />';

        $settings['style'][] = '<param name="item-heading-style" type="style" set="heading" previewmode="heading" label="' . n2_('Item') . ' - ' . n2_('Image box') . '" default="' . htmlspecialchars(self::$style, ENT_QUOTES) . '" />';
    }

}

N2Plugin::addPlugin('ssitem', 'N2SSPluginItemFactoryImageBox');

N2Pluggable::addAction('smartsliderDefault', 'N2SSPluginItemFactoryImageBox::onSmartsliderDefaultSettings');

