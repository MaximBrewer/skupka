<?php
/**
* @copyright    Copyright (C) 2011 InteraMind Advanced Analytics. All rights reserved.


**/

defined( '_JEXEC' ) or die( 'Restricted access' );

?>

<table class="html-email" cellspacing="0" cellpadding="0" border="0" width="100%">  
	<tr>
		<th width="50%">
		    <?php echo JText::_('COM_VIRTUEMART_USER_FORM_BILLTO_LBL'); ?>
		</th>
		<th width="50%" >
		    <?php echo JText::_('COM_VIRTUEMART_USER_FORM_SHIPTO_LBL'); ?>
		</th>
    </tr>
    <tr>
	<td valign="top" width="50%">
	    <?php
		if(!empty($BTuserFields)){
			foreach ($BTuserFields['fields'] as $userField) {
				if (!empty($userField['value']) && $userField['type'] != 'delimiter' && $userField['type'] != 'hidden') {
					$value = empty($userField['value']) ? '' : $userField['value'];
					echo '<span>'.$this->escape($value).'</span>';
					if ($userField['name'] != 'title' && $userField['name'] != 'first_name' && $userField['name'] != 'middle_name' && $userField['name'] != 'zip') {
						echo '<br>';
					}
					else{
						echo ' ';
					}
				}
			}
		}
	    
	    ?>

	</td>
	<td valign="top" width="50%">
<?php
		if(!empty($STuserFields)){
			foreach ($STuserFields['fields'] as $userField) {
				if (!empty($userField['value']) && $userField['type'] != 'delimiter' && $userField['type'] != 'hidden') {
					$value = empty($userField['value']) ? '' : $userField['value'];
					echo '<span>'.$this->escape($value).'</span>';
					if ($userField['name'] != 'title' && $userField['name'] != 'first_name' && $userField['name'] != 'middle_name' && $userField['name'] != 'zip') {
						echo '<br>';
					}
					else{
						echo ' ';
					}
				}
			}
		}
		
    

?>
	</td>
    </tr>
</table>