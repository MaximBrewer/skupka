<?php

/**
 * @package Joomla
 * @subpackage mavikThumbnails 2
 * @copyright 2014 Vitaliy Marenkov
 * @author Vitaliy Marenkov <admin@mavik.com.ua>
 * @license GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Plugin\Content\MavikThumbnails;

defined('_JEXEC') or die();

/**
 * Class for work with IMG-tag
 *
 */
class ImgTag
{

    /**
     * @var array
     */
    private $attributes;

    /**
     * Plugin params for this image
     *
     * @var \Joomla\Registry\Registry
     */
    private $params;

    /**
     * @var int
     */
    private $height;

    /**
     * @var int
     */
    private $width;

    /**
     * @var boolean
     */
    private $sizeInPixels = true;

    /**
     * Width is setted in styles
     *
     * @var boolean
     */
    private $widthInStyle = false;

    /**
     * Height is setted in styled
     *
     * @var boolean
     */
    private $heightInStyle = false;

    /**
     * Thumbnail is created
     * 
     * @var boolean
     */
    private $isThumb = false;
    private $hasCaption = false;

    /**
     * Parse string with img-tag
     * 
     * @param string $str Img-tag as string.
     */
    function parse($str)
    {
        $this->isThumb = false;
        $this->sizeInPixels = true;
        // Parse string - get all attributes.
        preg_match_all('/(?<=\s)(?<attribute>[\w\-_]+)(\s*=\s*(?<quote>[\"\']?)(?<value>.*?)\k<quote>)?(?=(\s|(\/?>)))/s', $str, $matches, PREG_SET_ORDER);
        $this->attributes = array();
        foreach ($matches as $match) {
            $this->attributes[strtolower($match['attribute'])] = $match['value'];
        }

        /**
         *  Define visible size
         */
        
        // Get size from attributes
        
        $this->width = isset($this->attributes['width']) ? $this->attributes['width'] : null;
        $this->height = isset($this->attributes['height']) ? $this->attributes['height'] : null;
        
        if (preg_match('/\d+\s*(.*)/i', $this->width, $matches)) {
            $this->width = (int) $this->width;
            if ($matches[1] && strtolower($matches[1]) != 'px') {
                $this->sizeInPixels = false;
            }
        } else {
            $this->width = null;
        }

        if (preg_match('/\d+\s*(.*)?/i', $this->height, $matches)) {
            $this->height = (int) $this->height;
            if ($matches[1] && strtolower($matches[1]) != 'px') {
                $this->sizeInPixels = false;
            }
        } else {
            $this->height = null;
        }

        // Get size from style
        
        if (!empty($this->attributes['style'])) {
            if (preg_match('/(?<!\-)\bwidth\s*:\s*(\d+)\s*(\w*)/si', $this->attributes['style'], $matches)) {
                $this->width = $matches[1];
                $this->widthInStyle = true;
                if (strtolower($matches[2]) != 'px') {
                    $this->sizeInPixels = false;
                }
            }
            if (preg_match('/(?<!\-)\bheight\s*:\s*(\d+)\s*(\w*)?/si', $this->attributes['style'], $matches)) {
                $this->height = $matches[1];
                $this->heightInStyle = true;
                if (strtolower($matches[2]) != 'px') {
                    $this->sizeInPixels = false;
                }
            }
        }

        /**
         * END: Define visible size
         */        
        
        // Caption
        $class = & $this->attributes['class'];
        $class = preg_replace('/\bcaption\b/i', '', $class, -1, $count);
        $this->hasCaption = $count;
    }

    /**
     * Set attribute value
     *
     * @param string $name
     * @param string $value
     */
    function setAttribute($name, $value)
    {
        $name = strtolower($name);
        $this->attributes[$name] = $value;
    }

    /**
     * Get attribute value
     *
     * @param string $name
     * @return string
     */
    function getAttribute($name)
    {
        $name = strtolower($name);
        return isset($this->attributes[$name]) ? $this->attributes[$name] : null;
    }

    /**
     * @return array
     */
    function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @return \Joomla\Registry\Registry
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Visible width
     *
     * @return int
     */
    function getWidth()
    {
        return $this->width;
    }

    /**
     * Visible height
     *
     * @return int
     */
    function getHeight()
    {
        return $this->height;
    }

    /**
     * Set visible width
     *
     * @param int $value
     */
    function setWidth($value)
    {
        $value = round($value);

        if ($this->getAttribute('width')) {
            $this->setAttribute('width', $value);
        }

        if ($this->widthInStyle) {
            $this->setAttribute('style', preg_replace('/(?<!\-)\bwidth\s*:\s*\d+\s*px/', 'width: ' . $value . 'px', $this->getAttribute('style')));
        } elseif ($this->heightInStyle) {
            $this->setAttribute('style', $this->getAttribute('style') . ' width: ' . $value . 'px;');
        } else {
            $this->setAttribute('width', $value);
        }
        $this->width = $value;
    }

    /**
     * Set visible height
     *
     * @param int $value
     */
    function setHeight($value)
    {
        $value = round($value);

        if ($this->getAttribute('height')) {
            $this->setAttribute('height', $value);
        }

        if ($this->heightInStyle) {
            $this->setAttribute('style', preg_replace('/(?<!\-)\bheight\s*:\s*\d+\s*px/', 'height: ' . $value . 'px', $this->getAttribute('style')));
        } elseif ($this->widthInStyle) {
            $this->setAttribute('style', $this->getAttribute('style') . ' height: ' . $value . 'px;');
        } else {
            $this->setAttribute('height', $value);
        }
        $this->height = $value;
    }

    /**
     * Get style without size
     * 
     * It's used for moving attribute "style" from image to link
     * 
     * @return string
     */
    public function getStyleWithoutSize()
    {
        if ($this->heightInStyle || $this->widthInStyle) {
            return preg_replace('/(?<!\-)\b(height|width)\s*:\s*\d+\s*px/', '', $this->getAttribute('style'));
        } else {
            return $this->getAttribute('style');
        }
    }

    /**
     * Clear style, keep only size
     * 
     * It's used for moving styles from image to link
     */
    public function setStyleOnlySize()
    {
        $style = '';
        if ($this->widthInStyle) {
            $style .= "width: {$this->width}px; ";
        }
        if ($this->heightInStyle) {
            $style .= "height: {$this->height}px;";
        }
        $this->setAttribute('style', $style);
    }

    public function hasCaption()
    {
        return $this->hasCaption;
    }

    public function isSizeInPixels()
    {
        return $this->sizeInPixels;
    }

    /**
     * Init params for this image
     * 
     * @param \Joomla\Registry\Registry $params
     */
    public function initParams(\Joomla\Registry\Registry $params)
    {
        $this->params = clone $params;
        foreach ($this->attributes as $name => $value) {
            if (strpos(strtolower($name), 'data-mavikthumbnails-') === 0) {
                $this->params->set(substr($name, 21), $value);
            }
        }
    }

    public function toString($allAttributes = false)
    {
        $imgTag = '<img ';
        foreach ($this->attributes as $name => $value) {
            if ($allAttributes || strpos($name, 'data-mavikthumbnails-') !== 0) {
                $imgTag .= "$name=\"$value\" ";
            }
        }
        $imgTag .= '/>';
        return $imgTag;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }

}

?>