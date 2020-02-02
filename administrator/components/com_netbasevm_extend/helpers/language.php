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

jimport('joomla.language.language');

/**
 * Little extend to access some protected properties and maintain compatibility.
 */
class InvoiceLanguage extends JLanguage {

	public function load($extension = 'joomla', $basePath = JPATH_BASE, $lang = null, $reload = false, $default = true)
	{
            return parent::load($extension, $basePath, $lang, $reload);	
	}
	
	/**
	 * Possiblity to load frontend overrides also from frontend
	 */
	public function loadOverrides($path){
		
		$filename = $path . "/language/overrides/".$this->lang.".override.ini";
		
		if (file_exists($filename) && $contents = $this->parse($filename)) {
			if (is_array($contents)) {
				$this->override = $contents;
			}
			unset($contents);
		}
	}
}

?>