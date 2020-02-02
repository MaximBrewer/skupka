<?php
/**
* @file
* @brief    boxplus: a lightweight pop-up window engine for Joomla
* @author   Levente Hunyadi
* @version  1.0.3
* @remarks  Copyright (C) 2011-2017 Levente Hunyadi
* @remarks  Licensed under GNU/GPLv3, see http://www.gnu.org/licenses/gpl-3.0.html
* @see      http://hunyadi.info.hu/projects/boxplus
*/

/*
* boxplus: a lightweight pop-up window engine for Joomla
* Copyright 2011-2017 Levente Hunyadi
*
* boxplus is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* boxplus is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with boxplus.  If not, see <http://www.gnu.org/licenses/>.
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

if (!defined('BOXPLUS_DEBUG')) {
    // Forces debug mode. Debug uses uncompressed version of scripts rather than the bandwidth-saving minified versions.
    define('BOXPLUS_DEBUG', false);
}

// import library dependencies
jimport('joomla.event.plugin');

class BoxPlusColors {
    /** Maps color names to color codes. */
    private static $colors;

    public static function translate($value) {
        if (!isset(self::$colors)) {
            $colors = array(
                'AliceBlue'=>0xF0F8FF,
                'AntiqueWhite'=>0xFAEBD7,
                'Aqua'=>0x00FFFF,
                'Aquamarine'=>0x7FFFD4,
                'Azure'=>0xF0FFFF,
                'Beige'=>0xF5F5DC,
                'Bisque'=>0xFFE4C4,
                'Black'=>0x000000,
                'BlanchedAlmond'=>0xFFEBCD,
                'Blue'=>0x0000FF,
                'BlueViolet'=>0x8A2BE2,
                'Brown'=>0xA52A2A,
                'BurlyWood'=>0xDEB887,
                'CadetBlue'=>0x5F9EA0,
                'Chartreuse'=>0x7FFF00,
                'Chocolate'=>0xD2691E,
                'Coral'=>0xFF7F50,
                'CornflowerBlue'=>0x6495ED,
                'Cornsilk'=>0xFFF8DC,
                'Crimson'=>0xDC143C,
                'Cyan'=>0x00FFFF,
                'DarkBlue'=>0x00008B,
                'DarkCyan'=>0x008B8B,
                'DarkGoldenRod'=>0xB8860B,
                'DarkGray'=>0xA9A9A9,
                'DarkGrey'=>0xA9A9A9,
                'DarkGreen'=>0x006400,
                'DarkKhaki'=>0xBDB76B,
                'DarkMagenta'=>0x8B008B,
                'DarkOliveGreen'=>0x556B2F,
                'Darkorange'=>0xFF8C00,
                'DarkOrchid'=>0x9932CC,
                'DarkRed'=>0x8B0000,
                'DarkSalmon'=>0xE9967A,
                'DarkSeaGreen'=>0x8FBC8F,
                'DarkSlateBlue'=>0x483D8B,
                'DarkSlateGray'=>0x2F4F4F,
                'DarkSlateGrey'=>0x2F4F4F,
                'DarkTurquoise'=>0x00CED1,
                'DarkViolet'=>0x9400D3,
                'DeepPink'=>0xFF1493,
                'DeepSkyBlue'=>0x00BFFF,
                'DimGray'=>0x696969,
                'DimGrey'=>0x696969,
                'DodgerBlue'=>0x1E90FF,
                'FireBrick'=>0xB22222,
                'FloralWhite'=>0xFFFAF0,
                'ForestGreen'=>0x228B22,
                'Fuchsia'=>0xFF00FF,
                'Gainsboro'=>0xDCDCDC,
                'GhostWhite'=>0xF8F8FF,
                'Gold'=>0xFFD700,
                'GoldenRod'=>0xDAA520,
                'Gray'=>0x808080,
                'Grey'=>0x808080,
                'Green'=>0x008000,
                'GreenYellow'=>0xADFF2F,
                'HoneyDew'=>0xF0FFF0,
                'HotPink'=>0xFF69B4,
                'IndianRed'=>0xCD5C5C,
                'Indigo'=>0x4B0082,
                'Ivory'=>0xFFFFF0,
                'Khaki'=>0xF0E68C,
                'Lavender'=>0xE6E6FA,
                'LavenderBlush'=>0xFFF0F5,
                'LawnGreen'=>0x7CFC00,
                'LemonChiffon'=>0xFFFACD,
                'LightBlue'=>0xADD8E6,
                'LightCoral'=>0xF08080,
                'LightCyan'=>0xE0FFFF,
                'LightGoldenRodYellow'=>0xFAFAD2,
                'LightGray'=>0xD3D3D3,
                'LightGrey'=>0xD3D3D3,
                'LightGreen'=>0x90EE90,
                'LightPink'=>0xFFB6C1,
                'LightSalmon'=>0xFFA07A,
                'LightSeaGreen'=>0x20B2AA,
                'LightSkyBlue'=>0x87CEFA,
                'LightSlateGray'=>0x778899,
                'LightSlateGrey'=>0x778899,
                'LightSteelBlue'=>0xB0C4DE,
                'LightYellow'=>0xFFFFE0,
                'Lime'=>0x00FF00,
                'LimeGreen'=>0x32CD32,
                'Linen'=>0xFAF0E6,
                'Magenta'=>0xFF00FF,
                'Maroon'=>0x800000,
                'MediumAquaMarine'=>0x66CDAA,
                'MediumBlue'=>0x0000CD,
                'MediumOrchid'=>0xBA55D3,
                'MediumPurple'=>0x9370D8,
                'MediumSeaGreen'=>0x3CB371,
                'MediumSlateBlue'=>0x7B68EE,
                'MediumSpringGreen'=>0x00FA9A,
                'MediumTurquoise'=>0x48D1CC,
                'MediumVioletRed'=>0xC71585,
                'MidnightBlue'=>0x191970,
                'MintCream'=>0xF5FFFA,
                'MistyRose'=>0xFFE4E1,
                'Moccasin'=>0xFFE4B5,
                'NavajoWhite'=>0xFFDEAD,
                'Navy'=>0x000080,
                'OldLace'=>0xFDF5E6,
                'Olive'=>0x808000,
                'OliveDrab'=>0x6B8E23,
                'Orange'=>0xFFA500,
                'OrangeRed'=>0xFF4500,
                'Orchid'=>0xDA70D6,
                'PaleGoldenRod'=>0xEEE8AA,
                'PaleGreen'=>0x98FB98,
                'PaleTurquoise'=>0xAFEEEE,
                'PaleVioletRed'=>0xD87093,
                'PapayaWhip'=>0xFFEFD5,
                'PeachPuff'=>0xFFDAB9,
                'Peru'=>0xCD853F,
                'Pink'=>0xFFC0CB,
                'Plum'=>0xDDA0DD,
                'PowderBlue'=>0xB0E0E6,
                'Purple'=>0x800080,
                'Red'=>0xFF0000,
                'RosyBrown'=>0xBC8F8F,
                'RoyalBlue'=>0x4169E1,
                'SaddleBrown'=>0x8B4513,
                'Salmon'=>0xFA8072,
                'SandyBrown'=>0xF4A460,
                'SeaGreen'=>0x2E8B57,
                'SeaShell'=>0xFFF5EE,
                'Sienna'=>0xA0522D,
                'Silver'=>0xC0C0C0,
                'SkyBlue'=>0x87CEEB,
                'SlateBlue'=>0x6A5ACD,
                'SlateGray'=>0x708090,
                'SlateGrey'=>0x708090,
                'Snow'=>0xFFFAFA,
                'SpringGreen'=>0x00FF7F,
                'SteelBlue'=>0x4682B4,
                'Tan'=>0xD2B48C,
                'Teal'=>0x008080,
                'Thistle'=>0xD8BFD8,
                'Tomato'=>0xFF6347,
                'Turquoise'=>0x40E0D0,
                'Violet'=>0xEE82EE,
                'Wheat'=>0xF5DEB3,
                'White'=>0xFFFFFF,
                'WhiteSmoke'=>0xF5F5F5,
                'Yellow'=>0xFFFF00,
                'YellowGreen'=>0x9ACD32
            );
            self::$colors = array_merge($colors, array_combine(array_map('strtolower', array_keys($colors)), array_values($colors)));
        }

        if (isset(self::$colors[$value])) {
            return sprintf('#%06x', self::$colors[$value]);  // translate color name to color code
        } else {
            return false;
        }
    }
}

