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
*/

// load OPC loader
//require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'loader.php'); 

defined('_JEXEC') or die('Restricted access');

// called from public static function overridePaymentHtml(&$html, $cart, $vm_id=0, $name, $type)

$html2 = '<br/><a href="'.JRoute::_('index.php?option=com_virtuemart&view=cart&task=editpayment&Itemid=' . JRequest::getInt('Itemid'), false).'">'.JText::_('VMPAYMENT_PAYPAL_CC_ENTER_INFO').'</a>';

$html = str_replace($html2, '', $html); 
$html = str_replace('class="paymentMethodOptions" style="display: none;"', 'class="paymentMethodOptions" ', $html); 
