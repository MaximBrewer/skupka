<?php

/**
 * @package Unite Gallery
 * @author UniteCMS.net / Valiano
 * @copyright (C) 2012 Unite CMS, All Rights Reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */

class UniteGalleryManager{

	const TYPE_MAIN = "main";
	const TYPE_ITEMS_INLINE = "inline";
	
	protected $type = null;
	protected $hasCats = true;
	protected $hasSortBy = false;
	
	protected $options = array();
	
	protected $objCats = null;
	protected $selectedCategory = "";
	private $managerAddHtml = "";
	private $errorMessage = null;
	protected $itemsLoaderText = "";
	protected $textItemsSelected = "";
	
	
	protected function a___________REWRITE_FUNCTIONS________(){}
	
	/**
	 * put items buttons
	 */
	protected function putItemsButtons(){
	
		?>
	put buttons from child classes
	<?php
	}
	
	
	/**
	 * put filters - function for override
	 */
	protected function putItemsFilters(){}
	
	
	/**
	 * ge tmenu single item
	 */
	protected function getMenuSingleItem(){
	
		$arrMenuItem = array();
		$arrMenuItem["no_action"] = __("No Action",UNITEGALLERY_TEXTDOMAIN);
	
		return($arrMenuItem);
	}
	
	/**
	 * get item field menu
	 */
	protected function getMenuField(){
	
		$arrMenuField = array();
		$arrMenuField["no_action"] = __("No Action",UNITEGALLERY_TEXTDOMAIN);
	
		return($arrMenuField);
	}
	
	
	/**
	 * put additional html here
	 */
	protected function putAddHtml(){
		dmp("put add html here by child class");
	}
	
	
	/**
	 * get no items text
	 */
	protected function getNoItemsText(){
		$text = __("No Items", UNITEGALLERY_TEXTDOMAIN);
		return($text);
	}
	
	protected function a___________SET_DATA_BEFORE_PUT________(){}
	
	
	/**
	 * set manager add html, must be called before put
	 */
	protected function setManagerAddHtml($addHtml){
		$this->managerAddHtml = $addHtml;
	}
	
	protected function a___________MAIN_FUNCTIONS________(){}
	
	
	/**
	 * validate inited function
	 */
	private function validateInited(){
		if(empty($this->type))
			UniteFunctionsUG::throwError("The manager is not inited");
	}
	
	
	/**
	 * put categories html
	 */
	private function putHtmlCats(){
		
		$htmlCatList = $this->objCats->getHtmlCatList();
		
		?>
			<div id="categories_wrapper" class="categories_wrapper unselectable">
			 	<div class="manager_title">
			 		<?php _e("Categories", UNITEGALLERY_TEXTDOMAIN)?> 
			 	</div>
			 	<div class="manager_buttons">
			 		<a id="button_add_category" type="button" class="unite-button-secondary"><?php _e("Add",UNITEGALLERY_TEXTDOMAIN)?></a>
			 		<a id="button_remove_category" type="button" class="unite-button-secondary button-disabled"><?php _e("Delete",UNITEGALLERY_TEXTDOMAIN)?></a>
			 		<a id="button_edit_category" type="button" class="unite-button-secondary button-disabled"><?php _e("Edit",UNITEGALLERY_TEXTDOMAIN)?></a>
			 	</div>
			 	<hr>
			 	<div id="cats_section" class="cats_section">
				 	<div class="cat_list_wrapper">			 
						<ul id="list_cats" class="list_cats">
							<?php echo $htmlCatList?>
						</ul>					
				 	</div>
			 	</div>			 	
			</div>
		<?php
	}
	
