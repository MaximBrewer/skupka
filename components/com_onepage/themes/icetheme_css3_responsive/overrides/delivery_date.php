<?php
/*
*
* @copyright Copyright (C) 2007 - 2012 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* One Page checkout is free software released under GNU/GPL and uses code from VirtueMart
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
*/

if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); 
if (!empty($calendard_gif)) {
$css = '
 input#opc_date_picker {
   background: transparent url('.$calendard_gif.') no-repeat 90% 5px !important;
   padding-right: 3px; 
 
}
';
JFactory::getDocument()->addStyleDeclaration($css); 
}
?>


<div class="op_inside opc_section delivery_section_wrapper section_box">
<div class="op_rounded">
	<div><div><div><div>
	   <div class="op_rounded_fix" style="width: 100%;">  
                             <h3>
                                <span class="col-module_header_r">
                                <span class="col-module_header_l">
                                <span class="col-module_header_arrow">
                                    <span class="col-module_header_color"><?php echo OPCLang::_('COM_ONEPAGE_CHOOSE_DESIRED_DELIVERY_DATE'); ?></span>                           		
                                </span>
                                </span>
                             	</span>  
                        	</h3>
<div class="op_rounded_content">
								
								 <div><p><?php echo JText::_('COM_ONEPAGE_CHOOSE_DESIRED_DELIVERY_DATE_DESC'); ?></p></div>
								
								 <div><input <?php echo $required; ?> type="text" value="<?php echo $now; ?>" id="opc_date_picker" class="opc_date_picker" name="opc_date_picker" />
								</div>
								<br style="clear: both;"/>
								

</div>
	  </div>
	 </div></div></div></div>
</div>
</div>
