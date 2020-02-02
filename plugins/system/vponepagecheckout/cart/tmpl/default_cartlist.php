<?php
/**
 *---------------------------------------------------------------------------------------
 * @package      VP One Page Checkout - Joomla! System Plugin
 * @subpackage   For VirtueMart 3+
 *---------------------------------------------------------------------------------------
 * @copyright    Copyright (C) 2012-2017 VirtuePlanet Services LLP. All rights reserved.
 * @license      GNU General Public License version 2 or later; see LICENSE.txt
 * @authors      Abhishek Das
 * @email        info@virtueplanet.com
 * @link         https://www.virtueplanet.com
 *---------------------------------------------------------------------------------------
 * $Revision: 3 $
 * $LastChangedDate: 2017-09-20 20:00:08 +0530 (Wed, 20 Sep 2017) $
 * $Id: default_cartlist.php 3 2017-09-20 14:30:08Z Abhshek Das $
 * --------------------------------------------------------------------------------------
*/
defined('_JEXEC') or die;

$style = $this->params->get('style', 1);

if($style == 1 || $style == 3)
{
	// For style 1 and style 3 layout we need to have a different type of price list layout
	echo $this->loadTemplate('pricelistnarrow');
}
else
{
	// For style 2 and style 4 layout we use the same price list sublayout as first stage.
	// default_pricelist.php layout will always display full cart table when we are in final stage.
	echo $this->loadTemplate('pricelist');
}