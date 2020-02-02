<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @copyright    Copyright (C) 2013 InteraMind Ltd. All rights reserved.
 * @license      http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @package		This file is part of InteraMind VM Email Manager Plus Component
 **/

class emp_helper {
	static $isInitialzedVM = false;
	static $vmModels = array();
	/**
	 * Returns classnames of implementors of specified base class in a directory.
	 * Searches only for .php files in the directory, and ignores the base class itself.
	 * @return array
	 */
	static function getImplementors($baseDirName, $innerDir ,$baseClass, $prefix = 'emp', $extension = '.php') {
		$results = array();
		$handler = opendir($baseDirName.DIRECTORY_SEPARATOR.$innerDir);
		while ($file = readdir($handler)) {
			if ($file != '.' && $file != '..' && emp_helper::EndsWith($file, $extension)) {
				$className = $prefix."_".$innerDir."_".emp_helper::baseName($file, $extension);
				$baseClasses = explode(',',$baseClass);
				if (!in_array($className, $baseClasses)) {
					include_once $baseDirName.DIRECTORY_SEPARATOR.$innerDir.DIRECTORY_SEPARATOR.$file;
					try {
						$class = new ReflectionClass($className);
						if ($class->getParentClass() && !$class->isAbstract()) {
							$parent = $class->getParentClass()->getName();

							foreach ($baseClasses as $cls) {
								if ($parent == $cls) {
									$results[] = $className;
									break;
								}
							}
						}
					} catch (Exception $e) {
						//echo $e->getMessage();
					}
				}
			}
		}

		// tidy up: close the handler
		closedir($handler);

		return $results;
	}

	/**
	 * Returns classnames of implementors of specified base class in a directory.
	 * Searches only for .php files in the directory, and ignores the base class itself.
	 * @return array
	 */
	public static function isImplementor($dirName, $fileName, $baseClass, $extension = '.php') {
		if (!file_exists($dirName.DIRECTORY_SEPARATOR.$fileName)) {
			return false;
		}
		include_once ($dirName.DIRECTORY_SEPARATOR.$fileName);
		$className = emp_helper::baseName($fileName, $extension);
		$class = new ReflectionClass($className);
		$baseClasses = explode(',',$baseClass);
		if ($class->getParentClass()) {
			$parent = $class->getParentClass()->getName();
			if (is_array($baseClasses)) {
				foreach ($baseClasses as $cls) {
					if ($parent == $cls)
						return true;
				}
			} else {
				if ($parent == $baseClasses) {
					return true;
				}
			}
		}
		return false;
	}

	static function EndsWith($FullStr, $EndStr) {
		$StrLen = strlen($EndStr);
		$FullStrEnd = substr($FullStr, strlen($FullStr) - $StrLen);
		return $FullStrEnd == $EndStr;
	}

	static function baseName($FullStr, $EndStr) {
		$StrLen = strlen($EndStr);
		$base = substr($FullStr, 0, strlen($FullStr) - $StrLen);
		return $base;
	}

	function sendMail($from, $fromname, $recipient, $subject, $body, $mode=0, $cc=null, $bcc=null, $images=null, $replyto=null, $replytoname=null, $attachments = null )
	{
		if(emp_helper::isDemo()){
			JError::raiseWarning(1, JText::_('Send is disabled in demo system'));
			return false;
		}

		if(empty($from)){
			$from = $this->getMailDefaultFromEmail();
		}
		if(empty($fromname)){
			$fromname = $this->getMailDefaultFromName();
		}

		$mail = JFactory::getMailer();

		$mail->setSender(array($from, $fromname));
		$mail->setSubject($subject);
		$mail->setBody($body);

		if ( $mode ) {
			$mail->IsHTML(true);
		}

		$mail->addRecipient($recipient);
		if(!empty($cc))
			$mail->addCC($cc);
		if(!empty($bcc))
			$mail->addBCC($bcc);
			
		$mail->AltBody = $body;

		if( $images ) {
			foreach( $images as $image) {
				$mail->AddEmbeddedImage( $image['path'], $image['name'], $image['filename'], $image['encoding'], $image['mimetype']);
			}
		}
		if( $attachments ) {
			foreach( $attachments as $attachment) {
				$mail->addAttachment($attachment);
			}
		}

		if( is_array( $replyto ) ) {
			$numReplyTo = count($replyto);
			for ( $i=0; $i < $numReplyTo; $i++){
				$mail->addReplyTo( array($replyto[$i], $replytoname[$i]) );
			}
		} elseif( isset( $replyto ) ) {
			$mail->addReplyTo( array( $replyto, $replytoname ) );
		}

		return  $mail->Send();
	}

