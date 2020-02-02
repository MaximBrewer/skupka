<?php
/**
 * vmLanguage class
 *
 * initialises and holds the JLanguage objects for VirtueMart
 *
 * @package	VirtueMart
 * @subpackage Language
 * @author Max Milbers
 * @copyright Copyright (c) 2016 - 2017 VirtueMart Team. All rights reserved.
 */

class vmLanguage {

	public static $jSelLangTag = false;
	public static $currLangTag = false;
	public static $jLangCount = 1;
	public static $languages = array();

	/**
	 * Initialises the vm language class. Attention the vm debugger is not working in this function, because the right checks are done after the language
	 * initialisiation.
	 * @param bool $siteLang
	 */
	static public function initialise($siteLang = false){

		if(self::$jSelLangTag!==false){
			return ;
		}

		self::$jLangCount = 1;

		//Determine the shop default language (default joomla site language)
		if(VmConfig::$jDefLang===false){

			VmConfig::$jDefLangTag = VmConfig::get('vmDefLang','');
			if(empty(VmConfig::$jDefLangTag)) {
				if (class_exists('JComponentHelper') && (method_exists('JComponentHelper', 'getParams'))) {
					$params = JComponentHelper::getParams('com_languages');
					VmConfig::$jDefLangTag = $params->get('site', 'en-GB');
				} else {
					VmConfig::$jDefLangTag = 'en-GB';//use default joomla
					vmError('JComponentHelper not found');
				}
			}

			VmConfig::$jDefLang = strtolower(strtr(VmConfig::$jDefLangTag,'-','_'));
		}

		$l = JFactory::getLanguage();
		//Set the "joomla selected language tag" and the joomla language to vmText
		self::$jSelLangTag = $l->getTag();
		self::$languages[self::$jSelLangTag] = $l;
		vmText::$language = $l;

		$siteLang = self::$currLangTag = self::$jSelLangTag;
		if( JFactory::getApplication()->isAdmin()){
			$siteLang = vRequest::getString('vmlang',$siteLang );
			if (!$siteLang) {
				$siteLang = self::$jSelLangTag;
			}
		}

		self::setLanguageByTag($siteLang);

	}

	static public function setLanguageByTag($siteLang){

		if(empty($siteLang)){
			$siteLang = self::$currLangTag;
		}
		self::setLanguage($siteLang);

		// this code is uses logic derived from language filter plugin in j3 and should work on most 2.5 versions as well
		if (class_exists('JLanguageHelper') && (method_exists('JLanguageHelper', 'getLanguages'))) {
			$languages = JLanguageHelper::getLanguages('lang_code');
			self::$jLangCount = count($languages);
			if(isset($languages[$siteLang])){
				VmConfig::$vmlangSef = $languages[$siteLang]->sef;
			} else {

				if(isset($languages[self::$jSelLangTag])){
					VmConfig::$vmlangSef = $languages[self::$jSelLangTag]->sef;
				}
			}
		}

		$langs = (array)VmConfig::get('active_languages',array(VmConfig::$jDefLangTag));
		VmConfig::$langCount = count($langs);
		if(!in_array($siteLang, $langs)) {
			$siteLang = VmConfig::$jDefLangTag;	//Set to shop language
		}

		VmConfig::$vmlangTag = $siteLang;
		VmConfig::$vmlang = strtolower(strtr($siteLang,'-','_'));

		VmConfig::$defaultLangTag = VmConfig::$jDefLangTag;
		VmConfig::$defaultLang = strtolower(strtr(VmConfig::$jDefLangTag,'-','_'));

		if(count($langs)>1){
			$lfbs = VmConfig::get('vm_lfbs','');
			if(count($langs)==2 and VmConfig::$vmlangTag==VmConfig::$defaultLangTag and VmConfig::get('dualFallback',false) ){
				foreach($langs as $lang){
					if($lang!=VmConfig::$vmlangTag){
						VmConfig::$defaultLangTag = $lang;
						VmConfig::$defaultLang = strtolower(strtr(VmConfig::$defaultLangTag,'-','_'));
					}
				}
			} else if(!empty($lfbs)){
				vmdebug('my lfbs '.$lfbs);
				$pairs = explode(';',$lfbs);
				if($pairs and count($pairs)>0){
					$fbsAssoc = array();
					foreach($pairs as $pair){
						$kv = explode('~',$pair);
						if($kv and count($kv)===2){
							$fbsAssoc[$kv[0]] = $kv[1];
						}
					}
					if(isset($fbsAssoc[$siteLang])){
						VmConfig::$defaultLangTag = $fbsAssoc[$siteLang];
						VmConfig::$defaultLang = strtolower(strtr(VmConfig::$defaultLangTag,'-','_'));
						//VmConfig::$jDefLangTag = $fbsAssoc[$siteLang];
					}
					VmConfig::set('fbsAssoc',$fbsAssoc);
				}
			}
		}

		//JLangTag if also activevmlang set as FB, ShopLangTag($jDefLangTag), vmLangTag, vm_lfbs overwrites


		//@deprecated just fallback
		defined('VMLANG') or define('VMLANG', VmConfig::$vmlang );
		self::debugLangVars();
	}

