<?php
/**
 * Controller for the OPC ajax and checkout
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


require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.'opc.php'); 



class JControllerOpc extends VirtueMartControllerOpc {
  function opcthird() {
	  parent::opcthird(true); 
	  //redirect:
	  $order_id = JRequest::getInt('virtuemart_order_id', 0); 
	  if (!empty($order_id)) {
		  $url = 'index.php?option=com_virtuemart&view=orders&task=edit&virtuemart_order_id='.$order_id; 
	  // JFactory::getApplication()->redirect('); 
	  }
	  else {
		  $url = ''; 
	  }
	  
	  echo '<script>if ((typeof window.frameElement != \'undefined\') && (window.frameElement)) 
	  { parent.location.reload(); }
	  else window.location = \''.$url.'\'; 
  
  </script>'; 

  }
} 