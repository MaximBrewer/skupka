<?php
/**
 * @package Unite Gallery
 * @author UniteCMS.net / Valiano
 * @copyright (C) 2012 Unite CMS, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('_JEXEC') or die('Restricted access');


/**
 * Supports a modal article picker.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_content
 * @since		1.6
 */
class JFormFieldGallery extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'Gallery';

	/**
	 * 
	 * include all the files needed
	 */
	protected function requireFramework(){
		
		$pathComponent = JPATH_ADMINISTRATOR."/components/com_unitegallery/";
		require_once $pathComponent."includes.php";
	}
	
	
	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * @since	1.6
	 */
	protected function getInput()
	{
		$this->requireFramework();
				
		$selectedID = $this->value;
		
		if(empty($selectedID))
			$selectedID = JRequest::getCmd("galleryid");
		
		$objGalleries = new UniteGalleryGalleries();
		$arrGalleries = $objGalleries->getArrGalleriesShort(true);
				
		$html = "<select id='{$this->id}_id' name='{$this->name}'>";

		$firstChosen = false;
		foreach($arrGalleries as $id=>$title){
			
			//if empty selected if - check the first not empty id
			$selected = "";
			
			if(empty($selectedID)){
				if($id != "empty" && $firstChosen == false){
					$selected = 'selected="selected"';
					$firstChosen = true;
				}
			}else{
				if($id == $selectedID)
					$selected = 'selected="selected"';
			}
			
			$html .= "<option value='$id' $selected>$title</option>";
		}		
		$html .= "</select>";
		
		return $html;
	}
	
	
}

?>