class BoxPlusSettings {
    /** Color theme. */
    public $theme = 'dark';
    /** Rounded corners. */
    public $rounded = false;
    /** Border style, or false for default (inherit from stylesheet file). */
    public $border_style = false;
    /** Border width [px], or false for default (inherit from stylesheet file). */
    public $border_width = false;
    /** Border color as a hexadecimal value in between 000000 or ffffff inclusive, or false for default. */
    public $border_color = false;
    /** Padding [px], or false for default (inherit from slideshow.css). */
    public $padding = false;

    public $slideshow = 0;
    public $autostart = false;
    public $loop = false;

    public $navigation = 'bottom';
    public $controls = 'below';
    public $captions = 'below';

    public $width = 800;
    public $height = 600;

    public $title = false;
    public $description = false;

    public $duration = 250;
    public $transition = false;

    public $protection = true;
    public $strict = false;
    public $activationtag = 'boxplus';

    public function getArray() {
        $params = array();
        foreach (get_class_vars(__CLASS__) as $name => $value) {  // enumerate properties in class
            $params[$name] = $this->$name;
        }
        return $params;
    }

    public function setArray(array $params) {
        foreach (get_class_vars(__CLASS__) as $name => $value) {  // enumerate properties in class
            if (isset($params[$name])) {
                $this->$name = $params[$name];
            }
        }

        $this->validate();
    }

