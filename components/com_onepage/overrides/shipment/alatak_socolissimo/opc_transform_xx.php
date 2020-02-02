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
* possible values: 
* $layout_name, $name, $psType, &$ref, &$method='', &$htmlIn=''
*/
defined('_JEXEC') or die('Restricted access');

$id = $method->virtuemart_shipmentmethod_id; 
if (is_array($htmlIn))
{
foreach ($htmlIn as &$html)
{
 $html = str_replace('id="shipment_id_'.$id.'"', ' onclick="return opc_soco(this);" id="shipment_id_'.$id.'"', $html); 
}
}
else
{
 $htmlIn = str_replace('id="shipment_id_'.$id.'"', ' onclick="return opc_soco(this);" id="shipment_id_'.$id.'"', $htmlIn); 
}