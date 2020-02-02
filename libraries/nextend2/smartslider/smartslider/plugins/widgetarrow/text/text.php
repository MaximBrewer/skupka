<?php
N2Loader::import('libraries.plugins.N2SliderWidgetAbstract', 'smartslider');
N2Loader::import('libraries.image.color');

class N2SSPluginWidgetArrowText extends N2SSPluginWidgetAbstract {

    private static $key = 'widget-arrow-';

    var $_name = 'text';

    static function getDefaults() {
        return array(
            'widget-arrow-style'                    => 'eyJuYW1lIjoiU3RhdGljIiwiZGF0YSI6W3siYmFja2dyb3VuZGNvbG9yIjoiMDAwMDAwYWIiLCJwYWRkaW5nIjoiOHwqfDEwfCp8OHwqfDEwfCp8cHgiLCJib3hzaGFkb3ciOiIwfCp8MHwqfDB8KnwwfCp8MDAwMDAwZmYiLCJib3JkZXIiOiIwfCp8c29saWR8KnwwMDAwMDBmZiIsImJvcmRlcnJhZGl1cyI6IjMiLCJleHRyYSI6IiJ9LHsiYmFja2dyb3VuZGNvbG9yIjoiNWNiYTNjZmYifV19',
            'widget-arrow-font'                     => 'eyJuYW1lIjoiU3RhdGljIiwiZGF0YSI6W3siY29sb3IiOiJmZmZmZmZmZiIsInNpemUiOiIxMnx8cHgiLCJ0c2hhZG93IjoiMHwqfDB8KnwwfCp8MDAwMDAwZmYiLCJhZm9udCI6Ik1vbnRzZXJyYXQiLCJsaW5laGVpZ2h0IjoiMS4zIiwiYm9sZCI6MCwiaXRhbGljIjowLCJ1bmRlcmxpbmUiOjAsImFsaWduIjoibGVmdCIsImV4dHJhIjoiIn0se31dfQ==',
            'widget-arrow-previous-position-mode'   => 'simple',
            'widget-arrow-previous-position-area'   => 6,
            'widget-arrow-previous-position-offset' => 15,
            'widget-arrow-next-position-mode'       => 'simple',
            'widget-arrow-next-position-area'       => 7,
            'widget-arrow-next-position-offset'     => 15,
            'widget-arrow-previous-label'           => n2_('PREV'),
            'widget-arrow-next-label'               => n2_('NEXT')
        );
    }

    function onArrowList(&$list) {
        $list[$this->_name] = $this->getPath();
    }

    function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . 'text' . DIRECTORY_SEPARATOR;
    }

    static function getPositions(&$params) {
        $positions = array();

        $positions['previous-position'] = array(
            self::$key . 'previous-position-',
            'previous'
        );

        $positions['next-position'] = array(
            self::$key . 'next-position-',
            'next'
        );

        return $positions;
    }

    static function render($slider, $id, $params) {

        list($displayClass, $displayAttributes) = self::getDisplayAttributes($params, self::$key);

        N2LESS::addFile(N2Filesystem::translate(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'text' . DIRECTORY_SEPARATOR . 'style.n2less'), $slider->cacheId, array(
            "sliderid" => $slider->elementId
        ), NEXTEND_SMARTSLIDER_ASSETS . '/less' . NDS);

        $font  = N2FontRenderer::render($params->get(self::$key . 'font'), 'link', $slider->elementId, 'div#' . $slider->elementId . ' ', $slider->fontSize);
        $style = N2StyleRenderer::render($params->get(self::$key . 'style'), 'heading', $slider->elementId, 'div#' . $slider->elementId . ' ');

        return self::getHtml($params, 'previous', $displayClass, $displayAttributes, $font, $style, "window['" . $id . "'][nextend.rtl.previous]();return false;") . self::getHtml($params, 'next', $displayClass, $displayAttributes, $font, $style, "window['" . $id . "'][nextend.rtl.next]();return false;");
    }

    private static function getHtml(&$params, $side, $displayClass, $displayAttributes, $font, $styleClass, $onClick) {

        list($style, $attributes) = self::getPosition($params, self::$key . $side . '-');

        $label = '';
        switch ($side) {
            case 'previous':
                $label = 'Previous slide';
                break;
            case 'next':
                $label = 'Next slide';
                break;
        }

        $html = N2Html::openTag("div", $displayAttributes + $attributes + array(
                "class"      => $displayClass . " n2-ib n2-ow nextend-arrow nextend-arrow-{$side} {$font}",
                "style"      => $style,
                'role'       => 'button',
                'aria-label' => $label
            ));


        $html .= N2Html::link($params->get(self::$key . $side . '-label'), '#', array(
            "onclick" => $onClick,
            "class"   => $styleClass . ' n2-ib n2-ow'
        ));

        $html .= N2Html::closeTag("div");

        return $html;
    }

    public static function prepareExport($export, $params) {
        $export->addVisual($params->get(self::$key . 'font'));
        $export->addVisual($params->get(self::$key . 'style'));
    }

    public static function prepareImport($import, $params) {

        $params->set(self::$key . 'font', $import->fixSection($params->get(self::$key . 'font', '')));
        $params->set(self::$key . 'style', $import->fixSection($params->get(self::$key . 'style', '')));
    }

}

N2Plugin::addPlugin('sswidgetarrow', 'N2SSPluginWidgetArrowText');
