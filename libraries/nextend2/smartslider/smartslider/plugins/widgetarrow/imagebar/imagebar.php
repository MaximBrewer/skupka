<?php
N2Loader::import('libraries.plugins.N2SliderWidgetAbstract', 'smartslider');

class N2SSPluginWidgetArrowImageBar extends N2SSPluginWidgetAbstract {

    private static $key = 'widget-arrow-';

    var $_name = 'imagebar';

    static function getDefaults() {
        return array(
            'widget-arrow-previous-position-mode'   => 'simple',
            'widget-arrow-previous-position-area'   => 2,
            'widget-arrow-previous-position-offset' => 0,
            'widget-arrow-next-position-mode'       => 'simple',
            'widget-arrow-next-position-area'       => 4,
            'widget-arrow-next-position-offset'     => 0,
            'widget-arrow-width'                    => 100,
            'widget-arrow-previous-color'           => 'ffffffcc',
            'widget-arrow-previous'                 => '$ss$/plugins/widgetarrow/imagebar/imagebar/previous/simple-horizontal.svg',
            'widget-arrow-mirror'                   => 1,
            'widget-arrow-next-color'               => 'ffffffcc',
            'widget-arrow-next'                     => '$ss$/plugins/widgetarrow/imagebar/imagebar/next/simple-horizontal.svg'
        );
    }


    function onArrowList(&$list) {
        $list[$this->_name] = $this->getPath();
    }

    function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . 'imagebar' . DIRECTORY_SEPARATOR;
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

        N2LESS::addFile(N2Filesystem::translate(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'imagebar' . DIRECTORY_SEPARATOR . 'style.n2less'), $slider->cacheId, array(
            "sliderid" => $slider->elementId
        ), NEXTEND_SMARTSLIDER_ASSETS . '/less' . NDS);
        N2JS::addFile(N2Filesystem::translate(dirname(__FILE__) . '/imagebar/arrow.min.js'), $id);
    

        $previous      = $params->get(self::$key . 'previous');
        $previousColor = $params->get(self::$key . 'previous-color');
        if ($params->get(self::$key . 'mirror')) {
            $next      = str_replace('imagebar/previous/', 'imagebar/next/', $previous);
            $nextColor = $previousColor;
        } else {
            $next      = $params->get(self::$key . 'next');
            $nextColor = $params->get(self::$key . 'next-color');
        }

        $html = '';
        $html .= self::getHTML($slider, $id, $params, 'previous', $previous, $previousColor);
        $html .= self::getHTML($slider, $id, $params, 'next', $next, $nextColor);

        $images = array();
        foreach ($slider->slides AS $slide) {
            $images[] = $slide->getThumbnail();
        }

        N2JS::addInline('new N2Classes.SmartSliderWidgetArrowImageBar("' . $id . '", ' . json_encode($images) . ');');

        return $html;
    }

    /**
     * @param N2SmartSlider $slider
     * @param               $id
     * @param               $params
     * @param               $side
     *
     * @return string
     */
    private static function getHTML($slider, $id, &$params, $side, $image, $color) {

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

        switch ($side) {
            case 'previous':
                $backgroundImage = $slider->getPreviousSlide()
                                          ->getThumbnail();
                break;
            case 'next':
                $backgroundImage = $slider->getNextSlide()
                                          ->getThumbnail();
                break;
        }

        $style .= 'width: ' . intval($params->get(self::$key . 'width')) . 'px';

        $label = '';
        switch ($side) {
            case 'previous':
                $label = 'Previous slide';
                break;
            case 'next':
                $label = 'Next slide';
                break;
        }

        return N2Html::tag('div', $displayAttributes + $attributes + array(
                'id'         => $id . '-arrow-' . $side,
                'class'      => $displayClass . 'nextend-arrow nextend-arrow-imagebar n2-ib n2-ow nextend-arrow-' . $side,
                'style'      => $style,
                'role'       => 'button',
                'aria-label' => $label,
                'tabindex'   => '0'
            ), N2Html::tag('div', array(
                'class' => 'nextend-arrow-image n2-ow',
                'style' => 'background-image: url(' . $backgroundImage . ');'
            ), '') . N2Html::tag('div', array(
                'class' => 'nextend-arrow-arrow n2-ow',
                'style' => 'background-image: url(' . $image . ');'
            ), ''));
    }
}

N2Plugin::addPlugin('sswidgetarrow', 'N2SSPluginWidgetArrowImageBar');
