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
defined('_JEXEC') or die();
if (! class_exists('MapYandexLoader')) {
    require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_mapyandex'.DS.'libraries'.DS.'loader.php');
}
mapyandeximport('mapyandex.render.renderadmin');

class JFormFieldMapYandex extends JFormField
{
	protected $type 		= 'MapYandex';

	protected function getInput() {
		
		$db = &JFactory::getDBO();

       //build the list of categories
		$query = 'SELECT * FROM '.$db->nameQuote('#__map_yandex');
		$db->setQuery( $query );
		$phocagallerys = $db->loadObjectList();

		// TODO - check for other views than category edit
		$view 	= JRequest::getVar( 'view' );
		$catId	= -1;
		if ($view == 'phocagalleryc') {
			$id 	= $this->form->getValue('id'); // id of current category
			if ((int)$id > 0) {
				$catId = $id;
			}
		}
		
		// Initialize JavaScript field attributes.
		$attr = '';
		$attr .= $this->element['onchange'] ? ' onchange="'.(string) $this->element['onchange'].'"' : '';
		$attr .= ' class="inputbox"';
		
		$document					= JFactory::getDocument();
		$document->addCustomTag('<script type="text/javascript">
function changeCatid() {
	var catid = document.getElementById(\'jform_catid\').value;
	var href = document.getElementById(\'pgselectytb\').href;
    href = href.substring(0, href.lastIndexOf("&"));
    href += \'&catid=\' + catid;
    document.getElementById(\'pgselectytb\').href = href;
}
</script>');
		
	return JHTML::_('select.genericlist',  $phocagallerys, ''.$control_name.'['.$name.']', 'class="inputbox"', 'id', 'name_map_yandex', $value, $control_name.$name );
	}
}
?>