	static function getEmbeddedImages($content){
		$storeAddressLogoStyle = self::getGlobalParam('store_address_logo_style');
		if($storeAddressLogoStyle == 0)
			return null;
			
		//check if store logo is required in mail body
		if(strpos($content, '[STORE_ADDRESS_FULL_HEADER]') === false){
			return null;
		}
			
		//$dbv = $this->getVendorDB();
		$dbv = self::getVendor();
		$EmbeddedImages = array();
		if(isset($dbv->images)){
			$image = $dbv->images[0];
			$EmbeddedImages[] = array('path' => $image->getUrl(),
										'name' => "vendor_name",
										'filename' => $image->file_name,
										'encoding' => "base64",
										'mimetype' => $image->file_mimetype);
		}

		return $EmbeddedImages;
	}

	public function getMailDefaultFromEmail(){
		$dbv = self::getVendor();
		return $dbv->email;
	}

	public function getMailDefaultFromName(){
		$dbv = self::getVendor();
		return $dbv->vendor_store_name;
	}

	public static function getVersion(){
		return emp_helper::getComponetManifestElement('version');
	}

	public static function getComponetManifestElement($elementName) {
		jimport('joomla.filesystem.folder');
		$folder = JPATH_ADMINISTRATOR .DIRECTORY_SEPARATOR. 'components'.DIRECTORY_SEPARATOR.'com_vmeeplus';
		if (JFolder::exists($folder)) {
			$xmlFilesInDir = JFolder::files($folder, '.xml$');
		} else {
			$folder = JPATH_SITE .DIRECTORY_SEPARATOR. 'components'.DIRECTORY_SEPARATOR.$component;
			if (JFolder::exists($folder)) {
				$xmlFilesInDir = JFolder::files($folder, '.xml$');
			} else {
				$xmlFilesInDir = null;
			}
		}

		$xml_items = '';
		if (count($xmlFilesInDir))
		{
			foreach ($xmlFilesInDir as $xmlfile)
			{
				if ($data = JApplicationHelper::parseXMLInstallFile($folder.DIRECTORY_SEPARATOR.$xmlfile)) {
					foreach($data as $key => $value) {
						$xml_items[$key] = $value;
					}
				}
			}
		}

		if (isset($xml_items[$elementName]) && $xml_items[$elementName] != '' ) {
			return $xml_items[$elementName];
		} else {
			return '';
		}
	}

	static function getGlobalParam($paramName){
		$compParams =JComponentHelper::getParams( 'com_vmeeplus' );
 		return $compParams->get('params.'. $paramName);
	}

	static function getServiceTriggers(){
		$serviceTriggers = array(	'TRIGGER_ADMIN_ORDER_CONFIRMATION',
				'TRIGGER_ORDER_CONFIRMATION',
				'TRIGGER_ORDER_STATUS_CHANGED',
				'TRIGGER_USER_DETAILS_CHANGED',
				'TRIGGER_USER_REGISTRATION',
				'TRIGGER_ADMIN_ORDER_STATUS_CHANGED',
				'TRIGGER_ADMIN_USER_REGISTRATION',
				'TRIGGER_WAITING_LIST',
				'TRIGGER_ADMIN_WAITING_LIST');
		return $serviceTriggers;
	}

	static public function fixImagePath($str){
		$siteURL = self::getSiteURL();
		$str = preg_replace('/src=\"(?!cid)(?!http).*/Uis', "src=\"".$siteURL, $str);
		$str = str_replace("url(components", "url(".$siteURL."components", $str);
		return $str;
	}

