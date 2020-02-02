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
* This loads before first ajax call is done, this file is called per each shipping html generated
*/
defined('_JEXEC') or die('Restricted access');

// local variables are defined in \components\com_onepage\helpers\transform.php
// $vm_id, $html (is the original output)

$dispatcher = JDispatcher::getInstance();

$result = ''; 
$method = null; 

$returnValues = $dispatcher->trigger('getPluginHtmlOPC', array(&$result, &$method, 'shipment', $vm_id, $cart));

$def_html = $result; 

$file = JPATH_SITE.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'pick_pack.json'; 
jimport( 'joomla.filesystem.file' );
if (file_exists($file))
{
  $data = file_get_contents($file); 
  
  $json = json_decode($data); 
  
  
  if (!empty($json))
 {
   if (!empty($json->OPCtime))
   {
   $time = $json->OPCtime; 
   $now = time(); 
   if (($now - $time) > (24 * 60 * 60)) $refresh = true; 
   }
   else $refresh = true; 
  
 }
 else $refresh = true; 
}
else $refresh = true; 



if ((!empty($refresh)) || (empty($json)))
{

$url = 'http://partner.pickpackpont.hu/stores/validboltlista.xml'; 

$data = OPCloader::fetchUrl($url); 

$json = simplexml_load_string($data); 


if (!empty($json))
{
  $json->OPCtime = time(); 
  
foreach ($json as $key=>$val)
 {
   if (empty($json->$key)) $json->$key = ''; 
 }

 $json_data = json_encode($json); 
 JFile::write($file, $json_data); 
 
 $xmlfile = JPATH_CACHE .DIRECTORY_SEPARATOR. 'validboltlista.xml';
 $_SESSION['vm_ppp_xml'] = $xmlfile;
 
 JFile::write($xmlfile, $data); 
 
}
}


