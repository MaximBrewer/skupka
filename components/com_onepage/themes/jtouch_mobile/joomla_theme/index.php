<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
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
* 
*/

$path = 'components/com_onepage/themes/'.$selected_template.'/'; 

?><!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta http-equiv="content-type" content="text/html; charset=utf-8" />		

<link rel="stylesheet" href="<?php echo JHTMLOPC::getFullUrl('jquery.mobile.stepper.css', $path, array()); ?>" type="text/css" />
<link rel="stylesheet" href="<?php echo JHTMLOPC::getFullUrl('onepage.css', $path, array()); ?>" type="text/css" />
<link rel="stylesheet" href="<?php echo JHTMLOPC::getFullUrl('jquery.ui.all.css', 'components/com_virtuemart/assets/css/ui/'); ?>" type="text/css" />
<link rel="stylesheet" href="<?php echo JHTMLOPC::getFullUrl('jquery.mobile.custom.theme.min.css', 'components/com_onepage/themes/extra/jquery-mobile/'); ?>" type="text/css"></link>
<link rel="stylesheet" href="http://code.jquery.com/mobile/1.4.4/jquery.mobile-1.4.4.min.css" />

<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
<script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>

<script src="<?php echo JHTMLOPC::getFullUrl('jquery.mobile.custom.min.js', 'components/com_onepage/themes/extra/jquery-mobile/'); ?>" type="text/javascript"></script>
<script src="<?php echo JHTMLOPC::getFullUrl('jquery.widgetw.js', 'components/com_onepage/themes/extra/jquery-mobile/'); ?>" type="text/javascript"></script>


<script src="<?php echo JHTMLOPC::getFullUrl('jquery.noconflict.js', 'components/com_virtuemart/assets/js/', false); ?>" type="text/javascript"></script>
<script src="<?php echo JHTMLOPC::getFullUrl('jquery.ui.core.min.js', 'components/com_virtuemart/assets/js/'); ?>" type="text/javascript"></script>

<script src="<?php echo JHTMLOPC::getFullUrl('opcping.js', 'components/com_onepage/assets/js/'); ?>" type="text/javascript"></script>
<script src="<?php echo JHTMLOPC::getFullUrl('sync.js', 'components/com_onepage/assets/js/'); ?>" type="text/javascript"></script>
<script src="<?php echo JHTMLOPC::getFullUrl('onepage.js', 'components/com_onepage/assets/js/'); ?>" type="text/javascript"></script>
<script src="<?php echo JHTMLOPC::getFullUrl('tabcontent.js', $path, false); ?>" type="text/javascript"></script>
<script src="<?php echo JHTMLOPC::getFullUrl('jquery.mobile.stepper.js', 'components/com_onepage/themes/extra/jQuery-Mobile-Stepper-Widget-master/', false); ?>" type="text/javascript"></script>


<title><?php echo OPCLang::_('COM_VIRTUEMART_CART_TITLE'); ?></title>
  
<script src="/components/com_virtuemart/assets/js/jquery.ui.datepicker.min.js" type="text/javascript"></script>
<script src="/components/com_virtuemart/assets/js/i18n/jquery.ui.datepicker-en-GB.js" type="text/javascript"></script>

 
  <script src="/components/com_onepage/assets/js/vmcreditcard.js" type="text/javascript"></script>
  <script src="/components/com_onepage/ext/doublemail/js/doublemail.js?opcversion=2_0_257_268_140914" type="text/javascript"></script>
  <script src="/cache/com_onepage/opc_dynamic_en-GB_2ff4261d13f52233a0d498969562a168.js?opcversion=2_0_257_268_140914" type="text/javascript"></script>

</head>
<body>
<div data-role="page" data-quicklinks="false" data-add-back-btn="true" data-back-btn-text="Back" data-theme="d" class="page-0">

<div data-role="header" class="jqm-header">
	
</div><!-- /header -->

<div role="main" class="ui-content jqm-content " >
 <?php echo $output; ?>
</div>
</div>
</body>
</html>