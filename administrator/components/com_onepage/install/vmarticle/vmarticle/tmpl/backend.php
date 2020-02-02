<?php
 
 $x = get_defined_vars();
 $field = $viewData['field'];
 $cs = $viewData['currencies']; 
 $id = $field->virtuemart_customfield_id; 
 //$name = 'customfield_value['.$id.'][custom_multiattribs]'; 
 //$name = 'field['.$id.'][customfield_value]'; 
 $name = 'customfield_params['.$id.'][custom_multiattribs]'; 
 $values = $viewData['values']; 

?>
<div title="Attributes" id="vmMainPageOPC">

<fieldset>
	<legend>Attributes</legend>
	
	<div >
	
	<div class="row-fluid" >


<div class="span3 mt_input_div">Attribute Name</div>
<?php 

foreach ($cs as $c) { 
  
  $csa[$c->virtuemart_currency_id] = $c->virtuemart_currency_id; 
  ?> <div class="span3 mt_input_div"><?php echo $c->currency_code_3; ?> </div>  <?php
}

?>
<div class="mt_clear" />
 
  
  
</div>
	
<div class="at_wrap" id="append_to_table_<?php echo $id; ?>">

<?php 

$csa = array(); 

ob_start(); 
?>


<div class="row-fluid" >


<div class="span3 mt_input_div"><input class="mt_input mt_first" type="text" name="<?php echo $name; ?>[{n}][attrib]"  value="{value_{n}_attrib}" placeholder="Attribute Name" /></div>
<?php 

foreach ($cs as $c) { 
  
  $csa[$c->virtuemart_currency_id] = $c->virtuemart_currency_id; 
  ?> <div class="span3 mt_input_div"><input class="mt_input " type="text" name="<?php echo $name; ?>[{n}][currency_price_<?php echo $c->virtuemart_currency_id; ?>]" value="{value_{n}_<?php echo $c->virtuemart_currency_id; ?>}" placeholder="Price addition (<?php echo $c->currency_code_3; ?>)" /> </div>  <?php
}

?><a href="#" class="add_more add_more_class_{n} more_class_hidden" rel="append_to_table_<?php echo $id; ?>" id="prefix<?php echo $id; ?>_{n}" onclick="return addMore(this);" current_id="<?php echo $id; ?>">Add More</a>
<div class="mt_clear" />
 
  
  
</div>

<?php 
$row = ob_get_clean(); 

$n =0; 
	
if (!empty($values))
{
$cx = count($values); 
foreach ($values as $k=>$v)
{
	
	$row_x = str_replace('{n}', $n, $row); 
	foreach ($v as $z => $an) {
	 $row_x = str_replace('{value_'.$n.'_'.$z.'}', $an, $row_x); 
	 
	}
	$n++;
	
	if ($n === $cx) {
		$row_x = str_replace('more_class_hidden', '', $row_x); 
	}
	echo $row_x; 
}

}
$row_0 = str_replace('{n}', 0, $row); 
$row_0 = str_replace('{value_0_attrib}', '', $row_0); 
$row_0 = str_replace('more_class_hidden', '', $row_0); 

$row_js = str_replace('{value_{n}_attrib}', '', $row); 
$row_js = str_replace('more_class_hidden', '', $row_js); 

foreach ($csa as $cid)
{
 
 $row_js = str_replace('{value_{n}_'.$cid.'}', '', $row_js); 
 $row_0 = str_replace('{value_0_'.$cid.'}', '', $row_0); 
 
}


if (empty($values)) echo $row_0; 



$doc = JFactory::getDocument(); 
		
$row_js = str_replace("\r\n", '', $row_js); 		
$row_js = str_replace("\n", '', $row_js); 
$row_js = str_replace("\r", '', $row_js); 
$row_js = str_replace("'", "\'", $row_js); 

		$css = "\n".' a.more_class_hidden { display: none; } '."\n"; 
		$css .= ' input[name="field['.$id.'][customfield_price]"] { display: none; } '."\n"; 
		$doc->addStyleDeclaration($css); 
$js = '
/*<![CDATA[*/
if (typeof rowIns == \'undefined\') rowIns = []; 

rowIns['.$id.'] = \''.$row_js.'\'; 

jQuery(document).ready(function () {
var el = jQuery(\'#vmMainPageOPC\'); 

// div < td < tr 

el.parent().parent().children(\'td\').slice(1, 2).addClass("hide_me");

}); 

/*]]>*/
'; 
		$doc->addScriptDeclaration($js); 


?>


</div>
	</div>
</fieldset>



</div>

