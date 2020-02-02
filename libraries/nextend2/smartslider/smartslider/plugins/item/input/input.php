<?php
N2Loader::import('libraries.slider.slides.slide.item.itemFactoryAbstract', 'smartslider');

class N2SSPluginItemFactoryInput extends N2SSPluginItemFactoryAbstract {

    var $type = 'input';

    protected $priority = 100;

    private static $inputFont = 'eyJuYW1lIjoiU3RhdGljIiwiZGF0YSI6W3siY29sb3IiOiIwMDAwMDBmZiIsInNpemUiOiIxNXx8cHgiLCJ0c2hhZG93IjoiMHwqfDB8KnwwfCp8MDAwMDAwZmYiLCJhZm9udCI6Ik1vbnRzZXJyYXQsQXJpYWwiLCJsaW5laGVpZ2h0IjoiNDRweCIsImJvbGQiOjAsIml0YWxpYyI6MCwidW5kZXJsaW5lIjowLCJhbGlnbiI6ImxlZnQiLCJsZXR0ZXJzcGFjaW5nIjoibm9ybWFsIiwid29yZHNwYWNpbmciOiJub3JtYWwiLCJ0ZXh0dHJhbnNmb3JtIjoibm9uZSIsImV4dHJhIjoiaGVpZ2h0OjQ0cHg7In0se30se31dfQ==';

    private static $buttonFont = 'eyJuYW1lIjoiU3RhdGljIiwiZGF0YSI6W3siY29sb3IiOiJmZmZmZmZmZiIsInNpemUiOiIxNHx8cHgiLCJ0c2hhZG93IjoiMHwqfDB8KnwwfCp8MDAwMDAwZmYiLCJhZm9udCI6Ik1vbnRzZXJyYXQsQXJpYWwiLCJsaW5laGVpZ2h0IjoiNDRweCIsImJvbGQiOjAsIml0YWxpYyI6MCwidW5kZXJsaW5lIjowLCJhbGlnbiI6ImxlZnQiLCJsZXR0ZXJzcGFjaW5nIjoibm9ybWFsIiwid29yZHNwYWNpbmciOiJub3JtYWwiLCJ0ZXh0dHJhbnNmb3JtIjoibm9uZSIsImV4dHJhIjoiIn0se30se31dfQ==';

    protected $group = 'Advanced';

    protected $class = 'N2SSItemInput';

    public function __construct() {
        $this->_title = n2_x('Input', 'Slide item');
    }

    private static function initDefaultFont() {
        static $inited = false;
        if (!$inited) {
            $res = N2StorageSectionAdmin::get('smartslider', 'default', 'item-input-font');
            if (is_array($res)) {
                self::$inputFont = $res['value'];
            }
            if (is_numeric(self::$inputFont)) {
                N2FontRenderer::preLoad(self::$inputFont);
            }
            $res = N2StorageSectionAdmin::get('smartslider', 'default', 'item-input-button-font');
            if (is_array($res)) {
                self::$buttonFont = $res['value'];
            }
            if (is_numeric(self::$buttonFont)) {
                N2FontRenderer::preLoad(self::$buttonFont);
            }
            $inited = true;
        }
    }

    private static $style = '';
    private static $inputStyle = 'eyJuYW1lIjoiU3RhdGljIiwiZGF0YSI6W3siYmFja2dyb3VuZGNvbG9yIjoiZmZmZmZmZmYiLCJwYWRkaW5nIjoiMHwqfDIwfCp8MHwqfDIwfCp8cHgiLCJib3hzaGFkb3ciOiIwfCp8MHwqfDB8KnwwfCp8MDAwMDAwZmYiLCJib3JkZXIiOiIwfCp8c29saWR8KnwwMDAwMDBmZiIsImJvcmRlcnJhZGl1cyI6IjAiLCJleHRyYSI6IiJ9LHt9XX0=';
    private static $buttonStyle = 'eyJuYW1lIjoiU3RhdGljIiwiZGF0YSI6W3siYmFja2dyb3VuZGNvbG9yIjoiMDRiYzhmZmYiLCJwYWRkaW5nIjoiMHwqfDM1fCp8MHwqfDM1fCp8cHgiLCJib3hzaGFkb3ciOiIwfCp8MHwqfDB8KnwwfCp8MDAwMDAwZmYiLCJib3JkZXIiOiIwfCp8c29saWR8KnwwMDAwMDBmZiIsImJvcmRlcnJhZGl1cyI6IjAiLCJleHRyYSI6IiJ9LHt9XX0=';

