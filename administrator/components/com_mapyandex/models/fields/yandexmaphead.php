<?php
/*
 * @package		Joomla.Framework
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 *
 * @component Phoca Component
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License version 2 or later;
 */
defined('JPATH_BASE') or die;
jimport('joomla.html.html');
jimport('joomla.form.formfield');

class JFormFieldYandexMapHead extends JFormField
{
	protected $type = 'YandexMapHead';
	
	protected function getInput() {
		return '';
	}
	
	protected function getLabel() {
	
		// Temporary solution
		JHTML::stylesheet( 'administrator/components/com_phocagallery/assets/phocagalleryoptions.css' );
		
		echo '<div class="clr"></div>';
		
		$phocaImage	= ( (string)$this->element['phocaimage'] ? $this->element['phocaimage'] : '' );
		
		$image 		= '';
		$style		= 'background: #CCE6FF; color: #0069CC;padding:5px;margin:5px 0;';
		
	
		

		echo '<div class="clr"></div>';
	}
	
	/*
	protected function getLabel() {
		$phocaImage	= ( (string)$this->element['phocaimage'] ? $this->element['phocaimage'] : '' );
		
		$image 		= '';
		$style		= 'background: #CCE6FF; color: #0069CC; padding: 5px; vertical-align: middle';
		
		if ($phocaImage != ''){
			$image 	= JHTML::_('image', 'administrator/components/com_phocagallery/assets/images/'. $phocaImage, '' );
			$style	= 'background: #CCE6FF; color: #0069CC; height:65px; padding: 5px; vertical-align: middle';
		}
		
		if ($this->element['label']) {
		
			return $image;
			/*return ''
			.'<li><div style="'.$style.'"><label>'. $image.'</label>'
			.'<strong>'. JText::_($this->element['label']) . '</strong>'
			.'</div></li>';*/
			
			
			
		//echo '<div class="clr"></div>';
		/*return ''
			.'<li><div style="'.$style.'"><label>'. $image.'</label>'
			.'<strong>'. JText::_($this->element['label']) . '</strong>'
			.'</div></li>';
			
		return $image;
		echo '<div class="clr"></div>';
	
			
		}
	}*/
}
?>