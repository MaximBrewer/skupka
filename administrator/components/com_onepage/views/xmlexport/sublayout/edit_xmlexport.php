<?php
/**
 * @version		
 * @package		RuposTel OnePage Utils
 * @subpackage	com_onepage
 * @copyright	Copyright (C) 2005 - 2013 RuposTel.com
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

foreach ($this->opc_forms as $form)
{
	?><fieldset class="adminform"><?php
	echo $form; 
	
	
	?></fieldset><?php
}
