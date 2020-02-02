function UCManagerAdminCats(){

	var g_selectedCatID = -1;
	var g_catClickReady = false;
	var g_catFieldRightClickReady = true;		//avoid double menu on cat field
	var g_maxCatHeight = 450;
	var g_manager;
	
	//event functions
	this.events = {
		onRemoveSelectedCategory: function(){},
		onHeightChange: function(){}
	};
	
	
	var g_temp = {
			isInited: false
	};
	
	var t = this;
	

	function _______________INIT______________(){}
	
	/**
	 * validate that the object is inited
	 */
	function validateInited(){
		if(g_temp.isInited == false)
			throw new Error("The categories is not inited");
		
	}
	
	
	/**
	 * init the categories
	 */
	function initCats(objManager){
				
		if(g_temp.isInited == true)
			throw new Error("Can't init cat object twice");

		g_manager = objManager;
		
		g_temp.isInited = true;

		if(!g_ugAdmin)
			g_ugAdmin = new UniteAdminUG();		
		
		initEvents();
		
		//update sortable categories		
		jQuery( "#list_cats" ).sortable({
			axis:'y',
			start: function( event, ui ) {
				g_catClickReady = false;
			},
			update: function(){
				updateCatOrder();
				//save sorting order
			}
		});		
		
		// set update title onenter function
		jQuery("#input_cat_title").keyup(function(event){
			if(event.keyCode == 13)
				updateCategoryTitle();
		});
		
		
	}
	
	
	function _______________GETTERS______________(){}
	
	/**
	 * 
	 * get category by id
	 */
	function getCatByID(catID){
		var objCat = jQuery("#category_" + catID);
		return(objCat);
	}
	
	
	
	/**
	 * check if some category selected
	 * 
	 */
	this.isCatSelected = function(catID){
		if(catID == g_selectedCatID)
			return(true);
		
		return(false);
	}
	
	
	function _______________SETTERS______________(){}
	
	
	/**
	 * remove category from html
	 */
	function removeCategoryFromHtml(catID){
		
		jQuery("#category_"+catID).remove();
		
		if(catID == g_selectedCatID)
			g_selectedCatID = -1;
		
		disableCatButtons();
	}
	
	
	
	
	
	/**
	 * 
	 * open the edit category dialog by category id
	 */
	function editCategoryByID(catID){
		var cat = getCatByID(catID);
		
		if(cat.length == 0){
			trace("category with id: " + catID + " don't exists");
			return(false);
		}
		
		var dialogEdit = jQuery("#dialog_edit_category");
		
		dialogEdit.data("catid", catID);
		
		//update catid field		
		jQuery("#span_catdialog_id").html(catID);
		
		var title = cat.data("title");
		
		jQuery("#input_cat_title").val(title).focus();
			
		var buttonOpts = {};
		
		buttonOpts[g_ugtext.cancel] = function(){
			jQuery("#dialog_edit_category").dialog("close");
		};
		
		buttonOpts[g_ugtext.update] = function(){							
			updateCategoryTitle();
		};
		
		jQuery("#dialog_edit_category").dialog({
			buttons:buttonOpts,
			minWidth:500,
			modal:true,
			open:function(){
				jQuery("#input_cat_title").select();
			}
		});
	}

	
	
	
	/**
	 * set first category selected
	 */
	function selectFirstCategory(){
		var arrCats = getArrCats();
		if(arrCats.length == 0)
			return(false);
		
		var firstCat = arrCats[0];
		var catID = jQuery(firstCat).data("id");
		t.selectCategory(catID);
	}
	
	
	
	
	/**
	 * add category
	 */
	function addCategory(){
		
		g_manager.ajaxRequestManager("add_category","",g_ugtext.adding_category,function(response){
			
			var html = response.htmlCat;
			
			jQuery("#list_cats").append(html);
			
			//update html cats select
			var htmlSelectCats = response.htmlSelectCats;
			jQuery("#select_item_category").html(htmlSelectCats);
			
			t.events.onHeightChange();
		});		
	}
	
	
	/**
	 * remove some category by id
	 */
	function removeCategoryByID(catID){
		 
		if(confirm(g_ugtext.do_you_sure_remove) == false)
			return(false);
		
		var data = {};
		data.catID = catID;
		
		//get if selected category will be removed
		var isSelectedRemoved = (catID == g_selectedCatID);
		
		g_manager.ajaxRequestManager("remove_category",data,g_ugtext.removing_category,function(response){
			removeCategoryFromHtml(catID);
			
			//update html cats select
			var htmlSelectCats = response.htmlSelectCats;
			jQuery("#select_item_category").html(htmlSelectCats);
			
			//clear the items panel
			if(isSelectedRemoved == true){
				
				//run event
				t.events.onRemoveSelectedCategory();
								
				g_selectedCatID = -1;
				t.checkSelectFirstCategory();
			}
			
			//fire height change event
			t.events.onHeightChange();
			
		});
		
	}
	
	
	/**
	 * function invoke from the dialog update button
	 */
	function updateCategoryTitle(){
		var dialogEdit = jQuery("#dialog_edit_category");
		
		var catID = dialogEdit.data("catid");		
		
		var cat = getCatByID(catID);
		
		var numItems = cat.data("numaddons");
		
		var newTitle = jQuery("#input_cat_title").val();
		var data = {
			catID: catID,
			title: newTitle
		};
		
		dialogEdit.dialog("close");
		
		var newTitleShow = newTitle;
		if(numItems && numItems != undefined && numItems > 0)
			newTitleShow += " ("+numItems+")";
			
		cat.html("<span>" + newTitleShow + "</span>");
		
		cat.data("title",newTitle);
		
		g_manager.ajaxRequestManager("update_category",data,g_ugtext.updating_category);
	}
	
	/**
	 * export category items
	 */
	function exportCatItems(catID){
		var urlAjax = g_ugAdmin.getUrlAjax("export_cat_items", "catid="+catID);
		
		location.href = urlAjax;
	}

	
	
	/**
	 * run category action
	 */
	this.runCategoryAction = function(action, catID){
		
		switch(action){
			case "add_category":
				addCategory();
			break;
			case "edit_category":
				editCategoryByID(catID);
			break;
			case "delete_category":
				removeCategoryByID(catID);
			break;
			case "export_cat_items":
				exportCatItems(catID);
			break;
			case "import_cat_items":
				g_manager.openImportItemsDialog(catID);
			break;
			default:
				return(false);
			break;
		}
	
		return(true);
	}

	
	/**
	 * enable category buttons
	 */
	function enableCatButtons(){
		
		//cat butons:
		g_ugAdmin.enableButton("#button_remove_category, #button_edit_category");
		
		//items buttons:
		g_ugAdmin.enableButton("#button_add_images, #button_add_video");
	}
	
	
	/**
	 * enable category buttons
	 */
	function disableCatButtons(){
		
		g_ugAdmin.disableButton("#button_remove_category, #button_edit_category");
		
		//items buttons:
		g_ugAdmin.disableButton("#button_add_images, #button_add_video");		
	}

	
	/**
	 * update categories order
	 */
	function updateCatOrder(){
		
		//get sortIDs
		var arrSortCats = jQuery( "#list_cats" ).sortable("toArray");
		var arrSortIDs = [];
		for(var i=0;i < arrSortCats.length; i++){
			var catHtmlID = arrSortCats[i];
			var catID = catHtmlID.replace("category_","");
			arrSortIDs.push(catID);
		}
		
		var data = {cat_order:arrSortIDs};
		g_manager.ajaxRequestManager("update_cat_order",data,g_ugtext.updating_categories_order);
	}
	
	function _______________EVENTS______________(){}
	
	/**
	 * on remove category button click
	 */
	function onRemoveCategoryClick(){
		
			if(!g_ugAdmin.isButtonEnabled(this))
				return(false);
			
			if(g_selectedCatID == -1)
				return(false);
			
			removeCategoryByID(g_selectedCatID);
	}
	
	
	/**
	 * on edit category button click
	 */
	function onEditCategoryClick(){

		if(!g_ugAdmin.isButtonEnabled(this))
			return(false);
		
		if(g_selectedCatID == -1)
			return(false);
		
		editCategoryByID(g_selectedCatID);
	}
	
	
	/**
	 * on category list item click
	 */
	function onCatListItemClick(event){

		if(g_ugAdmin.isRightButtonPressed(event))
    		return(true);
		
		if(g_catClickReady == false)
			return(false);
		
		if(jQuery(this).hasClass("selected-item"))
			return(false);
		
		var catID = jQuery(this).data("id");
		t.selectCategory(catID);
		
	}
	
	/**
	 * on cat list item mousedown
	 */
	function onCatListItemMousedown(event){
	
		if(g_ugAdmin.isRightButtonPressed(event))
			return(true);
		
		g_catClickReady = true;
		
	}

	
	/**
	 * on category context menu click
	 */
	function onCategoryContextMenu(event){
		
		g_catFieldRightClickReady = false;
		
		var objCat = jQuery(this);
		var catID = objCat.data("id");
		var objMenu = jQuery("#rightmenu_cat");
		
		objMenu.data("catid",catID);
		g_manager.showMenuOnMousePos(event, objMenu);
	}

	
	/**
	 * on categories context menu
	 */
	function onCatsFieldContextMenu(event){
		
		event.preventDefault();
		
		if(g_catFieldRightClickReady == false){
			g_catFieldRightClickReady = true;
			return(true);
		}
		
		var objMenu = jQuery("#rightmenu_catfield");
		g_manager.showMenuOnMousePos(event, objMenu);
	}
	
	
	
	/**
	 * init events
	 */
	function initEvents(){
		
		//add category
		jQuery("#button_add_category").click(addCategory);
		
		//remove category:
		jQuery("#button_remove_category").click(onRemoveCategoryClick);
		
		//edit category
		jQuery("#button_edit_category").click(onEditCategoryClick);
		
		//list categories actions
		jQuery("#list_cats").delegate("li", "mouseover", function() {
			jQuery(this).addClass("item-hover");
			
		});
		
		jQuery("#list_cats").delegate("li", "mouseout", function() {
			jQuery(this).removeClass("item-hover");
		});
		
		jQuery("#list_cats").delegate("li", "click", onCatListItemClick);
		
		jQuery("#list_cats").delegate("li", "mousedown", onCatListItemMousedown );
		
		//init context menus
		jQuery("#list_cats").delegate("li","contextmenu",onCategoryContextMenu);
		jQuery("#cats_section").bind("contextmenu",onCatsFieldContextMenu);
		
	}
	
	this._______________EXTERNAL_GETTERS______________ = function(){}
	
	/**
	 * get selected category ID
	 */
	this.getSelectedCatID = function(){
		
		return(g_selectedCatID);
	}
	
	/**
	 * get category data
	 */
	this.getCatData = function(catID){
		var objCat = getCatByID(catID);
		
		var data = {};
		data.numItems = objCat.data("numitems");
		data.title = objCat.data("title");
		data.sortby = objCat.data("sortby");
		
		data.id = catID;
		
		return(data);
	}
	
	/**
	 * update cat sortby
	 */
	this.updateCatSortby = function(catID, sortby){
		var objCat = getCatByID(catID);
		objCat.data("sortby", sortby);
	}
	
	
	/**
	 * return if some category selected
	 */
	this.isSomeCatSelected = function(){
		
		if(g_selectedCatID == -1)
			return(false);
		
		return(true);
	}
	
	
	/**
	 * get height of the categories list
	 */
	this.getCatsHeight = function(){
		
		var catsWrapper = jQuery("#cats_section .cat_list_wrapper");
		var catHeight = catsWrapper.height();

		if(catHeight > g_maxCatHeight)
			catHeight = g_maxCatHeight;
		
		return(catHeight);
	}
	
	/**
	 * get arr categories
	 */
	function getArrCats(){
		var arrCats = jQuery("#list_cats li").get();
		return(arrCats);
	}
	
	
	/**
	 * get num categories
	 */
	this.getNumCats = function(){
		var numCats = jQuery("#list_cats li").length;
		return(numCats);
	}
	
	
	/**
	 * get mouseover category
	 */
	this.getMouseOverCat = function(){

		var arrCats = getArrCats();
		
		for(var index in arrCats){
			var objCat = arrCats[index];
			objCat = jQuery(objCat);
			
			var isMouseOver = objCat.ismouseover();
			if(isMouseOver == true)
				return(objCat);
		}
		
		return(null);
	}
	
	
	this._______________EXTERNAL_SETTERS______________ = function(){}
	
	/**
	 * update category sortby 
	 */
	this.updateCatSortbyData = function(catID, sortby){
		
		var objCat = getCatByID(catID);
		
		objCat.data("sortby", sortby);
		
	}
	
	
	/**
	 * set cat section height
	 */
	this.setHeight = function(height){
		
		jQuery("#cats_section").css("height", height+"px");
		
	}
	
	/**
	 * set html cats list
	 */
	this.setHtmlListCats = function(htmlCats){
		
		jQuery("#list_cats").html(htmlCats);
		
	}
	
	/**
	 * select some category by id
	 */
	this.selectCategory = function(catID){
		
		var cat = jQuery("#category_"+catID);
		if(cat.length == 0){
			//g_ugAdmin.showErrorMessage("category with id: "+catID+" not found");
			return(false);
		}
		
		cat.removeClass("item-hover");
		
		if(cat.hasClass("selected-item"))
			return(false);
		
		g_selectedCatID = catID;
		
		jQuery("#list_cats li").removeClass("selected-item");
		cat.addClass("selected-item");
		
		enableCatButtons();
		
		g_manager.onCatSelect(catID);
		
	}
	
	/**
	 * check if number of cats = 1, if do, select it
	 */
	this.checkSelectFirstCategory = function(){
		
		var arrCats = getArrCats();
		if(arrCats.length == 1)
			selectFirstCategory();
	}
	
	/**
	 * get context menu category ID
	 */
	this.getContextMenuCatID = function(){
		var catID = jQuery("#rightmenu_cat").data("catid");
		return(catID);
	}
	
	
	
	/**
	 * init categories
	 */
	this.init = function(objManager){
		
		initCats(objManager);
		
	}
	
	
}