    private static function initDefaultStyle() {
        static $inited = false;
        if (!$inited) {
            $res = N2StorageSectionAdmin::get('smartslider', 'default', 'item-input-container-style');
            if (is_array($res)) {
                self::$style = $res['value'];
            }
            if (is_numeric(self::$style)) {
                N2StyleRenderer::preLoad(self::$style);
            }
            $res = N2StorageSectionAdmin::get('smartslider', 'default', 'item-input-style');
            if (is_array($res)) {
                self::$inputStyle = $res['value'];
            }
            if (is_numeric(self::$inputStyle)) {
                N2StyleRenderer::preLoad(self::$inputStyle);
            }
            $res = N2StorageSectionAdmin::get('smartslider', 'default', 'item-input-button-style');
            if (is_array($res)) {
                self::$buttonStyle = $res['value'];
            }
            if (is_numeric(self::$buttonStyle)) {
                N2StyleRenderer::preLoad(self::$buttonStyle);
            }
            $inited = true;
        }
    }

    public static function onSmartsliderDefaultSettings(&$settings) {
        self::initDefaultFont();
        $settings['font'][] = '<param name="item-input-font" type="font" previewmode="paragraph" label="' . n2_('Item') . ' - ' . n2_('Input') . '" default="' . self::$inputFont . '" />';
        $settings['font'][] = '<param name="item-input-button-font" type="font" previewmode="hover" label="' . n2_('Item') . ' - ' . n2_('Input button') . '" default="' . self::$buttonFont . '" />';

        self::initDefaultStyle();
        $settings['style'][] = '<param name="item-input-container-style" type="style" set="heading" previewmode="heading" label="' . n2_('Item') . ' - ' . n2_('Input container') . '" default="' . self::$style . '" />';
        $settings['style'][] = '<param name="item-input-style" type="style" set="heading" previewmode="heading" label="' . n2_('Item') . ' - ' . n2_('Input') . '" default="' . self::$inputStyle . '" />';
        $settings['style'][] = '<param name="item-input-button-style" type="style" set="heading" previewmode="button" label="' . n2_('Item') . ' - ' . n2_('Input button') . '" default="' . self::$buttonStyle . '" />';
    }


    function getValues() {
        self::initDefaultFont();
        self::initDefaultStyle();

        return array(
            'placeholder' => 'What are you looking for?',
            'action'      => 'https://www.google.com/search',
            'method'      => 'GET',
            'target'      => '_self',
            'parameters'  => 'ie=utf-8&oe=utf-8',
            'name'        => 'q',
            'inputfont'   => self::$inputFont,
            'buttonfont'  => self::$buttonFont,
            'style'       => self::$style,
            'inputstyle'  => self::$inputStyle,
            'buttonstyle' => self::$buttonStyle,
            'buttonlabel' => 'Search',
            'submit'      => '',
            'fullwidth'   => 0,
            'class'       => '',
            'onsubmit'    => '',
            'onkeyup'     => ''
        );
    }

    function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . $this->type . DIRECTORY_SEPARATOR;
    }

    public function getFilled($slide, $data) {
        return $data;
    }

    public function prepareExport($export, $data) {
        $export->addVisual($data->get('font'));
        $export->addVisual($data->get('style'));
    }

    public function prepareImport($import, $data) {
        $data->set('font', $import->fixSection($data->get('font')));
        $data->set('style', $import->fixSection($data->get('style')));

        return $data;
    }

}

N2Plugin::addPlugin('ssitem', 'N2SSPluginItemFactoryInput');

N2Pluggable::addAction('smartsliderDefault', 'N2SSPluginItemFactoryInput::onSmartsliderDefaultSettings');

