<?php

(defined('_VALID_MOS') OR defined('_JEXEC')) or die('Direct Access to this location is not allowed.');
if (JVERSION=='1.0') {
class TOOLBAR_ttfsp {
	function _EDIT() {
		global $id;
		mosMenuBar::startTable();
		mosMenuBar::save();
		mosMenuBar::spacer();
		mosMenuBar::apply();
		mosMenuBar::spacer();
		mosMenuBar::cancel();
		mosMenuBar::spacer();
		mosMenuBar::endTable();
	}
	function ADDTIMES_tt() {
		mosMenuBar::startTable();
		mosMenuBar::save('savetimes');
		mosMenuBar::endTable();
	}
	function CONFIG_tt() {
		mosMenuBar::startTable();
		mosMenuBar::save('saveconfig');
		mosMenuBar::spacer();
		mosMenuBar::cancel('cancelconfig');
		mosMenuBar::endTable();
	}
	function DEFAULT_tt() {
		mosMenuBar::startTable();
		mosMenuBar::editListX();
		mosMenuBar::spacer();
		mosMenuBar::endTable();
	}
	function MESS_MENU() {
		mosMenuBar::startTable();
		mosMenuBar::deleteList();
		mosMenuBar::spacer();
		mosMenuBar::endTable();
	}
	function ITEMS_tt() {
		mosMenuBar::startTable();
		mosMenuBar::publishList();
		mosMenuBar::spacer();
		mosMenuBar::unpublishList();
		mosMenuBar::spacer();
		mosMenuBar::deleteList();
		mosMenuBar::spacer();
		mosMenuBar::editListX();
		mosMenuBar::spacer();
		mosMenuBar::addNewX();
		mosMenuBar::spacer();
		mosMenuBar::endTable();
	}
}
} else {
	if ( substr(JVERSION,0,1)=='3'){ // J 3.X
class TOOLBAR_ttfsp {
public static function _EDIT() {
		global $id;
		JToolBarHelper::save('save');
		JToolBarHelper::apply('apply');
		JToolBarHelper::cancel('cancel');
	}
public static function ADDTIMES_tt() {
		JToolBarHelper::apply('savetimes');
	}
public static function ADDTIMES_tm() {
		JToolBarHelper::apply('savetm');
	}
public static function CONFIG_tt() {
		JToolBarHelper::save('saveconfig');
		JToolBarHelper::cancel('cancelconfig');
		$option = 'com_ttfsp';		
		JToolBarHelper::preferences($option, 550, 875);					
	}
public static function DEFAULT_tt() {
		JToolBarHelper::editList();
	}
public static function ORDERS_tt() {
		JToolBarHelper::editList();
		JToolBarHelper::deleteList('remove');
	}
public static function MESS_MENU() {
		JToolBarHelper::deleteList('remove');
	}
public static function ITEMS_tt() {
		JToolBarHelper::publishList('publish');
		JToolBarHelper::unpublishList('unpublish');
		JToolBarHelper::deleteList('remove');
		JToolBarHelper::editList('edit');
		JToolBarHelper::addNew('editA');
	}
}	
	} else {
class TOOLBAR_ttfsp {
	function _EDIT() {
		global $id;
		JToolBarHelper::save('save');
		JToolBarHelper::apply('apply');
		JToolBarHelper::cancel('cancel');
	}
	function ADDTIMES_tt() {
		JToolBarHelper::apply('savetimes');
	}
	function ADDTIMES_tm() {
		JToolBarHelper::apply('savetm');
	}
	function CONFIG_tt() {
		JToolBarHelper::save('saveconfig');
		JToolBarHelper::cancel('cancelconfig');
$option = 'com_ttfsp';		
		JToolBarHelper::preferences($option, 550, 875);			
	}
	function DEFAULT_tt() {
		JToolBarHelper::editListX();
	}
	function MESS_MENU() {
		JToolBarHelper::deleteList('remove');
	}
	public static function ORDERS_tt() {
		JToolBarHelper::editList();
		JToolBarHelper::deleteList('remove');
	}
	function ITEMS_tt() {
		JToolBarHelper::publishList('publish');
		JToolBarHelper::unpublishList('unpublish');
		JToolBarHelper::deleteList('remove');
		JToolBarHelper::editListX('edit');
		JToolBarHelper::addNewX('editA');
	}
}
}
}
?>
