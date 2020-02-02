<?php
/**
 * @version		$Id: default.php 21837 2011-07-12 18:12:35Z dextercowley $
 * @package		RuposTel OnePage Utils
 * @subpackage	com_onepage
 * @copyright	Copyright (C) 2005 - 2013 RuposTel.com
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
$document = JFactory::getDocument();

JHTMLOPC::script('opcbe.js', 'administrator/components/com_onepage/assets/js/', false);
$document->setTitle(JText::_('COM_ONEPAGE_UTILS')); 
$document->addStyleDeclaration('
#toolbar-box { display: none; } ');

$default_config = array('vm_lang'=>0, 'vm_menu_en_gb'=>0, 'selected_menu'=>0, 'menu_'=>0, 'tojlanguage'=>'*'); 
$session = JFactory::getSession(); 
$config = $session->get('opc_utils', $default_config); 

if (!version_compare(JVERSION,'2.5.0','ge'))
{
  $j15 = true; 
  
}
$defaults = $this->defaults; 



?>
<form action="index.php" method="post">
 <?php if (!empty($this->cats)) 

{
 ?>
<fieldset <?php if (!empty($j15)) echo ' disabled="disabled" '; ?> >
<legend><?php echo JText::_('COM_ONEPAGE_UTILS_VM_TO_J_LABEL'); ?></legend>
<?php if (!empty($j15)) echo '<div>'.JText::_('COM_ONEPAGE_ONLY_J25').'</div>'; ?>
<div><?php echo JText::_('COM_ONEPAGE_UTILS_DESC'); ?></div>
<div><?php echo '<div style="color: red;">'.JText::_('COM_ONEPAGE_UTILS_NOTE').' </div>'; echo JText::_('COM_ONEPAGE_UTILS_NOTE2'); ?>
</div><br />
<table  >
<tr>
<td>
<?php 

echo JText::_('COM_ONEPAGE_UTILS_SELECT_VM_CHILD'); ?><br /><?php echo JText::_('COM_ONEPAGE_UTILS_SELECT_VM_CHILD_DESC'); ?>
</td>
<td>
<select name="vm_lang" onchange="return op_unhideMenuVM(this);">
<?php
if (empty($this->cats)) $this->cats = array(); 
foreach ($this->cats as $lang=>$arr)
{
  echo '<option '; 
  if (!empty($config['vm_lang']) && ($lang==$config['vm_lang'])) 
  {
  $first_lang = $config['vm_lang']; 
  echo ' selected="selected" '; 
  }
  if (empty($config['vm_lang']))
  if (!isset($first_lang))
  $first_lang = $lang; 
  
  echo ' value="'.$lang.'">'.$lang.'</option>'; 
}
?>
</select>
<?php
if (empty($this->cats))  $this->cats = array(); 
foreach ($this->cats as $lang=>$arr)
{
 if (!empty($config['vm_menu_'.$first_lang])) $first_vm = $config['vm_menu_'.$first_lang];  
 
 if (!isset($first_vm))
 $first_vm = $lang;
?><select <?php if ($lang != $first_lang) echo ' style="display: none;" ';  ?> name="vm_menu_<?php echo $lang; ?>" id="vm_menu_<?php echo $lang; ?>"  >
<option value="0">--- <?php echo JText::_('COM_ONEPAGE_UTILS_ALL'); ?> ---</option>
<?php

foreach ($arr as $key2=>$mymenu)
{
?>

<?php


   if (!isset($mymenu['virtuemart_category_id'])) continue; 
   echo '<option '; 
   if (!empty($config['vm_menu_'.$first_lang]) && ($mymenu['virtuemart_category_id'] == $config['vm_menu_'.$first_lang]))
   echo ' selected="selected" '; 
   echo ' value="'.$mymenu['virtuemart_category_id'].'">'.$mymenu['category_name'].'</option>'; 
   // recursion here: 
   if (!empty($mymenu['children']))
   $this->printChildren($mymenu['children'], 'virtuemart_category_id', 'category_name', '->');
 
}
?>
</select>

<?php
}
?>
</td>
</tr>
<tr>
<td>


<?php echo JText::_('COM_ONEPAGE_UTILS_TO_MOVE_JOOMLA_MENU'); ?>
</td>
<td>
<select name="selected_menu" onchange="return op_unhideMenu(this);">
<option value="0">--- <?php echo JText::_('COM_ONEPAGE_UTILS_NEW'); ?> ---</option>

<?php 
if (empty($config['selected_menu'])) $first = 0; 
else $first = $config['selected_menu']; 
foreach ($this->menus as $menu)
{
 //$first = $menu['menutype']; 
 echo '<option value="'.$menu['menutype'].'" '; 
 if ($menu['menutype'] == $config['selected_menu']) echo ' selected="selected" '; 
 echo '>'.$menu['title'].'</option>'; 
}
?>
</select>
</td>
</tr>
<tr>
<td>

<script type="text/javascript">
 var last_menu = 'menu_<?php echo $first; ?>'; 
 var last_menu_vm = 'vm_menu_<?php echo $first_lang; ?>'; 
</script>
<?php echo JText::_('COM_ONEPAGE_UTILS_WITH_PARENT_MENU_ITEM'); ?>
</td>
<td>
<select name="menu_0" id="menu_0" <?php if (!empty($first)) echo ' style="display: none;" '; ?> disabled="disabled" ><option value="">-</option></select>
<?php
foreach ($this->sortedmenu as $key2=>$m)
{
?>
<select name="menu_<?php echo $key2; ?>" id="menu_<?php echo $key2; ?>" <?php if ($key2 !== $first) echo ' style="display: none;" '; ?> >
<option value="1">--- <?php echo JText::_('COM_ONEPAGE_UTILS_TOP'); ?> ---</option>
<?php

foreach ($m as $key=>$mymenu)
 {
   if (empty($key)) continue; 
   if (!isset($mymenu['published'])) continue;
   if ($mymenu['published']<0) continue; 
   if (!isset($mymenu['id'])) { 
   continue; 
    }
   
   echo '<option '; 
if ((!empty($config['menu_'.$key2])) && ($config['menu_'.$key2]==$mymenu['id'])) echo ' selected="selected" ';    
   echo ' value="'.$mymenu['id'].'">'.$mymenu['item_type'].'</option>'; 
   // recursion here: 
   if (!empty($mymenu['children']))
   $this->printChildren($mymenu['children'], 'id', 'item_type', '->');
 }
?>
</select>
<?php

}
?>
</td>
</tr>
<tr>
<td>
<?php
$lang =  JFactory::getLanguage(); 
$langs = $lang->getKnownLanguages(); 
echo ''.JText::_('COM_ONEPAGE_UTILS_INS_TO_LANG'); 
?>
</td>
<td>
<?php
echo '<select name="tojlanguage">'; 
echo '<option value="*">All</option>'; 
foreach ($langs as $key=>$l)
{
  echo '<option ';
  if (!empty($config['tojlanguage']) && ($config['tojlanguage']==$key)) echo ' selected="selected" '; 
  echo ' value="'.$key.'">'.$l['name'].'</option>'; 
}
echo '</select>'; 
?>
</td>
</tr>
</table>
<input type="hidden" name="option" value="com_onepage" />
<input type="hidden" name="task" value="movemenu" />
<input type="hidden" name="view" value="utils" />
<?php if (!empty($this->cats)) { ?>
<input type="submit" name="Proceed" class="btn button btn-primary" />
<?php } ?>
</fieldset>
<?php 
} ?>
</form> 
<form action="index.php" method="post" id="fulltextsearch">
<fieldset <?php if (!empty($j15)) echo ' disabled="disabled" '; ?> >
<legend ><?php echo JText::_('COM_ONEPAGE_UTILS_SEARCH_FULLTEXT'); ?></legend>
<div><?php echo JText::_('COM_ONEPAGE_UTILS_SEARCH_DESC'); ?></div>

<table>
<tr>
<td>Search for text in your joomla installation: 
</td>
<td><input type="text" value="<?php if (!empty($config['searchtext'])) echo $config['searchtext']; else echo JRequest::getVar('searchwhat', ''); ?>" name="searchwhat" />
</td>
<td>
<select name="ext">
 <option <?php if (JRequest::getVar('ext', '') === '*') echo ' selected="selected" '; ?> value="*">*.*</option>
 <option <?php if (JRequest::getVar('ext', '') === 'css') echo ' selected="selected" '; ?> value="css">*.css</option>
 <option <?php if (JRequest::getVar('ext', '') === 'php') echo ' selected="selected" '; ?>  value="php">*.php</option>
 <option <?php if (JRequest::getVar('ext', '') === 'gif') echo ' selected="selected" '; ?>  value="gif">*.gif</option>
 <option <?php if (JRequest::getVar('ext', '') === 'css') echo ' selected="selected" '; ?>  value="css">*.css</option>
 <option <?php if (JRequest::getVar('ext', '') === 'js') echo ' selected="selected" '; ?>  value="js">*.js</option>
</select>
<input type="text" placeholder="Custom extension search" alt="Input something like *.ini" value="<?php echo JRequest::getVar('custom_ext', ''); ?>" name="custom_ext" />
</td>
</tr>
<tr><td>
<input type="checkbox" name="excludecache" value="1" <?php 
$x = JRequest::getVar('xc', -1); 
if ($x === -1) echo ' checked="checked" '; 
if (!empty($x)) echo ' checked="checked" '; 
?> id="excludecache" />
</td>
<td>
<label for="excludecache">Exclude cache</label>
</td>
</tr>
<tr><td><input type="checkbox" name="casesensitive" value="1" <?php 
$x = JRequest::getVar('cs', -1); 
if ($x === -1) echo ' checked="checked" '; 
if (!empty($x)) echo ' checked="checked" '; 
?> id="casesensitive" /></td><td><label for="casesensitive">Case sensitive</label></td></tr>
<tr><td><input type="checkbox" name="onlysmall" value="1" <?php 
$x = JRequest::getVar('os', -1); 
if ($x === -1) echo ' checked="checked" '; 
if (!empty($x)) echo ' checked="checked" '; 
?> id="onlysmall" /></td><td><label for="onlysmall">Only smaller than 500kb</label></td></tr>
<tr>
<td><input type="submit" class="btn button btn-primary"/></td></tr></table>

<div style="background-color: #e2e8e2;"><?php echo $this->results; ?></div>

</fieldset>


<input type="hidden" name="option" value="com_onepage" />
<input type="hidden" name="task" value="searchtext" />
<input type="hidden" name="view" value="utils" />

</form>
<form action="index.php" method="post">
<fieldset >
<legend>Search for BOM in php files</legend>
<input type="hidden" name="option" value="com_onepage" />
<input type="hidden" name="task" value="searchbom" />
<input type="hidden" name="view" value="utils" />
<input type="submit" class="btn button btn-primary" value="Search for BOM"/>
</fieldset>
</form>


<?php
$langs = VmConfig::get('active_languages', array()); 

$do_unset = false; 
require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 	
if (!in_array('en-GB', $langs) and OPCmini::tableExists('virtuemart_products_en_gb')) {
  $langs['en-GB'] = 'en-GB'; 
  $do_unset = true; 
}
if (!empty($langs)) { 

?>
<form action="index.php" method="post" id="productcopy">
<fieldset >
<legend>Copy all <em>products</em> between missing language tables</legend><p> (will copy all <em>from language</em> entries into <em> to language</em> tables. At the end you can remove those products which are not available at any language tables with the below command.</p>
<input type="hidden" name="option" value="com_onepage" />
<input type="hidden" name="task" value="copyproducts" />
<input type="hidden" name="view" value="utils" />
<input type="submit" class="btn button btn-primary" value="Copy Products Names and Descriptinios from Language to Language"/>

From language table: 
<select name="lang_from">
<?php
$db = JFactory::getDBO(); 
foreach ($langs as $l) { 

$lt = strtolower($l); 
$lt = str_replace('-', '_', $lt); 
$q = 'select count(virtuemart_product_id) from `#__virtuemart_products_'.$lt.'` where 1=1'; 
$db->setQuery($q); 
$c = $db->loadResult(); 
$c = (int)$c; 

echo '<option value="'.$l.'">'.$l.' ('.$c.')</option>';  }
?>
</select>

To language table: 
<select name="lang_to">
<?php
foreach ($langs as $l) { echo '<option value="'.$l.'">'.$l.'</option>'; }
?>
</select>

</fieldset>
</form>

<?php 

if ($do_unset) { 
  unset($langs['en-GB']); 
}

} 

if (!in_array('en-GB', $langs) and OPCmini::tableExists('virtuemart_categories_en_gb')) {
	$langs['en-GB'] = 'en-GB'; 
}
if (!empty($langs)) { 

?>
<form action="index.php" method="post" id="categorycopy">
<fieldset >
<legend>Copy all <em>categories</em> between language tables</legend>
<input type="hidden" name="option" value="com_onepage" />
<input type="hidden" name="task" value="copycategories" />
<input type="hidden" name="view" value="utils" />
<input type="submit" class="btn button btn-primary" value="Copy Category Names and Descriptinios from Language to Language"/>

From language table: 
<select name="lang_from">
<?php
$db = JFactory::getDBO(); 
foreach ($langs as $l) { 

$lt = strtolower($l); 
$lt = str_replace('-', '_', $lt); 
$q = 'select count(virtuemart_category_id) from `#__virtuemart_categories_'.$lt.'` where 1=1'; 
$db->setQuery($q); 
$c = $db->loadResult(); 
$c = (int)$c; 

echo '<option value="'.$l.'">'.$l.' ('.$c.')</option>';  }
?>
</select>

To language table: 
<select name="lang_to">
<?php
foreach ($langs as $l) { echo '<option value="'.$l.'">'.$l.'</option>'; }
?>
</select>

</fieldset>
</form>
<?php 

if ($do_unset) { 
  unset($langs['en-GB']); 
}
} 



if (!in_array('en-GB', $langs) and OPCmini::tableExists('virtuemart_shipmentmethods_en_gb')) {
	$langs['en-GB'] = 'en-GB'; 
}


if (!empty($langs)) { 

?>
<form action="index.php" method="post" id="shipmentcopy">
<fieldset >
<legend>Copy all <em>shipment methods</em> between missing language tables</legend>
<input type="hidden" name="option" value="com_onepage" />
<input type="hidden" name="task" value="copyshipments" />
<input type="hidden" name="view" value="utils" />
<input type="submit" class="btn button btn-primary" value="Copy Shipment Method Names and Descriptinios from Language to Language"/>

From language table: 
<select name="lang_from">
<?php
$db = JFactory::getDBO(); 
foreach ($langs as $l) { 

$lt = strtolower($l); 
$lt = str_replace('-', '_', $lt); 
$q = 'select count(virtuemart_shipmentmethod_id) from `#__virtuemart_shipmentmethods_'.$lt.'` where 1=1'; 
$db->setQuery($q); 
$c = $db->loadResult(); 
$c = (int)$c; 

echo '<option value="'.$l.'">'.$l.' ('.$c.')</option>';  }
?>
</select>

To language table: 
<select name="lang_to">
<?php
foreach ($langs as $l) { echo '<option value="'.$l.'">'.$l.'</option>'; }
?>
</select>

</fieldset>
</form>
<?php 

if ($do_unset) { 
  unset($langs['en-GB']); 
}

} 



if (!in_array('en-GB', $langs) and OPCmini::tableExists('virtuemart_paymentmethods_en_gb')) {
	$langs['en-GB'] = 'en-GB'; 
}
if (!empty($langs)) { 

?>
<form action="index.php" method="post" id="paymentcopy">
<fieldset >
<legend>Copy all <em>payment methods</em> between missing language tables</legend>
<input type="hidden" name="option" value="com_onepage" />
<input type="hidden" name="task" value="copypayments" />
<input type="hidden" name="view" value="utils" />
<input type="submit" class="btn button btn-primary" value="Copy Payment Method Names and Descriptinios from Language to Language"/>

From language table: 
<select name="lang_from">
<?php
$db = JFactory::getDBO(); 
foreach ($langs as $l) { 

$lt = strtolower($l); 
$lt = str_replace('-', '_', $lt); 
$q = 'select count(virtuemart_paymentmethod_id) from `#__virtuemart_paymentmethods_'.$lt.'` where 1=1'; 
$db->setQuery($q); 
$c = $db->loadResult(); 
$c = (int)$c; 

echo '<option value="'.$l.'">'.$l.' ('.$c.')</option>';  }
?>
</select>

To language table: 
<select name="lang_to">
<?php
foreach ($langs as $l) { echo '<option value="'.$l.'">'.$l.'</option>'; }
?>
</select>

</fieldset>
</form>

<?php 

if ($do_unset) { 
  unset($langs['en-GB']); 
}

}



if (!empty($langs)) { 

?>
<form action="index.php" method="post" id="paymentcopy">
<fieldset >
<legend>Copy all <em>manufacturers</em> between missing language tables</legend>
<input type="hidden" name="option" value="com_onepage" />
<input type="hidden" name="task" value="copymanufs" />
<input type="hidden" name="view" value="utils" />
<input type="submit" class="btn button btn-primary" value="Copy Manufacturers Names and Descriptions from Language to Language"/>

From language table: 
<select name="lang_from">
<?php
$db = JFactory::getDBO(); 
foreach ($langs as $l) { 

$lt = strtolower($l); 
$lt = str_replace('-', '_', $lt); 
$q = 'select count(virtuemart_manufacturer_id) from `#__virtuemart_manufacturers_'.$lt.'` where 1=1'; 
$db->setQuery($q); 
$c = $db->loadResult(); 
$c = (int)$c; 

echo '<option value="'.$l.'">'.$l.' ('.$c.')</option>';  }
?>
</select>

To language table: 
<select name="lang_to">
<?php
foreach ($langs as $l) { echo '<option value="'.$l.'">'.$l.'</option>'; }
?>
</select>

</fieldset>
</form>

<?php 

if ($do_unset) { 
  unset($langs['en-GB']); 
}

}
?>





<form action="index.php" method="post">
<fieldset >
<legend>Remove products not available in language table</legend><p>This will remove all products which are not available in language table of selected language. Affected tables: #__virtuemart_products, #__virtuemart_product_en_gb (and rest of the language tables), #__virtuemart_product_medias, #__virtuemart_product_categories</p>
<input type="hidden" name="option" value="com_onepage" />
<input type="hidden" name="task" value="removeproducts" />
<input type="hidden" name="view" value="utils" />


Remove products NOT available in this table: 
<select name="lang_check">
<?php


foreach ($langs as $l) { echo '<option value="'.$l.'">'.$l.'</option>'; }
?>
</select>

<input type="submit" class="btn button btn-primary" value="Remove Missing Language Products"/>



</fieldset>
</form>




<form action="index.php" method="post">
<fieldset >
<legend>Remove categories with no products associated (this removes only the deepest categories and thus you will need to run this function multiple times to remove whole tree)</legend>
<input type="hidden" name="option" value="com_onepage" />
<input type="hidden" name="task" value="removecategories" />
<input type="hidden" name="view" value="utils" />


<input type="submit" class="btn button btn-primary" value="Remove Categories with no products"/>



</fieldset>
</form>



<form action="index.php" method="post">
<fieldset >
<legend>Group products into a single category if they are more than in one category with the same name</legend>
<input type="hidden" name="option" value="com_onepage" />
<input type="hidden" name="task" value="grouproductstocategories" />
<input type="hidden" name="view" value="utils" />
<label for="unprefered">
Set Category IDs separated by comma which are the least prefered (if you imported categories under IMPORT category for example, enter ID of the IMPORT category)
<input type="text" id="unprefered" name="unprefered" value="" />
</label>

<label for="prefered">
Set Category IDs separated by comma which are the MOST prefered (if you imported categories under IMPORT category for example, enter ID of the IMPORT category)
<input type="text" id="prefered" name="prefered" value="28,369,805,12652,260" />
</label>

<input type="submit" class="btn button btn-primary" value="Group products by category names"/>



</fieldset>
</form>

<fieldset >
<legend>List duplicate SKUs (before adding unique index to #__virtuemart_products.product_sku)</legend>
<form action="index.php" method="post">
<input type="hidden" name="option" value="com_onepage" />
<input type="hidden" name="task" value="get_skus" />
<input type="hidden" name="view" value="utils" />
<input type="submit" class="btn button btn-primary" value="Search products with the same SKU..."/>
</form>

</fieldset>





<fieldset >
<legend>Add unique index to #__virtuemart_products.product_sku (this can speed up various SKU related imports, exports and other logic). Index must be re-added after each Virtuemart upgrade !</legend>
<form action="index.php" method="post">
<input type="hidden" name="option" value="com_onepage" />
<input type="hidden" name="task" value="add_product_sku_index" />
<input type="hidden" name="view" value="utils" />
<input type="submit" class="btn button btn-primary" value="Add product_sku index..."/>
</form>

</fieldset>

<form action="index.php" method="post">
<fieldset >
<legend>Users in Joomla without any order in VM</legend>
<?php 



?>
<form action="index.php" method="post">
<input type="hidden" name="option" value="com_onepage" />
<input type="hidden" name="task" value="searchusers" />
<input type="hidden" name="view" value="utils" />
<input type="submit" class="btn button btn-primary" value="Search and show users without orders..."/>
</form>

<form action="index.php" method="post">
<input type="hidden" name="option" value="com_onepage" />
<input type="hidden" name="task" value="removeusers" />
<input type="hidden" name="view" value="utils" />
This query modifies #__users and #__user_usergroup_map and cannot be reversed: <br />
<input type="submit" class="btn button btn-primary" value="Remove users without orders (cannot be reversed !)..."/>
</form>
</fieldset>



<fieldset <?php if (!empty($j15)) echo ' disabled="disabled" '; ?> >
<legend>Products to Child Products</legend>
<div>This small utility will convert your products like: <br />
<table>
<table>
<tr><th>Name</th><th>SKU</th></tr>
<tr><td>Machanic TShirt<td><td>3PW155680X</td></tr>
<tr><td>Machanic TShirt S<td><td>3PW1556801</td></tr>
<tr><td>Machanic TShirt M<td><td>3PW1556802</td></tr>
</table>
</div>
<p>Logic: </p>
<p>1. Will take the SKU except the last letter of the SKU (3PW155680). </p>
<p>2. Will do a search in the #__virtuemart_products table to see if other products match with this SKU (3PW155680%). </p>
<p>3. Will find the parent product that has the shortest name among the results</p>
<p>4. Will assign this parent ID to it's new child products</p>
<p>5. Will create the custom fields within the parent (if it has no "generic child variant" custom attribute</p>
<table>




<tr><td>
<form action="index.php" method="post" name="childProductsForm" id="childProductsForm">

<select class="inputbox" id="virtuemart_category_id" name="virtuemart_category_id" >
					<option value=""><?php echo JText::_('COM_ONEPAGE_ANY'); ?></option>
					<?php 
					require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'shopfunctions.php'); 
					
					$selected_cats = JRequest::getVar('virtuemart_category_id'); 
					if (!is_array($selected_cats))
						$selected_cats = array($selected_cats); 
					$this->category_tree = ShopFunctions::categoryListTree($selected_cats);
					echo $this->category_tree; ?>
				</select>
				
				<?php $min = JRequest::getVar('min', 2); ?>
<label for="min_p">Minimum number of products per similar SKU: <input type="number" value="<?php echo $min; ?>" id="min_p" name="min" /></label><br />
<input type="submit" value="Assign child products" class="btn button btn-primary" /></td></tr>


<input type="hidden" name="option" value="com_onepage" />
<input type="hidden" name="task" value="createchilds" />
<input type="hidden" name="view" value="utils" />


</form>



</table>

</fieldset>





<?php $childPairing = JRequest::getVar('childpairing'); ?>
<?php if (!empty($childPairing)) {
	?>
	
	
<form action="index.php" method="post" name="childProductsForm2" id="childProductsForm2">
<input type="hidden" name="option" value="com_onepage" />
<input type="hidden" name="task" value="pair" id="pair_task" />
<input type="hidden" name="what" value="" id="what_id" />
<input type="hidden" name="view" value="utils" />
<?php $selected_cats = JRequest::getVar('virtuemart_category_id', 0);  ?>
<input type="hidden" name="virtuemart_category_id" value="<?php echo $selected_cats; ?>" />
<?php $min = JRequest::getVar('min', 2); ?>
<input type="hidden" name="min" value="<?php echo 	$min; ?>" />
<script>
function pair(task, id)
{
	var d = document.getElementById('pair_task'); 
	d.value = task; 
	var d2 = document.getElementById('what_id'); 
	d2.value = id; 
	
	d.form.submit(); 
	return false; 
}

function pair_multi()
{
	return pair('pair'); 
}


</script>
	
	<?php
	$restdata = ''; 
		$groups = $this->model->buildProducts(); 

		foreach ($groups as $k=>$v)
		{
			if (empty($v)) continue; 
			ob_start(); 
			?><fieldset><legend><?php echo $k; ?></legend>
			<?php
			 $cx = ''; 
			 $sk=count($v); 
			 foreach ($v as $k2=>$v2)
			 {
				
				 if (empty($v2['best_name'])) 
				 {
					 $cx .= 'Skipped: '.$v2['product_sku'].' -> '.$v2['product_name'].'<br />'; 
					 $sk--; 
					 continue; 
				 }
				 $best_name = $v2['best_name']; 
				 $new = false; 
				 if (!empty($v2['parent_type']))
				 {
					 $new = true; 
				 }
				 ?><div><b>Action:  </b></div><br /><select name="action_'.<?php echo $k.'_'.$k2; ?>">
				   <option value="0">Do nothing</option>
				   <option <?php if (!empty($new)) echo ' selected="selected" '; ?> value="new">Create new parent</option>
				   <option <?php if (empty($new)) echo ' selected="selected" '; ?> value="1">Set this product to be the parent:</option>
				   
				   </select>
				   <br />
				   
				   <?php if ($new) { ?>
				    <div style="clear: both;">Suggested parent name: <br /><input type="text" value="<?php echo htmlentities($best_name); ?>" name="parent_name_<?php echo $k.'_'.$k2; ?>" /></div>
				   <?php } ?>
				   <br />
				   <div style="clear: both;">Set this existing product to be parent: </div><br />
				   <select name="existing_parent" >
				     <option value="">-</option>
				     <?php  
					 $names = ''; 
					 foreach ($groups[$k] as $k23=>$v23) { 
					 if (!empty($v23['product_parent_id'])) { $is_child = true; }
					 else $is_child = false; 
					 $names .= '<div style="clear: both;'; 
					 if ($is_child) { $names .= ' color: green; margin-left: 50px; '; }
					 $names .= '">Product sku: <a href="/index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id='.$v23['virtuemart_product_id'].'">'.$v23['product_sku']." (id:".$v23['virtuemart_product_id'].") </a> name: ".$v23['product_name']."</div><br />";
					 ?>
					   <option <?php if ($v23['product_name'] == $best_name) echo ' selected="selected" '; ?> value="<?php echo htmlentities($k23); ?>"><?php echo htmlentities($k23.' '.$v23['product_name']); ?></option>
					 <?php } ?>
				   </select>
				   <br />
				   <div style="clear: both;">
				 <?php
				 
				 ?></div>
				
				 <?php
				 echo $names; 
				 break; 
			 }
			?>
			 <div style="clear: both;">
				 <?php
				 echo $cx; 
				 ?>
				 </div>
				 <br />
				 <label style="float: left;" for="id_<?php echo htmlentities($k2); ?>"><input type="checkbox" name="pair_multi_ids[]" value="<?php echo $k; ?>" class="multi_pair" id="id_<?php echo htmlentities($k2); ?>" />Add to queue</label>
				 <div style="clear: both;">
				 <input type="button" value="Pair ..." onclick="return pair('pairSingle', '<?php echo htmlentities($k); ?>');" />
				 </div>
			</fieldset>
			<?php
			$data = ob_get_clean();
			if (!empty($sk))
			{
				echo $data; 
			}
			else
			{
				$restdata .= $data; 
			}
		}
		echo $restdata; 


	
	
	?>
	<input type="button" value="Process Queue" onclick="return pair_multi();" />
	</form>
	<?php
 } ?>



<form action="index.php" method="post" name="innodbform" id="innodbform">
<fieldset <?php if (!empty($j15)) echo ' disabled="disabled" '; ?> >
<legend>MyISAM to InnoDB updater</legend>
<div>Please create a backup of your database before using this feature. This will will automatically alter all your tables to InnoDB or MyISAM</div>

<table>


<tr><td><input type="checkbox" value="1" name="only_if_dif" /><label>Run Alter Table only if the current definition is not equal to desired definition. Running alter table that already is assigned to the desired engine may cause optimize and recreate the table's indexes which may take some time.</label></td></tr>

<tr><td><input type="submit" value="All tables to InnoDB" onclick="submitbutton('toinnodb');" class="btn button  btn-success" /></td></tr>

<tr><td><input type="submit" value="Virtuemart tables to InnoDB" onclick="submitbutton('vminnodb');" class="btn button  btn-success"/></td></tr>

<tr><td><input type="submit" value="All tables to MyIsam" onclick="submitbutton('tomyisam');" class="btn button  btn-success" /></td></tr>

<tr><td><input type="submit" value="Virtuemart tables to MyIsam" onclick="submitbutton('vmmyisam');" class="btn button  btn-success" /></td></tr>


<tr><td><input type="submit" value="All tables to their original engine" onclick="return submitbutton('tooriginal');" class="btn button  btn-success" /></td></tr>


</table>
<script>
function submitbutton(task)
{
  var d = document.getElementById('taskdb'); 
  if (d != null)
  d.value = task; 
  
  d2 = document.getElementById('innodbform'); 
  d2.submit(); 
  
  return false; 
}
</script>

</fieldset>


<input type="hidden" name="option" value="com_onepage" />
<input type="hidden" name="task" id="taskdb" value="display" />
<input type="hidden" name="view" value="utils" />

</form>



<fieldset><legend><?php 

echo JText::_('COM_ONEPAGE_CSV_IMPORT'); ?> - <?php echo JText::_('COM_ONEPAGE_CSV_IMPORT_QUICK_PRICEIMPORT'); ?></legend>

<?php echo JText::_('COM_ONEPAGE_CSV_IMPORT_FILE_DETAIL'); ?>
<textarea style="width:100%; clear:both;" rows="5">
product_sku,product_price
10000156,17901
</textarea>

<form action="index.php" name="upload" method="post" enctype="multipart/form-data" onsubmit="javascript: return new function() { sb = document.getElementById('us17').disabled=true;  return true; }">
	<input type="file" name="file_upload" /><br />
	Currency: 
	<select name="opcxvars_cur"><?php foreach ($this->currencies as $k=>$c) { 
	   ?><option <?php 
	   if ($c->virtuemart_currency_id === $defaults->cur) echo ' selected="selected" '; 
	   ?>value="<?php echo $c->virtuemart_currency_id; ?>"><?php echo $c->currency_code_3; ?></option><?php
	}
	?>
	</select>
	<br />
	Shopper Group: 
	<select name="opcxvars_sg"><option value="">ALL (default)</option><?php foreach ($this->sgs as $k=>$c) { 
	   ?>
	   <option <?php 
	  
	   if ($c['virtuemart_shoppergroup_id'] == $defaults->sg) echo ' selected="selected" ';
	   
	   ?>value="<?php echo $c['virtuemart_shoppergroup_id']; ?>"><?php echo JText::_($c['shopper_group_name']); ?></option><?php
	}
	?>
	</select>
	
	
	<input type="hidden" name="task" value="csv_upload" />                
	Email address to send a notice when the import had finished:
	<?php 
	
	?>
	<input type="text" name="opcxvars_recipient" value="<?php echo $defaults->recipient; ?>" />
	<input type="hidden" name="option" value="com_onepage" />

	<input type="hidden" name="view" value="utils" />
	<?php echo JHtml::_('form.token'); ?>
	
	<?php
		if ((class_exists('vRequest')) && (method_exists('vRequest', 'getFormToken'))) {
	?>
	<input type="hidden" name="<?php echo vRequest::getFormToken(); ?>" value="1" />
	<?php } ?>
		<br />
		<label for="backP"><input type="checkbox" value="1" name="opcxvars_back" id="backP" <?php if (!empty($defaults->back)) echo ' checked="checked" '; ?> >No Background Processing</label><br />
		<button type="submit" class="btn button btn-primary" id="us17" ><?php echo JText::_('COM_ONEPAGE_CSV_IMPORT'); ?></button><br />
		
		<input type="text" name="opcxvars_host" value="<?php echo $defaults->host; ?>" placeholder="DB remote host" />
		<input type="text" name="opcxvars_user" value="<?php echo $defaults->user; ?>" placeholder="DB remote user" />
		<input type="text" name="opcxvars_password" value="<?php echo $defaults->password; ?>" placeholder="DB remote password" />
		<input type="text" name="opcxvars_prefix" value="<?php echo $defaults->prefix; ?>" placeholder="Joomla remote prefix" />
		<input type="text" name="opcxvars_database" value="<?php echo $defaults->database; ?>" placeholder="Joomla database name" />
		
	
</form>
<form action="index.php" name="upload" method="post" enctype="multipart/form-data" >
	<button type="submit" class="btn button btn-success"  ><?php echo JText::_('COM_ONEPAGE_CSV_EXPORT_LABEL'); ?></button><br />
	
	<input type="hidden" name="option" value="com_onepage" />
	<input type="hidden" name="task" id="taskdb2" value="export_prices" />
	<input type="hidden" name="view" value="utils" />
	
	<?php echo JHtml::_('form.token'); ?>
	
	<?php
		if ((class_exists('vRequest')) && (method_exists('vRequest', 'getFormToken'))) {
	?>
	<input type="hidden" name="<?php echo vRequest::getFormToken(); ?>" value="1" />
	<?php } ?>
	
</form>
	
	

</fieldset>



<fieldset><legend><?php echo JText::_('COM_ONEPAGE_CSV_IMPORT'); ?> - <?php echo JText::_('COM_ONEPAGE_CSV_IMPORT_PRODUCTS'); ?></legend>

<?php echo JText::_('COM_ONEPAGE_CSV_IMPORT_PRODUCTS_DETAIL'); ?>
<textarea style="width:100%; clear:both;" rows="5">
"ID","NAME","SIZE","CATEGORY","SKU","PRICE","CURRENCY","SHORT DESCRIPTION","LONG DESCRIPTION","THUMBNAIL IMAGE","FULL IMAGE"
1,"unmatched zip hoodie","XS","STYLE EQUIPMENT","3PW1665201","9,5","EUR","KTM UNMATCHED ZIP HOODIE","Hooded sweat jacket with large print motif over front, back and sleeves.KTM logo with 3D-effect on chest, black lining in hood, extra-wide drawstrings.70 % cotton / 30 % polyester","images/stories/virtuemart/product/resized/3PW647003_0.jpg","images/stories/virtuemart/product/3PW647003_0.jpg"
1,"unmatched zip hoodie","S","STYLE EQUIPMENT","3PW1665202","9,5","EUR","KTM UNMATCHED ZIP HOODIE","Hooded sweat jacket with large print motif over front, back and sleeves.KTM logo with 3D-effect on chest, black lining in hood, extra-wide drawstrings.70 % cotton / 30 % polyester","images/stories/virtuemart/product/resized/3PW647003_0.jpg","images/stories/virtuemart/product/3PW647003_0.jpg"
</textarea>

<form action="index.php" name="upload" method="post" enctype="multipart/form-data" onsubmit="javascript: return new function() { sb = document.getElementById('us18').disabled=true;  return true; }">
	<br />
	Shopper Group: 
	<select name="sg"><?php foreach ($this->sgs as $k=>$c) { 
	   ?><option value="">ALL (default)</option>
	   <option <?php 
	  
	   
	   ?>value="<?php echo $c['virtuemart_shoppergroup_id']; ?>"><?php echo JText::_($c['shopper_group_name']); ?></option><?php
	}
	?>
	</select><br />
	
	<input type="file" name="file_upload" />
	<?php echo JHtml::_('form.token'); ?>
	<button type="submit" class="btn button btn-primary" id="us18" ><?php echo JText::_('COM_ONEPAGE_CSV_IMPORT'); ?></button><br />
	<input type="hidden" name="task" value="csv_upload_product" />                
	
	<input type="hidden" name="option" value="com_onepage" />
<input type="text" name="opcxvars_recipient" value="<?php echo $defaults->recipient; ?>" />
	<input type="hidden" name="view" value="utils" />
	<?php
		if ((class_exists('vRequest')) && (method_exists('vRequest', 'getFormToken'))) {
	?>
	<input type="hidden" name="<?php echo vRequest::getFormToken(); ?>" value="1" />
	<?php } ?>
</form>
	
	

</fieldset>

<fieldset><legend><?php echo JText::_('COM_ONEPAGE_UTILS_CATEGORY_TOOLS'); ?></legend>
<?php echo JText::_('COM_ONEAPGE_UTILS_MOVE_COPY'); ?>
<form action="index.php" name="upload" method="post" >
	<label for="COM_ONEPAGE_UTILS_ACTION_SOURCE"><?php echo JText::_('COM_ONEPAGE_UTILS_ACTION_SOURCE'); ?></label>
	<select name="source_cat" id="COM_ONEPAGE_UTILS_ACTION_SOURCE" >
<?php


if (empty($this->cats)) $this->cats = array(); 
foreach ($this->cats as $lang=>$arr)
foreach ($arr as $k=>$cat)
{
	
	if (!isset($cat['virtuemart_category_id'])) {
	  
	  continue; 
	}
	$cat['virtuemart_category_id'] = (int)$cat['virtuemart_category_id']; 
	
  echo '<option '; 
  $sc = (int)JRequest::getInt('source_cat', 0); if ($sc === $cat['virtuemart_category_id']) echo ' selected="selected" '; 
  echo ' value="'.$cat['virtuemart_category_id'].'">'.$cat['category_name'].'</option>'; 
}
?>
</select>

<label for="COM_ONEPAGE_UTILS_ACTION_DEST"><?php echo JText::_('COM_ONEPAGE_UTILS_ACTION_DEST'); ?></label>
<select name="dest_cat" id="COM_ONEPAGE_UTILS_ACTION_DEST">
<?php

if (empty($this->cats)) $this->cats = array(); 
foreach ($this->cats as $lang=>$arr)
foreach ($arr as $k=>$cat)
{
	
	if (!isset($cat['virtuemart_category_id'])) {
	  
	  continue; 
	}
	$cat['virtuemart_category_id'] = (int)$cat['virtuemart_category_id']; 
	
  echo '<option '; 
  $sc = (int)JRequest::getInt('dest_cat', 0); if ($sc === $cat['virtuemart_category_id']) echo ' selected="selected" '; 
  echo ' value="'.$cat['virtuemart_category_id'].'">'.$cat['category_name'].'</option>'; 
}
?>
</select>
<label for="action_cat"><?php echo JText::_('COM_ONEPAGE_UTILS_ACTION'); ?></label>
<select id="action_cat" name="action_cat">
 <option value="0"><?php echo JText::_('JLIB_HTML_BATCH_COPY'); ?></option>
 <option value="1"><?php echo JText::_('JLIB_HTML_BATCH_MOVE'); ?></option>
</select>
	
	<button type="submit" class="btn button"  ><?php echo JText::_('COM_ONEPAGE_UTILS_ACTION'); ?>...</button><br />
	<?php echo JHtml::_('form.token'); ?>
	<input type="hidden" name="option" value="com_onepage" />
	<input type="hidden" name="task" id="taskdb233" value="cat_prod_copy" />
	<input type="hidden" name="view" value="utils" />
	
	<?php
		if ((class_exists('vRequest')) && (method_exists('vRequest', 'getFormToken'))) {
	?>
	<input type="hidden" name="<?php echo vRequest::getFormToken(); ?>" value="1" />
	<?php } ?>
	
</form>
</fieldset>

<fieldset><legend><?php echo JText::_('COM_ONEPAGE_UTILS_FIX_IMAGES_CASE'); ?></legend>
<?php echo JText::_('COM_ONEPAGE_UTILS_FIX_IMAGES_CASE_DESC'); ?>
<form action="index.php" name="upload" method="post" >
<button type="submit" class="btn button"  ><?php echo JText::_('COM_ONEPAGE_UTILS_ACTION'); ?>...</button><br />
	<?php echo JHtml::_('form.token'); ?>
	<input type="hidden" name="option" value="com_onepage" />
	<input type="hidden" name="task" value="fix_img_filenames" />
	<input type="hidden" name="view" value="utils" />
	
	<?php
		if ((class_exists('vRequest')) && (method_exists('vRequest', 'getFormToken'))) {
	?>
	<input type="hidden" name="<?php echo vRequest::getFormToken(); ?>" value="1" />
	<?php } ?>

</form>
</fieldset>



<fieldset><legend>All products to default tax rules</legend>
This will run a query to update all products to "apply default tax rules" with:<br />
update `#__virtuemart_product_prices` set product_tax_id = 0 WHERE 1 limit 99999999

<form action="index.php" name="upload" method="post" >
<button type="submit" class="btn button  btn-primary"  ><?php echo JText::_('COM_ONEPAGE_UTILS_ACTION'); ?>...</button><br />
	<?php echo JHtml::_('form.token'); ?>
	<input type="hidden" name="option" value="com_onepage" />
	<input type="hidden" name="task" value="to_default_taxrules" />
	<input type="hidden" name="view" value="utils" />
	
	<?php
		if ((class_exists('vRequest')) && (method_exists('vRequest', 'getFormToken'))) {
	?>
	<input type="hidden" name="<?php echo vRequest::getFormToken(); ?>" value="1" />
	<?php } ?>

</form>
</fieldset>


<fieldset><legend>All products to all of their parent categories</legend>
This query will associate all of your products to their parent categories:<br />
Example: <br />
Category Structure: A > B > C > D<br />
Produt is assigned to category C<br />
After running this comand the product will be associated to A, B, and C<br />
This action cannot be reversed and it can influence your SEF paths since there is no way to tell Virtuemart which category is the main one<br />
<form action="index.php" name="upload" method="post" >
<button type="submit" class="btn button  btn-primary"  ><?php echo JText::_('COM_ONEPAGE_UTILS_ACTION'); ?>...</button><br />
	<?php echo JHtml::_('form.token'); ?>
	<input type="hidden" name="option" value="com_onepage" />
	<input type="hidden" name="task" value="to_parent_cats" />
	<input type="hidden" name="view" value="utils" />
	
	<?php
		if ((class_exists('vRequest')) && (method_exists('vRequest', 'getFormToken'))) {
	?>
	<input type="hidden" name="<?php echo vRequest::getFormToken(); ?>" value="1" />
	<?php } ?>

</form>
</fieldset>



<fieldset><legend><?php echo JText::_('COM_ONEPAGE_GENERAL_ADVISE'); ?></legend>
<p><?php echo JText::_('COM_ONEPAGE_GENERAL_ADVISE_REDIRECT_NONWWW_TOWWW'); ?></p>
<textarea style="width: 90%; height: 100px;" readonly="readonly">
# not to rewrite static files (speeds up the system): 
RewriteRule ^(.*?)\.(php|css|js|jpg|jpeg|png|pdf|cur|eot|ttf|woff|woff2|svg)$ - [L]

# Redirect http OR https to www
RewriteCond %{HTTP_HOST} ^[^.]+\.[^.]+$
RewriteCond %{HTTPS}s ^on(s)|
RewriteRule ^ http%1://www.%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
</textarea><br />
<a target="_blank" href="http://stackoverflow.com/questions/12050590/redirect-non-www-to-www-in-htaccess"><?php echo JText::_('COM_ONEPAGE_GENERAL_ADVISE_REDIRECT_NONWWW_TOWWW_SOURCE'); ?></a>
</fieldset>

<?php
$error_log = @ini_get('error_log'); 
$open_base_dir = @ini_get('open_basedir');
if (empty($open_base_dir))
if (!empty($error_log))
if (file_exists($error_log))
{
?>
<fieldset><legend><?php echo JText::_('COM_ONEPAGE_PHPERRORlOG'); ?></legend>
 <a href="index.php?option=com_onepage&view=utils&task=errorlog&format=raw&tmpl=component"><?php echo JText::_('COM_ONEPAGE_VIEWPHPERRORLOG'); ?></a>
</fieldset>
<?php 
}

