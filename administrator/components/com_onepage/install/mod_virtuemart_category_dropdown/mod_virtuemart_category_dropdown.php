<?php
// no direct access
defined('_JEXEC') or die;
require_once(__DIR__.DIRECTORY_SEPARATOR.'helper.php'); 
modVirtuemartCategorydropdownHelper::loadVm(); 

$url = Juri::root(); 
if (substr($url, -1) != '/') $url .= '/'; 
JHtml::script($url.'modules/mod_virtuemart_category_dropdown/helper.js'); 
JHtml::_('jquery.framework');
// end stAn


 $lang = modVirtuemartCategorydropdownHelper::getLangCode(); 
 if (!empty($lang)) $lang = '&lang='.$lang; 
 
JFactory::getLanguage()->load('mod_virtuemart_category_dropdown'); 
JFactory::getLanguage()->load('mod_virtuemart_category_dropdown', JPATH_SITE.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.'mod_virtuemart_category_dropdown'); 
JFactory::getLanguage()->load('mod_virtuemart_category_dropdown',__DIR__); 

if (is_object($module)) {
$module_id = $module->id; 
}

$module_id = (int)$module_id; 
if (empty($module_id)) $module_id = 0; 


$x = get_defined_vars(); 

if (empty($params)) 
{
	if ((is_object($module)) && (isset($module->params))) {
	  $pstring = $module->params; 
	}
	else
	{
		$pstring = json_encode(''); 
	}
	$params = new JRegistry($pstring); 
}


$myid = ''; 
$mid = $params->get('my_item_id', ''); 
if (!empty($mid))
$myid = '&my_item_id='.$mid; 

$maxlevel = 0; 
$top = modVirtuemartCategorydropdownHelper::getTopCats(); 




for ($i=1; $i<=5; $i++) { 
	$key = 'level'.$i.'_text'; 
	$text = $params->get($key, ''); 
	
	if (empty($text)) break; 
    $maxlevel = $i; 
}


$depth = modVirtuemartCategorydropdownHelper::getD($params); 
$product_id = JRequest::getVar('virtuemart_product_id', 0); 
$category_id = JRequest::getVar('virtuemart_category_id', 0); 



if ((!empty($product_id)) && (!empty($category_id)))
{
	$path = modVirtuemartCategorydropdownHelper::getPath($category_id, $depth); 
	
}
else
if (!empty($category_id))
	{
		$path = modVirtuemartCategorydropdownHelper::getPath($category_id, $depth); 
	}
else
{
	
}



$prevSelected = 0; 

for ($i=1; $i<=$maxlevel; $i++) {
 $z = $i; 
 $key = 'value'.$z;
 if (!empty($path[$key])) $topSelected = (int)$path[$key]; 
 else $topSelected = 0; 
 
 
 
 $hasR = false; 
 if ($i === 1)
 $cats[$i] = modVirtuemartCategorydropdownHelper::getCats(0, $topSelected, $hasR, $i, $module_id); 


 
 if (($i > 1) && (!empty($prevSelected)))
 {
 $cats[$i] = modVirtuemartCategorydropdownHelper::getCats($prevSelected, $topSelected, $hasR, $i, $module_id); 
 
 }
 /*
 if (!empty($cats[$i])) {
 $cx = implode("\n", $cats[$i]); 
 $cats[$i] = $cx; 
 }
 else
 {
	
	
 }
 */
 if (!empty($topSelected))
 $prevSelected = $topSelected; 
 
}


$options = modVirtuemartCategorydropdownHelper::getProducts($prevSelected, $product_id, $module_id); 
$options = implode("\n", $options); 



 
?>

<script type="text/javascript">
//<![CDATA[ 
if (typeof showProducts == 'undefined') var showProducts = new Array(); 
if (typeof catDropDowncaturl == 'undefined') var catDropDowncaturl = new Array(); 

showProducts[<?php echo $module_id; ?>] = <?php 
$showProducts = $params->get('showproducts', false); 
if (!empty($showProducts)) echo 'true'; 
else echo 'false'; 
?>; 

catDropDowncaturl[<?php echo $module_id; ?>] = '<?php echo JRoute::_($url.'index.php?option=com_ajax&module=virtuemart_category_dropdown&format=raw&nosef=1'.$lang.$myid.'&maxlevel='.$maxlevel); ?>&showProducts=<?php echo (int)$showProducts; ?>&module_id=<?php echo (int)$module_id; ?><?php 

$itemid = JRequest::getInt("Itemid", 0); 
if (!empty($itemid)) echo '&Itemid='.$itemid; 
?>'; 


//]]>
</script>
<?php

//modVirtuemartCategorydropdownHelper::getDepth(); 
$api=JFactory::getApplication();
		
		
		
require JModuleHelper::getLayoutPath('mod_virtuemart_category_dropdown', $params->get('layout', 'default'));

