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
class JFormFieldUgcategory extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'Category';

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
			$selectedID = JRequest::getCmd("categoryid");
		
		$objCategories = new UniteGalleryCategories();
		$arrCategories = $objCategories->getCatsShort("component");
				
		$html = "<select id='{$this->id}_id' name='{$this->name}'>";
		foreach($arrCategories as $id=>$title){
			
			$selected = "";				
			if($id == $selectedID)
				$selected = 'selected="selected"';
			
			$html .= "<option value='$id' $selected>$title</option>";
		}		
		$html .= "</select>";
		
		return $html;
	}
	
	
}

?>