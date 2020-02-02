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

class Vimeo extends EngineBase
{
    protected function prepareParams($attrs, $params, $content)
    {
        $videoList = ArrayHelper::getValue($attrs, 'vimeo', '');

        $params->set('vimeo_video', $videoList);

        if (!empty($attrs['layout']))
            $params->set('vimeo_layout', $attrs['layout']);

        return $params;
    }
}