    public function setParameters(JRegistry $params) {
        foreach (get_class_vars(__CLASS__) as $name => $value) {  // enumerate properties in class
            $this->$name = $params->get($name, $value);  // set property class value as default if not present in XML
        }

        $this->validate();
    }

    /**
    * Validates settings and resets invalid values to their defaults.
    */
    private function validate() {
        $default = new self();

        $this->theme = self::as_one_of($this->theme, array(false,'dark','light'));
        switch ($this->border_style) {
            case 'none': case 'dotted': case 'dashed': case 'solid': case 'double': case 'groove': case 'ridge': case 'inset': case 'outset': break;
            default: $this->border_style = false;
        }
        $this->border_width = self::as_nonnegative_integer($this->border_width);
        $this->border_color = self::as_color($this->border_color);
        $this->padding = self::as_nonnegative_integer($this->padding);

        $this->slideshow = self::as_nonnegative_integer($this->slideshow, $default->slideshow);
        $this->autostart = self::as_boolean($this->autostart);
        $this->loop = self::as_boolean($this->loop);

        $this->navigation = self::as_one_of($this->navigation, array('bottom','top','below','above','none'));
        $this->controls = self::as_one_of($this->controls, array('below','bottom','top','above','none'));
        $this->captions = self::as_one_of($this->captions, array('below','above','none'));

        $this->width = self::as_nonnegative_integer($this->width, $default->width);
        $this->height = self::as_nonnegative_integer($this->height, $default->height);

        $this->duration = self::as_nonnegative_integer($this->duration, $default->duration);
        $this->transition = self::as_one_of($this->transition, array(false,'ease','linear','quad','cubic','quart','quint','expo','circ','sine','back','bounce','elastic'));

        $this->protection = self::as_boolean($this->protection);
        $this->strict = self::as_boolean($this->strict);
        if (!is_string($this->activationtag) || !ctype_alpha($this->activationtag)) {
            $this->activationtag = $default->activationtag;
        }
    }

    /**
    * Casts a value to one of the set of values.
    */
    private static function as_one_of($value, array $values) {
        if (in_array($value, $values, true)) {
            return $value;
        } else {
            return reset($values);
        }
    }

    /**
    * Casts a value to a true or false value.
    */
    private static function as_boolean($value) {
        if (is_string($value)) {
            switch ($value) {
                case 'true': case 'on': case 'yes': case '1':
                    return true;
            }
            return false;
        } else {
            return (bool) $value;
        }
    }

