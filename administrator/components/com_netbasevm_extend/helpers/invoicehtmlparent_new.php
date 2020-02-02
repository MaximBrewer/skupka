<?php/*------------------------------------* -Netbase- Advanced Virtuemart Invoices for Virtuemart* Author    CMSMart Team* Copyright (C) 2012 http://cmsmart.net. All Rights Reserved.* @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL* Websites: http://cmsmart.net* Email: team@cmsmart.net* Technical Support:  Forum - http://bloorum.com/forums-----------------------------------------------------*/
// check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restrict Access');

define('TAG_REGEXP','\{[^{}]+\}');

/**
 * Parent class for Invoice HTML (not content dependent methods)
 */
class InvoiceHTMLParentNew
{
    var $language;	//tag
    var $db;
    var $images;
    var $params;
    var $currency;
    var $colsCount;
    var $fields;
    var $taxSum;
    var $subtotal = 0;
    var $deliveryNote;
    var $order;
    var $payment;
    var $vendor;

    /**
     * Translate by frontend.
     * 
     * @param string $string
     */
    function _($string, $extension = null)
    {
    	if ($extension=='com_virtuemart' AND COM_VMINVOICE_ISVM1) //vm 1 has its own translates
    		return $string;
    	
    	return NbordersHelper::frontendTranslate($string, $this->language, false, $extension);
    }
    
	function sprintf($string)
    {
    	//if ($extension=='com_virtuemart' AND COM_VMINVOICE_ISVM1) //vm 1 has its own translates
    	//	return $string;
    	
    	$args = func_get_args();
    	return NbordersHelper::frontendTranslate($string, $this->language, $args);
    }
    
    function InvoiceHTMLnew ($orderID, $language)
    {
    	$this->dispatcher = JDispatcher::getInstance();
    	$this->language = $language;
    	
        $this->db = JFactory::getDBO();
        	        
        $this->db->setQuery('SELECT * FROM `#__nborders_mailsended` WHERE `order_id` = '.(int)$orderID);
		$this->mailsended = $this->db->loadObject();
		
        
    }
    
    function toWords($price, $toPrice = true)
    {
    	static $nw;
    	static $nwLocale = 'en_US';
    	
    	if (!$nw){
    		include_once JPATH_ADMINISTRATOR.DS.'components'.DS.'com_netbasevm_extend'.DS.'libraries'.DS.'numbers_words'.DS.'words.php';
    		$nw = new Numbers_Words();
    		$locales = $nw->getLocales();
    		$locales = array_combine($locales,$locales);
    		$lang = JFactory::getLanguage();
    		if (isset($locales[str_replace('-','_',$lang->get('tag'))])) //try get correct locale
    			$nwLocale = str_replace('-','_',$lang->get('tag'));
    		elseif (($tag = explode('-',$lang->get('tag'))) && isset($locales[$tag[0]]))
    			$nwLocale = $tag[0];
    	}
    	
    	if ($nwLocale=='en_GB' && $toPrice)
    		$nwLocale = 'en_US';
    	
    	$return = $toPrice ? $nw->toCurrency($price, $nwLocale) : $nw->toWords($price, $nwLocale);
    	
    	if ($this->params->get('to_words_upper'))
    		$return = ucwords(str_replace('-',' ',$return));
    	
    	return $return;
    }
    
    /**
     * Replace [] language elements 
     */
    function replaceText($text)
    {
    	$text = trim($text[1],' []');
    	
    	//if is prepended component name like [com_content: Article]
    	if (preg_match('/^\s*((?:com|plg|mod|tpl)_\w+)\s*:/iU',$text,$matches)){
    		$text = preg_replace('/^\s*(?:com|plg|mod|tpl)_\w+\s*:\s*/iU','',$text);
    		return $this->_($text, $matches[1]);
    	}
    	
    	return $this->_($text);
    }
    
    /**
     * make {string_cpt}: {string} to special temporery table row
     */
    function makeTrs($tags)
    {
    	if (!isset($tags[4]))
    		$tags[4] = '';
    		
		return $tags[1].'<trstart><td align="right" width="60%">{'.$tags[2].'}: </td><td align="right" width="40%">{'.$tags[3].'}</td></trend>'.$tags[4];
    }
    
