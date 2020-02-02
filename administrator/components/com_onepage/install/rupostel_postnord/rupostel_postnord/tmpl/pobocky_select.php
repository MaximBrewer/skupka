<?php
defined('_JEXEC') or die('Restricted access');


$countp = count($viewData['pobocky_options']); 
?>
<select name="postnord_pobocky" id="postnord_pobocky" class="inputbox" vmid="<?php echo $viewData['virtuemart_shipmentmethod_id']; ?>" onChange="changepostnord(this.options[this.selectedIndex].value, true);" style="min-width: 200px;">
<?php if ($countp > 1) { ?>
<option value="0" <?php 
$price_key = 'price_id_'.$viewData['method']->virtuemart_shipmentmethod_id.'_0'; 
echo ' ismulti="true" data-json=\''.json_encode(array('postnord_pobocka'=>0, 'postnord_pobocky'=>0)).'\' multi_id="'.$price_key.'" value="0" '; 
?>><?php echo JText::_($viewData['method']->vyberte_pobocku_label); ?></option>  
<?php } ?>
<?php

				foreach ($viewData['pobocky_options'] as $ppp)
				 {
					
					
				    $option = '<option '; 
					if (($viewData['sind'] == $ppp->id) || ($countp === 1)) $option .= ' selected="selected" '; 
					$option .= ' ismulti="true" data-json=\''.json_encode(array('postnord_pobocka'=>$ppp->id, 'postnord_pobocky'=>$ppp->id)).'\' multi_id="'.$ppp->price_key.'" value="'.$ppp->id.'">'.$ppp->nazev.'</option>'; 
					echo $option; 
				 }
				 ?>
</select>