    /**
    * Casts a value to a nonnegative integer.
    */
    private static function as_nonnegative_integer($value, $default = 0) {
        if (is_null($value) || $value === '') {
            return false;
        } elseif ($value !== false) {
            $value = (int) $value;
            if ($value <= 0) {
                $value = $default;
            }
        }
        return $value;
    }

    /**
    * Casts a value to a positive integer.
    */
    private static function as_positive_integer($value, $default) {
        if (is_null($value) || $value === false || $value === '') {
            return $default;
        } else {
            $value = (int) $value;
            if ($value < 0) {
                $value = $default;
            }
            return $value;
        }
    }

    /**
    * Casts a value to a percentage value.
    */
    private static function as_percentage($value) {
        $value = (int) $value;
        if ($value < 0) {
            $value = 0;
        }
        if ($value > 100) {
            $value = 100;
        }
        return $value;
    }

    /**
    * Casts a value to a CSS hexadecimal color specification.
    */
    private static function as_color($value) {
        if (is_string($value)) {
            if (preg_match('/^#?([0-9A-Fa-f]{6}|[0-9A-Fa-f]{3})$/', $value)) {  // a hexadecimal color code
                return '#'.ltrim($value, '#');
            } else {  //  a color name
                return ShowPlusColors::translate($value);
            }
        } elseif (is_int($value)) {
            return sprintf('#%06x', $value);  // convert integer into hexadecimal digits
        } else {
            return false;
        }
    }

    /**
    * Casts a value to a CSS dimension measure with a unit.
    */
    protected static function as_css_measure($value) {
        if (!isset($value) || $value === false) {
            return false;
        } elseif (is_numeric($value)) {
            return $value.'px';
        } elseif (preg_match('#^(?:\\b(?:(?:0|[1-9][0-9]*)(?:[.][0-9]+)?(?:%|in|[cm]m|e[mx]|p[tcx])|0)\\s*){1,4}$#', $value)) {  // "1px" or "1px 2em" or "1px 2em 3pt" or "1px 2em 3pt 4cm" or "1px 0 0 4cm"
            return $value;
        } else {
            return 0;
        }
    }

    /**
    * Pop-up window settings to be later emitted as a JSON object.
    */
    public function getOptions() {
        $options = array(
            'slideshow' => $this->slideshow,
            'autostart' => $this->autostart,
            'loop' => $this->loop,
            'preferredWidth' => $this->width,
            'preferredHeight' => $this->height,
            'navigation' => $this->navigation,
            'controls' => $this->controls,
            'captions' => $this->captions,
            'contextmenu' => !$this->protection
        );
        return $options;
    }
}

/**
* A thin wrapper for Joomla around the JavaScript lightbox pop-up window implementation.
*/
class plgContentBoxPlus extends JPlugin {

    private static $debug = null;
    /** Default settings. */
    private $settings;
    /** Current settings. */
    private $current;
    /** Settings store. */
    private $store = array(
        'id' => array(),
        'rel' => array()
    );
    /** Custom styles to be added to the document <head> section. */
    private $styles = array();

    public function __construct( &$subject, $config ) {
        parent::__construct( $subject, $config );
        $this->settings = new BoxPlusSettings();
        $this->settings->setParameters($this->params);

        if (!isset(self::$debug)) {  // cannot alter debug mode once set
            self::$debug = BOXPLUS_DEBUG || (bool) $this->params->get('debug');
        }
    }

