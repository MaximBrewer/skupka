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

use \Joomla\Utilities\ArrayHelper as ArrayHelper;
use Arimagnificpopup\Plugin\Contenttype\Base As EngineBase;

class Gallery extends EngineBase
{
    protected function prepareParams($attrs, $params, $content)
    {
        $layout = ArrayHelper::getValue($attrs, 'layout', '');
        $folderList = ArrayHelper::getValue($attrs, 'gallery', '');

        if (!empty($layout))
            $params->set('gallery_layout', $layout);

        $params->set('gallery_search_dir', $folderList);
        $params->set('gallery_search_descrFile', 'ariimageslider.csv');

        return $params;
    }
}