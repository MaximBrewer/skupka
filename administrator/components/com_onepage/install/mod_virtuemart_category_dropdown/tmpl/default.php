<?php
defined('_JEXEC') or die;

$selected_brand = JRequest::getVar('virtuemart_category_id', ''); 
$selected_type = JRequest::getVar('virtuemart_product_id', ''); 


require_once(JPATH_SITE.DS.'modules'.DS.'mod_virtuemart_category_dropdown'.DS.'helper.php'); 


?>

<div class="catselectbox-top"></div>

<div class="catselectbox">
	<h2><?php echo JText::_('MOD_VIRTUEMART_CATEGORY_DROPDOWN_HEADING'); ?></h2>
	
    <form action="#" name="catDropDownfilter">
	<?php 
	for ($i=1; $i<=5; $i++) { 
	$key = 'level'.$i.'_text'; 
	
	  
	$text = $params->get($key, ''); 
	
	if (empty($text)) break; 
	
	$style = ''; 
	$j = $i - 1; 
	if ($i > 1) {
	if (isset($cats[$j])) {
	  $z = count($cats[$j]); 
	  if ($z === 1) $style = ' domreadyhide '; 
	}
	else
		if (empty($cats[$j])) $style = ' domreadyhide '; 
	}
	if ($i >= 2){
		if (empty($cats[$i])) $style = ' domreadyhide '; 
		else
		{
		$z = count($cats[$i]); 
		if ($z === 1) $style = ' domreadyhide '; 
		}
	  
	}
	
	
	?>
	
	<div class="brandbox levelwrap<?php echo $i; ?>  <?php echo $style; ?>" id="levelwrap_<?php echo $module_id; ?>_<?php echo $i; ?>" >
	 
	  <select name="level_select_<?php echo $i; ?>" class="level_select_<?php echo $i; ?> brand vm_category_dropdown" onchange="javascript: catDropDowncatfilterChange(this, <?php echo $module_id; ?>); " level="<?php echo $i; ?>" id="level_<?php echo $module_id; ?>_<?php echo $i; ?>" style="min-width: 130px; width: 130px; ">
		<?php /* <option selected="selected"><?php echo JText::sprintf($text); ?></option> */ 
		if (empty($cats[$i])) {
			?><option selected="selected"><?php echo JText::sprintf($text); ?></option><?php
		}
		
		?>
		<?php

		  if (!empty($cats[$i])) foreach ($cats[$i] as $option) echo $option; 
		  /*
		  if ($i === 1)
		  {
		  foreach($top as $row){
			echo "<option ";
			if ($row['virtuemart_category_id'] == $selected_brand) echo ' selected="selected" '; 
			echo " value='" .$row['virtuemart_category_id'] . "'>" . $row['category_name'] . "</option>";
		  }
		  }
		  */
		
		?>
	  </select> 
	</div>
    <?php } 
	if (!empty($showProducts)) {
	?>
	<div class="lastbox">
	  <select name="cartype" class="cartype vm_category_dropdown" onchange="return catDropDowncatselectproduct(this);" level="<?php echo $i; ?>" id="products_<?php echo $module_id; ?>">
		
		<?php echo $options; ?>
		
	  </select>
	</div>
	<?php } ?>
	</form>
</div>

<div class="catselectbox-bottom"></div>