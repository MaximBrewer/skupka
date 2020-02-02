<?php
/**
 * @package Unite Gallery
 * @author UniteCMS.net / Valiano
 * @copyright (C) 2012 Unite CMS, All Rights Reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */

class UniteGalleryManagerInline extends UniteGalleryManager{

	private $startAddon;
	
	
	/**
	 * construct the manager
	 */
	public function __construct(){
		
		$this->type = self::TYPE_ITEMS_INLINE;
		
		$this->init();
	}
	
	/**
	 * validate that the start addon exists
	 */
	private function validateStartAddon(){
		
		if(empty($this->startAddon))
			UniteFunctionsUG::throwError("The start addon not given");
		
	}
	
	
	/**
	 * init the data from start addon
	 */
	private function initStartAddonData(){
		
		//set init data
		$arrItems = $this->startAddon->getArrItems();
		
		$strItems = "";
		if(!empty($arrItems)){
			$strItems = json_encode($arrItems);
			$strItems = htmlspecialchars($strItems);
		}
		
		$addHtml = " data-init-items=\"{$strItems}\" ";
		
		$this->setManagerAddHtml($addHtml);
		
	}
	
	
	/**
	 * set start addon
	 */
	public function setStartAddon($addon){
		$this->startAddon = new UniteCreatorAddon();	//just for code completion
		$this->startAddon = $addon;
		
		$this->initStartAddonData();
	}
	
	
	/**
	 * get single item menu
	 */
	protected function getMenuSingleItem(){
		
		$arrMenuItem = array();
		$arrMenuItem["edit_item"] = __("Edit Item",UNITEGALLERY_TEXTDOMAIN);
		$arrMenuItem["remove_items"] = __("Delete",UNITEGALLERY_TEXTDOMAIN);
		$arrMenuItem["duplicate_items"] = __("Duplicate",UNITEGALLERY_TEXTDOMAIN);
		
		return($arrMenuItem);
	}

	/**
	 * get multiple items menu
	 */
	protected function getMenuMulitipleItems(){
		$arrMenuItemMultiple = array();
		$arrMenuItemMultiple["remove_items"] = __("Delete",UNITEGALLERY_TEXTDOMAIN);
		$arrMenuItemMultiple["duplicate_items"] = __("Duplicate",UNITEGALLERY_TEXTDOMAIN);
		return($arrMenuItemMultiple);
	}
	
	
	/**
	 * get item field menu
	 */
	protected function getMenuField(){
		$arrMenuField = array();
		$arrMenuField["add_item"] = __("Add Item",UNITEGALLERY_TEXTDOMAIN);
		$arrMenuField["select_all"] = __("Select All",UNITEGALLERY_TEXTDOMAIN);
		
		return($arrMenuField);
	}
	
	
	/**
	 * put items buttons
	 */
	protected function putItemsButtons(){
		
		$this->validateStartAddon();
		
		$itemType = $this->startAddon->getItemsType();
		
		//put add item button according the type
		switch($itemType){
			default:
			case UniteCreatorAddon::ITEMS_TYPE_DEFAULT:
			?>
 			<a data-action="add_item" type="button" class="unite-button-primary button-disabled ug-button-item ug-button-add"><?php _e("Add Item",UNITEGALLERY_TEXTDOMAIN)?></a>
			<?php 
			break;
			case UniteCreatorAddon::ITEMS_TYPE_IMAGE:
			?>
 			<a data-action="add_images" type="button" class="unite-button-primary button-disabled ug-button-item ug-button-add"><?php _e("Add Images",UNITEGALLERY_TEXTDOMAIN)?></a>
			<?php 
			break;
		}
		
		?>
	 		<a data-action="select_all_items" type="button" class="unite-button-secondary button-disabled ug-button-item ug-button-select" data-textselect="<?php _e("Select All",UNITEGALLERY_TEXTDOMAIN)?>" data-textunselect="<?php _e("Unselect All",UNITEGALLERY_TEXTDOMAIN)?>"><?php _e("Select All",UNITEGALLERY_TEXTDOMAIN)?></a>
	 		<a data-action="duplicate_items" type="button" class="unite-button-secondary button-disabled ug-button-item"><?php _e("Duplicate",UNITEGALLERY_TEXTDOMAIN)?></a>
	 		<a data-action="remove_items" type="button" class="unite-button-secondary button-disabled ug-button-item"><?php _e("Delete",UNITEGALLERY_TEXTDOMAIN)?></a>
	 		<a data-action="edit_item" type="button" class="unite-button-secondary button-disabled ug-button-item ug-single-item"><?php _e("Edit Item",UNITEGALLERY_TEXTDOMAIN)?> </a>
		<?php 
	}
	
	
	/**
	 * put add edit item dialog
	 */
	private function putAddEditDialog(){
		
		?>
			<div title="<?php _e("Edit Item",UNITEGALLERY_TEXTDOMAIN)?>" class="ug-dialog-edit-item" style="display:none;">
				<div class="ug-item-config-settings">
					<?php 
						if($this->startAddon)
							$this->startAddon->putHtmlItemConfig()
					 ?>
				</div>
			</div>
		<?php 
	}
	
	
	/**
	 * put additional html here
	 */
	protected function putAddHtml(){
			
		$this->putAddEditDialog();
	
	}
	
	
	/**
	 * init the addons manager
	 */
	protected function init(){
		
		$this->hasCats = false;
		
		parent::init();
	}
	
	
}