	static public function fixLinksPath($str){
		$siteURL = self::getSiteURL();
		$str = str_replace("href=\"..", "href=\"", $str);
		$str = preg_replace('/href=\"(?!cid)(?!http)(?!mailto).*/Uis', "href=\"".$siteURL, $str);
		//$str = preg_replace('/href=\"(?!http).*/Uis', "href=\"".$siteURL, $str);
		return $str;
	}

	static public function getSiteURL(){
		$url = JUri::root();
		if(stripos($url, 'virtuemart') !== false){
			//$url is not built right probably due to inialization that was done via Pypal notify.php
			//use virtuemart constant instead
			$url = substr_replace(URL, '', -1, 1)."/";
		}
		return $url;
	}

	static public function preg_pos($hs_pattern, $hs_subject, &$hs_foundstring, $hn_offset = 0) {
		$hs_foundstring = NULL;

		if (preg_match($hs_pattern, $hs_subject, $ha_matches, PREG_OFFSET_CAPTURE, $hn_offset)) {
			$hs_foundstring = $ha_matches[0][0];
			return $ha_matches[0][1];
		}
		else {
			return FALSE;
		}
	}

	static public function preg_pos_all($hs_pattern, $hs_subject, &$ha_foundstring, $hn_offset = 0, $hn_limit = 0) {
		$ha_positions = array();
		$ha_foundstring = array();
		$hn_count = 0;
		while (false !== ($pos = self::preg_pos($hs_pattern, $hs_subject, $hs_foundstring, $hn_offset)) && ($hn_limit == 0 || $hn_count < $hn_limit)) {
			$ha_positions[] = $pos;
			$ha_foundstring[] = $hs_foundstring;
			$hn_offset = $pos + 1;                     // alternatively: '$pos + strlen($hs_foundstring)'
			++$hn_count;
		}
		return $ha_positions;
	}

	static public function addURLParameter ($url, $paramName, $paramValue) {
		$url = trim($url);
		$urlArr = parse_url($url);
		$siteUrl = self::getSiteURL();
		//No need to add parameter to outside links
		if(strpos($url, $siteUrl) === false){
			return $url;
		}
		$query = parse_url($url,PHP_URL_QUERY);
		$queryArr = array();
		parse_str($query,$queryArr);
		if(count($queryArr) == 1 && isset($queryArr['Itemid']) && $paramName == 'umk'){
			//in case there is only one parameter in the url, and this parameter is Itemid
			//adding the umk parameter will interfere with the default routing, therefore
			//we add the full query, taken from the menu
			$front = JApplication::getInstance('site');
			$menu =$front->getMenu(true);
			$item = $menu->getItem($queryArr['Itemid']);
			if($item !== NULL && is_array($item->query)) {
				$vars = $item->query;
				foreach ($vars as $name=>$value){
					$url = self::addURLParameter($url, $name, $value);
				}
			}
		}
		// first check whether the parameter is already
		// defined in the URL so that we can just update
		// the value if that's the case.

		if (preg_match('/[?&]('.$paramName.')=[^&]*/', $url)) {

			// parameter is already defined in the URL, so
			// replace the parameter value, rather than
			// append it to the end.
			$url = preg_replace('/([?&]'.$paramName.')=[^&]*/', '$1='.$paramValue, $url) ;
		} else {
			// can simply append to the end of the URL, once
			// we know whether this is the only parameter in
			// there or not.
			$url .= strpos($url, '?') ? '&' : '?';
			$url .= $paramName . '=' . $paramValue;
		}
		return $url ;
	}

	static public function isDemo(){
		require_once JPath::clean(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_vmeeplus".DIRECTORY_SEPARATOR."vmeepro.cfg.php");
		return ISDEMO === true;
	}

	static function getVmModels($vmModelName){
		if(!key_exists($vmModelName, self::$vmModels)){
			self::loadVmModel($vmModelName);
		}
		return self::$vmModels[$vmModelName];
	}
	
	static private function loadVmModel($vmModelName){
		self::$vmModels[$vmModelName] = JModelLegacy::getInstance($vmModelName, 'VirtuemartModel');
	}
	
	static function loadVirtueMartFiles(){
		if(!self::$isInitialzedVM){
			if (!class_exists('VmConfig')) require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php');
			if (!class_exists('ShopFunctions')) require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'shopfunctions.php');
			if (!class_exists('VirtueMartModelCustomfields')) require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'customfields.php');
			if (!class_exists('CurrencyDisplay')) require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'currencydisplay.php');

