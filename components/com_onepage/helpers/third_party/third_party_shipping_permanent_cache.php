<?php
/* 
*
* @copyright Copyright (C) 2007 - 2012 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* One Page checkout is free software released under GNU/GPL and uses code from VirtueMart
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
* this file is loaded from
*
* \components\com_onepage\overrides\vmplugin.php
* public function plgVmGetSpecificCache
*
* ZIP and Country are default caches and do not need to get added here
*/
defined('_JEXEC') or die('Restricted access');

		switch ($this->_name)
		{
			case 'fedex_multibox':
			case 'jcfedex': 
				$residential = (empty($to_address['company'])) ? true : false;
				if (function_exists('mb_strtolower'))
				$hash = mb_strtolower(@$to_address['address_1'].@$to_address['address_2'].@$to_address['city']).@$to_address['virtuemart_state_id'].$residential.JRequest::getVar('fedex_rate', '');
				else
				$hash = strtolower(@$to_address['address_1'].@$to_address['address_2'].@$to_address['city']).@$to_address['virtuemart_state_id'].$residential.JRequest::getVar('fedex_rate', '');
				
				return $hash; 
			default: 
				return $this->_name; 
		}
