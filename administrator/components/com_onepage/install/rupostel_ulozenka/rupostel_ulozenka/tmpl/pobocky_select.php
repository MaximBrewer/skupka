<?php
defined('_JEXEC') or die('Restricted access');


?>
<select name="ulozenka_pobocky" id="ulozenka_pobocky" class="inputbox" vmid="<?php echo $viewData['virtuemart_shipmentmethod_id']; ?>" onchange="return changeUlozenka(this.options[this.selectedIndex].value, true, <?php echo $viewData['method']->virtuemart_shipmentmethod_id; ?>);" style="min-width: 200px;">
<option value="0" <?php 
$price_key = 'price_id_'.$viewData['method']->virtuemart_shipmentmethod_id.'_0'; 
echo ' ismulti="true" data-json=\''.json_encode(array('ulozenka_pobocka'=>0, 'ulozenka_pobocky'=>0)).'\' multi_id="'.$price_key.'" value="0" '; 
?>><?php echo JText::_($viewData['method']->vyberte_pobocku_label); ?></option>
<?php


				foreach ($viewData['pobocky_options'] as $ppp)
				 {
					
					
				    $option = '<option '; 
					if ($viewData['sind'] == $ppp->id) $option .= ' selected="selected" '; 
					$option .= ' ismulti="true" data-json=\''.json_encode(array('ulozenka_pobocka'=>$ppp->id, 'ulozenka_pobocky'=>$ppp->id)).'\' multi_id="'.$ppp->price_key.'" value="'.$ppp->id.'">'.$ppp->nazev.'</option>'; 
					echo $option; 
				 }
				 ?>
</select>