	/**
	 * function for override
	 */
	protected function putInitItems(){} 
	
	
	/**
	 * put items html sortby
	 */
	protected function putItemsHtmlSortBy(){
		
		$arrSortby = UniteGalleryCategory::getArrSortby();
		
		?>
			<div id="um_sortby_items_wrapper" class="um-sortby-wrapper unite-sortby-items unite-disabled" >
				
				<span>
				<?php _e("Sort By", UNITEGALLERY_TEXTDOMAIN)?>:
				</span>
				
				<select id="um_select_sortby_items" disabled="disabled">
				<?php 
					foreach($arrSortby as $key => $title){
						?>
							<option value="<?php echo $key?>"><?php echo $title?></option>
						<?php 
					}
				?>
				</select>
				
			</div>
		<?php 		
	}
	
	
	/**
	 * put items wrapper html
	 */
	private function putItemsWrapper(){
		?>
						<div class="items_wrapper unselectable">
						 	
						 	<?php if($this->hasCats == true):?>
						 	
						 	<div class="manager_title">
						 		<?php _e("Items",UNITEGALLERY_TEXTDOMAIN)?>
						 		<?php if($this->hasSortBy)
						 				$this->putItemsHtmlSortBy();
						 		?>
						 	</div>
						 	
						 	<?php endif?>
														
							<?php $this->putItemsFilters() ?>
						 	
						 	<div id="manager_buttons" class="manager_buttons">
						 		
						 		<?php $this->putItemsButtons()?>
						 		
						 	</div>
						 	
						 	<hr>
						 	
						 	<div id="items_outer" class="items_outer">
						 		
								<div id="items_list_wrapper" class="items_list_wrapper unselectable">
									<div id="items_loader" class="items_loader" style="display:none;">
										<?php echo $this->itemsLoaderText?>...
									</div>
									
									<div id="no_items_text" class="no_items_text" style="display:none;">
										<?php echo $this->getNoItemsText()?>
									</div>
									
									<ul id="ug_list_items" class="ug-list-items unselectable ug-listitems-<?php echo $this->type?>"><?php $this->putInitItems()?></ul>
									<div id="drag_indicator" class="drag_indicator" style="display:none;"></div>
									<div id="shadow_bar" class="shadow_bar" style="display:none"></div>
									<div id="select_bar" class="select_bar" style="display:none"></div>
								</div>
							
							</div>								
						</div>
		<?php 
	}
	
	
	/**
	 * html status operations html
	 */
	private function putStatusLineOperations(){
		
		?>
		
							<div class="status_operations">
								<div class="status_num_selected">
									<span id="num_items_selected">0</span> <?php echo $this->textItemsSelected?>
								</div>

								<?php if($this->hasCats == true): 
									$htmlCatSelect = $this->objCats->getHtmlSelectCats();
								?>
								
								<div id="item_operations_wrapper" class="item_operations_wrapper unite-disabled">
									
									<select id="select_item_operations" disabled="disabled">
									 	<option value="copy"><?php _e("Copy To",UNITEGALLERY_TEXTDOMAIN)?></option>
									 	<option value="move"><?php _e("Move To",UNITEGALLERY_TEXTDOMAIN)?></option>
									</select>
									
									<select id="select_item_category" disabled="disabled">
										<?php echo $htmlCatSelect ?>
									</select>				
									 
									 <a id="button_items_operation" class="unite-button-secondary button-disabled" href="javascript:void(0)">GO</a>
								 </div>
								 
								 <?php endif?>
								 
							</div>
		
		<?php 
		
	}
	
	
	/**
	 * put status line html
	 */
	private function putStatusLine(){
		
		?>
						<div class="status_line">
							<div class="status_loader_wrapper">
								<div id="status_loader" class="status_loader" style="display:none;"></div>
							</div>
							<div class="status_text_wrapper">
								<span id="status_text" class="status_text" style="display:none;"></span>
							</div>
							
			<?php 
				$this->putStatusLineOperations();
			?>
			
							
						</div>
		<?php 
	}
	
