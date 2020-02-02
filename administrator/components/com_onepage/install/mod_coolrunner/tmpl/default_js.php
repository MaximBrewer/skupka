<?php if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );
   $root = Juri::root(); 
		if (substr($root, -1)!=='/') $root .= '/'; 
		
		JHtml::script($root.'modules/mod_coolrunner/media/spin.min.js'); 
		JHtml::script($root.'modules/mod_coolrunner/media/droppoints_all.js'); 
		JHtml::script($root.'modules/mod_coolrunner/media/opc.js'); 
				JHtml::script($root.'modules/mod_coolrunner/media/jquery.cookie.js'); 
				$key = $params->get('mapkey', ''); 
				$clientid = $params->get('clientid', '');
				$q = ''; 
				if (!empty($clientid)) {
					$q .= '&client='.$clientid; 
					$q .= '&v=3.26'; 
				}
				else {
					$q .= '&key='.$key; 
				}
				
				//$q .= '&callback=AllDroppoints.initmap'; 
				$gurl = '//maps.googleapis.com/maps/api/js?language=auto'.$q; 
				
				$doc = JFactory::getDocument();
				$doc->addScript($gurl, 'text/javascript', true, true);
		//JHtml::script('//maps.googleapis.com/maps/api/js?language=auto'.$q); 
        //JHtml::script($root.'modules/mod_coolrunner/media/markerclusterer_compiled.js'); 
		JHtml::script($root.'modules/mod_coolrunner/media/markerclusterer.js'); 
		JHtml::script($root.'modules/mod_coolrunner/media/jquery.simplemodal.js'); 
		JHtml::stylesheet('//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.css'); 
		JHtml::stylesheet($root.'modules/mod_coolrunner/media/module.css'); 
		JHtml::stylesheet($root.'modules/mod_coolrunner/media/jquery.simplemodal.css'); 
		//JHtml::stylesheet($root.'modules/mod_coolrunner/media/indleveringssteder.css'); 
		//indleveringssteder.css
	


$db = JFactory::getDBO(); 
$q = 'select `virtuemart_shipmentmethod_id` from `#__virtuemart_shipmentmethods` where `shipment_element` = "rupostel_coolrunner" and `published` = 1'; 
$res = array(); 
try
{
$db->setQuery($q); 
$res = $db->loadAssocList(); 



if (empty($res)) $res = array(); 
}
catch (Exception $e)
{

	// do nothing... 
}
$dao = $post = $gls = array(); 
$js2 = ''; 

$shipping_methods = $params->get('shipping_methods_post', ''); 
if (!empty($shipping_methods)) {
$ex = explode(',',$shipping_methods); 
if (is_array($ex))
{	
foreach ($ex as $s)
{
	$s = trim($s); 
	$a = array(); $a['virtuemart_shipmentmethod_id'] = (int)$s; 
	$res[] = $a; 
	$s = (int)$s; 
	if (!empty($s)) { 
	$post[] = $s; 
	$js2 .= ' coolrunner_methods_post.push('.$s.'); '; 
	}
}
}
}

$shipping_methods = $params->get('shipping_methods_dao', ''); 
if (!empty($shipping_methods)) {
$ex = explode(',',$shipping_methods); 
if (is_array($ex))
{	
foreach ($ex as $s)
{
	$s = trim($s); 
	$a = array(); $a['virtuemart_shipmentmethod_id'] = (int)$s; 
	$res[] = $a; 
	$s = (int)$s; 
	if (!empty($s)) { 
	$dao[] = (int)$s; 
	$js2 .= ' coolrunner_methods_dao.push('.$s.'); '; 
	}
}
}
}

$shipping_methods = $params->get('shipping_methods_gls', ''); 
if (!empty($shipping_methods)) {
$ex = explode(',',$shipping_methods); 
if (is_array($ex))
{	
foreach ($ex as $s)
{
	$s = trim($s); 
	$a = array(); $a['virtuemart_shipmentmethod_id'] = (int)$s; 
	$res[] = $a; 
	$s = (int)$s; 
	if (!empty($s)) { 
	$gls[] = $s; 
	$js2 .= ' coolrunner_methods_gls.push('.$s.'); '; 
	}
}
}
}

$js = ' var coolrunner_methods = []; var coolrunner_methods_post = []; var coolrunner_methods_dao = []; var coolrunner_methods_gls = [];'; 
$n = array(); 
foreach ($res as $row) {
	$k = (int)$row['virtuemart_shipmentmethod_id']; 
	$n[$k] = $k; 
}

foreach ($n as $v)
{
	$js .= ' coolrunner_methods.push('.$v.'); '; 
}
$js .= " var siteUrl = '".$root."'; "; 



$doc = JFactory::getDocument(); 
if (method_exists($doc, 'addScriptDeclaration')) { 
$doc->addScriptDeclaration("\n".$js.' '.$js2."\n"); 
}


