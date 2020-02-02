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

namespace Arimagnificpopup\Layout\Mediagallery;

defined('_JEXEC') or die;

use \Arisoft\Layout\Mediagallery\Mediagallery as MediaGallery;

class Base extends MediaGallery
{
	public $textPrefix = 'PLG_ARIMAGNIFICPOPUP';
	
    protected function prepareItemElements(&$containerAttrs, &$elAttrs)
    {
        if (!is_array($containerAttrs))
            return ;

        $class = $this->getOption('class');
        if (empty($class))
            return ;

        if (!isset($containerAttrs['class']))
            $containerAttrs['class'] = '';

        $containerAttrs['class'] .= ' ' . $class;
    }
}