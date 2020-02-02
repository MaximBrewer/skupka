<?php
N2Loader::import('libraries.plugins.N2SliderWidgetAbstract', 'smartslider');

class N2SSPluginWidgetArrowGrow extends N2SSPluginWidgetAbstract {

    private static $key = 'widget-arrow-';

    var $_name = 'grow';

    static function getDefaults() {
        return array(
            'widget-arrow-previous-position-mode'   => 'simple',
            'widget-arrow-previous-position-area'   => 6,
            'widget-arrow-previous-position-offset' => 15,
            'widget-arrow-next-position-mode'       => 'simple',
            'widget-arrow-next-position-area'       => 7,
            'widget-arrow-next-position-offset'     => 15,
            'widget-arrow-style'                    => 'eyJuYW1lIjoiU3RhdGljIiwiZGF0YSI6W3siYmFja2dyb3VuZGNvbG9yIjoiMDAwMDAwODAiLCJwYWRkaW5nIjoiM3wqfDN8KnwzfCp8M3wqfHB4IiwiYm94c2hhZG93IjoiMHwqfDB8KnwwfCp8MHwqfDAwMDAwMGZmIiwiYm9yZGVyIjoiMHwqfHNvbGlkfCp8MDAwMDAwZmYiLCJib3JkZXJyYWRpdXMiOiI1MCIsImV4dHJhIjoiIn0seyJiYWNrZ3JvdW5kY29sb3IiOiIwMWFkZDNmZiJ9XX0=',
            'widget-arrow-font'                     => 'eyJuYW1lIjoiU3RhdGljIiwiZGF0YSI6W3siY29sb3IiOiJmZmZmZmZmZiIsInNpemUiOiIxMnx8cHgiLCJ0c2hhZG93IjoiMHwqfDB8KnwwfCp8MDAwMDAwZmYiLCJhZm9udCI6Ik1vbnRzZXJyYXQiLCJsaW5laGVpZ2h0IjoiMS4zIiwiYm9sZCI6MCwiaXRhbGljIjowLCJ1bmRlcmxpbmUiOjAsImFsaWduIjoibGVmdCIsImV4dHJhIjoiIn0se31dfQ==',
            'widget-arrow-animation-delay'          => 0,
            'widget-arrow-previous-color'           => 'ffffffcc',
            'widget-arrow-previous'                 => '$ss$/plugins/widgetarrow/grow/grow/previous/simple-horizontal.svg',
            'widget-arrow-mirror'                   => 1,
            'widget-arrow-next-color'               => 'ffffffcc',
            'widget-arrow-next'                     => '$ss$/plugins/widgetarrow/grow/grow/next/simple-horizontal.svg'
        );
    }


    function onArrowList(&$list) {
        $list[$this->_name] = $this->getPath();
    }

    function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . 'grow' . DIRECTORY_SEPARATOR;
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

        N2LESS::addFile(N2Filesystem::translate(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'grow' . DIRECTORY_SEPARATOR . 'style.n2less'), $slider->cacheId, array(
            "sliderid" => $slider->elementId
        ), NEXTEND_SMARTSLIDER_ASSETS . '/less' . NDS);
        N2JS::addFile(N2Filesystem::translate(dirname(__FILE__) . '/grow/arrow.min.js'), $id);
    

        $previous      = $params->get(self::$key . 'previous');
        $previousColor = $params->get(self::$key . 'previous-color');
        if ($params->get(self::$key . 'mirror')) {
            $next      = str_replace('grow/previous/', 'grow/next/', $previous);
            $nextColor = $previousColor;
        } else {
            $next      = $params->get(self::$key . 'next');
            $nextColor = $params->get(self::$key . 'next-color');
        }

        $fontClass  = N2FontRenderer::render($params->get(self::$key . 'font'), 'hover', $slider->elementId, 'div#' . $slider->elementId . ' ', $slider->fontSize);
        $styleClass = N2StyleRenderer::render($params->get(self::$key . 'style'), 'heading', $slider->elementId, 'div#' . $slider->elementId . ' ');

        $html = '';
        $html .= self::getHTML($slider, $id, $params, 'previous', $previous, $fontClass, $styleClass, $previousColor);
        $html .= self::getHTML($slider, $id, $params, 'next', $next, $fontClass, $styleClass, $nextColor);

        $titles = array();
        foreach ($slider->slides AS $slide) {
            $titles[] = N2Translation::_($slide->getTitle());
        }

        N2JS::addInline('new N2Classes.SmartSliderWidgetArrowGrow("' . $id . '", ' . json_encode($titles) . ', ' . $params->get(self::$key . 'animation-delay') . ');');

        return $html;
    }

    private static function getHTML($slider, $id, &$params, $side, $image, $fontClass, $styleClass, $color) {

        list($displayClass, $displayAttributes) = self::getDisplayAttributes($params, self::$key);

        list($style, $attributes) = self::getPosition($params, self::$key . $side . '-');

        $ext = pathinfo($image, PATHINFO_EXTENSION);
        if (substr($image, 0, 1) == '$' && $ext == 'svg') {

            list($color, $opacity) = N2Color::colorToSVG($color);
            $image = 'data:image/svg+xml;base64,' . base64_encode(str_replace(array(
                    'fill="#FFF"',
                    'opacity="1"'
                ), array(
                    'fill="#' . $color . '"',
                    'opacity="' . $opacity . '"'
                ), N2Filesystem::readFile(N2ImageHelper::fixed($image, true))));
        } else {
            $image = N2ImageHelper::fixed($image);
        }

        $label = '';
        switch ($side) {
            case 'previous':
                $title = $slider->getPreviousSlide()
                                ->getTitle();
                $label = 'Previous slide';
                break;
            case 'next':
                $title = $slider->getNextSlide()
                                ->getTitle();
                $label = 'Next slide';
                break;
        }

        return N2Html::tag('div', $displayAttributes + $attributes + array(
                'id'         => $id . '-arrow-' . $side,
                'class'      => $displayClass . $styleClass . 'nextend-arrow n2-ib n2-ow nextend-arrow-grow nextend-arrow-' . $side,
                'style'      => $style,
                'role'       => 'button',
                'aria-label' => $label,
                'tabindex'   => '0'
            ), N2Html::tag('div', array(
                'class' => $fontClass . ' nextend-arrow-title n2-ow'
            ), $title) . N2Html::tag('div', array(
                'class' => 'nextend-arrow-arrow n2-ow',
                'style' => 'background-image: url(' . $image . ');'
            ), ''));
    }

    public static function prepareExport($export, $params) {
        $export->addVisual($params->get(self::$key . 'style'));
        $export->addVisual($params->get(self::$key . 'font'));
    }

    public static function prepareImport($import, $params) {

        $params->set(self::$key . 'style', $import->fixSection($params->get(self::$key . 'style', '')));
        $params->set(self::$key . 'font', $import->fixSection($params->get(self::$key . 'font', '')));
    }
}

N2Plugin::addPlugin('sswidgetarrow', 'N2SSPluginWidgetArrowGrow');
