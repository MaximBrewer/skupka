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

// check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restrict Access');

// import JFile
jimport('joomla.filesystem.file');

// define constants
if (!defined('COM_NETBASEVM_EXTEND_ISJ16'))
    define('COM_NETBASEVM_EXTEND_ISJ16', version_compare(JVERSION, '1.6.0') >= 0);
if (!defined('VMI_NL'))
    define('VMI_NL', "\n");



$isVm1 = false;
$isVm2 = false;

if (file_exists(JPATH_ADMINISTRATOR . '/components/com_virtuemart/compat.joomla1.5.php'))
    $isVm1 = true;
elseif (file_exists(JPATH_ADMINISTRATOR . '/components/com_virtuemart/admin.virtuemart.php'))
    $isVm2 = true;
else
    JError::raiseWarning(0, 'Netbase VM Extend: VirtueMart not installed. If you are experiencing this message on site, please, disable Netbase VM Extend Autorun plugin.');

define('COM_NETBASEVM_EXTEND_ORDERS_ISVM1', $isVm1);
define('COM_NETBASEVM_EXTEND_ORDERS_ISVM2', $isVm2);

define('COM_VMINVOICE_ISVM1', $isVm1);
define('COM_VMINVOICE_ISVM2', $isVm2);

if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2) {
    NbordersHelper::importVMFile('version.php');
    if (isset(vmVersion::$RELEASE) AND version_compare(vmVersion::$RELEASE, '2.0.2') == -1)
        JError::raiseWarning(0, JText::sprintf('COM_NETBASEVM_EXTEND_VM_VERSION_LOWER', '2.0.1'));
}

abstract class NbordersHelper {

    static $originalLanguage = null; //original language of admin

    /**
     * Get Netbase VM Extend parameters. If not defined in db, defaults from XML are set.
     * 
     * @return JParameter
     */

