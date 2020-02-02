 <?php
/**
 * @version		$Id: default.php 21837 2011-07-12 18:12:35Z  $
 * @package		RuposTel OnePage Utils
 * @subpackage	com_onepage
 * @copyright	Copyright (C) 2005 - 2013 RuposTel.com
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
?>
<script type="text/javascript">
//<![CDATA[  
		if (!((typeof pickupdeliveryloaded != 'undefined') && (pickupdeliveryloaded != null)))
		{
		if (typeof callAfterResponse != 'undefined')
		addOpcTriggerer("callAfterResponse",  "afterResponse(html)"); 
		//callAfterResponse.push("afterResponse(html)"); 
		
		if (typeof callBeforeLoader != 'undefined')
		addOpcTriggerer("callBeforeLoader",  "beforeLoader(cmd)"); 
		//callBeforeLoader.push("beforeLoader(cmd)"); 
		
		if (typeof callSubmitFunct != 'undefined')
		addOpcTriggerer("callSubmitFunct",  "validatePickupDelivery"); 
		//callSubmitFunct.push("validatePickupDelivery"); 

		
		var pickupdeliveryloaded = true; 
		}
		<?php 
		if (!empty($viewData['js'])) echo $viewData['js']; 
		
		if (!empty($viewData['method']->debug)) {
			?> var pf_debug = true; <?php 
		}
		else {
			?> var pf_debug = false; <?php 
		}
		?>
		
		
		/*
	window.addEvent('domready', function() {

			
			SqueezeBox.assign($$('a.pfdmodal'), {
				parse: 'rel'
			});
		});
		*/

	
		
//]]> 
</script>

<?php
if (class_exists('JHtmlOPC'))
JHtmlOPC::_('behavior.modal', 'a.pdfmod'); 