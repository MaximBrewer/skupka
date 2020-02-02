<?php  defined('_JEXEC') or die();
/*
 * @author Valérie Isaksen
 * @copyright Copyright (C) 2013 alatak.net  - All rights reserved.
 * @license listraxx_license.txt Proprietary
 * You are not allowed to distribute or sell this code.
 * You are not allowed to modify this code.
 * http://www.istraxx.com
 */

?>
<div id="paymentForm">
<input type="radio" name="virtuemart_paymentmethod_id"
       id="payment_id_<?php echo $viewData['plugin']->virtuemart_paymentmethod_id; ?>"
       value="<?php echo $viewData['plugin']->virtuemart_paymentmethod_id; ?>" >




<label for="payment_id_<?php echo $viewData['plugin']->virtuemart_paymentmethod_id; ?>">

    <span class="vmpayment">
        <?php if (!empty($viewData['payment_logo'] )) { ?>
         <span class="vmCartPaymentLogo"><?php echo $viewData ['payment_logo']; ?> </span>
     <?php } ?>
        <span class="vmpayment_name"><?php echo $viewData['plugin']->payment_name; ?></span>
        <?php if (!empty($viewData['plugin']->payment_desc )) { ?>
            <span class="vmpayment_description"><?php echo $viewData['plugin']->payment_desc; ?></span>
        <?php } ?>
        <?php if (!empty($viewData['payment_cost']  )) { ?>
            <span class="vmpayment_cost"> (<?php echo JText::_ ('COM_VIRTUEMART_PLUGIN_COST_DISPLAY') .' '.  $viewData['payment_cost']  ?>)</span>
        <?php } ?>

    </span>
</label>
<div id="extra_payment_<?php echo $viewData['plugin']->virtuemart_paymentmethod_id; ?>" style="display: none;">
    <div class="ddmandate_box">
        <div class="ddmandate_box_displaypayment">


            <div class="ddmandate_box_bottom">
                <div class="ddmandate_left" style="width: 100%">
                    <div
                        class="ddmandate_box_title"><?php echo JText::_('VMPAYMENT_ISTRAXX_DDMANDATE_DEBTORNAME'); ?></div>
                    <input   type="text"
                           name="ddmandate_debtorname"
                           value="<?php echo $viewData['payment_params']['ddmandate_debtorname']; ?>"
                           style="width: 100%"/>
                </div>
                <div class="ddmandate_left" style="width: 100%">
                    <div class="ddmandate_box_title"><?php echo JText::_('VMPAYMENT_ISTRAXX_DDMANDATE_STREET'); ?></div>
                    <textarea rows="3" cols="30" style="width: 100%" name="ddmandate_street"
                               > <?php echo $viewData['payment_params']['ddmandate_street']; ?></textarea>
                </div>
                <div class="ddmandate_left" style="width: 40%">
                    <div class="ddmandate_box_title"><?php echo JText::_('COM_VIRTUEMART_SHOPPER_FORM_ZIP'); ?></div>
                    <input   type="text"
                           name="ddmandate_zip"
                           value="<?php echo $viewData['payment_params']['ddmandate_zip']; ?>" style="width: 98%"/>
                </div>

                <div class="ddmandate_right" style="width: 60%">
                    <div class="ddmandate_box_title"><?php echo JText::_('COM_VIRTUEMART_SHOPPER_FORM_CITY'); ?></div>
                    <input   type="text"
                           name="ddmandate_city"
                           value="<?php echo $viewData['payment_params']['ddmandate_city']; ?>" style="width: 100%"/>
                </div>
                <div class="ddmandate_left" style="width: 100%">
                    <div
                        class="ddmandate_box_title"><?php echo JText::_('VMPAYMENT_ISTRAXX_DDMANDATE_BANK_NAME'); ?></div>
                    <input   type="text"
                           name="ddmandate_bankname"
                           value="<?php echo $viewData['payment_params']['ddmandate_bankname']; ?>"
                           style="width: 100%"/>
                </div>
                <div class="ddmandate_left" style="width: 70%">
                    <div class="ddmandate_box_title"><?php echo JText::_('VMPAYMENT_ISTRAXX_DDMANDATE_IBAN'); ?></div>
                    <input maxlength="35"
                           type="text" name="ddmandate_iban" id="ddmandate_iban"
                           value="<?php echo $viewData['payment_params']['ddmandate_iban']; ?>" style="width: 98%"/>
                </div>
                <div class="ddmandate_right" style="width: 30%">
                    <div class="ddmandate_box_title"><?php echo JText::_('VMPAYMENT_ISTRAXX_DDMANDATE_BIC'); ?></div>
                    <input maxlength="11"
                           type="text" name="ddmandate_bic"
                           value="<?php echo $viewData['payment_params']['ddmandate_bic']; ?>" style="width: 100%"/>
                </div>
            </div>

            <div class="ddmandate_signing_text_box">
                <?php echo $viewData['signing_text']; ?>
            </div>
            <div class="ddmandate_refund_text_box">
                <?php echo $viewData['refund_text']; ?>
            </div>
            <div class="ddmandate_signaturepad">
                <label
                    for="ddmandate_signature_name">
               <?php echo JText::_('VMPAYMENT_ISTRAXX_DDMANDATE_ENTER_YOUR_NAME') ?></label>
                <input type="text" name="ddmandate_signature_name"
                       value="<?php echo $viewData['payment_params']['ddmandate_signature_name']; ?>"
                       id="ddmandate_signature_name" class="ddmandate_signature_name"  >

                
                <ul class="sigNav" style="display: block;">
                   
                   
                    <li class="clearButton" style="display: list-item;"><a
                            href="#clear"><?php echo JText::_('VMPAYMENT_ISTRAXX_DDMANDATE_CLEAR') ?></a></li>
                </ul>
                <div class="sig sigWrapper current" style="display: block;">
				    <div class="typed"></div>
                    
                    <canvas class="pad" width="380" height="55"></canvas>
					<input type="hidden" name="output" class="output">
                    <input type="hidden" name="ddmandate_signature_output" class="ddmandate_signature_output">
                </div>

            </div>
        </div>
  </div>
 </div>
</div>
<!--/label -->

<?php
$document = JFactory::getDocument();
$js = "
/* <![CDATA[ */




jQuery(document).ready(function($) {


$('#paymentForm').addClass('sigPad');
	var sig = " . $viewData['payment_params']['ddmandate_signature_output'] . "
	$('.sigPad').signaturePad({drawOnly : true,
		name:'.ddmandate_signature_name',
		output:'.ddmandate_signature_output',
		defaultAction: 'drawIt',
		drawOnly: true,
		sig : '.sig2',
		lineTop:50,
		sigNav : '.sigNav',
		errorMessageDraw:'" . JText::_("VMPAYMENT_ISTRAXX_DDMANDATE_SIGN_DOCUMENT") . "',
		iVerticalSectors:8,
		iVerticalSectorsWithContent:3,
		iMinimumPixelsPerSector:5,
		iMaximumPixelsPerSector:200,
		validateFields : false
	}).regenerate(sig);
});


 
/* ]]> */
";
$document->addScriptDeclaration($js);


