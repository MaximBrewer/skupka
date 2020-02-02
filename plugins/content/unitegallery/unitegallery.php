<?php
/**
 * @package		Joomla.Plugin
 * @subpackage	Content.loadmodule
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

class plgContentUnitegallery extends JPlugin{

	
	/**
	 * get gallery output html
	 */
	private function getGalleryOutput($galleryID, $catID){

		if(empty($galleryID))
			UniteFunctionsUG::throwError("gallery alias not found");
				
		$content = HelperUG::outputGallery($galleryID, $catID, "alias");
		
		return($content);
	}
	
	
	/**
	 * Plugin that loads module positions within content
	 *
	 * @param	string	The context of the content being passed to the plugin.
	 * @param	object	The article object.  Note $article->text is also available
	 * @param	object	The article params
	 * @param	int		The 'page' number
	 */
	public function onContentPrepare($context, &$article, &$params, $page = 0){
				
		// Don't run this plugin when the content is being indexed
		if ($context == 'com_finder.indexer')
			return true;
		
	
		if (strpos($article->text, 'unitegallery') === false)
			return true;
		
		$regex = '/{unitegallery\s+(.*?)}/i';
		preg_match_all($regex, $article->text, $arrMatches, PREG_SET_ORDER);
		
		if(empty($arrMatches))
			return(true);
		
		//require gallery files.
		$pathIncludes = JPATH_ADMINISTRATOR."/components/com_unitegallery/includes.php";
		if(!file_exists($pathIncludes))
			return(true);
		
		require_once $pathIncludes;
				
		foreach($arrMatches as $match){
			if(!isset($match[1]))
				continue;

			$arguments = $match[1];
			
			$keywords = preg_split("/\s+/", $arguments);			
			$galleryID = $keywords[0];
			
			$catID = null;
			if(count($keywords) > 1){
				$strcat = $keywords[1];
				$strcat = str_replace("catid=", "", $strcat);
				$strcat = str_replace("catid =", "", $strcat);
				$strcat = str_replace("catid = ", "", $strcat);
				$catID = trim($strcat);
			}
									
			try{
			
				$output = $this->getGalleryOutput($galleryID, $catID);
			
			}catch(Exception $e){
				$message = "Unite Gallery Error: ".$e->getMessage();
				$operations = new UGOperations();
				
				$output = $operations->getErrorMessageHtml($message);
			}
			
			//replace only first occurance
			$article->text = $article->text = preg_replace("|$match[0]|", addcslashes($output, '\\$'), $article->text, 1);
			
		}
						
		
	}
}
