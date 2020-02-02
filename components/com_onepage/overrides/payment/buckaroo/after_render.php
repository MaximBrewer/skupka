<?php
defined('_JEXEC') or die;
$css = '
#vmMainPageOPC .wrapper_paymentdetails {
 display: block !important; 
}
'; 

$document = JFactory::getDocument();

//to show it always: 
//$document->addStyleDeclaration( $css );

$js = '

function buShow'.$vm_id.'(id)
{
  var el = document.getElementById(\'payment_id_\'+id); 
  var payment_id = Onepage.getPaymentId();
  if (payment_id == id)
   {
       var d = document.getElementById(\'wrapper_paymentdetails\'+id); 
	 if (d != null) 
	  d.style.display = \'block\'; 
   }
  else
   {
      var d = document.getElementById(\'wrapper_paymentdetails\'+id); 
	  if (d != null) 
	  d.style.display = \'none\'; 
   }
  
}
if (typeof callAfterResponse != \'undefined\')
addOpcTriggerer("callAfterPaymentSelect",  "buShow'.$vm_id.'('.$vm_id.')"); 

'; 
$document->addScriptDeclaration($js); 
//$document->addScript(JURI::Root() . "components/com_onepage/overrides/payment/buckaroo/buckaroo_custom.js");