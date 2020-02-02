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

defined('_JEXEC') or die('Restrict Access');

class TableConfig extends JTable
{
    
    var $id = null;    
    var $params = null;
    var $template_header = null;
    var $template_body = null;
    var $template_items = null;
    var $template_footer = null;
    
    var $template_dn_header = null;
    var $template_dn_body = null;
    var $template_dn_items = null;
    var $template_dn_footer = null;
    
    var $template_restore = null;
    
    var $template_dn_restore = null;
	
    
    function TableConfig (& $db)    
    {        
        parent::__construct('#__vminvoice_config', 'id', $db);    
    }

}
?>