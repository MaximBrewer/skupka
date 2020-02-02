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

class TableFields extends JTable
{
    
    var $id = null;   
    //var $pdf_logo		= null;    
    var $bank_name = null;    
    var $account_nr = null;    
    var $bank_code_no = null;    
    var $bic_swift = null;    
    var $iban = null;    
    var $vat_id = null;    
    var $tax_number = null;    
    var $registration_court = null;    
    var $phone = null;    
    var $email = null;    
    var $web_url = null;    
    var $note_start = null;    
    var $note_end = null;    
    var $show_bank_name = null;   
    var $show_account_nr = null;    
    var $show_bank_code_no = null;    
    var $show_bic_swift = null;    
    var $show_iban = null;    
    var $show_vat_id = null;    
    var $show_tax_number = null;    
    var $show_registration_court = null;    
    var $show_phone = null;    
    var $show_email = null;    
    var $show_web_url = null;

    function tablefields (& $db)
    {        
        parent::__construct('#__vminvoice_additional_field', 'id', $db);    
    }

}
