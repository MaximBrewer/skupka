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
 * $Id: default_cartform.php 3 2017-09-20 14:30:08Z Abhshek Das $
 * --------------------------------------------------------------------------------------
*/
defined('_JEXEC') or die;
?>
<div class="inner-wrap">
	<form id="cartFormFields">
		<?php echo $this->loadTemplate ('cartfields'); ?>
	</form>
</div>