	static public function debugLangVars(){
		//vmdebug('LangCount: '.VmConfig::$langCount.' $siteLang: '.$siteLang.' VmConfig::$vmlangSef: '.VmConfig::$vmlangSef.' self::$_jpConfig->lang '.VmConfig::$vmlang.' DefLang '.VmConfig::$defaultLang);
		if(VmConfig::$langCount==1){
			$l = VmConfig::$langCount.' Language, default shoplanguage (VmConfig::$jDefLang): '.VmConfig::$jDefLang.' '.VmConfig::$jDefLangTag;
		} else {
			$l = VmConfig::$langCount.' Languages, default shoplanguage (VmConfig::$jDefLang): '.VmConfig::$jDefLang.' '.VmConfig::$jDefLangTag;
			//if(VmConfig::$jDefLang!=VmConfig::$defaultLang){
			if(self::getUseLangFallback()){
				$l .= ' Fallback language (VmConfig::$defaultLang): '.VmConfig::$defaultLang;
			}
			$l .= ' Selected VM language (VmConfig::$vmlang): '.VmConfig::$vmlang.' '.VmConfig::$vmlangTag.' SEF: '.VmConfig::$vmlangSef;
		}
		vmdebug($l);
	}


	static private function setLanguage($tag){

		if(!isset(self::$languages[$tag])) {
			self::getLanguage($tag);
		}
		if(!empty(self::$languages[$tag])) {
			vmdebug('Set language '.$tag. ' '.self::$languages[$tag]->getTag());
			vmText::$language = self::$languages[$tag];
			//vmText::setLanguage(self::$languages[$tag]);
		}
		self::$currLangTag = $tag;
		vmdebug('vmText is now set to '.$tag);
	}

	static public function getLanguage($tag = 0){

		if(empty($tag)) {
			$tag = VmConfig::$vmlangTag;	//When the tag was changed, the jSelLangTag would be wrong
		}

		if(!isset(self::$languages[$tag])) {
			if($tag == self::$jSelLangTag) {
				self::$languages[$tag] = JFactory::getLanguage();
				vmdebug('loadJLang created $l->getTag '.$tag);
			} else {
				self::$languages[$tag] = JLanguage::getInstance($tag, false);
				vmdebug('loadJLang created JLanguage::getInstance '.$tag);
			}
		}
		return self::$languages[$tag];
	}

	/**
	 * loads a language file, the trick for us is that always the config option enableEnglish is tested
	 * and the path are already set and the correct order is used
	 * We use first the english language, then the default
	 *
	 * @author Max Milbers
	 * @static
	 * @param $name
	 * @return bool
	 */
	static public function loadJLang($name, $site = false, $tag = 0, $cache = true){

		static $loaded = array();
		//VmConfig::$echoDebug  = 1;
		if(empty($tag)) {
			$tag = self::$currLangTag;
		}
		self::getLanguage($tag);

		$h = (int)$site.$tag.$name;
		if($cache and isset($loaded[$h])){
			vmText::$language = self::$languages[$tag];
			return self::$languages[$tag];
		} else {
			if(!isset(self::$languages[$tag])){
				vmdebug('No language loaded '.$tag.' '.$name);
				VmConfig::$logDebug = true;
				vmTrace('No language loaded '.$tag.' '.$name,true);
				return false ;
			}
		}

		if($site){
			$path = $basePath = VMPATH_SITE;
		} else {
			$path = $basePath = VMPATH_ADMIN;
		}

		if($tag!='en-GB' and VmConfig::get('enableEnglish', true) ){
			$testpath = $basePath.'/language/en-GB/en-GB.'.$name.'.ini';
			if(!file_exists($testpath)){
				if($site){
					$epath = VMPATH_ROOT;
				} else {
					$epath = VMPATH_ADMINISTRATOR;
				}
			} else {
				$epath = $path;
			}
			self::$languages[$tag]->load($name, $epath, 'en-GB', true, false);
		}

		$testpath = $basePath.'/language/'.$tag.'/'.$tag.'.'.$name.'.ini';
		if(!file_exists($testpath)){
			if($site){
				$path = VMPATH_ROOT;
			} else {
				$path = VMPATH_ADMINISTRATOR;
			}
		}

		self::$languages[$tag]->load($name, $path, $tag, true, true);
		$loaded[$h] = true;
		vmdebug('loaded '.$h.' '.self::$languages[$tag]->getTag());
		vmText::$language = self::$languages[$tag];
		//vmText::setLanguage(self::$languages[$tag]);
		return self::$languages[$tag];
	}

	/**
	 * @static
	 * @author Max Milbers, Valerie Isaksen
	 * @param $name
	 */
	static public function loadModJLang($name){

		$tag = self::$currLangTag;;
		self::getLanguage($tag);

		$path = $basePath = JPATH_VM_MODULES.'/'.$name;
		if(VmConfig::get('enableEnglish', true) and $tag!='en-GB'){
			if(!file_exists($basePath.'/language/en-GB/en-GB.'.$name.'.ini')){
				$path = JPATH_ADMINISTRATOR;
			}
			self::$languages[$tag]->load($name, $path, 'en-GB');
			$path = $basePath = JPATH_VM_MODULES.'/'.$name;
		}

		if(!file_exists($basePath.'/language/'.$tag.'/'.$tag.'.'.$name.'.ini')){
			$path = JPATH_ADMINISTRATOR;
		}
		self::$languages[$tag]->load($name, $path,$tag,true);

		return self::$languages[$tag];
	}

	static public function getUseLangFallback(){

		static $c = null;
		if($c===null){
			$c = VmConfig::$langCount>1 and VmConfig::$defaultLang!=VmConfig::$vmlang and !VmConfig::get('prodOnlyWLang',false) ;
		}
		return $c;
	}

	static public function getUseLangFallbackSecondary(){

		static $c = null;
		if($c===null){
			if(self::getUseLangFallback() and VmConfig::$defaultLang!=VmConfig::$jDefLang and VmConfig::$jDefLang!=VmConfig::$vmlang){
				$c = true;
			} else {
				$c = false;
			}
		}
		return $c;
	}
}