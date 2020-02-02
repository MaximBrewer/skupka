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

namespace Arimagnificpopup\Plugin\Contenttype;

defined('_JEXEC') or die;

use Arisoft\Utilities\ObjectFactory as ObjectFactory;

abstract class Base 
{
	private $name = null;

    public function getName()
    {
        if (is_null($this->name))
        {
			$name = explode('\\', strtolower(get_class($this)));
			$this->name = $name[count($name) - 1];
        }

        return $this->name;
    }
	
	public function processContent($attrs, $params, $content)
	{
		$preparedParams = $this->prepareParams($attrs, $params, $content);
		$handler = ObjectFactory::getObject($this->getName(), 'Arimagnificpopup\\Contenthandler\\Handler');
		
		if (is_null($handler))
			return '<div style="color:red;">' . JText::sprintf('PLG_ARIMAGNIFICPOPUP_WARNING_TYPEISNOTSUPPORTED', $this->getName()) . '</div>';
		
		return $handler->processContent($preparedParams, $content);
	}

	protected function prepareParams($attrs, $params, $content)
	{
		return $params;
	}
}