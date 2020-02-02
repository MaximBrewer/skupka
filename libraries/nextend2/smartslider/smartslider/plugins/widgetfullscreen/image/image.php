<?php

N2Loader::import('libraries.plugins.N2SliderWidgetAbstract', 'smartslider');

class N2SSPluginWidgetFullScreenImage extends N2SSPluginWidgetAbstract {

    private static $key = 'widget-fullscreen-';

    var $_name = 'image';

    static function getDefaults() {
        return array(
            'widget-fullscreen-responsive-desktop' => 1,
            'widget-fullscreen-responsive-tablet'  => 0.7,
            'widget-fullscreen-responsive-mobile'  => 0.5,
            'widget-fullscreen-tonormal-image'     => '',
            'widget-fullscreen-tonormal-color'     => 'ffffffcc',
            'widget-fullscreen-tonormal'           => '$ss$/plugins/widgetfullscreen/image/image/tonormal/full1.svg',
            'widget-fullscreen-style'              => 'eyJuYW1lIjoiU3RhdGljIiwiZGF0YSI6W3siYmFja2dyb3VuZGNvbG9yIjoiMDAwMDAwYWIiLCJwYWRkaW5nIjoiMTB8KnwxMHwqfDEwfCp8MTB8KnxweCIsImJveHNoYWRvdyI6IjB8KnwwfCp8MHwqfDB8KnwwMDAwMDBmZiIsImJvcmRlciI6IjB8Knxzb2xpZHwqfDAwMDAwMGZmIiwiYm9yZGVycmFkaXVzIjoiMyIsImV4dHJhIjoiIn0seyJiYWNrZ3JvdW5kY29sb3IiOiIwMDAwMDBhYiJ9XX0=',
            'widget-fullscreen-position-mode'      => 'simple',
            'widget-fullscreen-position-area'      => 4,
            'widget-fullscreen-position-offset'    => 15,
            'widget-fullscreen-mirror'             => 1,
            'widget-fullscreen-tofull-image'       => '',
            'widget-fullscreen-tofull-color'       => 'ffffffcc',
            'widget-fullscreen-tofull'             => '$ss$/plugins/widgetfullscreen/image/image/tofull/full1.svg'
        );
    }

    function onFullScreenList(&$list) {
        $list[$this->_name] = $this->getPath();
    }

