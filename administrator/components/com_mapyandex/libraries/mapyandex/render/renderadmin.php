<?php
/*
 * @package Joomla 1.5
 * @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 *
 * @component Phoca Gallery
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined( '_JEXEC' ) or die( 'Restricted access' );

class MapYandexRenderAdmin
{
	public function renderExternalLink($extLink) {
	
		$extLinkArray	= explode("|", $extLink, 4);
		if (!isset($extLinkArray[0])) {$extLinkArray[0] = '';}
		if (!isset($extLinkArray[1])) {$extLinkArray[1] = '';}
		if (!isset($extLinkArray[2])) {$extLinkArray[2] = '_self';}
		if (!isset($extLinkArray[3])) {$extLinkArray[3] = 1;}
	
		return $extLinkArray;
	}
	
	public function quickIconButton( $link, $image, $text ) {
		
		$lang	= JFactory::getLanguage();
		$button = '';
		if ($lang->isRTL()) {
			$button .= '<div class="thumbnails ph-icon">';
		} else {
			$button .= '<div class="thumbnails ph-icon">';
		}
		$button .=	''
				   .'<a class="thumbnail ph-icon-inside" href="'.$link.'">'
				  .JHTML::_('image', 'administrator/components/com_mapyandex/assets/images/'.$image, $text )
				  .'<br />'
				   .'<span>'.$text.'</span></a>'
				   .'';
		$button .= '</div>';

		return $button;
	}
	
	
	public function renderThumbnailCreationStatus($status = 1, $onlyImage = 0) {
		switch ($status) {
			case 0:
				$statusData = array('disabled', 'false');
			break;
			case 1:
			Default:
				$statusData = array('enabled', 'true');
			break;
		}
		
		if ($onlyImage == 1) {
			return JHTML::_('image',  'components/com__mapyandex/assets/images/icon-16-'.$statusData[1].'.png',  JText::_('COM_PHOCAGALLERY_' . $statusData[0] ) );
		} else {
			return '<span class="hasTip" title="'.JText::_('COM_PHOCAGALLERY_THUMBNAIL_CREATION_STATUS_IS') 
			. ' ' . JText::_('COM_PHOCAGALLERY_' . $statusData[0] ). '::'
			. JText::_('COM_PHOCAGALLERY_THUMBNAIL_CREATION_STATUS_INFO').'">'
			. JText::_('COM_PHOCAGALLERY_THUMBNAIL_CREATION_STATUS') . ': '
			. JHTML::_('image', 'administrator/components/com__mapyandex/assets/images/icon-16-'.$statusData[1].'.png', JText::_('COM_PHOCAGALLERY_' . $statusData[0] ) ) . '</span>';
		}
	}
	
	public function CategoryTreeOption($data, $tree, $id=0, $text='', $currentId) {		

		foreach ($data as $key) {	
	
			$show_text =  $text . $key->name_map_yandex;
			
			if ($key->parentid == $id && $currentId != $id && $currentId != $key->value) {
				$tree[$key->value] 			= new JObject();
				$tree[$key->value]->text 	= $show_text;
				$tree[$key->value]->value 	= $key->value;
				$tree = MapYandexRenderAdmin::CategoryTreeOption($data, $tree, $key->value, $show_text . " - ", $currentId );	
			}	
		}
		return($tree);
	}
	
	public function approved( &$row, $i, $imgY = 'tick.png', $imgX = 'publish_x.png', $prefix='' )
	{
		$img 	= $row->approved ? $imgY : $imgX;
		$task 	= $row->approved ? 'disapprove' : 'approve';
		$alt 	= $row->approved ? JText::_( 'COM_PHOCAGALLERY_APPROVED' ) : JText::_( 'COM_PHOCAGALLERY_NOT_APPROVED' );
		$action = $row->approved ? JText::_( 'COM_PHOCAGALLERY_NOT_APPROVE_ITEM' ) : JText::_( 'COM_PHOCAGALLERY_APPROVE_ITEM' );

		$href = '
		<a href="javascript:void(0);" onclick="return listItemTask(\'cb'. $i .'\',\''. $prefix.$task .'\')" title="'. $action .'">
		<img src="images/'. $img .'" border="0" alt="'. $alt .'" /></a>'
		;

		return $href;
	}
	public function renderIeCssLink($cond = 0) {
		
		$condOutput = '[if IE]';
		
		switch ($cond) {
			case 1:
			$condOutput = '[if lt IE 8]';
			break;
			
			case 0:
			default:
			$condOutput = '[if IE]';
		}
		
		return " <!--".$condOutput.">\n"
			 ." <link rel=\"stylesheet\" href=\"".JURI::base(true)."/components/com_mapyandex/assets/mapyandex.css\" type=\"text/css\" />\n"
			 ." <![endif]-->\n";
	}
}
?>