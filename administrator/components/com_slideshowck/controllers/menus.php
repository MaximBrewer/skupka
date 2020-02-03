<?php
/**
 * @name		Slideshow CK
 * @package		com_slideshowck
 * @copyright	Copyright (C) 2019. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author		Cedric Keiflin - https://www.template-creator.com - https://www.joomlack.fr
 */
 
// No direct access
defined('CK_LOADED') or die;

use \Slideshowck\CKController;
use \Slideshowck\CKFof;

class SlideshowckControllerMenus extends CKController {

	function ajaxShowMenuItems() {
		// security check
		if (! CKFof::checkAjaxToken()) {
			exit();
		}

		$parentId = $this->input->get('parentid', 0, 'int');
		$menutype = $this->input->get('menutype', '', 'string');

		$model = $this->getModel('Menus', 'Slideshowck', array());
		$items = $model->getChildrenItems($menutype, $parentId);

		$links = array();
		$imagespath = SLIDESHOWCK_MEDIA_URI .'/images/';
		?>
		<div class="cksubfolder">
		<?php
		foreach ($items as $item) {
//			CKFof::dump($item);
			$aliasId = $item->id;
			if ($item->type == 'alias') {
				$itemParams = new JRegistry($item->params);
				$aliasId = $itemParams->get('aliasoptions', 0);
			}
			$Itemid = substr($item->link,-7,7) == 'Itemid=' ? $aliasId : '&Itemid=' . $aliasId;
		?>
			<div class="ckfoldertree parent">
				<div class="ckfoldertreetoggler <?php if ($item->rgt - $item->lft <= 1) { echo 'empty'; } ?>" onclick="ckToggleTreeSub(this, <?php echo $item->id ?>)" data-menutype="<?php echo $item->menutype; ?>"></div>
				<div class="ckfoldertreename hasTip" title="<?php echo $item->link . $Itemid ?>" onclick="ckSetMenuItemUrl('<?php echo $item->link . $Itemid ?>')"><img src="<?php echo $imagespath ?>folder.png" /><?php echo $item->title; ?></div>
			</div>
		<?php
		}
		?>
		</div>
		<?php
		exit;
	}
}
