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
* loaded from: \components\com_onepage\controllers\opc.php
* function runExt()
* 
*/
defined('_JEXEC') or die('Restricted access');

$vm_id = JRequest::getVar('virtuemart_shipmentmethod_id', 0); 

if (strpos($vm_id, ':')!==false) {
$fi = explode(':', $vm_id); 

$cart->virtuemart_shipmentmethod_id = $fi[0]; 

}