    /**
    * Adds CSS references to the <head> section and collects inline CSS.
    */
    private function addStyles($id = null) {

        $document = JFactory::getDocument();
        if (self::$debug) {
            $document->addStyleSheet(JURI::base(true).'/media/plg_boxplus/css/boxplusx.css');
        } else {
            $document->addStyleSheet(JURI::base(true).'/media/plg_boxplus/css/boxplusx.min.css');
        }

        $rules = array();
        switch ($this->current->theme) {
            case 'dark':
                $rules['background-color'] = 'rgba(0,0,0,0.8)';
                $rules['color'] = '#fff';
                break;
            case 'light':
                $rules['background-color'] = 'rgba(255,255,255,0.8)';
                $rules['color'] = '#000';
                break;
        }
        if ($this->current->border_width !== false && $this->current->border_style !== false && $this->current->border_color !== false) {
            $rules['border'] = $this->current->border_width.'px '.$this->current->border_style.' '.$this->current->border_color;
        } else {
            if ($this->current->border_width !== false) {
                $rules['border-width'] = $this->current->border_width.'px';
            }
            if ($this->current->border_style !== false) {
                $rules['border-style'] = $this->current->border_style;
            }
            if ($this->current->border_color !== false) {
                $rules['border-color'] = $this->current->border_color;
            }
        }
        if ($this->current->padding !== false) {
            $rules['padding'] = $this->current->padding.'px';
        }
        if ($this->current->rounded !== false) {
            if ($this->current->border_width !== false && $this->current->padding !== false) {
                $rules['border-radius'] = ($this->current->border_width + $this->current->padding).'px';
            } else if ($this->current->border_width !== false) {
                $rules['border-radius'] = $this->current->border_width.'px';
            } else if ($this->current->padding !== false) {
                $rules['border-radius'] = $this->current->padding.'px';
            } else {
                $rules['border-radius'] = '10px';
            }
        }
        $selectors['.boxplusx-dialog'] = $rules;

        $rules = array();
        if ($this->current->transition !== false) {
            switch ($this->current->transition) {
                case 'linear':
                    $func = 'linear'; break;
                case 'ease-in':
                    $func = 'ease-in'; break;
                case 'ease-out':
                    $func = 'ease-out'; break;
                case 'ease':
                    $func = 'ease'; break;
                case 'quad-in':  // http://easings.net/#easeInQuad
                    $func = 'cubic-bezier(0.55, 0.085, 0.68, 0.53)'; break;
                case 'quad-out':  // http://easings.net/#easeOutQuad
                    $func = 'cubic-bezier(0.25, 0.46, 0.45, 0.94)'; break;
                case 'quad':  // http://easings.net/#easeInOutQuad
                    $func = 'cubic-bezier(0.455, 0.03, 0.515, 0.955)'; break;
                case 'cubic-in':  // http://easings.net/#easeInCubic
                    $func = 'cubic-bezier(0.55, 0.055, 0.675, 0.19)'; break;
                case 'cubic-out':  // http://easings.net/#easeOutCubic
                    $func = 'cubic-bezier(0.215, 0.61, 0.355, 1)'; break;
                case 'cubic':  // http://easings.net/#easeInOutCubic
                    $func = 'cubic-bezier(0.645, 0.045, 0.355, 1)'; break;
                case 'quart-in':  // http://easings.net/#easeInQuart
                    $func = 'cubic-bezier(0.895, 0.03, 0.685, 0.22)'; break;
                case 'quart-out':  // http://easings.net/#easeOutQuart
                    $func = 'cubic-bezier(0.165, 0.84, 0.44, 1)'; break;
                case 'quart':  // http://easings.net/#easeInOutQuart
                    $func = 'cubic-bezier(0.77, 0, 0.175, 1)'; break;
                case 'quint-in':  // http://easings.net/#easeInQuint
                    $func = 'cubic-bezier(0.755, 0.05, 0.855, 0.06)'; break;
                case 'quint-out':  // http://easings.net/#easeOutQuint
                    $func = 'cubic-bezier(0.23, 1, 0.32, 1)'; break;
                case 'quint':  // http://easings.net/#easeInOutQuint
                    $func = 'cubic-bezier(0.86, 0, 0.07, 1)'; break;
                case 'expo-in':  // http://easings.net/#easeInExpo
                    $func = 'cubic-bezier(0.95, 0.05, 0.795, 0.035)'; break;
                case 'expo-out':  // http://easings.net/#easeOutExpo
                    $func = 'cubic-bezier(0.19, 1, 0.22, 1)'; break;
                case 'expo':  // http://easings.net/#easeInOutExpo
                    $func = 'cubic-bezier(1, 0, 0, 1)'; break;
                case 'circ-in':  // http://easings.net/#easeInCirc
                    $func = 'cubic-bezier(0.6, 0.04, 0.98, 0.335)'; break;
                case 'circ-out':  // http://easings.net/#easeOutCirc
                    $func = 'cubic-bezier(0.075, 0.82, 0.165, 1)'; break;
                case 'circ':  // http://easings.net/#easeInOutCirc
                    $func = 'cubic-bezier(0.785, 0.135, 0.15, 0.86)'; break;
                case 'sine-in':  // http://easings.net/#easeInSine
                    $func = 'cubic-bezier(0.47, 0, 0.745, 0.715)'; break;
                case 'sine-out':  // http://easings.net/#easeOutSine
                    $func = 'cubic-bezier(0.39, 0.575, 0.565, 1)'; break;
                case 'sine':  // http://easings.net/#easeInOutSine
                    $func = 'cubic-bezier(0.445, 0.05, 0.55, 0.95)'; break;
                case 'back-in':  // http://easings.net/#easeInBack
                    $func = 'cubic-bezier(0.6, -0.28, 0.735, 0.045)'; break;
                case 'back-out':  // http://easings.net/#easeOutBack
                    $func = 'cubic-bezier(0.175, 0.885, 0.32, 1.275)'; break;
                case 'back':  // http://easings.net/#easeInOutBack
                    $func = 'cubic-bezier(0.68, -0.55, 0.265, 1.55)'; break;
                case 'bounce':  // not supported in CSS
                case 'elastic':  // not supported in CSS
                default:
                    $func = 'linear'; break;
            }
            $rules['transition-timing-function'] = $func;
        }
        $rules['transition-duration'] = ($this->current->duration.'ms').', '.($this->current->duration.'ms');
        $selectors['.boxplusx-dialog.boxplusx-animation'] = $rules;

        // prepare CSS style declarations
        foreach ($selectors as $selector => $rules) {
            if (!empty($rules)) {
                if ($id) {
                    $sel = "#{$id} {$selector}";
                } else {
                    $sel = $selector;
                }

                // re-use previous selectors
                if (!isset($this->styles[$sel])) {
                    $this->styles[$sel] = $rules;
                } else {
                    $this->styles[$sel] = array_merge($this->styles[$sel], $rules);
                }
            }
        }
    }