			VmConfig::loadConfig();

			$lang = JFactory::getLanguage();
			$extension = 'com_virtuemart_orders';
			$base_dir = JPATH_SITE . '/components/com_virtuemart';
			$language_tag = null;
			$reload = true;
			$lang->load($extension, $base_dir, $language_tag, $reload);
			
			$extension = 'com_virtuemart_shoppers';
			$lang->load($extension, $base_dir, $language_tag, $reload);
			
			$extension = 'com_virtuemart';
			$lang->load($extension, $base_dir, $language_tag, $reload);
			
			$base_dir = JPATH_ADMINISTRATOR . '/components/com_virtuemart';
			$lang->load($extension, $base_dir, $language_tag, $reload);
			
			$lang->load('com_vmeeplus', JPATH_SITE, null, true);
			$lang->load('com_vmeeplus', JPATH_ADMINISTRATOR, null, true);

			self::$isInitialzedVM = true;
			JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart' . DIRECTORY_SEPARATOR . 'models');
			JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_virtuemart/tables');
		}
	}
	
	static public function getVendor(){
		self::loadVirtueMartFiles();
		$vendorModel = self::getVmModels('vendor');
		$vendor = $vendorModel->getVendor();
		$vendorModel->addImages($vendor,1);
		$vendor->email = $vendorModel->getVendorEmail($vendor->virtuemart_vendor_id);
	
		return $vendor;
	}
	
	static function getCategories($lang = null){
// 		self::loadVirtueMartFiles();
// 		$catModel = self::getVmModels('category');
// 		$categories = $catModel->getCategories(false);
// 		return $categories;
		if(is_null($lang)){
			self::loadVirtueMartFiles();
			$lang = VMLANG;
		}
		
		//we are not calling VM model because there is no way to get vendors without limit
		$query = 'SELECT * FROM `#__virtuemart_categories_'.$lang.'` as l JOIN `#__virtuemart_categories` as v using (`virtuemart_category_id`)';
		$query .= ' ORDER BY l.`virtuemart_category_id`';
		$db = JFactory::getDBO();
		$db->setQuery($query);
		$result = $db->loadObjectList();
		if(empty($result) && $lang != 'en_gb'){
			return self::getCategories('en_gb');
		}
		return $result;
		
	}
	
	static function getManufacturers($lang = null){
// 		self::loadVirtueMartFiles();
// 		$manModel = self::getVmModels('manufacturer');
// 		$manufacturers = $manModel->getManufacturers(false,true);
// 		return $manufacturers;

		if(is_null($lang)){
			self::loadVirtueMartFiles();
			$lang = VMLANG;
		}
		
		//we are not calling VM model because there is no way to get vendors without limit
		$query = 'SELECT * FROM `#__virtuemart_manufacturers_'.$lang.'` as l JOIN `#__virtuemart_manufacturers` as v using (`virtuemart_manufacturer_id`)';
		$query .= ' ORDER BY l.`virtuemart_manufacturer_id`';
		$db = JFactory::getDBO();
		$db->setQuery($query);
		$result = $db->loadObjectList();
		if(empty($result) && $lang != 'en_gb'){
			return self::getManufacturers('en_gb');
		}
		return $result;
	}
	
	static function getVendors($lang = null){
		if(is_null($lang)){
			self::loadVirtueMartFiles();
			$lang = VMLANG;
		}
		
		//we are not calling VM model because there is no way to get vendors without limit
		$query = 'SELECT * FROM `#__virtuemart_vendors_'.$lang.'` as l JOIN `#__virtuemart_vendors` as v using (`virtuemart_vendor_id`)';
		$query .= ' ORDER BY l.`virtuemart_vendor_id`';
		$db = JFactory::getDBO();
		$db->setQuery($query);
		$result = $db->loadObjectList();
		if(empty($result) && $lang != 'en_gb'){
			return self::getVendors('en_gb');
		}
		return $result;
	}
	
	static function getProducts($lang = null){
		if(is_null($lang)){
			self::loadVirtueMartFiles();
			$lang = VMLANG;
		}
		$query = 'SELECT p.virtuemart_product_id, p.product_sku, l.product_name FROM `#__virtuemart_products_'.$lang.'` as l JOIN `#__virtuemart_products` as p using (`virtuemart_product_id`)';
		$query .= ' ORDER BY l.`virtuemart_product_id`';
		//emp_logger::log('helper::get products query: ', emp_logger::LEVEL_DEBUG, $query);
		$db = JFactory::getDBO();
		$db->setQuery($query);
		$result = $db->loadObjectList();
		if(empty($result) && $lang != 'en_gb'){
			return self::getProducts('en_gb');
		}
		return $result;
	}
	
	static function convertToMysql($time, $convert = false) {
		$date = $convert == true ? date("Y-m-d G:i:s", $time) : $time;
		return $date ;
	}

	static function unserializeSession($session_data) {
		try{
			$method = ini_get("session.serialize_handler");
			$dataArr = array();
			switch ($method) {
				case "php":
					$dataArr =  self::unserialize_php($session_data);
					break;
				case "php_binary":
					$dataArr = self::unserialize_phpbinary($session_data);
					break;
				default:
					emp_logger::log('helper::unserializeSession: ', emp_logger::LEVEL_DEBUG, "Unsupported session.serialize_handler: " . $method . ". Supported: php, php_binary");
				$dataArr = false;
			}
			return $dataArr;
		}
		catch (Exception $e){
			emp_logger::log('helper::unserializeSession: ', emp_logger::LEVEL_DEBUG, $e->getMessage());
			return false;
		}
	}

	//VM doesn't support multi-language for shopper group names
	static function getShopperGroups(){
		self::loadVirtueMartFiles();
		$shopperGroupModel = self::getVmModels('shoppergroup');
		$shopperGroups = $shopperGroupModel->getShopperGroups(false,true);
		return $shopperGroups;		
	}
	
	private static function unserialize_php($session_data) {
		$return_data = array();
		$offset = 0;
		while ($offset < strlen($session_data)) {
			if (!strstr(substr($session_data, $offset), "|")) {
				throw new Exception("invalid data, remaining: " . substr($session_data, $offset));
			}
			$pos = strpos($session_data, "|", $offset);
			$num = $pos - $offset;
			$varname = substr($session_data, $offset, $num);
			$offset += $num + 1;
			$data = unserialize(substr($session_data, $offset));
			$return_data[$varname] = $data;
			$offset += strlen(serialize($data));
		}
		return $return_data;
	}

	private static function unserialize_phpbinary($session_data) {
		$return_data = array();
		$offset = 0;
		while ($offset < strlen($session_data)) {
			$num = ord($session_data[$offset]);
			$offset += 1;
			$varname = substr($session_data, $offset, $num);
			$offset += $num;
			$data = unserialize(substr($session_data, $offset));
			$return_data[$varname] = $data;
			$offset += strlen(serialize($data));
		}
		return $return_data;
	}
	
	public static function checkIfInstalled(&$error){
		include_once JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_vmeeplus'.DIRECTORY_SEPARATOR.'script.php';
		
		try{
			$installScript = new com_vmeeplusInstallerScript();
			if(!$installScript->isCodeIstalled('shopfunctionsf')){
				$file_path = $installScript->getVmFilePath('shopfunctionsf');
				$error .= "Email Manager's code is not installed properly in VirtueMart file: ".$file_path;
				$error .= "<br>Please check the directory and file permissions and try to re-install the component."; 
				$error .= "<br>The component will not work properly until you fix this problem!"; 
				return false;
			}
		}
		catch (Exception $e){
			$error .="Error. Could not check if Email Manager code installed properly in VM file: shopfunctionsf.php.<br>Exceptoin:".$e->getMessage();
		}
		
		return true;
	}
	
	public static function allEmailsDisabled() {
		$disabled = (bool) self::getGlobalParam('is_disable_all_emails');
		return $disabled;
	}
	
	public static function getLanguagesArray(){
		$langArray = array();
		$lang = JFactory::getLanguage();
		$siteLangs = $lang->getKnownLanguages(JPATH_SITE);
		foreach ($siteLangs as $code => $langObj){
			$langArray[$code] = $code;
		}
	
		return $langArray;
	}
}
?>