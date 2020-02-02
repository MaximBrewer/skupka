<?php
/*
 * ARI Framework
 *
 * @package		ARI Framework
 * @version		1.0.0
 * @author		ARI Soft
 * @copyright	Copyright (c) 2010 www.ari-soft.com. All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
 *
 */

namespace Arisoft\Joomla;

defined('_JEXEC') or die;

use JHtml, JURI, JFactory;

class Helper
{
    static private $jsHelperLoaded = false;

    static public function registerJsHelper()
    {
        if (self::$jsHelperLoaded)
            return ;

        $uri = JURI::root(true). '/media/arisoft/';
        $doc = JFactory::getDocument();

        JHtml::_('jquery.framework', true);
        $doc->addScript($uri . 'joomla/helper.js');

        self::$jsHelperLoaded = true;
    }
}