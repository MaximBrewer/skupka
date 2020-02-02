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

class Url extends EngineBase
{
    protected function prepareParams($attrs, $params, $content)
    {
        $url = ArrayHelper::getValue($attrs, 'url', '');
		$params->set('url_url', $url);
		
		return $params;
	}
}