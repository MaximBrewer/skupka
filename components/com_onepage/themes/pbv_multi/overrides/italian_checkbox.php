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
<div style="width: 100%;clear:both;text-align:left;">
<div style="width: 5%; float: left;">
<script type="text/javascript">
op_userfields.push('italianagreed'); 
custom_rendering_fields.push('italianagreed'); 
var italagreeerr = "<?php echo OPCloader::slash(JText::_('COM_ONEPAGE_ITALIAN_AGREE_ERROR')); ?>";
function validateItalian()
{
  d = document.getElementById('italianagreed_field'); 
  if (d != null)
  if (!d.checked)
  {
    alert(italagreeerr); 
    return false; 
  }
  return true; 
}
 addOpcTriggerer('callSubmitFunct', 'validateItalian'); 
</script>
<input value="1" type="checkbox" id="italianagreed_field" name="italianagreed" <?php if (!empty($agree_checked)) echo ' checked="checked" '; ?> class="terms-of-service required"  required="required" autocomplete="off" />

</div>
					<div style="width: 95%; float:left;"><label for="italianagreed_field" style="float: none; white-space: normal;"><span style="font-weight:bold;"><?php echo OPCLang::_('COM_ONEPAGE_ITALIAN_AGREE_LABEL').'</span><br />'; 
					echo OPCLang::_('COM_ONEPAGE_ITALIAN_AGREE_DESC'); 
					?>
					</label></div>
</div>	
<!-- END italian privacy checkbox -->