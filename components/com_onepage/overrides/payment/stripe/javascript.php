<?php
/**
 *
 * a Stripe payment Charge method:
 *
 * @version 1.01
 * @version Stripe PHP bindings from the Stripe API Libraries v1.6.2
 * @author Hervé Boinnard
 * @copyright Copyright (C) 2013 Hervé Boinnard - (C) 2012 Stephen.V. - All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 *
 * http://www.puma-it.ie
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
$method = $viewData['method']; 
$paymentForm = $viewData['paymentForm']; 
?>
<script type="text/javascript" src="https://js.stripe.com/v2/"></script>
<script type="text/javascript">
/* <![CDATA[ */
            
			var Status = false;
            function stripeResponseHandler(status, response) {
			//console.log(response); 
			//console.log(response.id); 
			//console.log(status); 
                if (response.error) {
                    // re-enable the submit button
                    // show the errors on the form
                   jQuery("#stripe_errors").html(response.error.message);
						//console.log(response.error); 
                    jQuery("#stripe_status").hide();
					Status = false;
					Onepage.endValidation(false); 
					Onepage.unblockButton(); 
					
					
                } else {
				    //console.log(response); 
                    var vmform = jQuery("<?php echo $paymentForm; ?>");
                    // token contains id, last4, and card type
                    var token = response.id;
					if (token == '') {
					Onepage.endValidation(false); 
					return false; 
					}
					
                    // insert the token into the form so it gets submitted to the server
                    jQuery("#stripe_token_id").val(token);
					jQuery("#stripeToken").val(token);
                    // and submit
					Status = true;
					Onepage.endValidation(true); 
                    vmform.get(0).submit();
                }
				
            }

            //
				
			    Stripe.setPublishableKey("<?php echo ($method->stripe_payment_mode == 1 ? $method->test_publishable_key : $method->live_publishable_key) ; ?>");
			jQuery(document).ready(function() {
				jQuery("#stripe_status").hide();
                
				initStripe(); 
				

				
            });

function initStripe()
{
   jQuery("input[name=virtuemart_paymentmethod_id]").click(function(event){ 
				// stAn: let's do this gernally, there is no  need to do this only per one plugin
					//if(!jQuery("#payment_id_<?php echo $viewData['VPID']; ?>").is(":checked")) {
					
						jQuery("#stripe_status").hide();
						jQuery("#stripe_errors").html("");	
					//}
				});
}

function getSelected()
{
  
 var d = document.getElementById('payment_id_<?php echo $viewData['VPID']; ?>'); 
  if (d != null)
   {
   
	if (typeof jQuery != 'undefined') jQuery(d).click(); 
	else
	if (typeof d.click != 'undefined')
	d.click(); 
	
	
   }
   return true; 
}			

function opc_createToken(wasValid)
     {
	   var rt = true; 
       jQuery(function($) {
       
	     var payment_id = Onepage.getPaymentId(); 
		 var ps = '<?php echo $viewData['VPID']; ?>'; 
		 
		 // for other plugins: 
	
		 if (payment_id != ps){   rt = true; return; }
		 
	     jQuery("#stripe_status").show();
		 jQuery("#stripe_errors").html("");
						// disable the submit button to prevent repeated clicks
						if(Status) 
						{
							Status = false;
							jQuery("#stripe_status").hide();
							jQuery("#stripe_errors").html("");
							
							rt = true;
							return;
						}
						
         
			Stripe.card.createToken({
							number: jQuery("#cc_number_<?php echo $viewData['VPID']; ?>").val(),
							cvc: jQuery("#cc_cvv_<?php echo $viewData['VPID']; ?>").val(),
							exp_month: jQuery("#cc_expire_month_<?php echo $viewData['VPID']; ?>").val(),
							exp_year: jQuery("#cc_expire_year_<?php echo $viewData['VPID']; ?>").val(),
							address_zip: jQuery("#zip_field").val(),
							address_line1: jQuery("#address_1_field").val(),
							address_city: jQuery("#city_field").val(),
							address_state: jQuery("#virtuemart_state_id option:selected").text(),
							address_country: jQuery("#virtuemart_country_id option:selected").text(),
						}, stripeResponseHandler);
         // Prevent the form from submitting with the default action
         rt = false; 
       });
	   
		return rt; 
      }; 


function checkStripeCC(el, pid)
{
  getSelected(); 
 if (typeof razCCerror == 'undefined') return; 
  		ccError=razCCerror(pid);
		CheckCreditCardNumber(el.value, pid);
		if (!ccError) {
			el.value='';
		}
}
	 

     
     
if (typeof addOpcTriggerer != 'undefined')
{
  addOpcTriggerer('callAfterPaymentSelect', 'initStripe()'); 
  addOpcTriggerer('callSubmitFunct', 'opc_createToken');
  addOpcTriggerer('callAfterResponse', 'initStripe()'); 
  
}			
/* ]]> */
</script>			