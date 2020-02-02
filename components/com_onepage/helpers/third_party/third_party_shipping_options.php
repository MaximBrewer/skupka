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
* Loaded from: \components\com_onepage\helpers\transform.php
*
* Uses $newoptions for adding options to select drop down of shipping, 
* public vars $num (number of shipping methods), $id[$k] -> current id of the shipping method, $value[$k] -> current value of the shippng method, $shipment_html -> current shipping being rendered
*
*/
defined('_JEXEC') or die('Restricted access');
  
// local variable: $html
if (strpos($shipment_html, 'data-cpsol')!==false)
				{
				   
				 
				  
				  
				  $dataa = OPCTransform::getFT($shipment_html, 'input', $multi, 'type', 'radio', '>', 'data-cpsol');  
				  $data = @json_decode($dataa[0], true); 
				  //$test = $currencyDisplay->createPriceDiv($product_price_display,'', '10',false,false, 1);
				  $price = $currencyDisplay->priceDisplay($data['rate']);
				  
				  $msg = $data['name'].', Estimated Delivery Date: '.$data['deliveryDate'].' ('.$price.')'; 
				  
				  $newoptions .= '<option value="'.$value[$k].'" id="'.$id[$k].'"'; 
				 //if ($hasextra)
					$newoptions .= ' rel="'.$id[$k].'" data-cpsol="'.$dataa[0].'"' ; 
				 $html = $msg; 
				 $newoptions .= '>'.$html.'</option>'; 
				 $num++; 
				 
				}

if (strpos($shipment_html, 'opc_zas_change')!==false)
{
  //getPluginNameOPC
$dispatcher = JDispatcher::getInstance();

$result = ''; 
$method = null; 

$returnValues = $dispatcher->trigger('getPluginHtmlOPC', array(&$result, &$method, 'shipment', $value[$k], $cart));

$name = $method->OPCname; 
$priceS = $method->OPCsalesprice; 
$price = $currencyDisplay->priceDisplay($priceS);
 
  
$file = JPATH_SITE.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'zasilkovna.json'; 
jimport( 'joomla.filesystem.file' );
if (file_exists($file))
{
  $data = file_get_contents($file); 
  
  $json = json_decode($data); 
  
  
  if (!empty($json))
 {
   $time = $json->OPCtime; 
   $now = time(); 
   if (($now - $time) > (24 * 60 * 60)) $refresh = true; 
   
  
 }
 else $refresh = true; 
}
else $refresh = true; 



if ((!empty($refresh)))
{
$zas_model = VmModel::getModel('zasilkovna');
$url = $zas_model->_zas_url.'api/v2/'.$zas_model->api_key.'/branch.json'; 
$data = OPCloader::fetchUrl($url); 

$json = @json_decode($data); 
if (!empty($json))
{
  $json->OPCtime = time(); 
  $data = @json_encode($json); 
}

 JFile::write($file, $data); 
}



if (!empty($json))
{

foreach ($json->data as $branch)
{
  // if ($branch->country == 'cz') $country = 'ČR'; 
  $cc = $branch->country; 
  if (!empty($method->country))
  if (!in_array($cc, $method->country)) 
  {
    continue; 
  }
  $country = $json->countries->$cc; 
  //data="\"{ \"branch_id\":\"'.$branch->id.'\", \"branch_currency\":\"'.$branch->currency.'\", \"branch_name_street\": \"'.$branch->nameStreet.'\"}\"" 
  // $rel_id is used to get proper calculation
  // $saved_id is used to retrieve additional info by opc controller at checkout
  $newoptions .= '<option data-branch-id="'.$branch->id.'" rel="'.$id[$k].'" saved_id="zasilkovna_'.$id[$k].'_'.$branch->id.'"  rel_id="'.$id[$k].'" value="'.$value[$k].'" extra_value="'.$branch->id.'">'.$name.': '.$country.', '.$branch->nameStreet.'('.$price.')</option>'; 
  
  
  $na = array(); 
  $na['branch_id'] = $branch->id; 
  $na['branch_name_street'] = $branch->nameStreet; 
  $na['branch_currency'] = $branch->currency; 
  $data = json_encode($na); 
  $newjson = '<input type="hidden" name="zasilkovna_'.$id[$k].'_'.$branch->id.'_extrainfo" value="'.base64_encode($data).'" />'; 
  
  $md5 = md5($newjson); 
  OPCloader::$inform_html[$md5] = $newjson; 
  
  /*
  if (class_exists('basketHelper'))
  basketHelper::$totals_html .= $newjson; 
  */
  
}


if (!defined('ZAS_ONCE'))
{
$post = '<input type="hidden" name="branch_id" id="branch_id" value="">
        <input type="hidden" name="branch_currency" id="branch_currency" value="">
        <input type="hidden" name="branch_name_street" id="branch_name_street" value="">'; 

		$md5 = md5($post); 
		OPCloader::$inform_html[$md5] = $post; 
		define('ZAS_ONCE', 1); 
}



}
}