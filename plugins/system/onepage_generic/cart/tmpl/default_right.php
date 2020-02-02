<?php 
/**
** Parts of this code is written by joomlaprofessionals.com Copyright (c) 2012, 2015 All Right Reserved.
** Many part of this code is from VirtueMart Team Copyright (c) 2004 - 2015. All rights reserved.
** Some parts might even be Joomla and is Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved. 
** http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
** This source is free software. This version may have been modified pursuant
** to the GNU General Public License, and as distributed it includes or
** is derivative of works licensed under the GNU General Public License or
** other free or open source software licenses.
**
** THIS CODE AND INFORMATION ARE PROVIDED "AS IS" WITHOUT WARRANTY OF ANY 
** KIND, EITHER EXPRESSED OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
** IMPLIED WARRANTIES OF MERCHANTABILITY AND/OR FITNESS FOR A
** PARTICULAR PURPOSE.

** <author>Joomlaproffs / Virtuemart team</author>
** <email>info@joomlaprofessionals.com</email>
** <date>2015</date>
*/
defined('_JEXEC') or die('Restricted access');
 
$plugin=JPluginHelper::getPlugin('system','onepage_generic');
$params=new JRegistry($plugin->params);

$button_primary_class  = $params->get("button_primary","opg-button-primary");

?>

<script type="text/javascript">
var selected_payment  = <?php echo $this->selectedPayment; ?>;
var selected_shipment  = <?php echo $this->selectedShipment; ?>;
jQuery(document).ready(function(){

  if(jQuery("#paymentsdiv").length > 0)
  {
	   jQuery("#paymentsdiv input").each(function(){
			if(jQuery(this).val() == selected_payment)  
			{
				 jQuery(this).prop('checked', true);
			}
	   });
  }
  
   jQuery("#paymentsdiv input").each(function(){
		if(jQuery(this).val() == selected_payment)  
		{
			 jQuery(this).prop('checked', true);
		}
   });

   jQuery("#shipmentdiv input").each(function(){
		if(jQuery(this).val() == selected_shipment)  
		{
			 jQuery(this).prop('checked', true);
		}
   });
});