    static function getParams() {
        static $params;

        if (!$params) {

            require_once (JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_netbasevm_extend' . DS . 'helpers' . DS . 'config.php');

            $db = JFactory::getDBO();
            $db->setQuery('SELECT * FROM `#__virtuemart_configs`');
            $paramsdata = $db->loadResult();
            $paramsdefs = JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_netbasevm_extend' . DS . 'models' . DS . 'config.xml';

            $params = new InvoiceConfig($paramsdefs, $paramsdata);
        }

        return $params;
    }

    /**
     * Gets GMT date and converts to Unix timestamp
     * @param unknown_type $date
     */
    static function gmStrtotime($date) {
        $tz = date_default_timezone_get();
        date_default_timezone_set('UTC');
        $ret = strtotime($date);
        date_default_timezone_set($tz);
        return $ret;
    }

    /**
     * Import file from VM framework. Displays warnings if not exists.
     * 
     * @param string $file
     * @param bool $admin backend
     * @param bool $optional optional. file not existing not reuslt in warning or other
     */
    static function importVMFile($file, $admin = true, $optional = false) {
        static $included;
        static $includedBase;

        if (empty($includedBase)) { //on first call, include "base" files
            $includedBase = true; //to not infinite loop
            if (COM_NETBASEVM_EXTEND_ORDERS_ISVM1) {  //TODO: cleanup
                NbordersHelper::importVMFile('virtuemart_parser.php', false);
                NbordersHelper::importVMFile('compat.joomla1.5.php');
                NbordersHelper::importVMFile('virtuemart.cfg.php');
                NbordersHelper::importVMFile('classes/vmAbstractObject.class.php');
                NbordersHelper::importVMFile('classes/ps_database.php');
                NbordersHelper::importVMFile('classes/ps_checkout.php');
                NbordersHelper::importVMFile('classes/ps_main.php');
                NbordersHelper::importVMFile('classes/request.class.php');
                NbordersHelper::importVMFile('classes/phpInputFilter/class.inputfilter.php');
            } else {
                //better define it here, can lead to errors with vmModel::getModel (?)
                defined('JPATH_VM_ADMINISTRATOR') or define('JPATH_VM_ADMINISTRATOR', JPATH_SITE . DS . 'administrator' . DS . 'components' . DS . 'com_virtuemart');
                NbordersHelper::importVMFile('helpers/config.php');
            }
        }

        if (!isset($included[$file][$admin])) {
            $file = str_replace('/', DS, $file);
            $file = trim($file, DS);

            $path = ($admin ? JPATH_ADMINISTRATOR : JPATH_SITE) . DS . 'components' . DS . 'com_virtuemart' . DS . $file;

            if (!file_exists($path) AND ! $optional) {
                echo 'Not existing import VM file:  ' . $path . "<br>";
                JError::raiseWarning(0, 'Netbase VM Extend: Non existing VirtueMart file:  ' . $path);
                $included[$file][$admin] = false;
            } elseif (file_exists($path) AND ! include_once($path)) {
                echo 'Can\'t import VM file:  ' . $path . "<br>";
                exit;
                JError::raiseWarning(0, 'Netbase VM Extend: Can\'t import VirtueMart file:  ' . $path);
                $included[$file][$admin] = false;
            } elseif (file_exists($path))
                $included[$file][$admin] = true;
        }
        return $included[$file][$admin];
    }

    static function markOrderSent($orderId, $invoice = true, $delivery_note = false) {
        $db = JFactory::getDBO();
        $sql = "SELECT order_id, order_mailed, dn_mailed FROM #__nborders_mailsended WHERE order_id = $orderId";
        $db->setQuery($sql);
        $status = $db->loadObject();
        if (is_null($status)) { // insert if no row with order_id
            $query = "INSERT INTO #__nborders_mailsended (`order_id`, `order_mailed`, `dn_mailed` ) VALUES ($orderId";
            $query .= ($invoice) ? ", 1" : ", 0";
            $query .= ($delivery_note) ? ", 1" : ", 0";
            $query .= ")";
            $db->setQuery($query);
            $db->execute();
            return true;
        } else { // update if positive change
            $update = false;
            $query = "UPDATE #__nborders_mailsended SET";
            if ($invoice && !$status->order_mailed) { // invoice can be changed to sent
                $update = true;
                $query .= " `order_mailed`=1";
                if ($delivery_note && !$status->dn_mailed) { // delivery note can be changed to sent
                    $query .= ", `dn_mailed`=1";
                }
            } elseif ($delivery_note && !$status->dn_mailed) { // delivery note can be changed to sent, but invoice can't
                $update = true;
                $query .= " `dn_mailed`=1";
            }
            $query .= " WHERE `order_id` = '$orderId'";
            if ($update) {
                $db->setQuery($query);
                $db->execute();
                return true;
            }
        }
        return false;
    }

    /**
     * Send email and return status.
     * 
     * @param int $orderId
     * @param bool $invoice
     * @param bool $delivery_note
     */
    static function sendMail($orderId, $invoice = true, $delivery_note = false) {
        $orderId = (int) $orderId;
        $invoiceNo = NbordersHelper::getInvoiceNo($orderId);
        
        $orderNumAndPass = InvoiceGetter::getOrderNumberAndPass($orderId);
        $orderNumber = $orderNumAndPass['order_number'];
        $orderPass = $orderNumAndPass['order_pass'];
        $app = JFactory::getApplication();
        $config = NbordersHelper::getParams();

        $BTuserInfo = InvoiceGetter::getOrderUserInfo($orderId, 'BT');
        $vendorId = InvoiceGetter::getOrderVendor($orderId);
        if (!$BTuserInfo) {
            JError::raiseWarning(0, 'Netbase VM Extend: Billing address for order ' . $orderId . ' not found.');
            return false;
        }


        $copyEmail = null;
        $mailTo = null;
        $mailFrom = null;
        $fromName = null;
        $bcc = null;

        switch ($config->get('use_conf')) {
            case 1:

                $vm_contact = InvoiceGetter::getVendorMailAndName($vendorId);
                $mailFrom = $vm_contact->email;
                $fromName = $vm_contact->name;

                break;
            case 2:
                $mailFrom = $config->get('admin_email');
                $fromName = $config->get('from_name');
                break;
        }

        if (!$mailFrom)
            $mailFrom = $app->getCfg('mailfrom'); //if some option empty (not defined or use_conf=0), use Joomla! default
        if (!$fromName)
            $fromName = $app->getCfg('fromname');

        //determine where to send mail and copy

        $customerMail = $BTuserInfo->email;

        if ($invoice) {
            $recipientConfig = $config->get('mail_send_to', 0);
            $copyEmail = $config->get('copy');
            $bcc = $config->get('bcc');
        } else {
            $recipientConfig = $config->get('mail_send_to_dn', 0);
            $copyEmail = $config->get('copy_dn');
            $bcc = $config->get('bcc_dn');
        }

        $copyEmail = $copyEmail ? preg_split('#[,;]#', $copyEmail) : array();

        if ($recipientConfig == 0) { //0: send to customer and vendor (add vendor to copy)
            $mailTo = $customerMail;
            $copyEmail[] = $mailFrom;
        } elseif ($recipientConfig == 1)//1: send only to customer = no copy
            $mailTo = $customerMail;
        elseif ($recipientConfig == 2)//2: send only to vendor (replace customer by vendor and no copy)
            $mailTo = $mailFrom;
        elseif ($recipientConfig == 3 && $copyEmail)//3: send only to e-mail(s) specified as copy
            $mailTo = array_shift($copyEmail);

        // check if mailTo is valid
        if (!$mailTo) {
            $app->enqueueMessage(JText::sprintf('MSG_EMAIL_INVALID_ERROR', $orderId), 'error');
            return false;
        }

        $document = ($invoice || $delivery_note) ? array() : NULL;
        $order_sent = false;
        $dn_sent = false;
        $renameBack = array();

        //append invoice file
        if ($invoice) {
            $document['in'] = self::getInvoiceFile($orderId);
            $order_sent = true;

            //because stupid joomla JMail doesnt support custom named attachments, we must rename file manually, than send, than rename back.
            if ($newName = $config->get('order_filename')) {
                jimport('joomla.filesystem.file');
                $newName = preg_replace('#\[\s*(.+)\s*\]#e', 'JText::_(\'$1\')', $newName);
                $newName = trim(JFile::makeSafe(str_replace(array('%o', '%i', '%n'), array($orderId, $invoiceNo, $orderNumber), trim($newName))));
                if (!preg_match('#\.pdf$#i', $newName))
                    $newName.='.pdf'; //add extension
                $newPath = ($sep = strrpos($document['in'], DIRECTORY_SEPARATOR)) ? substr($document['in'], 0, $sep) . DIRECTORY_SEPARATOR . $newName : $newName;
                if (@file_exists($newPath))
                    unlink($newPath);
                if (@rename($document['in'], $newPath)) {
                    $renameBack[] = array($newPath, $document['in']);
                    $document['in'] = $newPath;
                }
            }
        }

        //append delivery note file
        if ($delivery_note && $config->get('delivery_note')) {
            $document['dn'] = self::getDeliveryNoteFile($orderId);
            $dn_sent = true;

            //because stupid joomla JMail doesnt support custom named attachments, we must rename file manually, than send, than rename back.
            if ($newName = $config->get('dn_filename')) {
                jimport('joomla.filesystem.file');
                $newName = preg_replace('#\[\s*(.+)\s*\]#e', 'JText::_(\'$1\')', $newName);
                $newName = trim(JFile::makeSafe(str_replace(array('%o', '%i', '%n'), array($orderId, $invoiceNo, $orderNumber), trim($newName))));
                if (!preg_match('#\.pdf$#i', $newName))
                    $newName.='.pdf'; //add extension
                $newPath = ($sep = strrpos($document['in'], DIRECTORY_SEPARATOR)) ? substr($document['in'], 0, $sep) . DIRECTORY_SEPARATOR . $newName : $newName;
                if (@file_exists($newPath))
                    unlink($newPath);
                if (@rename($document['dn'], $newPath)) {
                    $renameBack[] = array($newPath, $document['dn']);
                    $document['dn'] = $newPath;
                }
            }
        }

        if ($invoice) { //if send invoice or both
            $subject = $config->get('mail_subject', 'Invoice');
            $message = $config->get('mail_message');
        } else { //send only dn
            $subject = $config->get('mail_dn_subject', 'Delivery note');
            $message = $config->get('mail_dn_message');
        }

        //replace marks by variables


        $firstName = $BTuserInfo->first_name;
        $middleName = $BTuserInfo->middle_name;
        $lastName = $BTuserInfo->last_name;
        $title = $BTuserInfo->title;
        $company = $BTuserInfo->company;


        $subject = preg_replace('/\[%orderId%\]/iU', $orderId, $subject);
        $subject = preg_replace('/\[%invoiceId%\]/iU', $invoiceNo, $subject);

        $subject = preg_replace('/\[%orderPass%\]/iU', $orderPass, $subject);
        $subject = str_ireplace('[%orderNumber%]', $orderNumber, $subject);

        $subject = preg_replace('/\[%title%\]/iU', $title, $subject);
        $subject = preg_replace('/\[%firstName%\]/iU', $firstName, $subject);
        $subject = preg_replace('/\[%middleName%\]/iU', $middleName, $subject);
        $subject = preg_replace('/\[%(second|last)Name%\]/iU', $lastName, $subject);
        $subject = preg_replace('/\[%company%\]/iU', $company, $subject);


        $message = preg_replace('/\[%orderId%\]/iU', $orderId, $message);
        $message = preg_replace('/\[%invoiceId%\]/iU', $invoiceNo, $message);

        $message = preg_replace('/\[%orderPass%\]/iU', $orderPass, $message);
        $message = str_ireplace('[%orderNumber%]', $orderNumber, $message);

        $message = preg_replace('/\[%title%\]/iU', $title, $message);
        $message = preg_replace('/\[%firstName%\]/iU', $firstName, $message);
        $message = preg_replace('/\[%middleName%\]/iU', $middleName, $message);
        $message = preg_replace('/\[%(second|last)Name%\]/iU', $lastName, $message);
        $message = preg_replace('/\[%company%\]/iU', $company, $message);


        if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2)
            $orderURL = JURI::root() . 'index.php?option=com_virtuemart&view=orders&layout=details&order_number=' . $orderNumber . '&order_pass=' . $orderPass;
        else
            $orderURL = JURI::root() . 'index.php?page=account.order_details&order_id=' . $orderId;
        $message = str_ireplace('[%orderURL%]', $orderURL, $message);


        $res = self::JoomSendMail($mailFrom, $fromName, $mailTo, $subject, $message, true, $copyEmail, $bcc, $document);

        if ($res === true) // test if mail was sent
            self::markOrderSent($orderId, $order_sent, $dn_sent);

        //rename sent files back
        if ($renameBack)
            foreach ($renameBack as $rename)
                rename($rename[0], $rename[1]);

        return $res;
    }