	/**
	 * put copy move menu
	 */
	private function putMenuCopyMove(){
		?>
			<ul id="menu_copymove" class="unite-context-menu" style="display:none">
				<li>
					<a href="javascript:void(0)" data-operation="copymove_copy"><?php _e("Copy Here",UNITEGALLERY_TEXTDOMAIN)?></a>
					<a href="javascript:void(0)" data-operation="copymove_move"><?php _e("Move Here",UNITEGALLERY_TEXTDOMAIN)?></a>
				</li>
			</ul>
		<?php
	}
	
	
	
	/**
	 * put single item menu
	 */
	private function putMenuSingleItem(){
		
		$arrMenuItem = $this->getMenuSingleItem();
				
		?>
			<!-- Right menu single -->
			
			<ul id="rightmenu_item" class="unite-context-menu" style="display:none">
				<?php foreach($arrMenuItem as $operation=>$text):?>
				<li>
					<a href="javascript:void(0)" data-operation="<?php echo $operation?>"><?php echo $text?></a>
				</li>
				<?php endforeach?>
			</ul>
		
		<?php 
	}
	
	/**
	 * get multiple items menu
	 */
	protected function getMenuMulitipleItems(){
		$arrMenuItemMultiple = array();
		$arrMenuItemMultiple["no_action"] = __("No Action",UNITEGALLERY_TEXTDOMAIN);
		return($arrMenuItemMultiple);
	}
	
	
	/**
	 * put multiple items menu
	 */
	private function putMenuMultipleItems(){
		
		$arrMenuItemMultiple = $this->getMenuMulitipleItems();
		
		?>
			<!-- Right menu multiple -->
			
			<ul id="rightmenu_item_multiple" class="unite-context-menu" style="display:none">
				<?php foreach($arrMenuItemMultiple as $operation=>$text):?>
				<li>
					<a href="javascript:void(0)" data-operation="<?php echo $operation?>"><?php echo $text?></a>
				</li>
				<?php endforeach?>
			</ul>
		
		<?php
	}
	
	
	/**
	 * get category menu
	 */
	protected function getMenuCategory(){
		
		$arrMenuCat = array();
		$arrMenuCat["no_action"] = __("No Action",UNITEGALLERY_TEXTDOMAIN);
		
		return($arrMenuCat);
	}
	
	/**
	 * put right menu category
	 */
	private function putMenuCategory(){

		//init category menu
		$arrMenuCat = $this->getMenuCategory();
		
		?>
			<!-- Right menu category -->
			<ul id="rightmenu_cat" class="unite-context-menu" style="display:none">
				<?php foreach($arrMenuCat as $operation=>$text):?>
				<li>
					<a href="javascript:void(0)" data-operation="<?php echo $operation?>"><?php echo $text?></a>
				</li>
				<?php endforeach?>
			</ul>
		
		<?php 
	}
	
	
	
	/**
	 * put right menu field
	 */
	private function putMenuField(){
		
		$arrMenuField = $this->getMenuField();
		
		
		?>
			<!-- Right menu field -->
			<ul id="rightmenu_field" class="unite-context-menu" style="display:none">
				<?php foreach($arrMenuField as $operation=>$text):?>
				<li>
					<a href="javascript:void(0)" data-operation="<?php echo $operation?>"><?php echo $text?></a>
				</li>
				<?php endforeach?>			
			</ul>
		
		<?php
	}
	
	
	
	/**
	 * put right menu category field
	 */
	private function putMenuCatField(){
		
		//init category field menu
		$arrMenuCatField = array();
		$arrMenuCatField["add_category"] = __("Add Category",UNITEGALLERY_TEXTDOMAIN);
		
		?>
			<!-- Right menu category field-->
			<ul id="rightmenu_catfield" class="unite-context-menu" style="display:none">
				<?php foreach($arrMenuCatField as $operation=>$text):?>
				<li>
					<a href="javascript:void(0)" data-operation="<?php echo $operation?>"><?php echo $text?></a>
				</li>
				<?php endforeach?>
			</ul>
		
		<?php
	}