</script>


  <div class="opg-width-1-1 opg-margin-bottom">
 	  <h3 class="opg-h3"><?php echo JText::_('PLG_SYSTEM_VMUIKIT_ONEPAGE_CHECKOUT'); ?></h3>
  </div>

  <?php
   $tempcount = 0;
   foreach($this->cart->BTaddress["fields"] as $singlefield) 
    {
	  if($singlefield['name']=='virtuemart_country_id' || $singlefield['name'] == "virtuemart_state_id") 
	  {
	    $tempcount++;
	  }
	}
   $hidecountry = $params->get('hide_countryfield',0);
   $hidestate = $params->get('hide_statefield',0);
   if($hidestate && $hidecountry)
   {
    $tempcount = 0;
   }
   if($tempcount > 0)
   {
	       echo ' <div class="opg-width-1-1 opg-panel opg-panel-box">';
   }
   
   $popupaddress = $params->get("popup_address", 1);
   if($popupaddress > 1)
   {
       $cartfields =  $this->cart->STaddress["fields"];
	   $countryfield = 'shipto_virtuemart_country_id';
	   $statefield  =  'shipto_virtuemart_state_id';
   }
   else
   {
	   $cartfields =  $this->cart->BTaddress["fields"];
	   $countryfield = 'virtuemart_country_id';
	   $statefield  =  'virtuemart_state_id';
   }
   
   foreach($cartfields as $singlefield) 
    {
	  if($singlefield['name']== $countryfield) 
	  {
	     $hidecountry_class = "";
	     if($hidecountry)
		 {
		   $hidecountry_class = "opg-hidden";
		 }
		 echo '<div class="'.$hidecountry_class.'">';
	     echo '				<label class="' . $singlefield['name'] . '" for="' . $singlefield['name'] . '_field">' . "\n";
	     echo '					' . $singlefield['title'] . ($singlefield['required'] ? ' *' : '') . "\n";
 	     echo '				</label>' . "<br />";
		 $replacetext = '<select onchange="javascript:updateaddress(1);"';
	  	 $singlefield['formcode']=str_replace('<select',$replacetext,$singlefield['formcode']);
		 $singlefield['formcode']=str_replace('vm-chzn-select','',$singlefield['formcode']);
		 echo $singlefield['formcode'];
		 echo '</div>';
	  }
	  else if($singlefield['name'] == $statefield)
	  {
	     $hidestate_class = "";
	     if($hidestate)
		 {
		   $hidestate_class = "opg-hidden";
		 }
		 echo '<div class="'.$hidestate_class.'">';
	     echo '				<label class="' . $singlefield['name'] . '" for="' . $singlefield['name'] . '_field">' . "\n";
	     echo '					' . $singlefield['title'] . ($singlefield['required'] ? ' *' : '') . "\n";
 	     echo '				</label>' . "<br />";

		 $replacetext = '<select onchange="javascript:updateaddress(2);"';
	  	 $singlefield['formcode']=str_replace('<select',$replacetext,$singlefield['formcode']);
		 if($singlefield['required'])
		 {
		  $singlefield['formcode']=str_replace('vm-chzn-select','required',$singlefield['formcode']);
		 }
		 else
		 {
		  $singlefield['formcode']=str_replace('vm-chzn-select','',$singlefield['formcode']);
		 } 
		 echo $singlefield['formcode'];
		 echo '</div>';
	  }
	}
  
   if($tempcount > 1)
    {
		 echo '</div>';
    }
  ?>

  
  <?php
   $shipmenthideclass= "";
   $oneshipmenthide = "no";
   if(count($this->shipments_shipment_rates) == 1)
   {
     if($params->get('hide_oneshipment',0))
	 {
	     $shipmenthideclass= "opg-hidden";
	 }
   }
    if($params->get('hide_oneshipment',0))
	 {
		 $oneshipmenthide = "yes";
	 }
	 ?>
   <input type="hidden" name="oneshipmenthide" id="oneshipmenthide" value="<?php echo $oneshipmenthide; ?>" />
   <input type="hidden" name="auto_shipmentid" id="auto_shipmentid" value="<?php echo vmconfig::get("set_automatic_shipment");  ?>" />
	 <?php

	  $listshipments = $params->get("list_allshipment", 0);				
	  if($listshipments)
	  {
	  ?>
	  <div id="shipment_select" class="opg-width-1-1 opg-panel-box opg-margin-small-top <?php echo  $shipmenthideclass; ?>">
	  <div id="shipmentdiv">
		   <h3 class="opg-panel-title"><?php echo JText::_('COM_VIRTUEMART_CART_EDIT_SHIPPING'); ?></h3>
	  <?php
	      echo '<div id="shipment_nill"></div>';
	      echo "<fieldset id='shipment_selection'>";					
	      echo '<ul class="opg-list" id="shipment_ul">';
		  foreach($this->shipments_shipment_rates as $rates) 
		  {
		     if(strpos($rates, "checked") !== false)
		     {
				 $actclass = "liselcted";
			 }
			 else
			 {
			     $actclass = "";
			 }
			 $replacetxt = "";
			 $replacetxt = '<input onclick="setshipment()"';
			 $rates = str_replace("<input", $replacetxt, $rates);
		     echo '<li class="'.$actclass.'">';
			 echo '<label class="opg-width-1-1">'.$rates.'</label>';
			 echo '</li><hr class="opg-margin-small-bottom opg-margin-small-top" />';
		  }
		  echo "</ul>";
		  if(count($this->shipments_shipment_rates) == 0)
		  {
			  $text = "";
		  	  $shipmentnilltext = JText::sprintf('COM_VIRTUEMART_NO_SHIPPING_METHODS_CONFIGURED', $text);
		  	  echo '<p id="shipmentnill" class="opg-text-warning">'.$shipmentnilltext.'</p>';
		  }
		  echo "</fieldset>";
	  ?>
	   </div>
	  </div>
	  <?php
	  }
	  else //LIST SHIPMENT ELSE START
      {
   ?>
  
  <div id="shipment_select" class="opg-width-1-1 opg-panel-box opg-margin-small-top <?php echo  $shipmenthideclass; ?>">
		<h3 class="opg-panel-title"><?php echo JText::_('COM_VIRTUEMART_CART_EDIT_SHIPPING'); ?></h3>
		<div id="shipment_fulldiv" class="opg-width-1-1">
        <?php
				 
					 $shipmentmethod_id = $this->cart->virtuemart_shipmentmethod_id;
					 $selectedshipment = "";
					 $shipmentpresent = 0;
					 foreach($this->shipments_shipment_rates as $rates) 
					 {
					     if(strpos($rates, "checked") !== false)
					  	 {
						
						    $tmpdis = strip_tags($rates , '<span><img>');
						    echo '<table class="opg-table opg-table-striped" id="shipmenttable"><tr id="shipmentrow"><td id="shipmentdetails">';
							$tmpdis =  str_replace("</span><span" , "</span><br /><span", $tmpdis);
							$tmpdis =  str_replace("vmshipment_description" , "vmshipment_description opg-text-small", $tmpdis);
							$tmpdis =  str_replace("vmshipment_cost" , "vmshipment_cost opg-text-small", $tmpdis);
						    echo $tmpdis;
							echo '</td>';
							if(count($this->shipments_shipment_rates) > 1)
							{
							    $target = "{target:'#shipmentdiv'}";
							    echo '<td id="shipchangediv" class="opg-width-1-4">';
					            echo '<a class="opg-button '.$button_primary_class.'" href="#" data-opg-modal="'.$target.'">'.JText::_("PLG_SYSTEM_VMUIKIT_ONEPAGE_CHNAGE").'</a>';
					 			echo '</td>';
							}
							echo '</tr></table>';
							$shipmentpresent = 1;
						 }
					 }	
					 
					 if(!$shipmentpresent)
					 {
					    if(count($this->shipments_shipment_rates) > 0)
						{
				            $tmpdis = strip_tags($this->shipments_shipment_rates[0] , '<span><img>');
						    echo '<table class="opg-table opg-table-striped" id="shipmenttable"><tr id="shipmentrow"><td id="shipmentdetails">';
						    $tmpdis =  str_replace("</span><span" , "</span><br /><span", $tmpdis);
							$tmpdis =  str_replace("vmshipment_description" , "vmshipment_description opg-text-small", $tmpdis);
							$tmpdis =  str_replace("vmshipment_cost" , "vmshipment_cost opg-text-small", $tmpdis);
							echo $tmpdis;
							echo '</td>';
							if(count($this->shipments_shipment_rates) > 1)
							{
							    $target = "{target:'#shipmentdiv'}";
							    echo '<td id="shipchangediv" class="opg-width-1-4">';
					            echo '<a class="opg-button '.$button_primary_class.'" href="#" data-opg-modal="'.$target.'">'.JText::_("PLG_SYSTEM_VMUIKIT_ONEPAGE_CHNAGE").'</a>';
					 			echo '</td>';
							}
							echo '</tr></table>'; 
							$shipmentpresent = 1;
						}
						else
						{
						  $text = "";
					  	  $shipmentnilltext = JText::sprintf('COM_VIRTUEMART_NO_SHIPPING_METHODS_CONFIGURED', $text);
					  	  echo '<p id="shipmentnill" class="opg-text-warning">'.$shipmentnilltext.'</p>';
						}
				    }
			?>
			</div>
		
   </div>
   <?php
   }//LIST SHIPMENT ELSE END
   $paymenthideclass= "";
   $onepaymenthide = "no";
   if(count($this->paymentplugins_payments) == 1)
   {
     if($params->get('hide_onepayment',0))
	 {
	     $paymenthideclass= "opg-hidden";
	 }
   }
   if($params->get('hide_onepayment',0))
	 {
		 $onepaymenthide = "yes";
	 }
   ?>
    <input type="hidden" name="onepaymenthide" id="onepaymenthide" value="<?php echo $onepaymenthide; ?>" />
    <input type="hidden" name="auto_paymentid" id="auto_paymentid" value="<?php echo vmconfig::get("set_automatic_payment");  ?>" />
   <?php
   $listpayments = $params->get("list_allpayment", 0);	
   if($listpayments)
   {
   ?>
   <div id="payment_select" class="opg-width-1-1 opg-panel-box opg-margin-small-top <?php echo $paymenthideclass; ?>">
   <div id="paymentdiv">
   <h3 class="opg-panel-title"><?php echo JText::_('COM_VIRTUEMART_CART_SELECTPAYMENT'); ?></h3>
   <?php
	    $paymentsarr = $this->paymentplugins_paymentsnew;
		echo '<div id="payment_nill"></div>';
	    echo '<div id="paymentsdiv">';
		echo '<ul class="opg-list" id="payment_ul">';
		foreach($paymentsarr as $pay)
		{
		      $pos = strpos($pay, '</span></span>');
			  $pay = substr($pay, 0, $pos); 
		      $replacetxt = "";
			  $replacetxt = '<input onclick="setpayment()"';
			  $pay = str_replace("<input", $replacetxt, $pay);
			  echo '<li>'.$pay.'<hr class="opg-margin-small-bottom opg-margin-small-top" /></li>';
		}
  	    echo '</ul>';
		if(count($paymentsarr) == 0)
		{
		    $text = "";
		    $paymentnilltext = JText::sprintf('COM_VIRTUEMART_NO_PAYMENT_METHODS_CONFIGURED', $text);
		    echo '<p id="paymentnill" class="opg-text-warning">'.$paymentnilltext.'</p>';
		}
		
	    echo '</div>';
   ?>
   </div>
   </div>	
   <?php
   }
   else //LIST PAYMENTS ELSE START
   {
  ?>
   <div id="payment_select" class="opg-width-1-1 opg-panel-box opg-margin-small-top <?php echo $paymenthideclass; ?>">
   <h3 class="opg-panel-title"><?php echo JText::_('COM_VIRTUEMART_CART_SELECTPAYMENT'); ?></h3>
	  <div id="payment_fulldiv" class="opg-width-1-1">
      <?php
				$paymentsarr = $this->paymentplugins_paymentsnew;
			    $paymentpresent = 0;

				foreach($this->paymentplugins_paymentsnew as $tmppay) 
				{
				    $vmpayid = '"'.$this->cart->virtuemart_paymentmethod_id.'"';
			 	    if(strpos($tmppay , "checked") !== false)
				    {
					
						    $tmpdis = strip_tags($tmppay , '<span><img>');
						    echo '<table class="opg-table opg-table-striped" id="paymentable"><tr id="paymentrow"><td id="paymentdetails">';
							$pos = strpos($tmpdis, '</span></span>');
						    $tmpdis = substr($tmpdis, 0, $pos); 
						    $tmpdis =  str_replace("</span><span" , "</span><br /><span", $tmpdis);
							$tmpdis =  str_replace("vmpayment_description" , "vmpaymentt_description opg-text-small", $tmpdis);
							$tmpdis =  str_replace("vmpayment_cost" , "vmpayment_cost opg-text-small", $tmpdis);
						    echo $tmpdis;
							echo '</td>';
							if(count($this->paymentplugins_paymentsnew) > 1)
							{  
							    $target = "{target:'#paymentdiv'}";
							    echo '<td id="paychangediv" class="opg-width-1-4">';
					            echo '<a class="opg-button '.$button_primary_class.'" data-opg-modal="'.$target.'">'.JText::_("PLG_SYSTEM_VMUIKIT_ONEPAGE_CHNAGE").'</a>';
					 			echo '</td>';
							}
							echo '</tr></table>'; 
							$paymentpresent = 1;
 				    }
				   
				}
				
				if(!$paymentpresent)
				{
					  if(count($this->paymentplugins_paymentsnew) > 0)
					  {
					        $paym_arr = array();
					        $paym_arr = $this->paymentplugins_paymentsnew;
				            $tmpdis = strip_tags($paym_arr[0] , '<span><img>');
						    echo '<table class="opg-table opg-table-striped" id="paymentable"><tr id="paymentrow"><td id="paymentdetails">';
							$pos = strpos($tmpdis, '</span></span>');
						    $tmpdis = substr($tmpdis, 0, $pos); 
						    $tmpdis =  str_replace("</span><span" , "</span><br /><span", $tmpdis);
							$tmpdis =  str_replace("vmpayment_description" , "vmpayment_description opg-text-small", $tmpdis);
							$tmpdis =  str_replace("vmpayment_cost" , "vmpayment_cost opg-text-small", $tmpdis);
						    echo $tmpdis;
							echo '</td>';
							if(count($this->paymentplugins_paymentsnew) > 1)
							{
							    $target = "{target:'#paymentdiv'}";
							    echo '<td id="paychangediv" class="opg-width-1-4" >';
					            echo '<a class="opg-button '.$button_primary_class.'" href="#" data-opg-modal="'.$target.'">'.JText::_("PLG_SYSTEM_VMUIKIT_ONEPAGE_CHNAGE").'</a>';
					 			echo '</td>';
							}
							echo '</tr></table>'; 
							$paymentpresent = 1;
							
					}
					else
					{
					    
					    $text = "";
					    $paymentnilltext = JText::sprintf('COM_VIRTUEMART_NO_PAYMENT_METHODS_CONFIGURED', $text);
					    echo '<p id="paymentnill" class="opg-text-warning">'.$paymentnilltext.'</p>';
					}
				}
			?>
			</div>
   </div>
   <?php
   } //LIST PAYMENT ELSE END			
   ?>
   <?php echo $this->loadTemplate('shopper'); ?>