    /**
    * Adds inline CSS to the document <head> section.
    */
    private function emitStyles() {
        $document = JFactory::getDocument();
        foreach ($this->styles as $selector => $rules) {
            if (!empty($rules)) {
                $cssrules = array();
                foreach ($rules as $name => $value) {
                    $cssrules[] = "{$name}:{$value};";
                }
                $css = implode('',$cssrules);
                $document->addStyleDeclaration("{$selector}{{$css}}");
            }
        }
        $this->styles = array();
    }

    /**
    * Adds javascript code to the HTML document <head> section.
    */
    private function addScripts() {
        $document = JFactory::getDocument();
        $document->addScript(JURI::base(true).'/media/plg_boxplus/js/boxplusx'.(self::$debug ? '' : '.min').'.js', 'text/javascript', true);
    }

    /**
    * Adds activation code to the HTML document <head> that invokes the boxplus window on links that can open in a lightbox.
    */
    private function addActivation() {
        static $activation = false;

        $this->addStyles();
        $this->addScripts();

        if (!$activation) {
            $activation = true;

            $document = JFactory::getDocument();
            $strict = json_encode($this->current->strict);
            $activationtag = json_encode($this->current->activationtag);
            $options = json_encode($this->current->getOptions());
            $script = array();
            $script[] = "document.addEventListener('DOMContentLoaded', function () {";
            $script[] = "BoxPlusXDialog.discover({$strict},'boxplus',{$options});";
            $script[] = "});";
            $document->addScriptDeclaration(implode('', $script));
        }
    }