    /**
     * Make one table of chained <trstart></trend>
     */
    function makeTable($trs)
    {
    	$trs = $trs[0];

    	if (preg_match_all('#<trstart>#i',$trs,$matches)>1){ //more than 1 row => make table
    		$trs = preg_replace('/<\/trend>.*<trstart>/Us',"</trend>\n <trstart>",$trs); //delete tags between rows (brs)
	    	$trs = str_replace('<trstart>','<tr>',$trs);
	    	$trs = str_replace('</trend>','</tr>',$trs);
			return "\n".'<table width="100%">'.$trs.'</table>'."\n";
    	}
    	else { //else no table 
    		$trs = preg_replace('#<\s*\/?\s*(trstart|trend|td).*>#Ui','',$trs);
	    	return $trs;
    	}
    }

    function getHTML($type, $currPage = null, $currPageInGroupAlias='', $totalPagesInGroupAlias='', $lastPage=true, $onlyOnePage=false)
    {
    	if ($this->params->get('debug',0))
    		error_reporting(E_ALL);
    	 
    	$cacheId = $type.'.'.$currPage.'.'.$currPageInGroupAlias.'.'.$totalPagesInGroupAlias.'.'.$lastPage.'.'.$onlyOnePage;
    	
    	//for cache (not building same html twice)
    	if (!isset($this->templatesHTML[$cacheId]))
    	{
	    	$this->template_type = $type;
	    	
	    	$this->currPageInGroup = $currPageInGroupAlias; //only alias, not value! value is known only at the end of generation
	    	$this->totalGroups = $totalPagesInGroupAlias; //only alias, not value! value is known only at the end of generation
	    	
	    	$this->lastPage = $lastPage; //only for footer calling
	    	$this->onlyOnePage = $onlyOnePage; //only for footer calling
	    	
	        //get template
	        $dn = $this->deliveryNote ? 'dn_' : '';
	        $db = JFactory::getDBO();
	        
	    	$db->setQuery('SELECT `template_'.strtolower($dn.$type).'` FROM `#__vminvoice_config`');
	    	$template = $db->loadResult();

	    	if ($template===false){
	    		JError::raiseError(500,'Template '.$type.' not defined in config database');
	    		return false;}

	    	//TODO: problems s kodovanim kdyz tam je A se striskou 0xc2 0xa3
	    	$br = '\s*<\s*br\s*\/?\s*>\s*';
	    	$colon = '\s*:\s*';
	    	
	    	//conditional tags
	    	$template = preg_replace('#\{lastpage\}(.*)\{\/lastpage\}#Uis', $lastPage ? '$1' : '', $template); //content ONLY on last page
	    	$template = preg_replace('#\{notlastpage\}(.*)\{\/notlastpage\}#Uis', !$lastPage ? '$1' : '', $template); //content on all pages EXCEPT last page

	    	$template = preg_replace('#\{onepage\}(.*)\{\/onepage\}#Uis', $onlyOnePage ? '$1' : '', $template); //content ONLY if pdf has one page
	    	$template = preg_replace('#\{notonepage\}(.*)\{\/notonepage\}#Uis', !$onlyOnePage ? '$1' : '', $template); //content only IF pdf has more pages
	    	
	    	$template = preg_replace('#\{firstpage\}(.*)\{\/firstpage\}#Uis', $currPage==1 ? '$1' : '', $template); //content ONLY on first page
	    	$template = preg_replace('#\{notfirstpage\}(.*)\{\/notfirstpage\}#Uis', $currPage!=1 ? '$1' : '', $template); //content only NOT on first page

 			//replace {field_cpt}: {field} by <trstart><td>{field_cpt}: </td><td>{field}</td></trend>
	    	$template = preg_replace_callback("#($br)?\{\s*([^{}]+)\s*\}\s*:\s*\{\s*([^{}]+)\s*\}($br)?#is",array( &$this, 'makeTrs'),$template); 
	    	//replace tags by content. BEWARE: redefine also this call at replacetags -> items
	    	$template = preg_replace_callback('#('.$br.')?('.TAG_REGEXP.')('.$colon.')?#is',array( &$this, 'replaceTags'),$template); //replace tags
	    	//remove empty <trstart>...</trend>
			$template = preg_replace('/(?:'.$br.')?<trstart><td[^>]*>\s*:?\s*<\/td>\s*<td[^>]*>\s*<\/td><\/trend>(?:'.$br.')?/is','',$template); 
			//replace bunch of <trstart></trend> ... by regular tables.
			$template = preg_replace_callback('/(?:\s*(?:'.$br.')*\s*<trstart>.*?<\/trend>\s*(?:'.$br.')*\s*)+/is',array( &$this, 'makeTable'),$template); 
			//$template = preg_replace('/<\s*(b|i|u)[^>]*>(&nbsp;|\s)*<\/\1>/siU','',$template); //remove empty tags
			$template = preg_replace('/^'.$br.'+/siU','',$template); //remove <br>s on start and end
			$template = preg_replace('/'.$br.'+$/siU','',$template);
			
			$template = $this->fillImagesPaths($template); // check and fill image absolute paths
			
			$template = preg_replace_callback("/(\[.+\])/sU",array( &$this, 'replaceText'),$template); //parse [strings] by JText
			$template = str_replace(' style=""','',$template); //remove empty stykes
	        //$template = preg_replace_callback("/(?<=[>:])([\s\w]+)(?=[<:])/",array( &$this, 'replaceText'),$template); //parse other strings (for eample between tags or before :) by JText
	    	
			//remove empty tags (mainly because of empty div that is inside row) 
			//https://sourceforge.net/projects/tcpdf/forums/forum/435311/topic/4509667
			$removeEmptyTags = trim($this->params->get('remove_empty_tags',''));
			$removeEmptyTags = str_replace(' ','',str_replace(',','|',$removeEmptyTags));
			if ($removeEmptyTags && $removeEmptyTags!='-')
				$template = preg_replace('/<\s*('.$removeEmptyTags.')( [^>]+)?>\s*<\s*\/\s*('.$removeEmptyTags.')\s*>/isU','',$template);
			
			$template = str_replace("\xc2\xa0",' ',$template); //remove "A with ^" //http://stackoverflow.com/a/4515394
			
			$this->templatesHTML[$cacheId] = $template;

    	}

    	return $this->templatesHTML[$cacheId];
    }

