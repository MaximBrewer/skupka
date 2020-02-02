<?php
/**
 * @package Unite Gallery
 * @author UniteCMS.net / Valiano
 * @copyright (C) 2012 Unite CMS, All Rights Reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */

class UniteGalleryManagerMain extends UniteGalleryManager{
	
	private $itemsType = "all";
	
	
	/**
	 * construct the manager
	 */
	public function __construct(){
		$this->type = self::TYPE_MAIN;
	
		$this->init();
	}
	
	
	/**
	 * get single item menu
	 */
	protected function getMenuSingleItem(){
		
		$arrMenuItem = array();
		$arrMenuItem["edit_item"] = __("Edit item",UNITEGALLERY_TEXTDOMAIN);
		$arrMenuItem["edit_item_title"] = __("Edit Title",UNITEGALLERY_TEXTDOMAIN);
		$arrMenuItem["preview_item"] = __("Preview Item",UNITEGALLERY_TEXTDOMAIN);
		$arrMenuItem["remove_item"] = __("Delete",UNITEGALLERY_TEXTDOMAIN);
		$arrMenuItem["duplicate_item"] = __("Duplicate",UNITEGALLERY_TEXTDOMAIN);
		
		return($arrMenuItem);
	}

	
	/**
	 * get item field menu
	 */
	protected function getMenuField(){
		$arrMenuField = array();
		
		$arrMenuField["select_all_items"] = __("Select All",UNITEGALLERY_TEXTDOMAIN);

		if($this->itemsType != "video")
			$arrMenuField["add_images"] = __("Add Images",UNITEGALLERY_TEXTDOMAIN);
		
		if($this->itemsType != "images")
			$arrMenuField["add_video"] = __("Add Video",UNITEGALLERY_TEXTDOMAIN);
		
		return($arrMenuField);
	}

	
	/**
	 * get multiple items menu
	 */
	protected function getMenuMulitipleItems(){
		$arrMenuItemMultiple = array();
		$arrMenuItemMultiple["remove_item"] = __("Delete",UNITEGALLERY_TEXTDOMAIN);
		$arrMenuItemMultiple["duplicate_item"] = __("Duplicate",UNITEGALLERY_TEXTDOMAIN);
		return($arrMenuItemMultiple);
	}
	
	
	/**
	 * get category menu
	 */
	protected function getMenuCategory(){
	
		$arrMenuCat = array();
		$arrMenuCat["edit_category"] = __("Edit Category",UNITEGALLERY_TEXTDOMAIN);
		$arrMenuCat["delete_category"] = __("Delete Category",UNITEGALLERY_TEXTDOMAIN);
		$arrMenuCat["export_cat_items"] = __("Export items",UNITEGALLERY_TEXTDOMAIN);
		$arrMenuCat["import_cat_items"] = __("Import items",UNITEGALLERY_TEXTDOMAIN);
	
		return($arrMenuCat);
	}
	
	
	/**
	 * get no items text
	 */
	protected function getNoItemsText(){
		if($this->hasCats == true)
			$text = __("Empty Category", UNITEGALLERY_TEXTDOMAIN);
		else
			$text = __("No Items Found", UNITEGALLERY_TEXTDOMAIN);
			
		return($text);
	}
	
	
	/**
	 * put items buttons
	 */
	protected function putItemsButtons(){
		?>
		
			<?php if($this->itemsType != "video"):?>
 			<a data-action="add_images" type="button" class="unite-button-secondary unite-button-blue button-disabled ug-button-item ug-button-add"><?php _e("Add Images",UNITEGALLERY_TEXTDOMAIN)?></a>
 			<?php endif?>
			
			<?php if($this->itemsType != "images"):?>
 			<a data-action="add_video" type="button" class="unite-button-secondary unite-button-blue button-disabled ug-button-item ug-button-add"><?php _e("Add Video",UNITEGALLERY_TEXTDOMAIN)?></a>
 			<?php endif?>
 			
 			<a data-action="select_all_items" type="button" class="unite-button-secondary button-disabled ug-button-item ug-button-select" data-textselect="<?php _e("Select All",UNITEGALLERY_TEXTDOMAIN)?>" data-textunselect="<?php _e("Unselect All",UNITEGALLERY_TEXTDOMAIN)?>"><?php _e("Select All",UNITEGALLERY_TEXTDOMAIN)?></a>
	 		
	 		<a data-action="edit_item_title" type="button" class="unite-button-secondary button-disabled ug-button-item ug-single-item"><?php _e("Edit Title",UNITEGALLERY_TEXTDOMAIN)?></a>
	 		<a data-action="edit_item" type="button" class="unite-button-secondary button-disabled ug-button-item ug-single-item"><?php _e("Edit item",UNITEGALLERY_TEXTDOMAIN)?> </a>
	 		
	 		<a data-action="duplicate_item" type="button" class="unite-button-secondary button-disabled ug-button-item"><?php _e("Duplicate",UNITEGALLERY_TEXTDOMAIN)?></a>
	 		<a data-action="remove_item" type="button" class="unite-button-secondary button-disabled ug-button-item"><?php _e("Delete",UNITEGALLERY_TEXTDOMAIN)?></a>
		<?php
	}
	
	