    /**
    * Fired when contents are to be processed by the plug-in.
    */
    public function onContentPrepare( $context, &$article, &$params, $limitstart ) {



        // skip plug-in activation when the content is being indexed
        if ($context === 'com_finder.indexer') {
            return;
        }

        if ($this->settings->strict && strpos($article->text, '{'.$this->settings->activationtag) === false) {
            return;  // short-circuit plug-in activation
        }


        $activationtag = preg_quote($this->settings->activationtag, '#');



        try {
            // find gallery tags and emit code
            $localcount = 0;
            $article->text = preg_replace_callback(
                '#[{]'.$activationtag.'\b([^{}]*)(?<!/)[}](.*)[{]/'.$activationtag.'[}]#sSU',
                function ($match) {
                    return $this->getLocalReplacement($match);
                },
                $article->text,
                -1,
                $localcount
            );

            // add script to bind images with the same rel attribute
            if (!empty($this->store['id']) || !empty($this->store['rel'])) {
                $selectors = array();

                // anchors found with their unique HTML identifier
                foreach ($this->store['id'] as $key => $settings) {
                    $options = $settings->getOptions();
                    $options['id'] = self::getDialogIdentifier($key, null);
                    $selectors['#'.$key] = $options;
                }
                $this->store['id'] = array();

                // anchors found with a rel attribute
                foreach ($this->store['rel'] as $key => $settings) {
                    $options = $settings->getOptions();
                    $options['id'] = self::getDialogIdentifier(null, $key);
                    $selectors["a[href][rel='{$key}']"] = $options;
                }
                $this->store['rel'] = array();

                // subscribe to DOM ready event
                $script = array();
                $script[] = "document.addEventListener('DOMContentLoaded', function () {";
                foreach ($selectors as $selector => $settings) {
                    $sel = json_encode($selector);
                    $options = json_encode($settings);
                    $script[] = "(new BoxPlusXDialog({$options})).bind(document.querySelectorAll({$sel}));";
                }
                $script[] = "});";
                $document = JFactory::getDocument();
                $document->addScriptDeclaration(implode('', $script));
            }

            $globalcount = 0;
            $article->text = preg_replace_callback(
                '#[{]'.$activationtag.'\b([^{}]*)/[}]#sS',
                function ($match) {
                    return $this->getGlobalReplacement($match);
                },
                $article->text,
                1,
                $globalcount
            );

            // no activation tags found but lenient mode is set and there are anchors with "rel" attributes
            if ((!$globalcount && !$localcount && !$this->settings->strict && preg_match('#rel=[\'"]?boxplus#', $article->text))) {

// if ((!$globalcount && !$localcount && !$this->settings->strict && preg_match('#rel=[\'"]?boxplus#', $article->text))||( "com_virtuemart.productdetails"==$context)) {
				
				
                $this->current = clone $this->settings;
                $this->addActivation();
                $this->current = null;
            }

            $this->emitStyles();



        } catch (Exception $e) {
            $app = JFactory::getApplication();
            $app->enqueueMessage( $e->getMessage(), 'error' );
        }



    }

    /**
    * Retrieves settings that belong to a previously used key or clones defaults.
    * @param {string} $key A string to use as a key.
    * @param {string} $section
    */
    private function retrieveLocalSettings($key = false, $section = 'rel') {
        if ($key && isset($this->store[$section]) && isset($this->store[$section][$key])) {
            return $this->store[$section][$key];
        } else {
            return clone $this->settings;
        }
    }

    /**
    * Stores the current settings under a key.
    * @param {string|integer} $key A string or integer to use as a key.
    */
    private function storeLocalSettings($key, $section = 'rel') {
        if (!isset($this->store[$section])) {
            $this->store[$section] = array();
        }
        $this->store[$section][$key] = $this->current;
        $this->current = null;
    }

    private static function getIdentifier($index) {
        return sprintf('bpl-%03d', $index);  // [l]ink identifier
    }

    private static function getDialogIdentifier($id, $rel) {
        if ($id) {
            return "bpi-{$id}";  // dialog opened by [i]ndividual (self-standing) link
        } else {
            return "bpr-{$rel}";  // dialog opened by [r]elated link
        }
    }

    /**
    * Local configuration overrides.
    * @param string $string A list of settings as name=value pairs.
    * @return array Configuration settings as an array.
    */
    private static function getOptions($string) {
        return self::string_to_array(htmlspecialchars_decode($string));
    }