    /*
     * Calculate Finnish reference number (viitenumero)
     */
    function countReferenceFI($tapahtuma)
    {
        $reverse = strrev($tapahtuma);
        $pituus  = strlen($tapahtuma);
        $n = 0;
        $kerroin = 7;
        $summa = 0;
        while($n  < $pituus) {
            $tulos = $kerroin * $reverse[$n];
            if ($kerroin == 1) {
                $kerroin = 7;
            }
            elseif ($kerroin == 3) {
                $kerroin = 1;
            }
            elseif ($kerroin == 7) {
                $kerroin = 3;
            }
            $summa = $summa + $tulos;
            $n = $n + 1;
        }
        $pituus2 = strlen($summa);
        $summa2 = (string) $summa;
        $summa2[$pituus2 - 1] = 0;
        $summa2 = $summa2 + 10;
        $viite = $summa2 - $summa;
        if ($viite == 10) $viite = 0;
        $viitenumero = (string) $tapahtuma;
        $viitenumero = $viitenumero.$viite;
        return($viitenumero);
    }
    
    /**
     * Replace all image src's in code by parsed ones based on config.
     */
    function fillImagesPaths($code)
    {
    	$paths = $this->params->get('images_paths','abs');

    	//TODO: compute also width and height. because images are too small if not specified
        $pattern = '#<img[^>]*src="([^"]*)"[^>]*>#iU';
        $match = null;
        if (preg_match_all($pattern, $code, $match))
            if (isset($match[0]))
                $images = $match[1];

        if (isset($images))
	        foreach ($images as $image)
	        	if ($replacement = $this->parseImagePath($image,$paths))
	        		$code = ltrim(str_replace('"'.$image.'"','"'.$replacement.'"',$code),' /');

		return $code;
    }
    