    /**
     * Simple override of JUtility::mail with MsgHTML function added to convert included images to embed.
     * Also, convert recepients separated by ; to array (to enable more recepients)
     */
    public static function JoomSendMail($from, $fromname, $recipient, $subject, $body, $mode = 0, $cc = null, $bcc = null, $attachment = null, $replyto = null, $replytoname = null) {
        // Get a JMail instance
        $mail = JFactory::getMailer();

        $from = reset(explode(';', $from)); //if more froms, use first

        $mail->setSender(array($from, $fromname));
        $mail->setSubject($subject);

        //if mail is using absolute urls to images on this site, convert to relative, because MsgHTML not embeds absolute images
        if (preg_match_all("#(src|background)=\"([A-z]+://.*)\"#Ui", $body, $images, PREG_SET_ORDER)) {
            foreach ($images as $image) {

                $relative = ltrim(str_replace(JURI::root(), '', $image[2]), '/');

                if ($relative != $image[2] AND file_exists(JPATH_SITE . DS . $relative)) //suceesfully stripped absolute domain and relative path works
                    $body = str_replace($image[0], $image[1] . '="' . $relative . '"', $body); //substitue image
            }
        }

        $mail->MsgHTML(JMailHelper::cleanText($body), JPATH_SITE);

        $mail->addRecipient(is_array($recipient) ? $recipient : preg_split('#[;,]#', $recipient)); //addresses separated by ; or , 
        if ($cc)
            $mail->addCC(is_array($cc) ? $cc : preg_split('#[;,]#', $cc)); //addresses separated by  ; or , 
        if ($bcc)
            $mail->addBCC(is_array($bcc) ? $bcc : preg_split('#[;,]#', $bcc)); //addresses separated by  ; or , 
        if ($attachment)
            $mail->addAttachment($attachment);

        // Take care of reply email addresses
        if (is_array($replyto)) {
            $numReplyTo = count($replyto);
            for ($i = 0; $i < $numReplyTo; $i++) {
                $mail->addReplyTo(array($replyto[$i], $replytoname[$i]));
            }
        } elseif (isset($replyto)) {
            $mail->addReplyTo(array($replyto, $replytoname));
        }

        return $mail->Send();
    }

    static function getBaseInvociesSubdir() {
        static $tmpDir;

        if (isset($tmpDir))
            return $tmpDir;

        jimport('joomla.filesystem.path');
        jimport('joomla.filesystem.folder');

        // define folder for storing invoices
        $mainframe = JFactory::getApplication();
        $tmp = JPath::clean(trim($mainframe->getCfg('tmp_path') ? $mainframe->getCfg('tmp_path') : $mainframe->getCfg('config.tmp_path')));
        $tmp = rtrim($tmp, DS) . DS;

        //note: replace space by underscore, TCPDF can have problem with it
        //http://www.artio.net/support-forums/vm-invoice/customer-support/tcpdf-error-pri-vice-polozkach-kosiku


        $invoicesSubDir = $tmp . str_replace(' ', '_', 'VM ' . trim(JText::_('COM_NETBASEVM_EXTEND_INVOICES'), '*') . DS);


        $invoicesSubDir = JPath::clean($invoicesSubDir);

        //echo $invoicesSubDir;die;
        //base: no subdir for Netbase VM Extends, use tmp root (but should be always writeable!)
        $tmpDir = $tmp;

        if ($tmpDir) {
            if (!JFolder::exists($invoicesSubDir)) { //tmp directory for VM invoices not exists
                if (JFolder::create($invoicesSubDir) && is_writable($invoicesSubDir))
                    $tmpDir = $invoicesSubDir;
            }
            else { //directory exists
                if (is_writable($invoicesSubDir))
                    $tmpDir = $invoicesSubDir;
            }
        }

        if (!is_writable($tmpDir)) {
            JError::raiseWarning(0, 'Netbase VM Extend: Tmp directory ' . str_replace(JPATH_SITE, '', $tmpDir) . ' is not writable. Without writable folder invoices cannot be created. Check you have properly set Path to Temp folder in your Joomla! Server configuration and this directory have write permissions (System Information -> Directory Permissions).');
            $tmpDir = false;
        }

        /*
          if ((!$dirname || !is_writeable(JPath::clean($dirname))) && function_exists('sys_get_temp_dir')) //if not, use systems temp folder
          $dirname = sys_get_temp_dir();
         */

        return $tmpDir;
    }

    /**
     * Get invoice file subdir based on order month
     * 
     * @param int/string 	$orderID
     * @param bool			wheater create directory, if not exists
     */
    static function getInvoiceSubdir($orderID, $createDirs = false) {
        static $subDirs;

        if (is_array($orderID))
            $orderID = 'multi';

        if (!isset($subDirs[$orderID])) {

            if (!is_numeric($orderID)) //multi
                $subDirs[$orderID] = '';
            else {
                $db = JFactory::getDBO(); //use subdir with order month


                if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2)
                    $db->setQuery("SELECT `created_on` FROM `#__virtuemart_orders` WHERE `virtuemart_order_id` = " . (int) $orderID);
                else
                    $db->setQuery("SELECT `cdate` FROM `#__vm_orders` WHERE `order_id` = " . (int) $orderID);


                $cdate = $db->loadResult();
                if (!is_numeric($cdate))
                    $cdate = NbordersHelper::gmStrtotime($cdate);

                $subDirs[$orderID] = $cdate > 0 ? date('Y-m', $cdate) . DS : '';
            }
        }

        if ($createDirs) { //if create that dir
            jimport('joomla.filesystem.folder');

            //create base invoice subdir
            if (!JFolder::exists(INVOICES_TMP . DS . $subDirs[$orderID])) //directory not exists
                if (!JFolder::create(INVOICES_TMP . DS . $subDirs[$orderID])) //try to create it
                    return ''; //if cannot create dir, return void
        }