    /**
    * Replacement for the activation tag syntax {boxplus param=value}text{/boxplus}.
    * @param $match A regular expression match captured by preg_replace_callback.
    */
    private function getLocalReplacement($match) {
        static $counter = 1;

        $options = array_merge(
            array(
                'href' => 'javascript:void(0);'
            ),
            self::getOptions($match[1])
        );
        if (isset($options['rel'])) {
            $this->current = $this->retrieveLocalSettings($options['rel'], 'rel');
        } else {
            $this->current = clone $this->settings;
        }

        // apply options extracted from activation tag
        $this->current->setArray($options);

        if (isset($options['rel'])) {  // use "rel" attribute supplied
            $id = null;
            $rel = $options['rel'];
        } else {  // generate unique identifier for link
            if (isset($options['id'])) {
                $id = $options['id'];
            } else {
                $id = self::getIdentifier($counter++);  // use counter value as numeric key
                $options['id'] = $id;
            }
            $rel = null;
        }

        // add styles and scripts to page header (may depend on settings in activation tag)
        $this->addStyles(self::getDialogIdentifier($id, $rel));
        $this->addScripts();

        // determine settings store key and section
        if (isset($options['rel'])) {  // use "rel" attribute supplied
            $key = $options['rel'];
            $section = 'rel';
        } else {
            $key = $options['id'];
            $section = 'id';
        }

        // update settings store
        $this->storeLocalSettings($key, $section);

        // transfer common attributes from activation tag to HTML anchor
        $attrs = array();
        foreach (array('id','href','rel','class','style','title') as $attr) {
            if (isset($options[$attr])) {
                $attrs[$attr] = $options[$attr];
            }
        }

        // emit HTML code
        $html = self::make_html('a', $attrs, $match[2]);
        return $html;
    }

    /**
    * Replacement for the activation tag syntax {boxplus param=value /}.
    * @param $match A regular expression match captured by preg_replace_callback.
    */
    private function getGlobalReplacement($match) {
        $this->current = clone $this->settings;
        $options = self::getOptions($match[1]);
        if (!empty($options)) {
            $this->current->setArray($options);
        }

        $this->addActivation();

        $this->current = null;
        return '';
    }

    /**
    * Converts a string containing key-value pairs into an associative array.
    * @param string $string The string to split into key-value pairs.
    * @param string $separator The optional string that separates the key from the value.
    * @return array An associative array that maps keys to values.
    */
    private static function string_to_array($string, $separator = '=', $quotechars = array("'",'"','|')) {
        $separator = preg_quote($separator, '#');
        if (is_array($quotechars)) {
            $quotedvalue = '';
            foreach ($quotechars as $quotechar) {
                $quotechar = preg_quote($quotechar[0], '#');  // escape characters with special meaning to regex
                $quotedvalue .= $quotechar.'[^'.$quotechar.']*'.$quotechar.'|';
            }
        } else {
            $quotechar = preg_quote($quotechar[0], '#');  // make sure quote character is a single character
            $quotedvalue = $quotechar.'[^'.$quotechar.']*'.$quotechar.'|';
        }
        $regularchar = '[A-Za-z0-9_.:/-]';
        $namepattern = '([A-Za-z_]'.$regularchar.'*)';  // html attribute name
        $valuepattern = '('.$quotedvalue.'-?[0-9]+(?:[.][0-9]+)?|'.$regularchar.'+)';
        $pattern = '#(?:'.$namepattern.$separator.')?'.$valuepattern.'#';

        $array = array();
        $matches = array();
        $result = preg_match_all($pattern, $string, $matches, PREG_SET_ORDER);
        if (!$result) {
            return false;
        }
        foreach ($matches as $match) {
            $name = $match[1];
            $value = trim($match[2], implode('', $quotechars));
            if (strlen($name) > 0) {
                $array[$name] = $value;
            } else {
                $array[] = $value;
            }
        }
        return $array;
    }

    /**
    * Builds HTML from tag name, attribute array and element content.
    * @param string $element Tag name.
    */
    private static function make_html($element, $attrs = false, $content = false) {
        $html = '<'.$element;
        if ($attrs !== false) {
            foreach ($attrs as $key => $value) {
                if ($value !== false) {
                    $html .= ' '.$key.'="'.htmlspecialchars($value).'"';
                }
            }
        }
        if ($content !== false) {
            $html .= '>'.$content.'</'.$element.'>';
        } else {
            $html .= '/>';
        }
        return $html;
    }
}