    /**
     * Parse image path and convert it to absolute/relative/relative with path.
     * Images used in HTML should be only relative, converted are here, f.e. /media/...
     * 
     * @param string $image
     * @param string $paths config option
     */
    function parseImagePath($image,$paths=null)
    {
    	if (!$paths)
    		$paths = $this->params->get('images_paths','rel_path');

    	$image = ltrim(trim($image),'/'.DS);
    		
   		$replacement = false;
	    if ($paths=='abs'){ //convert to absolute (e.g. http://example.com/joomla15/...
	    	
		      if (substr($image, 0, 4) !='http') { //image contains relative path
		           //change to absolute (and strip relative path to our server if is part of url)
		           $image = ltrim(str_replace(JPATH_SITE,'',$image),'/ ');
		           $image = ltrim(str_replace(JURI::root(false),'',$image),'/ '); //delete some prefixes
		           $replacement = JURI::root(false).$image;
		      }
	    }
	    elseif ($paths=='rel_path'){ //convert to relative + path (e.g. joomla15/...)
	    	
	        if (substr($image, 0, strlen(JURI::root(false))) == JURI::root(false) AND strlen(JURI::root(false))> 0)  //if image contains abolute path to this server..
		         $replacement = JURI::root(true) .'/'. ltrim(str_replace(JURI::root(false),'',$image),'/ '); //replace by relative
		    elseif (substr($image, 0, strlen(JURI::root(true))) != JURI::root(true) OR strlen(JURI::root(true)) == 0) //image contains relative path (and it doesen't have path prefix)
	             $replacement =  JURI::root(true) . '/' . ltrim($image,' /'); //add our path
	    }
    	elseif ($paths=='rel_full_path'){ //convert to full path (var/www/html/joomla15/...)
    		
    		if (substr($image, 0, strlen(JURI::root(false))) == JURI::root(false) AND strlen(JURI::root(false))> 0) //if image contains abolute path to this server..
    		 	$image = ltrim(str_replace(JURI::root(false),'',$image),'/ '); //delete it
    		
    		if (substr($image, 0, strlen(JPATH_SITE)) != JPATH_SITE) //if not already server path prepended
    			$replacement = rtrim(JPATH_SITE,'/'.DS).'/'.ltrim($image,'/'); //prepend it
	    }
	    else //let it be relative to Joomla root (paths are relative to Joomla root by base)
	    {
			//TODO:
			$replacement = $image;
	    }

	    return $replacement;
    }
    
    
    //return formatted date, based on format decide to use gmdate or gmstrftime function
    function formatGMDate($format, $time)
    {
    	//static $localeEncoding;
    	//static $localeSet;
    	
    	if (strpos($format, '%')===false)
    		return gmdate($format, $time);
    	
    	//http://www.onphp5.com/article/22, oh, finally some light into this
    	
		//if (!isset($localeSet)){
		//set it always, because setlocale is not thread-safe, so it can be switched in runnign script (?)
			$lang = JFactory::getLanguage();

			$localeSet = false;
			foreach ($lang->getLocale() as $locale) //prefer utf-8
				if (preg_match('#\.utf-?8$#i',$locale)) 
					if ($localeSet = setlocale(LC_TIME,$locale))
						break;
			
			if (!$localeSet) //on windows is probably not possible to set locale in utf-8
				$localeSet = setlocale(LC_TIME, $lang->getLocale()) or setlocale(LC_ALL, $lang->getLocale()); //? 

			$localeEncoding = false;
    		if (preg_match('#\.(\d+)$#',$localeSet, $matches)) //1250 ... codepage, on windows
    			$localeEncoding = 'WINDOWS-'.$matches[1];
    		elseif (preg_match('#\.(\w+)$#',$localeSet, $matches)) //assume there is encoding appended
    			$localeEncoding = preg_replace('#^(ISO)(\d.*)$#i','$1-$2',$matches[1]); //ISO8859-2 => ISO-8859-2
    		elseif (function_exists('nl_langinfo')) //hahaha
    			$localeEncoding = nl_langinfo("CODESET");
    		elseif (function_exists('mb_detect_encoding')) //? not works? outputs utf8 always?
    			$localeEncoding = mb_detect_encoding(gmstrftime($format, $time));
    	//}
    	
    	$res = gmstrftime($format, $time);
    	
    	$isUTF8 = !empty($localeEncoding) && (preg_match('#^utf-?8$#i',$localeEncoding) || strpos((string)$localeEncoding, '65001')!==false);
    	
    	if (!empty($localeEncoding) && !$isUTF8){ //if we know source encoding, convert to utf-8
    		if (function_exists('iconv'))
    				return iconv($localeEncoding, "UTF-8//IGNORE//TRANSLIT", $res);
    		if (function_exists('mb_convert_encoding'))
    			return mb_convert_encoding($res, "UTF-8//IGNORE//TRANSLIT", $localeEncoding);
    	}
    	
    	php_uname(); 
    	if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')  //failsafe. on windows, it is probably not in utf-8, use utf8_encode and keep fingers crossed
    		if (empty($localeEncoding) || !$isUTF8)   //? http://paltar.jetify.de/2011/04/strftime-and-utf8-under-windows/
    			return utf8_encode($res);

    	return $res;
    }
}
?>
