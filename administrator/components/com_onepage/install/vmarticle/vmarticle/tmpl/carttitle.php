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
*/

if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); 

$title = $viewData['title'];
$plugin_title = $viewData['plugin_title'];
$currencyDisplay = $viewData['currencyDisplay']; 
$price = $viewData['price']; 

?><span class="multi_attrib_title"><span class="plugin_title"><?php echo JText::_($plugin_title); ?>: </span><span class="attrib_title"><?php echo vmText::_($title); ?></span><span class="price"> (<?php echo $currencyDisplay->priceDisplay($price); ?></span>)</span>

<?php
