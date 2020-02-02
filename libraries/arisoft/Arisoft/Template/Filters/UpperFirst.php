<?php
/*
 * ARI Framework
 *
 * @package		ARI Framework
 * @version		1.0.0
 * @author		ARI Soft
 * @copyright	Copyright (c) 2009 www.ari-soft.com. All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
 *
 */
namespace Arisoft\Template\Filters;

defined('_JEXEC') or die;

use Arisoft\Template\Filter as Filter;

class UpperFirst extends Filter
{
	public function parse($val, $params)
	{
		return ucfirst(strtolower($val));
	}	
}