    function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . 'image' . DIRECTORY_SEPARATOR;
    }

    static function getPositions(&$params) {
        $positions = array();

        $positions['fullscreen-position'] = array(
            self::$key . 'position-',
            'fullscreen'
        );

        return $positions;
    }

    static function render($slider, $id, $params) {
        $html = '';

        $toNormal      = $params->get(self::$key . 'tonormal-image');
        $toNormalColor = $params->get(self::$key . 'tonormal-color');
        if (empty($toNormal)) {
            $toNormal = $params->get(self::$key . 'tonormal');
            if ($toNormal == -1) {
                $toNormal = null;
            } elseif ($toNormal[0] != '$') {
                $toNormal = N2Uri::pathToUri(dirname(__FILE__) . '/image/tonormal/' . $toNormal);
            }
        }

        if ($params->get(self::$key . 'mirror')) {
            $toFull      = str_replace('image/tonormal/', 'image/tofull/', $toNormal);
            $toFullColor = $toNormalColor;
        } else {
            $toFull      = $params->get(self::$key . 'tofull-image');
            $toFullColor = $params->get(self::$key . 'tofull-color');
            if (empty($toFull)) {
                $toFull = $params->get(self::$key . 'tofull');
                if ($toFull == -1) {
                    $toFull = null;
                } elseif ($toFull[0] != '$') {
                    $toFull = N2Uri::pathToUri(dirname(__FILE__) . '/image/tofull/' . $toFull);
                }
            }
        }


        if ($toNormal && $toFull) {


            $ext = pathinfo($toNormal, PATHINFO_EXTENSION);
            if (substr($toNormal, 0, 1) == '$' && $ext == 'svg') {
                list($color, $opacity) = N2Color::colorToSVG($toNormalColor);
                $toNormal = 'data:image/svg+xml;base64,' . base64_encode(str_replace(array(
                        'fill="#FFF"',
                        'opacity="1"'
                    ), array(
                        'fill="#' . $color . '"',
                        'opacity="' . $opacity . '"'
                    ), N2Filesystem::readFile(N2ImageHelper::fixed($toNormal, true))));
            } else {
                $toNormal = N2ImageHelper::fixed($toNormal);
            }

            $ext = pathinfo($toFull, PATHINFO_EXTENSION);
            if (substr($toFull, 0, 1) == '$' && $ext == 'svg') {
                list($color, $opacity) = N2Color::colorToSVG($toFullColor);
                $toFull = 'data:image/svg+xml;base64,' . base64_encode(str_replace(array(
                        'fill="#FFF"',
                        'opacity="1"'
                    ), array(
                        'fill="#' . $color . '"',
                        'opacity="' . $opacity . '"'
                    ), N2Filesystem::readFile(N2ImageHelper::fixed($toFull, true))));
            } else {
                $toFull = N2ImageHelper::fixed($toFull);
            }

            N2LESS::addFile(N2Filesystem::translate(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'image' . DIRECTORY_SEPARATOR . 'style.n2less'), $slider->cacheId, array(
                "sliderid" => $slider->elementId
            ), NEXTEND_SMARTSLIDER_ASSETS . '/less' . NDS);
            N2JS::addFile(N2Filesystem::translate(dirname(__FILE__) . '/image/fullscreen.min.js'), $id);
        

            list($displayClass, $displayAttributes) = self::getDisplayAttributes($params, self::$key);

            $styleClass = N2StyleRenderer::render($params->get(self::$key . 'style'), 'heading', $slider->elementId, 'div#' . $slider->elementId . ' ');


            list($style, $attributes) = self::getPosition($params, self::$key);


            N2JS::addInline('new N2Classes.SmartSliderWidgetFullScreenImage("' . $id . '", ' . n2_floatval($params->get(self::$key . 'responsive-desktop')) . ', ' . n2_floatval($params->get(self::$key . 'responsive-tablet')) . ', ' . n2_floatval($params->get(self::$key . 'responsive-mobile')) . ');');

            $html = N2Html::tag('div', $displayAttributes + $attributes + array(
                    'class' => $displayClass . $styleClass . 'n2-full-screen-widget n2-ib n2-ow n2-full-screen-widget-image nextend-fullscreen',
                    'style' => $style
                ), N2Html::image($toNormal, 'Full screen', array(
                    'class'        => 'n2-full-screen-widget-to-normal n2-ow',
                    'data-no-lazy' => '1',
                    'data-hack'    => 'data-lazy-src',
                    'tabindex'     => '0'
                )) . N2Html::image($toFull, 'Exit full screen', array(
                    'class'        => 'n2-full-screen-widget-to-full n2-ow',
                    'data-no-lazy' => '1',
                    'data-hack'    => 'data-lazy-src',
                    'tabindex'     => '0'
                )));
        }

        return $html;
    }

    public static function prepareExport($export, $params) {
        $export->addImage($params->get(self::$key . 'tonormal-image', ''));
        $export->addImage($params->get(self::$key . 'tofull-image', ''));

        $export->addVisual($params->get(self::$key . 'style'));
    }

    public static function prepareImport($import, $params) {

        $params->set(self::$key . 'tonormal-image', $import->fixImage($params->get(self::$key . 'tonormal-image', '')));
        $params->set(self::$key . 'tofull-image', $import->fixImage($params->get(self::$key . 'tofull-image', '')));

        $params->set(self::$key . 'style', $import->fixSection($params->get(self::$key . 'style', '')));
    }

}

N2Plugin::addPlugin('sswidgetfullscreen', 'N2SSPluginWidgetFullScreenImage');

class N2SSPluginWidgetFullScreenImageBlue extends N2SSPluginWidgetFullScreenImage {

    var $_name = 'imageBlue';

    static function getDefaults() {
        return array_merge(N2SSPluginWidgetFullScreenImage::getDefaults(), array(
            'widget-fullscreen-tonormal' => '$ss$/plugins/widgetfullscreen/image/image/tonormal/full2.svg',
            'widget-fullscreen-tofull'   => '$ss$/plugins/widgetfullscreen/image/image/tofull/full2.svg',
            'widget-fullscreen-style'    => 'eyJuYW1lIjoiU3RhdGljIiwiZGF0YSI6W3siYmFja2dyb3VuZGNvbG9yIjoiMDAwMDAwYWIiLCJwYWRkaW5nIjoiMTB8KnwxMHwqfDEwfCp8MTB8KnxweCIsImJveHNoYWRvdyI6IjB8KnwwfCp8MHwqfDB8KnwwMDAwMDBmZiIsImJvcmRlciI6IjB8Knxzb2xpZHwqfDAwMDAwMGZmIiwiYm9yZGVycmFkaXVzIjoiMyIsImV4dHJhIjoiIn0seyJiYWNrZ3JvdW5kY29sb3IiOiIwMGMxYzRmZiJ9XX0='
        ));
    }
}

N2Plugin::addPlugin('sswidgetfullscreen', 'N2SSPluginWidgetFullScreenImageBlue');