<?php
N2Loader::import('libraries.plugins.N2SliderWidgetAbstract', 'smartslider');
N2Loader::import('libraries.image.color');

class N2SSPluginWidgetBarVertical extends N2SSPluginWidgetAbstract {

    private static $key = 'widget-bar-';

    var $_name = 'vertical';

    static function getDefaults() {
        return array(
            'widget-bar-position-mode'    => 'simple',
            'widget-bar-position-area'    => 6,
            'widget-bar-position-offset'  => 0,
            'widget-bar-style'            => 'eyJuYW1lIjoiU3RhdGljIiwiZGF0YSI6W3siYmFja2dyb3VuZGNvbG9yIjoiMDAwMDAwYWIiLCJwYWRkaW5nIjoiMjB8KnwyMHwqfDIwfCp8MjB8KnxweCIsImJveHNoYWRvdyI6IjB8KnwwfCp8MHwqfDB8KnwwMDAwMDBmZiIsImJvcmRlciI6IjB8Knxzb2xpZHwqfDAwMDAwMGZmIiwiYm9yZGVycmFkaXVzIjoiMCIsImV4dHJhIjoiIn1dfQ==',
            'widget-bar-font-title'       => 'eyJuYW1lIjoiU3RhdGljIiwiZGF0YSI6W3siY29sb3IiOiJmZmZmZmZmZiIsInNpemUiOiIxNnx8cHgiLCJ0c2hhZG93IjoiMHwqfDB8KnwwfCp8MDAwMDAwYzciLCJhZm9udCI6Ik1vbnRzZXJyYXQiLCJsaW5laGVpZ2h0IjoiMS4zIiwiYm9sZCI6MCwiaXRhbGljIjowLCJ1bmRlcmxpbmUiOjAsImFsaWduIjoibGVmdCJ9LHsiY29sb3IiOiJmYzI4MjhmZiIsImFmb250IjoiZ29vZ2xlKEBpbXBvcnQgdXJsKGh0dHA6Ly9mb250cy5nb29nbGVhcGlzLmNvbS9jc3M/ZmFtaWx5PVJhbGV3YXkpOyksQXJpYWwiLCJzaXplIjoiMjV8fHB4In0se31dfQ==',
            'widget-bar-font-description' => 'eyJuYW1lIjoiU3RhdGljIiwiZGF0YSI6W3siY29sb3IiOiJmZmZmZmZmZiIsInNpemUiOiIxMnx8cHgiLCJ0c2hhZG93IjoiMHwqfDB8KnwwfCp8MDAwMDAwYzciLCJhZm9udCI6Ik1vbnRzZXJyYXQiLCJsaW5laGVpZ2h0IjoiMS42IiwiYm9sZCI6MCwiaXRhbGljIjowLCJ1bmRlcmxpbmUiOjAsImFsaWduIjoibGVmdCIsImV4dHJhIjoibWFyZ2luLXRvcDoxMHB4OyJ9LHsiY29sb3IiOiJmYzI4MjhmZiIsImFmb250IjoiZ29vZ2xlKEBpbXBvcnQgdXJsKGh0dHA6Ly9mb250cy5nb29nbGVhcGlzLmNvbS9jc3M/ZmFtaWx5PVJhbGV3YXkpOyksQXJpYWwiLCJzaXplIjoiMjV8fHB4In0se31dfQ==',
            'widget-bar-width'            => '200px',
            'widget-bar-height'           => '100%',
            'widget-bar-overlay'          => 0,
            'widget-bar-animate'          => 0
        );
    }

    function onBarList(&$list) {
        $list[$this->_name] = $this->getPath();
    }

