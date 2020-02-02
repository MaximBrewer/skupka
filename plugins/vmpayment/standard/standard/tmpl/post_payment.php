<?php

defined ('_JEXEC') or die();



/**

 * @author Valérie Isaksen

 * @version $Id$

 * @package VirtueMart

 * @subpackage payment

 * @copyright Copyright (C) 2004-Copyright (C) 2004-2015 Virtuemart Team. All rights reserved.   - All rights reserved.

 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php

 * VirtueMart is free software. This version may have been modified pursuant

 * to the GNU General Public License, and as distributed it includes or

 * is derivative of works licensed under the GNU General Public License or

 * other free or open source software licenses.

 * See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.

 *

 * http://virtuemart.net

 */



?>
<?php
jimport( 'joomla.application.module.helper' );
$module = JModuleHelper::getModules('position-korzina'); 
$attribs['style'] = 'xhtml'; 
echo JModuleHelper::renderModule($module[0], $attribs); 
?>




<table width="300" border="0" align="center" cellpadding="0" cellspacing="0">

  <tr>

   

    <td align="center"><div class="post_payment_order_number" style="width: 100%">

	<br />

<br />

<span class="post_payment_order_number_title"><?php echo vmText::_ ('COM_VIRTUEMART_ORDER_NUMBER'); ?> </span>

	<?php echo  $viewData["order_number"]; ?>

</div></td>

  </tr>

</table>

</div>
</div>
