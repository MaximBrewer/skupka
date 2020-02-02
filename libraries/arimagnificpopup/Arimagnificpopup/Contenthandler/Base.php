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

namespace Arimagnificpopup\Contenthandler;

defined('_JEXEC') or die;

abstract class Base
{
	public function processContent($params, $content)
	{
		return '';
	}

    protected function getLayoutContent($layout)
    {
        $layoutMethod = 'layout_' . ucfirst(strtolower($layout));

        if (!method_exists($this, $layoutMethod))
            return '';

        $args = func_get_args();
        array_shift($args);

        $res = call_user_func_array(array($this, $layoutMethod), $args);

        return $res;
    }
}