if (!empty($json))
{
$extra = '';
$sel = '<select name="branch_ppp" onchange="opc_zas_change(this, '.$vm_id.');">';
$sel .= '<option data-branch-id="" value="">–– Átvételi Pick Pack Pont... ––</option>';

if (!empty($json->Shop))
foreach ($json->Shop as $branch)
{
foreach ($branch as $key=>$val)
 {
   if (is_object($branch->$key)) 
   {
   $branch->$key = (string)''; 
   
   }
 }
if (!isset($branch->ShopCode)) continue; 
  // if ($branch->country == 'cz') $country = 'ČR'; 
  
  $value = $branch->ZipCode . ' ' . $branch->City . ', ' . $branch->Address
					. ' (' . $branch->Name . ' - ' . $branch->ShopCode . ')'; 
  
  $value = str_replace('"', '\"', $value);
  $extra .= '<input type="hidden" name="branch_data_'.$branch->ShopCode.'" value="'.$value.'" />'; 
  
  //$value = urlencode($value); 
  
  $sel .= '<option data-branch-id="'.$branch->ShopCode.'" value="'.$branch->ShopCode.'">'.$branch->ZipCode.', '.$branch->City.', '.$branch->Address.', '.$branch->Name.'</option>'; 

//extra:
$extra .= '
<div style="padding-top: 8px; clear:both;display: none;" id="zas_branch_'.$branch->ShopCode.'">
 
 
<div style="float: left; clear:right; max-width: 50%;margin:0; padding:0;">'; 

$extra .=' <strong>'.$branch->Name.'</strong><br/>'.$branch->Address.'<br/>'.$branch->ZipCode.' '.$branch->City.'<br />'.$branch->Description.'<br />'; 
  if (!empty($branch->IsBankCard))
  $extra .= 'Bankkártyás fizetés: van<br />'; 
  else
  $extra .= 'Bankkártyás fizetés: nincs<br />'; 
 $extra .= '
</div>
<div style="float: left; clear:right; width: 50%;margin:0; padding:0;">';

  
  $extra .= '<div style="margin-top: 0px;"><b>Nyitva</b></div>';
  if (isset($branch->Monday) && ($branch->Monday != '0:00-0:00'))
  $extra .= '<div >'.mb_convert_case(JText::_('MONDAY'), MB_CASE_TITLE, 'UTF-8').': '.$branch->Monday.'</div><br style="clear:both;display:none;"/>';
  
  if (isset($branch->Tuesday) && ($branch->Tuesday != '0:00-0:00'))
  $extra .= '<div >'.mb_convert_case(JText::_('TUESDAY'), MB_CASE_TITLE, 'UTF-8').': '.$branch->Tuesday.'</div><br style="clear:both;display:none;"/>';
  
 
  
  if (isset($branch->Wednesday) && ($branch->Wednesday != '0:00-0:00'))
  $extra .= '<div >'.mb_convert_case(JText::_('WEDNESDAY'), MB_CASE_TITLE, 'UTF-8').': '.$branch->Wednesday.'</div><br style="clear:both;display:none;"/>';
  if (isset($branch->Thursday) && ($branch->Thursday != '0:00-0:00'))
  $extra .= '<div >'.mb_convert_case(JText::_('THURSDAY'), MB_CASE_TITLE, 'UTF-8').': '.$branch->Thursday.'</div><br style="clear:both;display:none;"/>';
  if (isset($branch->Friday) && ($branch->Friday != '0:00-0:00'))
  $extra .= '<div >'.mb_convert_case(JText::_('FRIDAY'), MB_CASE_TITLE, 'UTF-8').': '.$branch->Friday.'</div><br style="clear:both;display:none;"/>';
  if (isset($branch->Saturday) && ($branch->Saturday != '0:00-0:00'))
  $extra .= '<div >'.mb_convert_case(JText::_('SATURDAY'), MB_CASE_TITLE, 'UTF-8').': '.$branch->Saturday.'</div><br style="clear:both;display:none;"/>';
  if (isset($branch->Sunday) && ($branch->Sunday != '0:00-0:00'))
  $extra .= '<div >'.mb_convert_case(JText::_('SUNDAY'), MB_CASE_TITLE, 'UTF-8').': '.$branch->Sunday.'</div><br style="clear:both;display:none;"/>';
  
  
  $extra .= '
 </div>
 
</div> <input type="hidden" name="branch_id'.$branch->ShopCode.'" id="branch_id'.$branch->ShopCode.'" value="'.$branch->ShopCode.'" />
        <input type="hidden" name="branch_currency'.$branch->ShopCode.'" id="branch_currency'.$branch->ShopCode.'" value="" />
        <input type="hidden" name="branch_name_street'.$branch->ShopCode.'" id="branch_name_street'.$branch->ShopCode.'" value="'.$branch->Address .'"/>';
  
}
$sel .= '</select>'.$extra;
$post = ''; 
if (!defined('ZAS_ONCE'))
{


$post = '<input type="hidden" name="branch_id" id="branch_id" value="">
        <input type="hidden" name="branch_currency" id="branch_currency" value="">
        <input type="hidden" name="branch_name_street" id="branch_name_street" value="">'; 

define('ZAS_ONCE', 1); 
}
else
{

}

$def_html = str_replace('name="virtuemart_shipmentmethod_id"', 'id="shipment_id_'.$vm_id.'" name="virtuemart_shipmentmethod_id"', $def_html); 
$ex = ''; 
//$html = $def_html.'<input type="radio" name="virtuemart_shipmentmethod_id" id="zas_vm_'.$vm_id.'" value="'.$vm_id.'"><div id="opc_zas_place">&nbsp;</div>'.$sel.$ex.$post; 
$html = '<div style="clear: both;">'.$def_html.'<div id="opc_zas_place" style="clear: both;">&nbsp;</div><div for="shipment_id_'.$vm_id.'">'.$sel.'</div>'.$ex.$post.'</div>'; 

}
else
{
 
}

