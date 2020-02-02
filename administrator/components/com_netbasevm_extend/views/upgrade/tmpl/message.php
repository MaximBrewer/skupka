<?php
/*------------------------------------
* -Netbase- Advanced Virtuemart Invoices for Virtuemart
* Author    CMSMart Team
* Copyright (C) 2012 http://cmsmart.net. All Rights Reserved.
* @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* Websites: http://cmsmart.net
* Email: team@cmsmart.net
* Technical Support:  Forum - http://bloorum.com/forums
-----------------------------------------------------*/
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$state      = $this->get('State');
$result     = $state->get('result');
$message    = $state->get('message');
?>
<table class="adminform">
<tr>
	<td align="left">
	<strong><?php echo $message; ?></strong>
	</td>
</tr>
<tr>
	<td colspan="2" align="center">
	[&nbsp;<a href="<?php echo $this->url; ?>" style="font-size: 16px; font-weight: bold">Continue ...</a>&nbsp;]
	</td>
</tr>
</table>
