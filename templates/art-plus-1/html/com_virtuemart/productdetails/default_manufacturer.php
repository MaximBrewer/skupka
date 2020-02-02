<?php
/**
 *
 * Show the product details page
 *
 * @package	VirtueMart
 * @author Max Milbers, Valerie Isaksen
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2014 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @version $Id: default_manufacturer.php 8794 2015-03-12 18:31:55Z Milbo $
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
?>
<div class="manufacturer">
	<?php
	$i = 1;

	$mans = array();
	// Gebe die Hersteller aus
	foreach($this->product->manufacturers as $manufacturers_details) {

		//Link to products
//-		$link = JRoute::_('index.php?option=com_virtuemart&view=manufacturer&virtuemart_manufacturer_id=' . $manufacturers_details->virtuemart_manufacturer_id. '&tmpl=component', FALSE); // *!* 2015-11-20
		$link = JRoute::_('index.php?option=com_virtuemart&view=category&showproducts=1&virtuemart_manufacturer_id=' . $manufacturers_details->virtuemart_manufacturer_id, FALSE); // *!* 2015-11-20
		
		
		$name = $manufacturers_details->mf_name;

		// Avoid JavaScript on PDF Output
		if (!$this->writeJs) {
			$mans[] = JHtml::_('link', $link, $name);
		} else {
//-			$mans[] = '<a class="manuModal" rel="{handler: \'iframe\', size: {x: 700, y: 850}}" href="'.$link .'">'.$name.'</a>'; // *!* 2015-11-20
			$mans[] = '<a  href="'.$link .'">'.$name.'</a>'; // *!* 2015-11-20
		}
	}
	echo implode(', ',$mans);
	?>
</div>