<?php
defined('_JEXEC') or  die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );
/*
*Cart Ajax Module
*
* @package VirtueMart
* @subpackage modules
*
* www.virtuemart.net
*/
defined('DS') or define('DS', DIRECTORY_SEPARATOR);
if (!class_exists( 'VmConfig' )) require(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'config.php');
VmConfig::loadConfig();

VmConfig::loadJLang('mod_vm_cart', true);
VmConfig::loadJLang('com_virtuemart', true);
vmJsApi::jQuery();
//$data->billTotal = $data->billTotal;
$doc = JFactory::getDocument();

$doc->addStyleSheet(JURI::base().'/modules/mod_vm_cart/assets/css/style.css');

/*
Цвет модуля
*/
$color = $params->get( 'color', '');

$colorModule = '.vmCartModule svg {fill:'.$color.'}
.cart_top .total_products,
.cart_content .show_cart a {
	background:'.$color.';
}
.vmCartModule {
	border-color:'.$color.';
}
.vmCartModule a,
.vmCartModule a:hover,
.vmCartModule .product_name a,
.vmCartModule .product_name a:hover,
.cart_top .total strong,
.cart_top .total strong:hover {
	color:'.$color.';
}';
$doc->addStyleDeclaration( $colorModule );
/*
Анимация
*/
$animation = $params->get( 'animation', 0 );
if($animation == 1){
	$animationModule = '	
.total_products {
  display: inline-block;
  vertical-align: middle;
  -webkit-transform: translateZ(0);
  transform: translateZ(0);
  box-shadow: 0 0 1px rgba(0, 0, 0, 0);
  -webkit-backface-visibility: hidden;
  backface-visibility: hidden;
  -moz-osx-font-smoothing: grayscale;
}

.vmCartModule:hover .total_products {
  -webkit-animation-name: hvr-buzz-out;
  animation-name: hvr-buzz-out;
  -webkit-animation-duration: 0.75s;
  animation-duration: 0.75s;
  -webkit-animation-timing-function: linear;
  animation-timing-function: linear;
  -webkit-animation-iteration-count: 1;
  animation-iteration-count: 1;
}';
}
if($animation == 2){
	$animationModule = '	
.total_products {
  display: inline-block;
  vertical-align: middle;
  -webkit-transform: translateZ(0);
  transform: translateZ(0);
  box-shadow: 0 0 1px rgba(0, 0, 0, 0);
  -webkit-backface-visibility: hidden;
  backface-visibility: hidden;
  -moz-osx-font-smoothing: grayscale;
}
#vmCartModule:hover .total_products{
  -webkit-animation-name: hvr-wobble-vertical;
  animation-name: hvr-wobble-vertical;
  -webkit-animation-duration: 1s;
  animation-duration: 1s;
  -webkit-animation-timing-function: ease-in-out;
  animation-timing-function: ease-in-out;
  -webkit-animation-iteration-count: 1;
  animation-iteration-count: 1;
}';
}
else {
	$animationModule = '';
}
$doc->addStyleDeclaration( $animationModule );

/*
Положение
*/
$position = $params->get( 'position', 0 );

if($position == 1){
$positionModule = '
.cart_content:before{
	left: 25px;
	width: 7px;
}
.cart_content{
	left: 0;
}
';
}
else{
	$positionModule = '';
}
$doc->addStyleDeclaration( $positionModule );

$doc->addScript(JURI::base().'/modules/mod_vm_cart/assets/js/update_cart.js');

$js = '
jQuery(document).ready(function(){
    jQuery("body").live("updateVirtueMartCartModule", function(e) {
        jQuery("#vmCartModule").updateVirtueMartCartModule();
    });
});
';
vmJsApi::addJScript('vm.CartModule.UpdateModule',$js);

$jsVars  = ' jQuery(document).ready(function(){
	jQuery(".vmCartModule").productUpdate();
});' ;
//vmJsApi::addJScript('vm.CartModule.UpdateProduct',$jsVars);


//This is strange we have the whole thing again in controllers/cart.php public function viewJS()
if(!class_exists('VirtueMartCart')) require(VMPATH_SITE.DS.'helpers'.DS.'cart.php');
$cart = VirtueMartCart::getCart(false);

$viewName = vRequest::getString('view',0);
if($viewName=='cart'){
	$checkAutomaticPS = true;
} else {
	$checkAutomaticPS = false;
}
$data = $cart->prepareAjaxData();

if (!class_exists('CurrencyDisplay')) require(VMPATH_ADMIN . DS. 'helpers' . DS . 'currencydisplay.php');
$currencyDisplay = CurrencyDisplay::getInstance( );

vmJsApi::cssSite();

$moduleclass_sfx = $params->get('moduleclass_sfx', '');
$show_product_list = (bool)$params->get( 'show_product_list', 1 ); // Display the Product Price?

require(JModuleHelper::getLayoutPath('mod_vm_cart'));
echo vmJsApi::writeJS();
 ?>