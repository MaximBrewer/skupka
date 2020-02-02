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
 * $Id: default_module.php 3 2017-09-20 14:30:08Z Abhshek Das $
 * --------------------------------------------------------------------------------------
*/
defined('_JEXEC') or die;

$modules = $this->getCartModules();
$count = count($modules);
$i = 0;
?>
<?php if($count > 0) : ?>
	<div class="proopc-cart-modules">
		<?php foreach($modules as $module) : ?>
			<?php if(!empty($module->moduleHtml)) : ?>
				<?php $i++; ?>
				<div class="proopc-row">
					<div class="cart-promo-mod<?php echo ($i == $count) ? ' last' : ''; ?>">
						<?php if($module->showtitle) : ?>
							<h3><?php echo $module->title ?></h3>
						<?php endif; ?>
						<div class="proopc-cart-module">
							<?php echo $module->moduleHtml; ?>
						</div>
					</div>
				</div>
			<?php endif; ?>
		<?php endforeach; ?>
	</div>
<?php endif; ?>