        return $subDirs[$orderID];
    }

    static function getInvoiceFile($orderID, $createDirs = false) {
        $code = (!is_array($orderID)) ? NbordersHelper::getInvoiceNo($orderID) : $orderID = 'multi'; // for multiple orders generated at once

        $lang = NbordersHelper::getInvoiceLanguage($orderID);
        $filename = trim(NbordersHelper::frontendTranslate('COM_NETBASEVM_EXTEND_INVOICE_', $lang), '*');

        $code.='_' . $lang;
        $filename = INVOICES_TMP . NbordersHelper::getInvoiceSubdir($orderID, $createDirs) . $filename . $code . '.pdf';

        return str_replace('/', DIRECTORY_SEPARATOR, $filename);
    }

    static function getDeliveryNoteFile($orderID, $createDirs = false) {
        $code = (!is_array($orderID)) ? NbordersHelper::getInvoiceNo($orderID) : $orderID = 'multi'; // for multiple orders generated at once

        $lang = NbordersHelper::getInvoiceLanguage($orderID);
        $filename = trim(NbordersHelper::frontendTranslate('COM_NETBASEVM_EXTEND_DELIVERYNOTE_', $lang), '*');

        $code.='_' . $lang;
        $filename = INVOICES_TMP . NbordersHelper::getInvoiceSubdir($orderID, $createDirs) . $filename . $code . '.pdf';

        return str_replace('/', DIRECTORY_SEPARATOR, $filename);
    }

    /**
     * Creates all non-created invoice numbers. Starting from last one. 
     * This is called at start of script for preventing creating invoice number in different order.
     */
    static function createInvoiceNos() {
        static $createdAll; //make sure to run this function only one time
        if ($createdAll === true)
            return false;
        $createdAll = true;

        $params = NbordersHelper::getParams();
        $db = JFactory::getDBO();

        if ($params->get('order_number', 'own') != 'own')
            return false;

        $order_status = (array) $params->get('order_status');
        $statusCond = array();

        //get order nos with no invoice number created yet & in desired status, ordered from oldest

        foreach ($order_status as $status)
            $statusCond[] = 'O.`order_status` = \'' . $status . '\'';

        if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2)
            $db->setQuery('SELECT O.`virtuemart_order_id` 
	            FROM `#__virtuemart_orders` O 
	            LEFT JOIN `#__nborders_mailsended` MS ON O.virtuemart_order_id=MS.order_id 
	            WHERE ((MS.`order_no` IS NULL) OR (MS.`order_no`=0) OR (MS.`order_no`=\'\'))
	            AND O.`virtuemart_order_id` > ' . ((int) $params->get('starting_order', 0)) . '
	            ' . (count($statusCond) ? ' AND (' . implode(' OR ', $statusCond) . ')' : '') . ' ORDER BY O.created_on ASC');
        else
            $db->setQuery('SELECT O.`order_id` 
	            FROM `#__vm_orders` O 
	            LEFT JOIN `#__nborders_mailsended` MS ON O.order_id=MS.order_id 
	            WHERE ((MS.`order_no` IS NULL) OR (MS.`order_no`=0) OR (MS.`order_no`=\'\'))
	            AND O.`order_id` > ' . ((int) $params->get('starting_order', 0)) . '
	            ' . (count($statusCond) ? ' AND (' . implode(' OR ', $statusCond) . ')' : '') . ' ORDER BY O.cdate ASC');


        $orderNos = $db->loadColumn();

        if (count($orderNos))
            foreach ($orderNos as $orderNo)
                NbordersHelper::getInvoiceNo($orderNo);
    }

    /**
     * Gets / creates (if own numberinng and conditions are met) invoice number. 
     * This have to be only place to create and get invoice numbers!
     * 
     * @param int 	$orderID
     * @param bool 	$force	force creation of onvoice number even if conditions arent met
     */
    static function getInvoiceNo($orderID, $force = false) {
        static $dbChecked;

        //TODO. caching
        NbordersHelper::createInvoiceNos(); //create all to-be-created invoice numbers, to make sure they are numbered from last one.

        $params = NbordersHelper::getParams();
        $db = JFactory::getDBO();

        if (empty($dbChecked)) { //delete invoices records for orders that were deleted (can mess up automatic numbering). TODO: only on place when we create new number (can be slow?)
            if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2)
                $db->setQuery('SELECT MS.id FRoM `#__nborders_mailsended` MS LEFT JOIN `#__virtuemart_orders` O ON MS.order_id = O.virtuemart_order_id WHERE O.virtuemart_order_id IS NULL');
            else
                $db->setQuery('SELECT MS.id FROM `#__nborders_mailsended` MS LEFT JOIN `#__vm_orders` O ON MS.order_id = O.order_id WHERE O.order_id IS NULL');


            if ($msDelete = $db->loadColumn()) {
                $db->setQuery('DELETE FROM `#__nborders_mailsended` WHERE id IN (' . implode(',', $msDelete) . ')');
                if (!$db->execute())
                    JError::raiseWarning(0, 'Cannot delete old invoices info: ' . $db->getErrorMsg());
            }

            $dbChecked = true;
        }

        $type = $params->get('order_number', 'own');
        if ($type == 'order' || $type == 'order_number') { //invoice number = order id or order number
            if ($type == 'order_number') {
                if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2)
                    $db->setQuery('SELECT `order_number` FROM `#__virtuemart_orders` WHERE `virtuemart_order_id` = ' . (int) $orderID);
                else
                    $db->setQuery('SELECT `order_number` FROM `#__vm_orders` WHERE `order_id` = ' . (int) $orderID);
                if (!$invoiceNo = $db->loadResult())
                    return false;
            } else
                $invoiceNo = $orderID;

            $db->setQuery("SELECT `id`,`order_date` FROM `#__nborders_mailsended` WHERE `order_id` = " . (int) $orderID);
            $resInv = $db->loadAssoc();

            if (empty($resInv['id'])) { //create row in mailsended if not created yet (mostly to store invoice date)
                $db->setQuery('INSERT INTO `#__nborders_mailsended` (`order_id`, `order_no`, `order_prefix`) VALUES (' . (int) $orderID . ', null, null)');
                $db->execute();
                $db->setQuery("SELECT `id`,`order_date` FROM `#__nborders_mailsended` WHERE `order_id` = " . (int) $orderID);
                $resInv = $db->loadAssoc();
            }
        } elseif ($type == 'own') {

            $startNo = $params->get('start_number');

            $db->setQuery('SELECT `id`,`order_id`, `order_no`, `order_prefix`, `order_date`
            	FROM `#__nborders_mailsended` WHERE `order_id` = ' . (int) $orderID);
            $resInv = $db->loadAssoc();

            if (!$resInv['order_no']) { //create invoice no
                if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2) {
                    $db->setQuery('SELECT virtuemart_order_id AS order_id, `order_status`,`created_on`, `modified_on` FROM `#__virtuemart_orders` WHERE `virtuemart_order_id` = ' . (int) $orderID);
                    $resOrder = $db->loadAssoc();
                    $resOrder['cdate'] = NbordersHelper::gmStrtotime($resOrder['created_on']);
                    $resOrder['mdate'] = NbordersHelper::gmStrtotime($resOrder['modified_on']);
                } else {
                    $db->setQuery('SELECT order_id, `order_status`,`cdate`,`mdate` FROM `#__vm_orders` WHERE `order_id` = ' . (int) $orderID);
                    $resOrder = $db->loadAssoc();
                }



                if (empty($resOrder)) //order not exists
                    return false;

                if (!$force) {
                    if ((int) $orderID < (int) $params->get('starting_order', 0)) //order has lower then minimal id for creation
                        return false;

                    // only create new number when order is in desired status(es)
                    if (!in_array($resOrder['order_status'], (array) $params->get('order_status')))
                        return false;
                }

                $prefix = $params->get('number_prefix'); //use default prefix from config
                // find last number
                $db->setQuery('SELECT MAX(`order_no`) FROM `#__nborders_mailsended` WHERE `order_prefix`=' . $db->Quote($prefix));
                $no = $db->loadResult();

                // set next number
                $no = ($no < $startNo) ? $startNo : ++$no;

                // store new number
                if (!$resInv['order_id'])
                    $db->setQuery('INSERT INTO `#__nborders_mailsended` (`order_id`, `order_no`, `order_prefix`) VALUES (' . (int) $orderID . ', ' . (int) $no . ', ' . $db->Quote($prefix) . ')');
                else
                    $db->setQuery('UPDATE `#__nborders_mailsended` SET `order_no` = ' . (int) $no . ', `order_prefix` = ' . $db->Quote($prefix) . " WHERE `order_id` = " . (int) $orderID);

                if (!$db->execute()) {
                    JError::raiseWarning(400, 'Netbase VM Extend: Failed storing new invoice number for order ' . $orderID);
                    return false;
                }
            } else { //row is already in db - load values
                $prefix = $resInv['order_prefix'];
                $no = $resInv['order_no'];
            }

            // make sure to prepend zeros if startNo length string is bigger than no
            $minLength = strlen($startNo);
            $length = strlen(strval($no));
            if ($minLength > $length) {
                $invoiceNo = str_repeat('0', $minLength - $length) . $no;
            }
            //
            else
                $invoiceNo = $no;

            $invoiceNo = $prefix . $invoiceNo; //final invoice no with prefix
        }
        // wrong setting
        else
            $invoiceNo = $orderID;

        //create default invoice date, if is not set yet
        if (empty($resInv['order_date'])) {
            //get default invoice date

            $invoiceDate = 0;
            $invoiceDateDefault = $params->get('order_date', 'ndate');
            if ($invoiceDateDefault == 'cdate' OR $invoiceDateDefault == 'mdate') { //use order modify or create date
                if (!isset($resOrder)) {

                    if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2) {
                        $db->setQuery('SELECT `created_on`, `modified_on` FROM `#__virtuemart_orders` WHERE `virtuemart_order_id` = ' . (int) $orderID);
                        $resOrder = $db->loadAssoc();
                        $resOrder['cdate'] = NbordersHelper::gmStrtotime($resOrder['created_on']);
                        $resOrder['mdate'] = NbordersHelper::gmStrtotime($resOrder['modified_on']);
                    } else {
                        $db->setQuery('SELECT `cdate`,`mdate` FROM `#__vm_orders` WHERE `order_id` = ' . (int) $orderID);
                        $resOrder = $db->loadAssoc();
                    }
                }
                $invoiceDate = $resOrder[$invoiceDateDefault];
            } elseif ($invoiceDateDefault == 'ndate')
                $invoiceDate = time();

            if ($invoiceDate) {
                $db->setQuery('UPDATE `#__nborders_mailsended` SET `order_date`=' . $invoiceDate . ', `order_lastchanged`=' . time() . '  WHERE `order_id` = ' . (int) $orderID);
                $db->execute();
            }
        }

        return $invoiceNo;
    }

    static function getInvoiceNos($orderID, $glue = '_') {
        return is_array($orderID) ? join($glue, $orderID) : $orderID;
    }

    static function getSendBoth() {
        $params = NbordersHelper::getParams();

        if ($params->get('send_both') && $params->get('delivery_note')) {
            return true;
        }
        return false;
    }

    /**
     * Determines if we can use already generated PDF. If file exists, but is not actual, deletes it.
     * 
     * @param	int		order id
     * @param	bool	true if invoice, false delivery note
     * @param	int		optional order's mdate, if we know it
     * 
     * @return	string	filename if is actual, false if is not actual or not exists
     */
    static function canUseActualPDF($orderID, $deliveryNote = false, $lastOrderChange = null) {


        static $displayingHistory;

        $params = NbordersHelper::getParams();

        if ($params->get('debug', 0) || $params->get('cache_pdf', 1) == 0)
            return false;

        $fileName = $deliveryNote ? self::getDeliveryNoteFile($orderID) : self::getInvoiceFile($orderID);

        //TODO: some kind of caching to fasten function. but must update cached value if we delete or create file or change something relevant..
        //TODO: check if we have all database cols. because it seems someone has errror during upgrade and not have last config update.
        if (JFile::exists($fileName)) {
            $db = JFactory::getDBO();

            $field = $deliveryNote ? 'dn_generated' : 'order_generated';
            $db->setQuery("SELECT `" . $field . "`,`order_lastchanged`  FROM `#__nborders_mailsended` WHERE `order_id` = " . (int) $orderID);
            $invoiceInfo = $db->loadObject(); //get last pdf generation
            $lastGeneration = (empty($invoiceInfo->$field)) ? 0 : $invoiceInfo->$field;

            $lastInvoiceDateChange = empty($invoiceInfo->order_lastchanged) ? 0 : $invoiceInfo->order_lastchanged;

            $alsoSelect = '';
            if (!$deliveryNote && !isset($displayingHistory[$deliveryNote])) //select also template to determien if we need history information also
                $alsoSelect = ', CONCAT(template_body,template_header,template_footer) AS `template`';
            elseif ($deliveryNote && !isset($displayingHistory[$deliveryNote]))
                $alsoSelect = ', CONCAT(template_dn_body,template_dn_header,template_dn_footer) AS `template`';

            $app = JFactory::getApplication();

            if ($alsoSelect)
                $displayingHistory[$deliveryNote] = (strpos($app->getTemplate(), '{order_history}') !== false); //determine if we need history on invoice
            if(isset($config->last_appearance_change))
            $lastAppearanceChange = $config->last_appearance_change; //get config last pdf appearance change
            else
             $lastAppearanceChange="";   

            if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2)
                $db->setQuery("SELECT O.`modified_on` AS order_mdate, V.`modified_on` AS vendor_mdate, UI.`modified_on` AS user_mdate
					FROM `#__virtuemart_orders` AS O 
					LEFT JOIN `#__virtuemart_vendors` AS V ON O.virtuemart_vendor_id=V.virtuemart_vendor_id
					LEFT JOIN `#__virtuemart_vmusers` AS U ON (U.virtuemart_vendor_id=O.virtuemart_vendor_id AND U.user_is_vendor=1)
					LEFT JOIN `#__virtuemart_userinfos` AS UI ON (U.virtuemart_user_id=UI.virtuemart_user_id AND UI.address_type='BT')
					WHERE O.`virtuemart_order_id` = " . (int) $orderID);
            else
                $db->setQuery("SELECT O.`mdate` AS order_mdate, V.`mdate` AS vendor_mdate, NULL AS user_mdate
					FROM `#__vm_orders` AS O 
					LEFT JOIN `#__vm_vendor` AS V ON O.vendor_id = V.vendor_id
					WHERE O.`order_id` = " . (int) $orderID);

            $lastOrderHistory = 0;
            if ($displayingHistory[$deliveryNote]) { //get latest change in order history
                if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2)
                    $db->setQuery('SELECT modified_on FROM #__virtuemart_order_histories WHERE virtuemart_order_id=' . (int) $orderID . ' ORDER BY modified_on DESC LIMIT 1');
                else
                    $db->setQuery('SELECT date_added FROM #__vm_order_history WHERE order_id=' . (int) $orderID . ' ORDER BY date_added DESC LIMIT 1');

                if ($lastOrderHistory = $db->loadResult())
                    $lastOrderHistory = NbordersHelper::gmStrtotime($lastOrderHistory);
            }

            $order = $db->loadObject();  //get lastorder and vendor change

            if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2) {
                $order->order_mdate = NbordersHelper::gmStrtotime($order->order_mdate);
                $order->vendor_mdate = NbordersHelper::gmStrtotime($order->vendor_mdate);
                $order->user_mdate = NbordersHelper::gmStrtotime($order->user_mdate);
            }

            if ($lastGeneration > $lastAppearanceChange AND
                    $lastGeneration > $order->order_mdate AND
                    $lastGeneration > $order->vendor_mdate AND
                    $lastGeneration > $order->user_mdate AND
                    //$lastGeneration > $lastFieldsChange AND
                    $lastGeneration > $lastInvoiceDateChange AND
                    $lastGeneration > $lastOrderHistory) //we can use this file
                return $fileName;

            //else //we cannot use, delete it (rather no)
            //	unlink($fileName); 
        }


        return false;
    }

    /**
     * 
     * Generate PDF for one order or more orders (in one PDF file!)
     * @param mixed $orderID OrderID or array of IDs
     * @param bool $mail	true means that function only generate file, false means it will also output that file
     * @param bool $deliveryNote
     * @param bool $force	true - re-generate also if already is generated
     */
    static function generatePDF($orderID, $mail = false, $deliveryNote = false, $force = false) {
        $orderID = (array) $orderID;

        /*
          $params = NbordersHelper::getParams();
          //set error reporting to maximum
          if ($params->get('debug')){
          error_reporting(E_ALL);
          header ('Content-type: text/html; charset=utf-8');
          }
         */

        //generate pdf(s)
        foreach ($orderID as $page => $id) {
            //if (!$force && NbordersHelper::canUseActualPDF($id, $deliveryNote)) //we can use stored
            //continue;

            if (!NbordersHelper::generateOnePDF($id, $deliveryNote))
                unset($orderID[$page]);
        }

        if ($mail) { //only generate file
            /*
              if ($params->get('debug')){
              self::flushJoomlaMessages();
              exit;}
             */
            return true;
        } else { //output to browser
            $filenames = array();

            foreach ($orderID as $id)
                $filenames[] = !$deliveryNote ? NbordersHelper::getInvoiceFile($id, true) : NbordersHelper::getDeliveryNoteFile($id, true);

            //print_r($filenames);die;

            if (count($filenames) > 1) { //multi pdfs
                //http://pdfmerger.codeplex.com/
                require_once(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_netbasevm_extend' . DS . 'libraries' . DS . 'pdfmerger' . DS . 'PDFMerger.php');
                $merger = new PDFMerger;
                foreach ($filenames as $filename)
                    $merger->addPDF($filename);

                //multi pdfs filename
                $filename = !$deliveryNote ? NbordersHelper::getInvoiceFile($orderID, true) : NbordersHelper::getDeliveryNoteFile($orderID, true);
                $fileString = $merger->merge('string', $filename);
                if (file_put_contents($filename, $fileString) === false)
                    die('Cannot store multi pdf ' . $filename);
            }
            elseif (count($filenames) == 1) //one pdf
                $filename = reset($filenames);
            else //error
                die("PDF not generated");
            /* 	
              if ($params->get('debug')){
              self::flushJoomlaMessages();
              exit();}
             */

            self::outputStoredPDF($filename);
            exit();
        }
    }

    public static function flushJoomlaMessages() {
        //get raised errors and notices frm Joomla! in mean time
        $app = JFactory::getApplication();
        foreach ($app->getMessageQueue() as $message)
            echo '<b>Joomla! ' . ucfirst($message['type']) . '</b>: ' . $message['message'] . "<br>" . PHP_EOL;
    }

    static function getInfoOrder($orderId, $deliveryNote) {
        // load HTML class
        require_once(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_netbasevm_extend' . DS . 'helpers' . DS . 'invoicehtmlparent_new.php');
        require_once(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_netbasevm_extend' . DS . 'helpers' . DS . 'invoicehtml_new.php');

        $language = NbordersHelper::getInvoiceLanguage($orderId);
        $html = new InvoiceHTMLnew($orderId, $deliveryNote, $language);
    }

    /**
     * Generates one invoice and store to tmp folder.
     * 
     * @param int	$orderId
     * @param bool	$deliveryNote
     */
    static function generateOnePDF($orderId, $deliveryNote) {
        if (!$invoiceNumber = NbordersHelper::getInvoiceNo($orderId))
            return false;

        //$params = NbordersHelper::getParams();
        //$library = 'tcpdf';
        //initialize PDF library
        //require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_netbasevm_extend'.DS.'helpers'.DS.'invoicetcpdf.php');
        //$pdf = new InvoiceTCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        NbordersHelper::getInfoOrder($orderId, $deliveryNote);

        //set metadata
        /*
          $pdf->invoiceAuthor = 'Netbase International Co.';
          $pdf->invoiceTitle = 'Netbase VM Extend - '. NbordersHelper::frontendTranslate('COM_NETBASEVM_EXTEND_INVOICE_NUMBER', $orderId).' '. $invoiceNumber;
          $pdf->invoiceSubject =  NbordersHelper::frontendTranslate('COM_NETBASEVM_EXTEND_INVOICE_NUMBER', $orderId).' '.$invoiceNumber;
          $pdf->invoiceKeywords = 'Netbase, Invoice, ' . $invoiceNumber;

          //create page
          $pdf->addInvoicePage($orderId,$deliveryNote);

          //get filename
          $fileName = !$deliveryNote ? self::getInvoiceFile($orderId,true) : self::getDeliveryNoteFile($orderId,true);

          //save file to tmp folder
          if (!$pdf->saveInvoicePDF($fileName))
          return false;

          //update db last generation
          NbordersHelper::updateLastGeneration($orderId, $deliveryNote);
         */
        return true;
    }

    /**
     * Outputs PDF file stored on disk with proper headers.
     * @param string $filename 	Name of file on disk
     */
    static function outputStoredPDF($filename) {
        ob_end_clean();
        header('Content-Type: application/pdf');
        header('Cache-Control: public, must-revalidate, max-age=0'); // HTTP/1.1
        header('Pragma: public');
        header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Content-Length: ' . filesize($filename));
        header('Content-Disposition: inline; filename="' . basename($filename) . '";');
        readfile($filename);
    }

    /**
     * Write last generation time of PDF into db
     * 
     * @param	int		order id
     * @param	bool	true if invoice, false if delivery note
     * 
     * @return	bool	db change success
     */
    static function updateLastGeneration($orderId, $deliveryNote = false) {
        if (is_numeric($orderId)) { //else multi
            $db = JFactory::getDBO();
            $db->setQuery("UPDATE `#__nborders_mailsended` SET `" . ($deliveryNote ? 'dn_generated' : 'order_generated') . "` = '" . time() . "' WHERE `order_id` = " . (int) $orderId);
            return $db->execute();
        } else
            return false;
    }

    static function getComponentInfo() {
        static $info;

        if (!isset($info)) {
            $info = array();

            $xml = JFactory::getXML('Simple');

            $xmlFile = JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_netbasevm_extend' . DS . 'vminvoice.xml';

            if (JFile::exists($xmlFile)) {
                if ($xml->loadFile($xmlFile)) {
                    $root = $xml->document;

                    $element = $root->getElementByPath('version');
                    $info['version'] = $element ? $element->data() : '';

                    $element = $root->getElementByPath('creationdate');
                    $info['creationDate'] = $element ? $element->data() : '';

                    $element = $root->getElementByPath('author');
                    $info['author'] = $element ? $element->data() : '';

                    $element = $root->getElementByPath('authoremail');
                    $info['authorEmail'] = $element ? $element->data() : '';

                    $element = $root->getElementByPath('authorurl');
                    $info['authorUrl'] = $element ? $element->data() : '';

                    $element = $root->getElementByPath('copyright');
                    $info['copyright'] = $element ? $element->data() : '';

                    $element = $root->getElementByPath('license');
                    $info['license'] = $element ? $element->data() : '';

                    $element = $root->getElementByPath('description');
                    $info['description'] = $element ? $element->data() : '';
                }
            }
        }

        return $info;
    }

    /**
     * Determine, in which language will be invoice.
     * 
     * @param int $orderId
     * @return string (language tag) / false.
     */
    static function getInvoiceLanguage($orderId = null) {
        static $langs;

        if (!isset($langs[$orderId])) {
            $possibleLangs = array();
            $params = NbordersHelper::getParams();

            //1. language was forcely set by URL, if allowed
            if (($tag = JRequest::getVar('order_language')) AND $params->get('frontend_current_lang'))
                $possibleLangs[] = $tag;

            //2. override by user language, if allowed
            if (is_numeric($orderId) AND $orderId > 0 AND $params->get('user_language', 1) == 1) {
                $oldReporting = error_reporting(PHP_VERSION >= 5.4 ? ((E_ALL & ~E_DEPRECATED) ^ E_NOTICE) : (E_ALL ^ E_NOTICE)); //prevent displaying joomfish php notice (?)
                if ($userId = InvoiceGetter::getOrderUserId($orderId)) {
                    $user = JFactory::getUser($userId);
                    $possibleLangs[] = $user->getParam('language');
                }
                if ($oldReporting > 0)
                    error_reporting($oldReporting);
            }

            //3. use site default frontend language
            $langParams = JComponentHelper::getParams('com_languages');
            $possibleLangs[] = $langParams->get('site');

            //4. en-GB
            $possibleLangs[] = 'en-GB';

            //unset empty, unset non-existent vm invoice frontend translations
            jimport('joomla.filesystem.file');
            $possibleLangs = array_unique($possibleLangs);
            //print_r($possibleLangs);
            foreach ($possibleLangs as $key => $tag)
                if (!$tag || !JFile::exists(JLanguage::getLanguagePath(JPATH_SITE, $tag) . DS . $tag . '.com_netbasevm_extend.ini'))
                    unset($possibleLangs[$key]);

            //raise warning if no suitable language (but en-GB should be always)
            //print_r($possibleLangs);
            if (!$possibleLangs)
                JError::raiseWarning(0, 'Netbase VM Extend: Cannot determine any front-end translation.');

            $langs[$orderId] = $possibleLangs ? reset($possibleLangs) : false;
        }

        return $langs[$orderId];
    }

    /**
     * For translation of invoice strings
     * 
     * @param string $string
     * @param string $language
     * @param false/array $sprintfArgs
     * @param string $extension
     * @return string;
     */
    static function frontendTranslate($string, $language, $sprintfArgs = false, $extension = 'com_netbasevm_extend') {
        static $langs;
        static $extensionsLoaded;

        if (is_numeric($language)) //passed order id
            $language = NbordersHelper::getInvoiceLanguage($language);

        //create InvoiceLanguage object
        if (!isset($langs[$language])) {
            require_once JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_netbasevm_extend' . DS . 'helpers' . DS . 'language.php';
            $langs[$language] = new InvoiceLanguage($language);
            $langs[$language]->loadOverrides(JPATH_SITE); //load (also) frotnend overrides, this is reason why we have own class
        }

        //load files for extension
        if (!isset($extensionsLoaded[$language][$extension])) {
            $loaded = array();

            //if other than en-GB: 1. load en-GB as basic
            if ($language != 'en-GB') {
                $loaded['backend_en'] = $langs[$language]->load($extension, JPATH_ADMINISTRATOR, 'en-GB', true);
                $loaded['frontend_en'] = $langs[$language]->load($extension, JPATH_SITE, 'en-GB', true);
            }

            //2. load backend, then frontend over it
            $loaded['backend'] = $langs[$language]->load($extension, JPATH_ADMINISTRATOR, $language, true, false);
            $loaded['frontend'] = $langs[$language]->load($extension, JPATH_SITE, $language, true, false);

            //display warning, Netbase VM Extend should have frontend file at this point
            if (!$loaded['frontend'] AND $extension == 'com_netbasevm_extend')
                JError::raiseWarning(0, 'Netbase VM Extend: Cannot load ' . $language . ' ' . $extension . ' language for front-end');

            //3. component lang subfolder in joomla > 1.6
            if (COM_NETBASEVM_EXTEND_ISJ16 AND strpos(strtolower($extension), 'com_') === 0) {
                $loaded['component_frontend'] = $langs[$language]->load($extension, JPATH_SITE . DS . 'components' . DS . $extension, $language, true, false);
                $loaded['component_backend'] = $langs[$language]->load($extension, JPATH_ADMINISTRATOR . 'components' . DS . $extension, $language, true, false);
            }

            if (!in_array(true, $loaded)) //check if any of load was successful
                JError::raiseWarning(0, 'Netbase VM Extend: Cannot load ' . $language . ' ' . $extension . ' language');

            $extensionsLoaded[$language][$extension] = true;
        }

        if ($sprintfArgs) { //equivalent of JText::sprintf
            $count = count($sprintfArgs);
            if (is_array($sprintfArgs[$count - 1])) {
                $sprintfArgs[0] = $langs[$language]->_($string, array_key_exists('jsSafe', $sprintfArgs[$count - 1]) ? $sprintfArgs[$count - 1]['jsSafe'] : false, array_key_exists('interpretBackSlashes', $sprintfArgs[$count - 1]) ? $sprintfArgs[$count - 1]['interpretBackSlashes'] : true);
                if (array_key_exists('script', $sprintfArgs[$count - 1]) && $sprintfArgs[$count - 1]['script']) {
                    call_user_func_array('sprintf', $sprintfArgs); //?
                    return $string;
                }
            } else
                $sprintfArgs[0] = $langs[$language]->_($string);

            return call_user_func_array('sprintf', $sprintfArgs);
        } else //regular JText::
            return $langs[$language]->_($string);
    }

    /**
     * Try to assign VM tax rate based on gross and net price. With tolerance from config.
     * @param unknown_type $gross
     * @param unknown_type $net
     */
    static function guessTaxRate($gross, $net) {
        if ($net == 0)
            return 0;

        static $taxRates = null;
        static $tolerance = null;

        $gross = (float) $gross;
        $net = (float) $net;

        if (!$taxRates) //load VM tax rates
            $taxRates = InvoiceGetter::getTaxRates();

        if (is_null($tolerance)) { //get config tolerance
            $params = NbordersHelper::getParams();
            $tolerance = str_replace('%', '', str_replace(',', '.', $params->get('taxrate_tolerance', 0.2))) / 100;
        }

        $computedRate = $gross / $net - 1;
        foreach ($taxRates as $rate) {
            $offset = abs($computedRate - $rate->value);
            if ($offset <= $tolerance AND ( (isset($lastoffset) AND $offset < $lastoffset) OR ! isset($lastoffset))) {
                $fittingRate = $rate->value;
                $lastoffset = $offset; //store much close was guessed rate to orig. value. most close wins. (if is in tolerance of course)
            }
        }

        return isset($fittingRate) ? $fittingRate : $computedRate;
    }

    static function getItemsFooterOrdering($dn, $getDefault = false) {
        static $ordering;

        $default = explode(',', 'tax_summary,subtotal,coupon_extended,calc_rules,coupon_simple,total_net,total_tax,total_discount,hr,total');
        if ($getDefault)
            return $default;

        if (!isset($ordering)) {

            $params = NbordersHelper::getParams();
            $mandatory = explode(',', 'tax_summary,subtotal,coupon_extended,calc_rules,coupon_simple,total_net,total_tax,total_discount,total'); //must contain these

            if (!($ordering = $params->get('items_footer_' . ($dn ? 'dn_' : '') . 'ordering')))
                $ordering = $default; //default

            if (!is_array($ordering))
                $ordering = explode(',', $ordering);

            $ordering = array_map('trim', $ordering);

            if ($missing = array_diff($mandatory, $ordering)) {
                JError::raiseWarning(0, 'Netbase VM Extend: Bad config for ' . 'items_footer_' . ($dn ? 'dn_' : '') . 'ordering. Missing values: ' . implode(', ', $missing) . '. Missing rows were added on end.');
                $ordering = array_merge($ordering, $missing);
            }
        }

        return $ordering;
    }

    /**
     * Get number of columns in first row of table
     * 
     * @param string $table
     */
    static function getNumCols($table) {
        if (!preg_match('#<\s*tr[^>]*>(.*)<\s*\/tr\s*>#iUs', $table, $row)) //match first row
            return 'No table rows in table for ';

        if (!$columns = NbordersHelper::getColumns($row[1])) //match columns in row
            return 'No table columns in first row for ';

        $cols = 0;
        foreach ($columns as $column) {
            if (isset($column[1]) && preg_match('#colspan\s*=\s*["\']?\s*(\d+)#i', $column[1], $colspan))
                $cols += $colspan[1];
            else
                $cols ++;
        }
        return $cols;
    }

    /**
     * Get columns array of html row. 
     * Array keys are not continuous, column keys are incremented by colspan.
     * @param string $rowHTML
     * 
     * @return false / array of columns. each element is array with 
     * 0: whole column 
     * 1: opening tag content 
     * 2: column content
     * 3: column colspan
     */
    static function getColumns($rowHTML) {
        if (!preg_match_all('#\s*<\s*(t[dh][^>]*)>(.*)<\s*\/\s*t[dh]\s*>\s*#isU', $rowHTML, $columns, PREG_SET_ORDER)) //match columns in row
            return false;

        $i = 0;
        $return = array();
        foreach ($columns as $column) {
            $return[$i] = $column;

            $colspan = 1;
            if (preg_match('#colspan\s*=\s*["\']?\s*(\d+)#i', $column[1], $tdColspan))
                $colspan = $tdColspan[1];

            $return[$i][3] = $colspan;

            $i+=$colspan;
        }
        return $return;
    }

    /**
     * Removes specified columns from row. 
     * If column to remove is inside colspan, colspan is lowered.
     * In other words, if you want to remove whole column with colspan, 
     * you must specify ALL keys inside colspan.
     * 
     * @param string 	table row
     * @param array		array of column keys to remove
     * @return string	table row
     */
    //TODO: preserve widths?
    static function removeColumns($row, $numbers) {
        $numbers = (array) $numbers;

        if (!preg_match('#<\s*tr([^>]*)>.*<\s*/\s*tr\s*>#isU', $row, $rowTag)) //get tr whole opening tag (may contain styles or align)
            die('No table row 2');

        $columns = NbordersHelper::getColumns($row);

        foreach ($numbers as $number) { //iterate through columns to delete
            $key = $number;

            while (!isset($columns[$key]) && $key > 0) //search for nearest existing key (can be inside colspan)
                $key--;

            if (!isset($columns[$key])) //no column with that key, nothing
                continue;

            //lower foregoing column colspan or delete column
            if (preg_match('#colspan\s*=\s*["\']?\s*(\d+)#i', $columns[$key][1], $tdColspan) && $tdColspan[1] > 1)
                $columns[$key][1] = preg_replace('#colspan\s*=\s*["\']?\s*(\d+)\s*["> ]#i', 'colspan="' . ($tdColspan[1] - 1) . '"', $columns[$key][1]);
            else
                unset($columns[$key]);
        }

        $row = '<tr' . $rowTag[1] . '>';
        foreach ($columns as $key => $column) //add new columns again
            $row.='<' . $column[1] . '>' . $column[2] . '</td>';

        return $row . '</tr>';
    }

    static function replaceColumn($row, $columnNo, $content) {
        $row = self::removeColumns($row, $columnNo);
        $row = self::addColumn($row, $columnNo, $content);
        return $row;
    }

    /**
     * Adds column to specified position. Columns after are pushed. 
     * If inserted inside colspan, it is splitted properly.
     */
    static function addColumn($row, $columnNo, $content) {
        if (!preg_match('#<\s*tr([^>]*)>.*<\s*/\s*tr\s*>#isU', $row, $rowTag)) //get tr whole opening tag (may contain styles or align)
            die('No table row 2');

        //insert colspan to column content
        /*
          $replaceColspan = $colspan>1 ? ' colspan="'.$colspan.'" ' : '';
          $content = preg_replace('#^\s*(<\s*t[dh][^>]*)colspan\s*=\s*"?\s*\d+\s*"?#is','$1'.$replaceColspan,$content);
         */
        //increment all array keys after insertion.
        $oldColumns = NbordersHelper::getColumns($row);
        $columns = array();
        foreach ($oldColumns as $key => $column)
            $columns[($key < $columnNo ? $key : $key + 1)] = $column;

        $key = $columnNo;
        while (!isset($columns[$key]) && $key > 0) //search for nearest existing column before	
            $key--;

        if (isset($columns[$key])) { //some columns before
            $colspanBefore = $columnNo - $key;

            $realColspan = $columns[$key][3];

            if ($realColspan > $colspanBefore) { //we will insert INSIDE colspan column
                //replace original colspan by fitting one (left slice)
                $replaceColspan = $colspanBefore > 1 ? ' colspan="' . $colspanBefore . '" ' : '';
                $columns[$key][0] = preg_replace('#^\s*(<\s*t[dh][^>]*)(colspan\s*=\s*"?\s*\d+\s*"?)?#is', '$1' . $replaceColspan, $content);

                //add new column + "dummy" empty column with colspan (right slice)
                $colspanAfter = $realColspan - $colspanBefore;
                $columns[$columnNo][0] = $content . '<td' . ($colspanAfter > 1 ? ' colspan="' . $colspanAfter . '"' : '') . '></td>';
            } else //we will insert after colspan td (not interfering), just ok
                $columns[$columnNo][0] = $content;
        } else // no columns before
            $columns[$columnNo][0] = $content;

        ksort($columns); //re-sort columns by their keys
        //connect columns back to row
        $newLine = '<tr' . $rowTag[1] . '>';
        foreach ($columns as $column)
            $newLine .= $column[0];
        $newLine.='</tr>';
        return $newLine;
    }

    /**
     * Unset all column widths and replace by new ones. Can be passed row or array of rows
     * @param string/array	HTML row(s)
     * @param	array	new widths in %
     * @return string/array	HTML row(s) with new widths
     */
    static function setColumnWidths($rows, $widths) {
        if (is_array($rows)) {

            foreach ($rows as &$row)
                $row = NbordersHelper::setColumnWidths($row, $widths);
            return $rows;
        } else
            $row = $rows;

        //rebuild item rows width new widths
        if (!preg_match('#<\s*tr([^>]*)>.*<\s*/\s*tr\s*>#isU', $row, $rowTag)) //get tr whole opening tag (may contain styles or align)
            die('No table row');

        $columns = NbordersHelper::getColumns($row);

        $newLine = '<tr' . $rowTag[1] . '>';

        foreach ($columns as $key => $column) {
            if ($column[3] > 1) { //column has colspan
                $newWidth = 0;
                for ($i = 0; $i < $column[3]; $i++) //sum all next widths until colspan end
                    if (isset($widths[$i + $key]))
                        $newWidth+=round($widths[$i + $key]);
            } else
                $newWidth = $widths[$key];

            $columns[$key][1] = preg_replace('#(\s|"|\'|;)width\s*:[^;"]*;?#is', '$1', $columns[$key][1]); //unset width set by style
            $columns[$key][1] = preg_replace('#\s*(\s)width\s*=\s*"?\s*\d*%?\s*"?\s*(>?)#is', '$1$2', $columns[$key][1]); //unset width set in attribute
            $newLine .= '<' . $columns[$key][1] . ' width="' . round($newWidth) . '%">' . $columns[$key][2] . '</td>' . "\n"; //set new width
        }
        $newLine.='</tr>';

        return $newLine;
    }

    static function setColumnStyle($row, $columnNo, $name, $value) {
        
    }

    static function imgSrc($img) {
        return JURI::root() . 'administrator/components/com_netbasevm_extend/assets/images/' . $img;
    }

}

//load languages
$lang = JFactory::getLanguage();
$tag = $lang->get('tag');

//load eng files as base (if local translation is missing, is used eng)
$lang->load("com_netbasevm_extend", JPATH_SITE, 'en-GB');
$lang->load("com_netbasevm_extend", JPATH_ADMINISTRATOR, 'en-GB');
$lang->load(COM_NETBASEVM_EXTEND_ISJ16 ? 'com_netbasevm_extend.sys' : 'com_netbasevm_extend.menu', JPATH_ADMINISTRATOR, 'en-GB'); //load menu language strings
$lang->load('com_virtuemart', JPATH_VM_SITE);
$loadLang = null; //now load current language

if ($tag != 'en-GB') {
    $lang->load("com_netbasevm_extend", JPATH_SITE, $loadLang, true); // Load front-end language file also
    $lang->load("com_netbasevm_extend", JPATH_ADMINISTRATOR, $loadLang, true);
    $lang->load(COM_NETBASEVM_EXTEND_ISJ16 ? 'com_netbasevm_extend.sys' : 'com_netbasevm_extend.menu', JPATH_ADMINISTRATOR, $loadLang, true); //load menu language strings
}



// define folder for storing invoices
define('INVOICES_TMP', NbordersHelper::getBaseInvociesSubdir());

//load data getter
require_once(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_netbasevm_extend' . DS . 'helpers' . DS . 'getter.php');

//load currency display
require_once (JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_netbasevm_extend' . DS . 'helpers' . DS . 'currencydisplay.php');
?>
