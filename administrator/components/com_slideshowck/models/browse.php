<?php
/**
 * @name		Slideshow CK
 * @package		com_slideshowck
 * @copyright	Copyright (C) 2019. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author		Cedric Keiflin - https://www.template-creator.com - https://www.joomlack.fr
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

class SlideshowckModelBrowse extends JModelList {

	/*
	 * Get a list of folders and files 
	 */
	public function getItemsList($type = 'image') {
		$input = JFactory::getApplication()->input;

		$type = $input->get('type', $type, 'string');

		switch ($type) {
			case 'video' :
				$filetypes = array('.mp4', '.ogv', '.webm');
				break;
			case 'audio' :
				$filetypes = array('.mp3', '.ogg');
				break;
			case 'image' :
			default :
				$filetypes = array('.jpg', '.jpeg', '.png', '.gif', '.tiff');
				break;
		}

		$folder = $input->get('folder', 'images', 'string');
		$tree = new stdClass();

		// look for all folder and files
		$this->getSubfolder(JPATH_SITE . '/' . $folder, $tree, implode('|', $filetypes), 1);

		$tree = $this->prepareList($tree);

		return $tree;
	}

	/* 
	 * List the subfolders and files according to the filter
	 */
	private function getSubfolder($folder, &$tree, $filter, $level) {
		$folders = JFolder::folders($folder, '.', $recurse = false, $fullpath = true);

		if (! count($folders)) return;

		foreach ($folders as $f) {
			// list all authorized files from the folder
			$files = JFolder::files($f, $filter, $recurse = false, $fullpath = false);
			$fName = JFile::makeSafe($f);
			$tree->$fName = new stdClass();
			$name = explode('/', $f);
			$name = end($name);
			$tree->$fName->name = $name;
			$tree->$fName->path = $f;
			$tree->$fName->files = $files;
			$tree->$fName->level = $level;

			// recursive loop
			$this->getSubfolder($f, $tree, $filter, $level+1);
		}
		return;
	}

	/* 
	 * Set level diff and check for depth
	 */
	private function prepareList($items) {
		if (! $items) return $items;

		$lastitem = 0;
		foreach ($items as $i => $item)
		{
			$item->deeper     = false;
			$item->shallower  = false;
			$item->level_diff = 0;

			if (isset($items->$lastitem))
			{
				$items->$lastitem->deeper     = ($item->level > $items->$lastitem->level);
				$items->$lastitem->shallower  = ($item->level < $items->$lastitem->level);
				$items->$lastitem->level_diff = ($items->$lastitem->level - $item->level);
			}
			$lastitem = $i;

			$item->basepath = str_replace(JPATH_SITE, '', $item->path);
			$item->basepath = str_replace('\\', '/', $item->basepath);
			$item->basepath = trim($item->basepath, '/');
		}

		return $items;
	}
}
