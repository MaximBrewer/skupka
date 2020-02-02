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

// load OPC loader
//require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'loader.php'); 

defined('_JEXEC') or die('Restricted access');

$PPids = array(); 
	  if ($pay->payment_element == 'piraeus')
	   {
		  $PPids[] = (int)$pay->virtuemart_paymentmethod_id; 
	      $params = new stdClass(); 
		  $ia = array(); 
	      $thisklarnaparams = explode(',', $pay->piraeus_installments);
		 
                                foreach ($thisklarnaparams as $item) {
                                                $item = explode(':', $item);
												
                                                $i = $item[0];
                                                $iaz = $item[1];
												$ki = $i.'_'.$iaz; 
												if (empty($i) || (empty($iaz))) continue; 
												//$ia[$i] = $iaz; 
												$ia[$ki] = $iaz; 
                                        }

		 foreach ($ia as $key=>$val)
		  {
		    
			   
			   
			    $clone = clone($pay);
				$clone->monthinstallmentsP = $val; 
				
				$clone->payment_type = $type;
				$clone->opcref =& $pay->opcref; 
				
				$clone->payment_id = 'piraeus_'.$val; 
				$clone->payment_id_override = 'piraeus_'.$val; 
			    $add[] =  $clone; 
			   
			   
		 }
			
		 

	   }
 
if (!empty($PPids)) {
JHTMLOPC::script('opc_pir.js', 'components/com_onepage/overrides/payment/piraeus/', false);	
$doc = JFactory::getDocument();

ob_start(); 
?>

 var pirMethods = []; 
 <?php foreach ($PPids as $pZ) {
   echo 'pirMethods.push('.(int)$pZ.'); '; 
 }
 ?>
 addOpcTriggerer('callAfterPaymentSelect', 'initPir()'); 

<?php 
$content = ob_get_clean(); 

$doc->addScriptDeclaration( $content );
}