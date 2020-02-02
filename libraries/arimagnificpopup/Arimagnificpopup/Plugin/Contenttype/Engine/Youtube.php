<?php
/*
 * ARI Magnific Popup
 *
 * @package		ARI Magnific Popup
 * @version		1.0.0
 * @author		ARI Soft
 * @copyright	Copyright (c) 2010 www.ari-soft.com. All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
 * 
 */

namespace Arimagnificpopup\Plugin\Contenttype\Engine;

defined('_JEXEC') or die;

use Arimagnificpopup\Plugin\Contenttype\Base As EngineBase;
use Joomla\Utilities\ArrayHelper as ArrayHelper;

class Youtube extends EngineBase
{
    protected function prepareParams($attrs, $params, $content)
    {
        $videoList = ArrayHelper::getValue($attrs, 'youtube', '');

        $params->set('youtube_video', $videoList);
        if (isset($attrs['imagesize']) && $attrs['imagesize'] !== '')
            $params->set('youtube_imagesize', $attrs['imagesize']);

        if (!empty($attrs['layout']))
            $params->set('youtube_layout', $attrs['layout']);

        return $params;
    }
}