	/**
	 * put category edit dialog
	 */
	private function putDialogEditCategory(){
		?>
			<div id="dialog_edit_category"  title="<?php _e("Edit Category",UNITEGALLERY_TEXTDOMAIN)?>" style="display:none;">
			
				<div class="dialog_edit_category_inner unite-inputs">
					
					<?php _e("Category ID", UNITEGALLERY_TEXTDOMAIN)?>: <b><span id="span_catdialog_id"></span></b>
					
					<br><br>
					
					<?php _e("Edit Name", UNITEGALLERY_TEXTDOMAIN)?>:
					<input type="text" id="input_cat_title" class="unite-input-regular">
				</div>
				
			</div>
		
		<?php
	}
	
	
	/**
	* put scripts according manager type
	 */
	public static function putScriptsIncludes($type){
		
		HelperUG::addScript("unitegallery_manager_items");
		HelperUG::addScript("unitegallery_manager");
		HelperUG::addStyle("unitegallery_manager","unitegallery_manager_css");
		
		switch($type){
			case self::TYPE_MAIN:
				HelperUG::addScript("unitegallery_manager_cats");
				HelperUG::addScript("unitegallery_manager_actions_main");
			break;
			case self::TYPE_ITEMS_INLINE:
				HelperUG::addScript("unitegallery_manager_actions_inline");
			break;
		}
		
	}
	
	
	/**
	 * output manager html
	 */
	public function outputHtml(){
		
		$this->validateInited();
		
		$addClass = "";
		if($this->hasCats == false)
			$addClass = " ug-nocats ";
		
		try{
		
		?>
		
		<div id="ug_managerw" class="ug-manager-outer" data-type="<?php echo $this->type?>" <?php echo $this->managerAddHtml?>>
		
			<div class="manager_wrapper <?php echo $addClass?> unselectable" >
			
				<?php if($this->hasCats == true): ?>
			
				<table class="layout_table" width="100%" cellpadding="0" cellspacing="0">
					
					<tr>
						<td class="cell_cats" width="220px" valign="top">
							<?php $this->putHtmlCats()?>
						</td>
						
						<td class="cell_items" valign="top">
						
							<?php $this->putItemsWrapper()?>
							
						</td>
					</tr>
					<tr>
						<td colspan="2">
							
							<?php $this->putStatusLine() ?>
							
						</td>
					</tr>
					
				</table>
	
				<?php else:?>
					
					<?php 
						$this->putItemsWrapper();
						$this->putStatusLine();
					?>
					
					
				<?php endif?>
				
			</div>	<!--  end manager wrapper -->
		
			<div id="manager_shadow_overlay" class="manager_shadow_overlay" style="display:none"></div>
		
			<?php 

			
				$this->putMenuSingleItem();
				$this->putMenuMultipleItems();
				$this->putMenuField();
				
				if($this->hasCats): 
						$this->putMenuCopyMove();
						$this->putMenuCategory();
						$this->putMenuCatField();
						$this->putDialogEditCategory();
				endif;
				
				$this->putAddHtml();
				
			?>
			
			</div>
			<?php 
			
			}catch(Exception $e){
				$message = "<br><br>manager error: <b>".$e->getMessage()."</b>";
				
				echo "</div>";
				echo "</div>";
				echo "</div>";
				
				echo "<div class='unite-color-red'>".$message."</div>";
				
				if(GlobalsUG::SHOW_TRACE == true)
					dmp($e->getTraceAsString());
			}
			
	}
	
	
	/**
	 * init manager
	 */
	protected function init(){
		
		//the type should be set already in child classes
		$this->validateInited();
		
		$this->itemsLoaderText = __("Getting Items", UNITEGALLERY_TEXTDOMAIN);
		$this->textItemsSelected = __("items selected",UNITEGALLERY_TEXTDOMAIN);
		
		if($this->hasCats){
			$this->objCats = new UniteGalleryCategories();
		}
		
	}
	
}