    function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . 'vertical' . DIRECTORY_SEPARATOR;
    }

    static function getPositions(&$params) {
        $positions = array();

        $positions['bar-position'] = array(
            self::$key . 'position-',
            'bar'
        );

        return $positions;
    }

    /**
     * @param $slider N2SmartSliderAbstract
     * @param $id
     * @param $params
     *
     * @return string
     */
    static function render($slider, $id, $params) {

        N2LESS::addFile(N2Filesystem::translate(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'vertical' . DIRECTORY_SEPARATOR . 'style.n2less'), $slider->cacheId, array(
            "sliderid" => $slider->elementId
        ), NEXTEND_SMARTSLIDER_ASSETS . '/less' . NDS);
        N2JS::addFile(N2Filesystem::translate(dirname(__FILE__) . '/vertical/bar.min.js'), $id);
    

        list($displayClass, $displayAttributes) = self::getDisplayAttributes($params, self::$key);

        $styleClass      = N2StyleRenderer::render($params->get(self::$key . 'style'), 'simple', $slider->elementId, 'div#' . $slider->elementId . ' ');
        $fontTitle       = N2FontRenderer::render($params->get(self::$key . 'font-title'), 'simple', $slider->elementId, 'div#' . $slider->elementId . ' ', $slider->fontSize);
        $fontDescription = N2FontRenderer::render($params->get(self::$key . 'font-description'), 'simple', $slider->elementId, 'div#' . $slider->elementId . ' ', $slider->fontSize);

        list($style, $attributes) = self::getPosition($params, self::$key);
        $attributes['data-offset'] = $params->get(self::$key . 'position-offset');

        $attributes2 = array();

        $style .= 'text-align: ' . $params->get(self::$key . 'align', 'left') . ';';
        $style2 = '';

        $width = $params->get(self::$key . 'width');
        if (is_numeric($width) || substr($width, -1) == '%' || substr($width, -2) == 'px') {
            $style .= 'width:' . $width . ';';
        } else {
            $attributes['data-sswidth'] = $width;
        }

        $height = $params->get(self::$key . 'height');
        if (is_numeric($height) || substr($height, -1) == '%' || substr($height, -2) == 'px') {
            $style2 .= 'height:' . $height . ';';
        } else {
            $attributes2['data-ssheight'] = $height;
        }

        $slides = array();
        for ($i = 0; $i < count($slider->slides); $i++) {
            $slides[$i] = array(
                'html'    => N2Html::tag('div', array('class' => $fontTitle . ' n2-ow'), N2Translation::_($slider->slides[$i]->getTitle())),
                'hasLink' => $slider->slides[$i]->hasLink
            );

            $description = $slider->slides[$i]->getDescription();
            if (!empty($description)) {
                $slides[$i]['html'] .= N2Html::tag('div', array('class' => $fontDescription . ' n2-ow'), N2Translation::_($description));
            }
        }

        $parameters = array(
            'overlay' => $params->get(self::$key . 'position-mode') != 'simple' || $params->get(self::$key . 'overlay') || substr($width, -2) != 'px',
            'area'    => intval($params->get(self::$key . 'position-area')),
            'animate' => intval($params->get(self::$key . 'animate'))
        );

        N2JS::addInline('new N2Classes.SmartSliderWidgetBarVertical("' . $id . '", ' . json_encode($slides) . ', ' . json_encode($parameters) . ');');

        return N2Html::tag("div", $displayAttributes + $attributes + $attributes2 + array(
                "class" => $displayClass . "nextend-bar nextend-bar-vertical n2-ow",
                "style" => $style . $style2 . ($slides[$slider->_activeSlide]['hasLink'] ? 'cursor:pointer;' : '')
            ), N2Html::tag("div", $attributes2 + array(
                "class" => $styleClass . ' n2-ow',
                "style" => $style2
            ), N2Html::tag("div", array('class' => 'n2-ow'), $slides[$slider->_activeSlide]['html'])));
    }

    public static function prepareExport($export, $params) {
        $export->addVisual($params->get(self::$key . 'style'));
        $export->addVisual($params->get(self::$key . 'font-title'));
        $export->addVisual($params->get(self::$key . 'font-description'));
    }

    public static function prepareImport($import, $params) {

        $params->set(self::$key . 'style', $import->fixSection($params->get(self::$key . 'style', '')));
        $params->set(self::$key . 'font-title', $import->fixSection($params->get(self::$key . 'font-title', '')));
        $params->set(self::$key . 'font-description', $import->fixSection($params->get(self::$key . 'font-description', '')));
    }

}

N2Plugin::addPlugin('sswidgetbar', 'N2SSPluginWidgetBarVertical');
