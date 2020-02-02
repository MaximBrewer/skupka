<?php
/**
 * Italian privacy checkbox support file
 *
 * @package One Page Checkout for VirtueMart 2
 * @subpackage opc
 * @author stAn
 * @author RuposTel s.r.o.
 * @copyright Copyright (C) 2007 - 2012 RuposTel - All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * One Page checkout is free software released under GNU/GPL and uses some code from VirtueMart
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * 
 */
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); 
?><!-- italian privacy checkbox -->
<div id="newsletter_checkbox_div">
<div class="checkbox_div_left">


<input type="hidden" value="<?php echo $opc_acy_id; ?>" name="acylistsdisplayed_dispall" />
<input type="checkbox" id="acy_list_<?php echo $opc_acy_id; ?>" class="acymailing_checkbox" name="acysub[]" value="<?php echo $opc_acy_id; ?>"/>


<input type="hidden" name="allVisibleLists" value="<?php echo $opc_acy_id; ?>" />



</div>
					<div class="checkbox_div_right" ><label for="acy_list_<?php echo $opc_acy_id; ?>" style="float: none; white-space: normal;"><span style="font-weight:bold;"><?php echo OPCLang::_('COM_VIRTUEMART_FIELDS_NEWSLETTER'); ?></span><br />
					<?php
					echo OPCLang::_('COM_ONEPAGE_NEWSLETTER_SUBSCRIPTION_DESC'); 
					?>
					</label></div>
</div>	
<!-- END italian privacy checkbox -->