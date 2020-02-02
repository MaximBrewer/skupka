<?php
defined ('_JEXEC') or die();
/**
 * @version $Id: payment_form.php 6510 2012-10-08 11:26:10Z alatak $
 *
 * @author Valérie Isaksen
 * @package VirtueMart
 * @copyright Copyright (c) 2004 - 2012 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

$code2 = $viewData['payment_params']['countryCode'];
$sType = $viewData['payment_params']['sType'];
if ($sType=='part') {
	$imageType='account';
} else {
	$imageType='invoice';
}


// missing house_extension,ysalary,companyName
?>
<!-- KLARNA BOX -->
<?php echo $viewData['payment_params']['checkout']; ?>
<script type="text/javascript">
	<!--
	klarna.countryCode = '<?php echo $viewData['payment_params']['countryCode']; ?>';
	klarna.language = '<?php echo $viewData['payment_params']['langISO']; ?>';
	klarna.sum = '<?php echo $viewData['payment_params']['sum']; ?>';
	klarna.eid = '<?php echo $viewData['payment_params']['eid']; ?>';
	klarna.flag = '<?php echo $viewData['payment_params']['flag']; ?>';
	klarna.unary_checkout = '<?php echo @$viewData['payment_params']['unary_checkout']; ?>';
	klarna.type = '<?php echo $sType ?>';
	klarna.lang_companyNotAllowed = '<?php echo JText::_ ('VMPAYMENT_KLARNA_COMPANY_NOT_ALLOWED'); ?>';
	klarna.pid = '<?php echo $viewData['payment_params']['payment_id']; ?>';
	if (typeof klarna.red_baloon_content == "undefined" || klarna.red_baloon_content == "") {
		klarna.red_baloon_content = '<?php echo @$viewData['payment_params']['red_baloon_content']; ?>';
		klarna.red_baloon_box = '<?php echo @$viewData['payment_params']['red_baloon_paymentBox']; ?>';
	}

	klarna.lang_personNum = '<?php echo JText::_ ('VMPAYMENT_KLARNA_PERSON_NUMBER'); ?>';
	klarna.lang_orgNum = '<?php echo JText::_ ('VMPAYMENT_KLARNA_ORGANISATION_NUMBER'); ?>';

	klarna.select_bday = '<?php echo @$viewData['payment_params']['fields']['birth_day']; ?>';
	klarna.select_bmonth = '<?php echo @$viewData['payment_params']['fields']['birth_month']; ?>';
	klarna.select_byear = '<?php echo @$viewData['payment_params']['fields']['birth_year']; ?>';
	klarna.gender = '<?php echo @$viewData['payment_params']['fields']['gender']; ?>';

	klarna.invoice_ITId = 'klarna_invoice_type';
	// Mapping to the real field names which may be prefixed
	klarna.params = {
		birth_day:'klarna_birth_day',
		birth_month:'klarna_birth_month',
		birth_year:'klarna_birth_year',
		companyName:'klarna_company_name',
		socialNumber:'klarna_socialNumber',
		firstName:'klarna_firstName',
		lastName:'klarna_lastName',
		gender:'klarna_gender',
		street:'klarna_street',
		homenumber:'klarna_homenumber',
		house_extension:'klarna_house_extension',
		city:'klarna_city',
		zipcode:'klarna_zip',
		reference:'klarna_reference',
		phoneNumber:'klarna_phone',
		emailAddress:'klarna_email',
		invoiceType:'klarna_invoice_type',
		shipmentAddressInput:'klarna_shipment_address',
		consent:'klarna_consent'


	}
    function changeKlarna()
	{
	    var e = document.getElementsByName("virtuemart_paymentmethod_id");
		sP = null; 
	  
	  if (e)
      if (e.checked)
	  {
	    sP = e; 

	  }
	  else
	  {

	  for (i=0;i<e.length;i++)
	  {
	   
	  
	   if (e[i].checked==true)
	     sP = e[i]; 
	  }
	  }
	  
	  if (sP != null)
	   {
	     atr = sP.getAttribute('data-stype'); 
		 el = document.getElementById('klarna_opc_method'); 
		 if ((typeof el != 'undefined') && (el!=null))
		 {
		 if (atr != null)
		 {
		 if (atr == 'invoice') el.value='klarna_invoice'; 
		 if (atr == 'part') el.value = 'klarna_partPayment'; 
		 if (atr == 'spec') el.value = 'klarna_speccamp';
		 
		 }
		 }
		 else
		 {
		   // create the input tag
		   var objElement = document.createElement("input");
		   objElement.setAttribute("type", "hidden");
		   objElement.setAttribute("name", "klarna_opc_method");
		   objElement.setAttribute("id", "klarna_opc_method");
		   if (atr != null)
		   objElement.setAttribute("value", atr);
		   else
		   objElement.setAttribute("value", 'invoice');
		   document.adminForm.appendChild(objElement); 
		 }
	   }
	  
	}
	function changeKlarnaTotals()
	{
	  send_special_cmd(null, 'runpay'); 
	}
	
	addOpcTriggerer('callAfterPaymentSelect', 'changeKlarna()'); 
	//addOpcTriggerer('callAfterShippingSelect', 'changeKlarnaTotals()'); 
	//-->
</script>
<?php 
if (!defined('klarna_paymentmethod_selected')) 
{
?>
<input type="hidden" value="<?php echo $sType; ?>" id="klarna_paymentmethod" name="klarna_paymentmethod" />
<?php
define('klarna_paymentmethod_selected', 1); 
}

if ($sType == 'spec') 
{
$document = JFactory::getDocument();
$document->addScript('//cdn.klarna.com/public/kitt/toc/v1.0/js/klarna.terms.min.js'); 
  } 
  ?>
<script type="text/javascript">
	jQuery(function () {
		klarna.methodReady('<?php echo $sType ?>');
	});
</script>



<div class="klarna_box_container">
<div class="klarna_box" style="border: none; width; 100%; min-width: 320px; display: block; background: none;" id="klarna_box_<?php echo $sType ?>">
<script type="text/javascript">
	openAgreement('<?php echo $viewData['payment_params']['countryCode']; ?>');
</script>
<div class="klarna_box_top">
	<div id="klarna_box_<?php echo $sType ?>_top_right" class="klarna_box_top_right">

		<div class="klarna_box_top_agreement">
<?php if ($sType == 'spec') 
			{ 
			?>
			<!-- Special payment External js(SPEC) -->
			<a id="specialCampaignPopupLink" href="javascript:ShowKlarnaSpecialPaymentPopup()"></a>
			<?php
		}
		else {
			$popupTotal = ($sType == 'part') ? $viewData['payment_params']['sum'] : $viewData['payment_params']['fee'];
			?>
			<!-- Part/invoice payment External js -->
			<a href="javascript:ShowKlarnaPopup('<?php echo $viewData["payment_params"]["eid"]; ?>', '<?php echo $popupTotal; ?>','<?php echo $sType; ?>')">
				<?php echo JText::_ ('VMPAYMENT_KLARNA_KLARNA_'.$sType.'_AGREEMENT'); ?>
			</a>
			<!-- payment External js END -->
			<?php } ?>
		</div>
		<div class="klarna_box_bottom_languageInfo">
			<img src="<?php echo VMKLARNAPLUGINWEBASSETS . '/images/' ?>share/notice.png"
			     alt="<?php echo JText::_ ('VMPAYMENT_KLARNA_LANGUAGESETTING_NOTE_' . $code2); ?>"/>
		</div>
	</div>
	<?php
	if ($sType == 'spec') 
	{
		$logo = VMKLARNAPLUGINWEBASSETS . '/images/' . 'logo/klarna_logo.png';
	}
	else 
	{
		//$logo = VMKLARNAPLUGINWEBASSETS . '/images/' . 'logo/klarna_' . $sType . '_' . $code2 . '.png';
		$logo ="https://cdn.klarna.com/public/images/".strtoupper($code2)."/badges/v1/". $imageType ."/".$code2."_". $imageType ."_badge_std_blue.png?height=55&eid=". $viewData['payment_params']['eid'];
	}
	?>
	<img class="klarna_logo" src="<?php echo $logo ?>"
	     alt="<?php echo JText::_ ('VMPAYMENT_KLARNA_IMG_LOGO_'.$sType); ?>"/>
</div>
<?php
include(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php'); 

$cart = VirtueMartCart::getCart();
$sc = ''; 
if (!empty($cart->BT))
if (!empty($cart->BT['socialNumber']))
$sc = $cart->BT['socialNumber']; 

if (empty($sc)) $sc = $viewData['payment_params']['fields']['socialNumber'];

if ($code2 != 'de')
if (in_array('socialNumber', $custom_rendering_fields) || (!isset($cart->BT['socialNumber'])))
echo '<input title="'.JText::_ ('VMPAYMENT_KLARNA_NOTICE_SOCIALNUMBER_' . strtoupper ($code2)).'" alt="'.JText::_('VMPAYMENT_KLARNA_PERSON_NUMBER').'" placeholder="'.JText::_('VMPAYMENT_KLARNA_PERSON_NUMBER').'" type="text" id="'.$sType.'_klarna_socialNumber_field" name="'.$sType.'_klarna_socialNumber" size="" value="'.$sc.'">'; 

?>
<div class="klarna_box_bottom" style="min-height: 0;">
<div class="klarna_box_bottom_contents">

<?php 
if ($code2 == 'de' || $code2 == 'nl') {  ?>

<div class="klarna_box_bottom_title"><?php echo vmText::_ ('VMPAYMENT_KLARNA_BIRTHDAY'); ?></div>
<div class="klarna_box_bottom_input_combo" style="width: 100%">
	<div class="klarna_left" style="width: 30%">
		<select style="width: 98%" placeholder="<?php echo vmText::_ ('VMPAYMENT_KLARNA_DATE_DAY'); ?>" name="<?php echo $sType ?>_klarna_birth_day"
		        class="selectBox_bday">
			<option value="0"><?php echo vmText::_ ('VMPAYMENT_KLARNA_DATE_DAY'); ?></option>
			<option value="01">01</option>
			<option value="02">02</option>
			<option value="03">03</option>
			<option value="04">04</option>
			<option value="05">05</option>
			<option value="06">06</option>
			<option value="07">07</option>
			<option value="08">08</option>
			<option value="09">09</option>
			<option value="10">10</option>
			<option value="11">11</option>
			<option value="12">12</option>
			<option value="13">13</option>
			<option value="14">14</option>
			<option value="15">15</option>
			<option value="16">16</option>
			<option value="17">17</option>
			<option value="18">18</option>
			<option value="19">19</option>
			<option value="20">20</option>
			<option value="21">21</option>
			<option value="22">22</option>
			<option value="23">23</option>
			<option value="24">24</option>
			<option value="25">25</option>
			<option value="26">26</option>
			<option value="27">27</option>
			<option value="28">28</option>
			<option value="29">29</option>
			<option value="30">30</option>
			<option value="31">31</option>
		</select>
	</div>
	<div class="klarna_left" style="width: 40%">
		<select style="width: 98%" name="<?php echo $sType ?>_klarna_birth_month" class="selectBox_bmonth" placeholder="<?php echo vmText::_ ('VMPAYMENT_KLARNA_DATE_MONTH'); ?>">
			<option value="0" selected="selected"><?php echo vmText::_ ('VMPAYMENT_KLARNA_DATE_MONTH'); ?></option>
			<option value="01"><?php echo vmText::_ ('VMPAYMENT_KLARNA_MONTH_1'); ?></option>
			<option value="02"><?php echo vmText::_ ('VMPAYMENT_KLARNA_MONTH_2'); ?></option>
			<option value="03"><?php echo vmText::_ ('VMPAYMENT_KLARNA_MONTH_3'); ?></option>
			<option value="04"><?php echo vmText::_ ('VMPAYMENT_KLARNA_MONTH_4'); ?></option>
			<option value="05"><?php echo vmText::_ ('VMPAYMENT_KLARNA_MONTH_5'); ?></option>
			<option value="06"><?php echo vmText::_ ('VMPAYMENT_KLARNA_MONTH_6'); ?></option>
			<option value="07"><?php echo vmText::_ ('VMPAYMENT_KLARNA_MONTH_7'); ?></option>
			<option value="08"><?php echo vmText::_ ('VMPAYMENT_KLARNA_MONTH_8'); ?></option>
			<option value="09"><?php echo vmText::_ ('VMPAYMENT_KLARNA_MONTH_9'); ?></option>
			<option value="10"><?php echo vmText::_ ('VMPAYMENT_KLARNA_MONTH_10'); ?></option>
			<option value="11"><?php echo vmText::_ ('VMPAYMENT_KLARNA_MONTH_11'); ?></option>
			<option value="12"><?php echo vmText::_ ('VMPAYMENT_KLARNA_MONTH_12'); ?></option>
		</select>
	</div>
	<div class="klarna_right" style="width: 30%">
		<select style="width: 100%" name="<?php echo $sType ?>_klarna_birth_year" class="selectBox_year" placeholder="<?php echo vmText::_ ('VMPAYMENT_KLARNA_DATE_YEAR'); ?>">
			<option selected="0"><?php echo vmText::_ ('VMPAYMENT_KLARNA_DATE_YEAR'); ?></option>
		</select>
	</div>
</div>


<?php }  ?>



<div class="">

	<div class="klarna_box_bottom_content">
		<?php if ($sType !== 'invoice') 
		{ 
		?>
		<div class="klarna_box_bottom_title"><?php echo JText::_ ('VMPAYMENT_KLARNA_' . $sType . '_PAYMENT'); ?>
		</div>
		<?php 
		if (!empty($viewData['payment_params']['pClasses'])) { 
		
		// to get rid of multiple plasses: 
		$na = array(); 
	    foreach ($viewData['payment_params']['pClasses'] as $pClass) {
		  if (!empty($pClass['string'] )) {
		     $na[$pClass['string']] = $pClass; 
		  }
		}
		
		?>
		<select name="<?php echo $sType ?>_klarna_paymentPlan">
		<?php
				foreach ($na as $pClass) {
					?>
					<option value="<?php echo $pClass['classId'] ?>">
						<?php echo  $pClass['string'] ?>
					</option>
						
					
					<?php
				}
				?>
		</select>
		<?php
		} 
		}
		?>

		
		
			
	</div>

</div>

<div class="" style="width: 100%; clear: both;">
<div class="klarna_box_bottom_content">
<?php if ($code2 != 'de' and $code2 != 'nl') { ?>
<?php // Now it is also asked for account payments
		if ($sType == 'invoice') { ?>
		<input type="hidden" name="<?php echo $sType ?>_klarna_invoice_type" value="private" />
		<?php
		if (false) { 
		?>
		
		<div class="klarna_box_bottom_title"><?php echo JText::_ ('VMPAYMENT_KLARNA_INVOICE_TYPE'); ?></div>
		
		<div class="klarna_box_bottom_radio_title" style="float: left">
		    <input type="radio" name="klarna_invoice_type" id="private" value="private" checked="checked" class="Klarna_radio"/>
			<label for="private"><?php echo JText::_ ('VMPAYMENT_KLARNA_INVOICE_TYPE_PRIVATE'); ?></label>
		</div>
		
		<div class="klarna_box_bottom_radio_title" style="float: none">
			<input type="radio" name="klarna_invoice_type" id="company" value="company" class="Klarna_radio"/>
			<label for="company"><?php echo JText::_ ('VMPAYMENT_KLARNA_INVOICE_TYPE_COMPANY'); ?></label>
		</div>
		<div class="klarna_box_bottom_input_combo"
		     style="width: 100%; display: none" id="invoice_box_company">
			<div id="left" style="width: 60%">
				<div class="klarna_box_bottom_title"><?php echo JText::_ ('VMPAYMENT_KLARNA_COMPANY_NAME'); ?></div>
				<input alt="<?php echo JText::_ ('VMPAYMENT_KLARNA_COMPANY_NAME'); ?>" type="text"
				       name="klarna_company_name" value="<?php echo @$viewData['payment_params']['fields']['company_name']; ?>"
				       style="width: 98%" />
			</div>
		</div>
	<?php  }
	}
	?>
<?php  } 
//echo JText::_ ('VMPAYMENT_KLARNA_NOTICE_SOCIALNUMBER_' . strtoupper ($code2));
?>
<div class="klarna_additional_information">
	<?php echo @$viewData['payment_params']['additional_information']; ?>
</div>

	<?php



if ($code2 == 'de') {
	$url = $viewData['payment_params']['agb_link'] . '&tmpl=component';
	$document = JFactory::getDocument ();
	$document->addScriptDeclaration ("
	jQuery(document).ready(function($) {
		$('a.agb').click( function(){
			$.facebox({
				iframe: '" . $url . "',
				rev: 'iframe|550|550'
			});
			return false ;
		});

	});
");
	?>
<div class="klarna_box_bottom_input_combo" style="width: 100%">
	<input type="checkbox" name="<?php echo $sType ?>_klarna_consent"
	       id="box_klarna_consent_<?php echo $sType ?>"
	       style="float: left; margin-right: 3px"/>

	<div class="klarna_box_bottom_title" style="width: 80%; margin-top: 3px">Mit der &Uuml;bermittlung der f&uuml;r die
		Abwicklung des Rechnungskaufes und einer Identit&auml;ts- und Bonit&auml;tspr&uuml;fung erforderlichen Daten an
		Klarna bin ich einverstanden. Meine <a
			href="javascript:ShowKlarnaConsentPopup('<?php echo $viewData["payment_params"]["eid"]; ?>','<?php echo $sType; ?>');">Einwilligung</a>
		kann ich jederzeit mit Wirkung f&uuml;r die Zukunft widerrufen. Es gelten die <a class="agb" rel="facebox"
		                                                                                 href="<?php echo $viewData['payment_params']['agb_link']; ?>">AGB</a>
		des H&auml;ndlers.
	</div>
</div>
	<?php
	} 
	?>
</div>
</div>
</div>
</div>
</div>
</div>
<input type="hidden" name="<?php echo $sType ?>_klarna_country_2_code" value="<?php echo $viewData['payment_params']['countryCode']; ?>"/>
<?php if ($code2 != 'se') { ?>
<input type="hidden" name="<?php echo $sType ?>_klarna_emailAddress" value="<?php echo $viewData["payment_params"]["fields"]['email']; ?>"/>
<?php } 