	/**
	 * put scripts
	 */
	private function putScripts(){
	
		$script = "
			jQuery(document).ready(function(){
				var selectedCatID = \"{$this->selectedCategory}\";
				var managerAdmin = new UCManagerAdmin();
				managerAdmin.initManager(selectedCatID);
			});
		";
	
		UniteProviderFunctionsUG::printCustomScript($script);
	}
	
	/**
	 * put dialog edit title 
	 */
	private function putDialogEditTitle(){
		?>
			<div id="dialog_edit_item_title"  title="<?php _e("Edit Title",UNITEGALLERY_TEXTDOMAIN)?>" style="display:none;">
			
				<div class="dialog_edit_title_inner unite-inputs mtop_20 mbottom_20" >
					<?php _e("Edit Title", UNITEGALLERY_TEXTDOMAIN)?>:
					<input type="text" id="input_item_title" class="unite-input-wide">
				</div>
				
			</div>
		
		<?php
	}
	
	/**
	 * put edit item dialog
	 */
	private function putDialogEditItem(){
	
		?>
		
		<div id="dialog_edit_item"  title="<?php _e("Edit Item",UNITEGALLERY_TEXTDOMAIN)?>" style="display:none;">
		
			<div id="dialog_edit_item_loader" class="loader_round">
				<?php _e("Loading Item Data...",UNITEGALLERY_TEXTDOMAIN)?>
			</div>
			
			<div id="dialog_edit_item_content" class="dialog_edit_item_content"></div>
			
			<div id="dialog_edit_error_message" class="unite_error_message" style="display:none"></div>
			
		</div>

		<?php 
	}
	
	
	/**
	 * put import items dialog
	 */
	private function putDialogImportItems(){
		?>
		
		<div id="dialog_import_items"  title="<?php _e("Import Items",UNITEGALLERY_TEXTDOMAIN)?>" style="display:none;">
			<br>
			<?php _e("Select export items file", UNITEGALLERY_TEXTDOMAIN)?>:
			
			<form id="form_import_items" name="form_import_items">
				<input type="file" name="import_file">
			</form>	
			
			<br><br>
						
			<a id="dialog_import_upload" href="javascript:void(0)" class="unite-button-primary"><?php _e("Import Items")?></a>
			
			<div id="dialog_import_items_loader" class="loader_text" style="display:none">
				<?php _e("Uploading Items file...",UNITEGALLERY_TEXTDOMAIN)?>
			</div>
			
			<div id="dialog_import_items_error" class="unite_error_message" style="display:none"></div>
			
			<div class="dialog_import_items_success" class="unite_success_message" style="display:none"><?php _e("Items Imported Successfully")?></div>
			
		</div>
		
		<?php 
	}
	
	
	/**
	 * put video dialog
	 */
	protected function putVideoDialog(){
		
		require GlobalsUG::$pathViews."system/video_dialog.php";
	}
	
	
	/**
	 * put additional html here
	 */
	protected function putAddHtml(){
		
		$this->putDialogEditTitle();
		$this->putDialogEditItem();
		$this->putDialogImportItems();
		
		$this->putScripts();
	}
	
	
	/**
	 * put init items
	 */
	protected function putInitItems(){
		/*
			$objitems = new UniteCreatoritems();
			$htmlitems = $objitems->getCatitemsHtml(null);
			
			echo $htmlitems;
		*/
	}
	
	
	/**
	 * init the items manager
	 */
	protected function init(){
		
		//set items type
		$isGalleryPage = GlobalsUGGallery::$isInited;
		if($isGalleryPage){
			$this->itemsType = GlobalsUGGallery::$objGalleryType->getItemsType();
			$this->selectedCategory = GlobalsUGGallery::$gallery->getParam("category");
		}
		
		$this->hasCats = true;
		$this->hasSortBy = true;
		
		parent::init();
		
		$this->itemsLoaderText = __("Getting items",UNITEGALLERY_TEXTDOMAIN);
		$this->textItemsSelected = __("items selected",UNITEGALLERY_TEXTDOMAIN);
		$this->filterActive = true;
	}
	
	
}