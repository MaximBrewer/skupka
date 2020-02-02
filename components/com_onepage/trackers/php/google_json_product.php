<?php
/*
*
* @copyright Copyright (C) 2007 - 2013 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* One Page checkout is free software released under GNU/GPL and uses code from VirtueMart
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
* stAn note: Always use default headers for your php files, so they cannot be executed outside joomla security 
*
*/

defined( '_JEXEC' ) or die( 'Restricted access' );


require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.'xmlexport.php'); 

$VirtueMartControllerXmlexport = new VirtueMartControllerXmlexport; 

$display = false; 
$withExtras = false; 
$pidformat = $this->params->pidformat; 
$pid_prefix = $this->params->pid_prefix; 
$pid_suffix = $this->params->pid_suffix; 
$product_id = $this->product->virtuemart_product_id; 
$desc_type = $this->params->desc_type; 

$productData = $VirtueMartControllerXmlexport->getproduct($product_id, $display, $withExtras, $pidformat, $pid_prefix, $pid_suffix); 
$this->isPureJavascript = true; 
if (!empty($productData)) {
?><script type="application/ld+json">
<?php 
if (defined('JSON_PRETTY_PRINT')) {
echo json_encode($productData, JSON_PRETTY_PRINT); 
}
else {
echo json_encode($productData); 
}
?>
</script><?php
}

