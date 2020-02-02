<?php
//load this template via: 
//
//$path = JModuleHelper::getLayoutPath('mod_productcustoms', 'default_category'); 
//make sure you create: 
//$params = new JRegistry(''); 
//$params->set('key', 'val'); 
//require($path); 

	
	foreach ($datas as $title=>$v) {
		
		?><fieldset><legend><?php echo $title; ?></legend>
		<?php foreach ($v as $id => $val) { ?>
		   <label for="id_<?php echo $id; ?>"><input type="checkbox" value="<?php echo $id; ?>" name="virtuemart_custom_id" data-name="virtuemart_custom_id" data-value="<?php echo $id; ?>" /><?php echo $val; ?></label>
			<?php
		}
		?>
		</fieldset>
		<?php
		
		
	}
	
