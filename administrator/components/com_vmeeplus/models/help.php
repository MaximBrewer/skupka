<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @copyright    Copyright (C) 2013 InteraMind Ltd. All rights reserved.
 * @license      http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @package		This file is part of InteraMind VM Email Manager Plus Component
 **/


jimport( 'joomla.application.component.model' );


class vmeeProModelHelp extends JModelLegacy {

	function sendDebugFiles(){
		$subject = JFactory::getApplication()->input->get( 'subject', '','RAW');
		$description = JFactory::getApplication()->input->get( 'description', ' - ','RAW');
		$open_ticket = JFactory::getApplication()->input->get( 'open_ticket', '0','RAW');
		$ticket_id = JFactory::getApplication()->input->get( 'ticket_id', ' - ','RAW');
		$cc_address = JFactory::getApplication()->input->get( 'cc_address', null,'RAW');
		$admin_user = JFactory::getApplication()->input->get( 'admin_user', ' - ','RAW');
		$admin_password = JFactory::getApplication()->input->get( 'admin_password', ' - ','RAW');
		
		if(empty($subject))
			$subject = "Email Manager Plus Debug info from";

		$site_url = JUri::root();
		$subject .= ' ('.$site_url.')';
		
		$to_address = "sales@interamind.com";
		if($open_ticket)
			$to_address = "support@interamind.com";
		
		$config = JFactory::getConfig();
		$path = $config->get('log_path');
		$path .=  DS . 'vmeepro.log.php';
		if(!file_exists($path)){
			$path = '';
		}
		
		$body = "<p>Hello InteraMind Support,</p>\n";
		$body .= "<p>Ticket ID: ".$ticket_id."</p>\n";
		$body .= "<p>Admin User: ".$admin_user." Password: ".$admin_password."</p>\n";
		
		$error = "";
		$isCodeInstalled = $this->checkIfInstalled($error);
		if(!$isCodeInstalled){
			$body .= "<p>".$error."</p>\n";
		}else{
			$body .= "<p>Component's code installed properly in VM files.</p>\n";
		}
		$body .= "<p>Problem description: <br>\n".$description."</p>\n";
		
		ob_start();
			//JHtml::_('behavior.switcher');
// 			jimport( 'joomla.html.html' );
// 			require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_admin'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'sysinfo.php');
// 			$sysmodel = new AdminModelSysInfo();
// 			$this->php_settings	= $sysmodel->get('PhpSettings');
// 			$this->config		= $sysmodel->get('config');
// 			$this->info			= $sysmodel->get('info');
// 			$this->php_info		= $sysmodel->get('PhpInfo');
// 			$this->directory	= $sysmodel->get('directory');
			echo "<br><br>\n";
			//tzvika-require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_admin'.DIRECTORY_SEPARATOR.'views' . DS. 'sysinfo' . DIRECTORY_SEPARATOR . 'tmpl'.DIRECTORY_SEPARATOR.'default_system.php');echo "<br><br>\n";
			//require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_admin'.DIRECTORY_SEPARATOR.'tmpl'.DIRECTORY_SEPARATOR.'sysinfo_phpsettings.php');echo "<br><br>\n";
			//require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_admin'.DIRECTORY_SEPARATOR.'tmpl'.DIRECTORY_SEPARATOR.'sysinfo_config.php');echo "<br><br>\n";
			//tzvika-require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_admin'.DIRECTORY_SEPARATOR.'tmpl'.DIRECTORY_SEPARATOR.'sysinfo_directory.php');echo "<br><br>\n";
//			require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_admin'.DIRECTORY_SEPARATOR.'tmpl'.DIRECTORY_SEPARATOR.'sysinfo_phpinfo.php');echo "<br><br>\n";
			$body .= ob_get_contents();
		ob_end_clean();
		
		$attachment_list = array($path);
		return $this->sendEmail($to_address, $subject, $body, $cc_address, $attachment_list);
	}
	
	function sendEmail($email, $subject, $body,  $cc_list, $attachment_list){
		$emp_helper = new emp_helper();
		return $emp_helper->sendMail($emp_helper->getMailDefaultFromEmail(),  $emp_helper->getMailDefaultFromName(), $email, $subject, $body, true, $cc_list, null, null, null, null, $attachment_list);
		
	}
	
	function checkIfInstalled(&$error){
		$isInstalled = true;
		$pre_msg = "Code is not installed properly in VirtueMart file: ";
		try{
			if(!$this->isCodeInFile("shopfunctionsf")){
				$error .= $pre_msg."shopfunctionsf.php<br>";	
				$isInstalled = false;
			}
		}
		catch (Exception $e){
			$error .="\nCould not check if files installed properly.\nExceptoin:".$e->getMessage();
		}
		
		return $isInstalled;
	}
	
	function isCodeInFile($vm_fileName){
		jimport('joomla.filesystem.file');
		$bRes = false;
		$filePath = $this->getVmFilePath($vm_fileName);
		$fileContent = file_get_contents($filePath);
		if(strpos($fileContent, "//VMEEPRO START", 0) !== false){
			$bRes = true;
		}
		
		return $bRes;
	}
	
	function getVmPath($admin = true){
		if($admin){
			return JPath::clean(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_virtuemart".DIRECTORY_SEPARATOR);
		}
		else{
			return JPath::clean(JPATH_SITE.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_virtuemart".DIRECTORY_SEPARATOR);
		}
	}
	
	function getVmFilePath($vm_fileName){
		$path = '';
		$admin = true;
		switch ($vm_fileName){
			case 'shopfunctionsf':
				$path = 'helpers';
				$admin = false;
				break;
		}
		$comVmPath = $this->getVmPath($admin);
		$filePath = $comVmPath.$path.DIRECTORY_SEPARATOR.$vm_fileName.".php";
	
		return $filePath;
	}
}