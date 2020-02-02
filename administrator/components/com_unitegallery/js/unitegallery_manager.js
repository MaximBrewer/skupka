function UCManagerAdmin(){
	
	var g_objWrapper = null;
	var t = this;
	var g_objCats, g_objItemsSortby = null;
	var g_objItems = new UGManagerAdminItems();
	var g_objActions, g_type, g_customOptions = {};
	
	var g_minHeight = 280;
	
	var g_temp = {
			hasCats: true,
			hasItemsSortby: false
	};
	
	this.events = {
		SELECT_CATEGORY: "select_category"	
	};
	
	
	function ___________GENERAL_FUNCTIONS________________(){}	//sap for outline	
	
	
	/**
	 * update global height, by of categories and items
	 */
	function updateGlobalHeight(catHeight, itemsHeight){
		
		if(!catHeight){
			if(g_objCats)
				var catHeight = g_objCats.getCatsHeight();
			else
				catHeight = 0;
		}
		
		if(!itemsHeight)
			var itemsHeight = g_objItems.getItemsMaxHeight();
		
		var maxHeight = catHeight;
		
		if(itemsHeight > maxHeight)
			maxHeight = itemsHeight;
				
		maxHeight += 20;			
		
		if(maxHeight < g_minHeight)
			maxHeight = g_minHeight;
		
		//set list height
		g_objItems.setHeight(maxHeight);
		
		if(g_objCats)
			g_objCats.setHeight(maxHeight);
		
	}
	
	
	/**
	 * validate that the manager is already inited
	 */
	function validateInited(){

		var isInited = g_objWrapper.data("inited");
		
		if(isInited === true)
			throw new Error("Can't init manager twice");
		
		
		g_objWrapper.data("inited", true);
		
	}
	
	
	
	/**
	 * init manager
	 */
	this.initManager = function(selectedCatID){
		initManager(selectedCatID);
	}

	
	/**
	 * return if the items field enabled
	 */
	this.isItemsAreaEnabled = function(){

		if(!g_objCats)
			return(true);
		
		if(g_objCats && g_objCats.isSomeCatSelected() == false)
			return(false);
		
		return(true);
	}
	
	
	/**
	 * 
	 * set some menu on mouse position
	 */
	this.showMenuOnMousePos = function(event,objMenu){
		
		var objOffset = g_objWrapper.offset();
		var managerY = objOffset.top;
		var managerX = objOffset.left;
		
		var menuX = Math.round(event.pageX - managerX);
		var menuY = Math.round(event.pageY - managerY);
		
		jQuery("#manager_shadow_overlay").show();
		objMenu.css({"left":menuX+"px","top":menuY+"px"}).show();
	}
	
	
	/**
	 * hide all context menus
	 */
	this.hideContextMenus = function(){
		jQuery("#manager_shadow_overlay").hide();
		jQuery("ul.unite-context-menu").hide();
	}
	
	
	/**
	 * get mouseover item
	 */
	this.getMouseOverItem = function(){
		
		if(g_objCats){
			var catItem = g_objCats.getMouseOverCat();
			if(catItem)
				return(catItem);
		}
		
		var item = g_objItems.getMouseOverItem();
		
		return(item);
	}

	
	/**
	 * on item context menu click
	 */
	function onContextMenuClick(){
		
		var objLink = jQuery(this);
		var action = objLink.data("operation");
		
		var actionFound = false;
		
		if(g_objCats){
			var catID = g_objCats.getContextMenuCatID();
			actionFound = g_objCats.runCategoryAction(action, catID);
		}
		
		if(actionFound == false)
			t.runItemAction(action);
		
		t.hideContextMenus();
	}
	
	
	/**
	 * init context menu events
	 * other context menu functions are located in the items
	 */
	function initContextMenus(){

		g_objWrapper.add("#manager_shadow_overlay").bind("contextmenu",function(event){
			event.preventDefault();
		});
		
		//on item right menu click
		jQuery(".unite-context-menu li a").mouseup(onContextMenuClick);
		
	}

	
	/**
	 * init gallery view
	 */		
	function initManager(selectedCatID){
		
		
		g_objWrapper = jQuery("#ug_managerw");
		if(g_objWrapper.length == 0)
			return(false);
		
		g_type = g_objWrapper.data("type");
		
		validateInited();
		
		if(!g_ugAdmin)
			g_ugAdmin = new UniteAdminUG();		
		
		//set if no cats
		var objCatsSection = jQuery("#cats_section");
		if(objCatsSection.length == 0){
			g_temp.hasCats = false;
			g_objCats = null;
		}else{
			g_objCats = new UCManagerAdminCats();
		}
		
		//set hasSortBy
		var objItemsSortBy = jQuery("#um_sortby_items_wrapper");
		g_temp.hasItemsSortby = (objItemsSortBy.length !== 0);
		
		if(g_temp.hasItemsSortby){
			initItemsSortby();
		}
		
		//-----------------
				
		if(g_temp.hasCats == true)
			initCategories();
		
		//init actions
		switch(g_type){
			case "main":
				g_objActions = new UGManagerActionsMain();
			break;
			case "inline":
				g_objActions = new UGManagerActionsInline();
			break;
			default:
				throw new Error("Wrong manager type: " + g_type);
			break;
		}
				
		if(g_objActions)
			g_objActions.init(t);
		
		//the items must be inited from the manager action file		
		g_objItems.validateInited();
		
		//check first item select
		if(g_objCats){

			if(selectedCatID)
				g_objCats.selectCategory(selectedCatID);
			else
				g_objCats.checkSelectFirstCategory();
		}
		
		updateGlobalHeight();
	};
	
	
	/**
	 * grigger event
	 */
	function triggerEvent(eventName, options){
		
		g_objWrapper.trigger(eventName, options);
	
	}
	
	
	/**
	 * on some event
	 */
	function onEvent(eventName, func){
		
		g_objWrapper.on(eventName, func);
		
	}
	
	
	function ___________SORTBY________________(){}	//sap for outline
	
	
	
	/**
	 * enable items sortby
	 */
	function enableItemsSortby(){
		
		jQuery("#um_sortby_items_wrapper").removeClass("unite-disabled");
		jQuery("#um_select_sortby_items").attr("disabled", false);
	
	}
	
	/**
	 * disable items sortby
	 */
	function disableItemsSortby(){
		
		jQuery("#um_sortby_items_wrapper").addClass("unite-disabled");
		jQuery("#um_select_sortby_items").attr("disabled", "disabled");
		
	}
	
	/**
	 * on sortby change
	 */
	function onSortbyChange(){
		g_objActions.onSortbyChange();
	}
	
	/**
	 * init sortby events
	 */
	function initSortByEvents(){
		
		//on select category enable sortby
		onEvent(t.events.SELECT_CATEGORY, function(event, data){
			
			var sortby = data.sortby;
			
			enableItemsSortby();
			
			//change select items
			jQuery("#um_select_sortby_items").val(sortby);
			
		});
    	
		
		g_objItemsSortby.change(onSortbyChange);
		
	}
	
	
	/**
	 * init items sortby
	 */
	function initItemsSortby(){
		
		g_objItemsSortby = jQuery("#um_select_sortby_items");
		
		disableItemsSortby();
		
		g_objItemsSortby.val("custom");
		
		initSortByEvents();
		
	}
	
	/**
	 * clear items sortby
	 */
	this.clearItemsSortby = function(){
		
		if(!g_objItemsSortby)
			return(false);
		
		g_objItemsSortby.val("custom");
	}
	
	function ___________CATEGORIES________________(){}	//sap for outline
	
	
	/**
	 * init the categories actions
	 */
	function initCategories(){
		
		g_objCats.init(t);
		
		//init events
		g_objCats.events.onRemoveSelectedCategory = function(){
			t.clearItemsPanel();
		};
		
		g_objCats.events.onHeightChange = function(){
			updateGlobalHeight();
		};
		
	}
	
	
	function ___________ITEMS_FUNCTIONS________________(){}	//sap for outline	
	
	
	/**
	 * update bottom operations
	 */
	function updateBottomOperations(){
		
		
		var numSelected = g_objItems.getNumItemsSelected();
		
		var numCats = 0;
		
		if(g_objCats)
			var numCats = g_objCats.getNumCats();
		
		jQuery("#num_items_selected").html(numSelected);
				
		//in case of less then 2 cats - disable operations
		if(numCats <= 1){
			
			jQuery("#item_operations_wrapper").hide();
			return(false);
		}
		
		//in case of more then one cat
		jQuery("#item_operations_wrapper").show();
		
		//enable operations
		if(numSelected > 0){
			jQuery("#select_item_category").prop("disabled","");
			jQuery("#select_item_operations").prop("disabled","");
			jQuery("#item_operations_wrapper").removeClass("unite-disabled");
			jQuery("#button_items_operation").removeClass("button-disabled");
			
		}else{		//disable operations
			jQuery("#select_item_category").prop("disabled","disabled");
			jQuery("#select_item_operations").prop("disabled","disabled");
			jQuery("#button_items_operation").addClass("button-disabled");
			jQuery("#item_operations_wrapper").addClass("unite-disabled");
		}
		
		//hide / show operation categories 
		jQuery("#select_item_category option").show();
		var arrOptions = jQuery("#select_item_category option").get();
		
		var firstSelected = false;
		
		var selectedCatID = g_objCats.getSelectedCatID();
		
		for(var index in arrOptions){
			var objOption = jQuery(arrOptions[index]);
			var value = objOption.prop("value");
			
			if(value == selectedCatID)
				objOption.hide();
			else
				if(firstSelected == false){
					objOption.attr("selected","selected");
					firstSelected = true;
				}
		}
		
	}


	/**
	 * run items action
	 */
	this.runItemAction = function(action){
		g_objActions.runItemAction(action);
	}

	
	/**
	 * init bottom operations
	 */
	this.initBottomOperations = function(){
		
		// do items operations
		jQuery("#button_items_operation").click(onBottomOperationsClick);
		
	}
	
	
	/**
	 * init items actions
	 */
	this.initItems = function(){
		
		g_objItems.initItems(t);
		
		//on selection change
		g_objItems.events.onItemSelectionChange = function(){
			updateBottomOperations();
		}
		
		//on items height change
		g_objItems.events.onHeightChange = function(itemsHeight){
			updateGlobalHeight(null, itemsHeight);
		}
		
		initContextMenus();
		
		//if items only - clear panel
		if(g_temp.hasCats == false)
			g_objItems.updatePanelView();
		
	}

	
	/**
	 * get categories object
	 */
	this.getObjCats = function(){
		return(g_objCats);
	}

	
	/**
	 * get items objects
	 */
	this.getObjItems = function(){
		
		return(g_objItems);
	}
	
	
	/**
	 * get wrapper object
	 */
	this.getObjWrapper = function(){
		
		return(g_objWrapper);
	}
	
	
    /**
     * on select category event
     */
    this.onCatSelect = function(catID){
    	g_objActions.runItemAction("get_cat_items", catID);
    	g_objItems.unselectAllItems("selectCategory");	
    	
    	var catData = g_objCats.getCatData(catID);
    	
    	triggerEvent(t.events.SELECT_CATEGORY, catData);
    	
    }
	
    
	/**
	 * run gallery ajax request
	 */
	this.ajaxRequestManager = function(action,data,status,funcSuccess){
		
		if(!status)
			var status = g_ugtext.updating;
		
		jQuery("#status_loader").show();
		jQuery("#status_text").show().html(status);
		
		g_ugAdmin.ajaxRequest(action,data,function(response){
			jQuery("#status_loader").hide();
			jQuery("#status_text").hide();
			if(typeof funcSuccess == "function")
				funcSuccess(response);
			
			g_objItems.checkSelectRelatedItems();
		});
		
	}
	
	
	/**
	 * 
	 * on bottom GO button click,  move items
	 */
	function onBottomOperationsClick(){
				
		var arrIDs = g_objItems.getSelectedItemIDs();
				
		if(arrIDs.length == 0)
			return(false);
		
		var selectedCatID = g_objCats.getSelectedCatID();
		
		var operation = jQuery("#select_item_operations").val();
		
		var targetCatID = jQuery("#select_item_category").val();
		if(targetCatID == selectedCatID){
			alert("Can't move addons to same category");
			return(false);
		}
				
		var data = {};
		data.targetCatID = targetCatID;
		data.selectedCatID = selectedCatID;
		data.arrItemIDs = arrIDs;
		data.operation = operation;
		
		g_objActions.runItemAction("copymove_data", data);
			
	}
	
	
	/**
	 * set actions options
	 * some data goes directly to options
	 */
	this.setCustomOptions = function(options){
		g_customOptions = options;
	}
	
	
	/**
	 * get custom option by name
	 */
	this.getCustomOption = function(name){
		if(g_customOptions.hasOwnProperty(name) == false)
			return(undefined);
		
		var value = g_customOptions[name];
		
		return(value);
	}
	
	
	
	/**
	 * get all items data - from actions
	 */
	this.getItemsData = function(){

		if(typeof g_objActions.getItemsData != "function")
			throw new Error("get items data function not exists in this type");
		
		var arrItems = g_objActions.getItemsData();
		
		return(arrItems);
	}
	
	
	/**
	 * get items data json
	 */
	this.getItemsDataJson = function(){
		var data = t.getItemsData();
		if(typeof data != "object")
			return("");
		
		var dataJson = JSON.stringify(data);
		
		return(dataJson);
	}
	
	
	/**
	 * set items from data
	 */
	this.setItemsFromData = function(arrItems){
		if(typeof g_objActions.setItemsFromData != "function")
			throw new Error("set items from data function not exists in this type");
		
		g_objActions.setItemsFromData(arrItems);
	} 
	
	
	/**
	 * clear items panel
	 */
	this.clearItemsPanel = function(){
		g_objItems.clearItemsPanel();
	}
	
	
	this.___________SPECIAL_GALLERY_FUNCTIONS________________ = function(){}		
	
	/**
	 * preview item image
	 */
	this.previewItemImage = function(){
		
		g_objItems.previewItemImage();
	}
	
	/**
	 * open import item dialog
	 */
	this.openImportItemsDialog = function(catID){
		g_objActions.openImportItemsDialog(catID);
	}
	
	
};