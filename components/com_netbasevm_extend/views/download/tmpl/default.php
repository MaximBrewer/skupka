<?php

if (!defined('_VALID_MOS') && !defined('_JEXEC'))
die('Direct Access to ' . basename(__FILE__) . ' is not allowed.');

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

?>

<form method="get" name="downloadForm">
    <div align="center">
        <input type="text" class="inputbox" value="<?php echo $this->download_code ?>" size="32" name="download_code" />
        <br /><br />
        <input type="submit" class="button" value="Start" />
     </div>
    <input type="hidden" name="option" value="com_digitolldownloads" />
    <input type="hidden" name="view" value="download" />
    <input type="hidden" name="task" value="request" />
</form>