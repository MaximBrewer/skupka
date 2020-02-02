function UGManagerActionsMain(){
	
	var g_objCats, g_objItems, g_objItemsSortby;
	var g_manager = new UCManagerAdmin();
	var g_options;
	var g_settings = new UniteSettingsUG();

	if(!g_ugAdmin){
		var g_ugAdmin = new UniteAdminUG();
	}
	
	
	/**
	 * on item button click
	 */
	this.runItemAction = function(action, data){
		
		switch(action){
			case "add_images":
				addImageItem();
			break;
			case "add_video":
				addMediaItem();
			break;
			case "update_order":
				updateItemsOrder();
			break;
			case "select_all_items":
				g_objItems.selectUnselectAllItems();
			break;
			case "duplicate_item":
				duplicateItems();
			break;
			case "item_default_action":
			case "edit_item":
				editItem();
			break;
			case "edit_item_title":
				editItemTitle();
			break;
			case "remove_item":
				removeSelectedItems();				
			break;
			case "copymove_data":
				copyMoveItems(data);
			break;
			case "copymove_move":
				onCopyMoveOperationClick("move");
			break;
			case "copymove_copy":
				onCopyMoveOperationClick("copy");
			break;
			case "get_cat_items":
		    	getSelectedCatItems(data);	//data - catID
			break;
			case "preview_item":
				g_manager.previewItemImage();
			break;
			default:
				trace("wrong item action: " + action);
			break;
		}
		
	}
	
	
	/**
	 * init items
	 */
	function initItems(){
		
		initEditTitleDialog();
		
		g_manager.initBottomOperations();
		
		initImportItemsDialog();
	}

	
	/**
	 * set combo lists from response
	 */
	function setHtmlListCombo(response){
		var htmlItems = response.htmlItems;
		var htmlCats = response.htmlCats;
		
		g_objItems.setHtmlListItems(htmlItems);
		
		if(g_objCats)
			g_objCats.setHtmlListCats(htmlCats);
	}

	
	/**
	 * make some copy/move operation and close the dialog
	 */
	function onCopyMoveOperationClick(operation){
		
		var objDrag = g_objItems.getObjDrag();
		
		var data = {};
		data.targetCatID = objDrag.targetItemID;
		data.selectedCatID = g_objCats.getSelectedCatID();
		data.arrItemIDs = objDrag.arrItemIDs;
		data.operation = operation;
		
		copyMoveItems(data);
		
		g_objItems.resetDragData();
	}
	
	
	function ___________ITEMS_DIALOGS________________(){}

	
	/**
	 * open import items dialog
	 */
	this.openImportItemsDialog = function(catID){
		
		var buttonOpts = {};
		
		jQuery("#dialog_import_items").data("catid", catID);
		
		buttonOpts[g_ugtext.cancel] = function(){
			jQuery("#dialog_import_items").dialog("close");
		};
		
		jQuery("#dialog_import_items_error").hide();
		jQuery("#dialog_import_upload").show();
		
		jQuery("#dialog_import_items").dialog({
			buttons:buttonOpts,
			minWidth:600,
			modal:true,
		});
		
	}
	
	
	/**
	 * set import dialog actions
	 */
	function initImportItemsDialog(){
		
		jQuery("#dialog_import_upload").click(function(){
			var catID =	jQuery("#dialog_import_items").data("catid");
			var data = {catID: catID};
			
			g_ugAdmin.setAjaxLoaderID("dialog_import_items_loader");
			g_ugAdmin.setErrorMessageID("dialog_import_items_error");
			g_ugAdmin.setAjaxHideButtonID("dialog_import_upload");
			g_ugAdmin.setSuccessMessageID("dialog_import_items_success");
			
			g_ugAdmin.ajaxRequest("import_cat_items", data, function(response){
				
				jQuery("#dialog_import_items_success").show().html(response.message);
				
				jQuery("#dialog_import_items").dialog("close");
				
				g_objCats.setHtmlListCats(response.htmlCats);
				
				var selectedCatID = g_objCats.getSelectedCatID();
				
				if(catID == selectedCatID)
					g_objItems.setHtmlListItems(response.htmlItems);
				
			}, "#form_import_items");
			
		});
		
	}
	
	
	/**
	 * init quick edit dialog
	 */
	function initEditTitleDialog(){
		
		// set update title onenter function
		jQuery("#dialog_edit_item_title").keyup(function(event){
			if(event.keyCode == 13)
				updateItemTitle();
		});
		
	}
	
	
	
	/**
	 * edit item title function
	 */
	function editItemTitle(){
		
		var arrIDs = g_objItems.getSelectedItemIDs();
		
		if(arrIDs.length == 0)
			return(false);
		
		var itemID = arrIDs[0];
		
		var objItem = g_objItems.getItemByID(itemID);
		
		var title = objItem.data("title");
				
		var objDialog = jQuery("#dialog_edit_item_title");
		
		jQuery("#input_item_title").val(title).focus();
		
		var buttonOpts = {};
		
		buttonOpts[g_ugtext.cancel] = function(){
			jQuery("#dialog_edit_item_title").dialog("close");
		};
		
		buttonOpts[g_ugtext.update] = function(){
			updateItemTitle();
		}
		
		objDialog.data("itemid",itemID);
		
		objDialog.dialog({
			buttons:buttonOpts,
			minWidth:500,
			modal:true,
			open:function(){
				jQuery("#input_item_title").select();
			}
		});
		
	}
	
	
	/**
	 * update item title - on dialog update press
	 */
	function updateItemTitle(){
		var objDialog = jQuery("#dialog_edit_item_title");
		var itemID = objDialog.data("itemid");
		
		var objItem = g_objItems.getItemByID(itemID);
		
		var titleHolder = objItem.find(".item_title");
		
		var newTitle = jQuery("#input_item_title").val();
		var data = {
			itemID: itemID,
			title: newTitle
		};
		
		objDialog.dialog("close");
		
		//update the items
		objItem.data("title", newTitle);
		titleHolder.html(newTitle);
		
		g_manager.ajaxRequestManager("update_item_title",data,g_ugtext.updating_title);		
	}
	
	
	/**
	 * add item request
	 */
	function addItem_request(data){
		
		g_manager.ajaxRequestManager("add_item",data,g_ugtext.adding_item,function(response){
			
			g_objItems.appendItem(response.htmlItem);
			g_objCats.setHtmlListCats(response.htmlCats);
			
		});
		
	}
	
		
	/**
	 * add some item in gallery view
	 */
	function addImageItem(){
		
		var selectedCatID = g_objCats.getSelectedCatID();
		
		if(selectedCatID == -1)
			return(false);
				
		g_ugAdmin.openAddImageDialog(g_ugtext.add_image ,function(urlImage, imageID){
			
			var data = {};
			data.type = "image";
			data.catID = selectedCatID;
			data.urlImage = urlImage;
			
			if(imageID)
				data.imageID = imageID;
			
			addItem_request(data);
			
		}, true);//open add image dialog
	}
	
	
	/**
	 * add video function
	 */
	function addMediaItem(){
		
		
		var selectedCatID = g_objCats.getSelectedCatID();
		
		if(selectedCatID == -1)
			return(false);
		
		g_ugAdmin.openVideoDialog(function(data){
			data.catID = selectedCatID;
			
			addItem_request(data);
		
		});
		
	}
	
		
	
	function ___________ITEMS_RELATED_OPERATIONS________________(){}	//sap for outline	

	
	
	/**
	 * on dialog add item click
	 */
	function addItem(){
		
		var selectedCatID = 0;
		
		if(g_objCats)
			selectedCatID = g_objCats.getSelectedCatID();
		
		var data = {
				title: jQuery("#dialog_add_item_title").val(),
				name: jQuery("#dialog_add_item_name").val(),
				description: jQuery("#dialog_add_item_description").val(),
				catid: selectedCatID
		};
		
		g_ugAdmin.dialogAjaxRequest("dialog_add_item", "add_item", data, function(response){

			var objItem = g_objItems.appendItem(response.htmlItem);
			
			//update categories list
			if(g_objCats)
				g_objCats.setHtmlListCats(response.htmlCats);
			
			g_objItems.selectSingleItem(objItem);
			
			//var urlItem = response["url_item"];
			//location.href = urlItem;
			
		});
		
	}
	
	
	/**
	 * copy / move items
	 */
	function copyMoveItems(data){
		
		//set status text
		var text = g_ugtext.moving_items;
		
		g_manager.ajaxRequestManager("copy_move_items",data , g_ugtext.moving_items, function(response){
			setHtmlListCombo(response);
		});
		
	}
	
	
	/**
	 * get item data from server
	 */
	function getItemData(itemID, callbackFunction){
		
		var data = {itemid:itemID};
		g_manager.ajaxRequestManager("get_item_data",data,g_ugtext.loading_item_data,callbackFunction);
	}
	
	
	/**
	 * get category items
	 */
	function getSelectedCatItems(selectedCatID){
		
		if(!selectedCatID)
			var selectedCatID = 0;
		
		g_objItems.setItemsLoaderState();
		
		var data = {};
		data["catID"] = selectedCatID;
		
		g_ugAdmin.ajaxRequest("get_cat_items",data,function(response){
			
			setItemsHTML(response.itemsHtml);
			
		});
	}
	
	
	/**
	 * remove items
	 */
	function removeItems(arrIDs){
		
		var data = {};
		data.arrItemsIDs = arrIDs;
		
		data.catid = 0;
		
		if(g_objCats)
			data.catid = g_objCats.getSelectedCatID();
		
		g_manager.ajaxRequestManager("remove_items",data, g_ugtext.removing_items, function(response){
			setHtmlListCombo(response);
		});
		
	}
	
	
    /**
     * remove selected items
     */
    function removeSelectedItems(){
		if(g_ugAdmin.isButtonEnabled(this) == false)
			return(false);
		
		if(confirm(g_ugtext.confirm_remove_items) == false)
			return(false);
		
		var arrIDs = g_objItems.getSelectedItemIDs();
		
		removeItems(arrIDs);
    }


	/**
	 * edit item image dialog
	 */
	function editItem_image(objItem){
				
		var itemID = objItem.data("id");
		var itemTitle = objItem.data("title");
				
		objDialog = jQuery("#dialog_edit_item");
		objDialog.data("itemid",itemID);
		
		var dialogTitle = g_ugtext.edit_item + ": " + itemTitle;
		
		var buttonOpts = {};
		
		buttonOpts[g_ugtext.cancel] = function(){
			objDialog.dialog("close");
		};
		
		buttonOpts[g_ugtext.update] = function(){
			
			//validate input:
			var newTitle = jQuery("#ug_item_title").val();
			newTitle = jQuery.trim(newTitle);
			
			if(newTitle == ""){
				jQuery("#dialog_edit_error_message").show().html(g_ugtext.please_fill_item_title);
				return(true);
			}
			
			//update title in html item
			jQuery("#ug_item_"+itemID+" .item_title").html(newTitle);
			jQuery("#ug_item_"+itemID).data("title", newTitle);
			
			jQuery("#dialog_edit_item").dialog("close");
			
			var objItemData = g_settings.getSettingsObject("form_item_settings");
			var data = {itemID: itemID, 
					    params: objItemData};
			
			g_manager.ajaxRequestManager("update_item_data",data,g_ugtext.updating_item_data);
			
		};
		
		jQuery("#dialog_edit_item_loader").show();		
		jQuery("#dialog_edit_item_content").html("");
		jQuery("#dialog_edit_error_message").hide();
		
		objDialog.dialog({
			buttons:buttonOpts,
			minWidth:800,
			modal:true,
			title: dialogTitle,
			open:function(){
				
				getItemData(itemID, function(response){
					
					jQuery("#dialog_edit_item_loader").hide();
					jQuery("#dialog_edit_item_content").html(response.htmlSettings);
					
					//update setting object events
					g_settings.updateEvents();
					
					//try to set focus on description
					jQuery("#dialog_edit_item #description").focus();
					
				});
							
			}
		});
	}
	
	
	/**
	 * edit item media dialog
	 */
	function editItem_media(objItem){
		
		var itemTitle = objItem.data("title");
		
		var data = {};
		data.itemID = objItem.data("id");
		data.dialogTitle = g_ugtext.edit_media_item + ": " + itemTitle;
		data.requestFunction = getItemData;
		
		g_ugAdmin.openVideoDialog(function(response){
			
			g_manager.ajaxRequestManager("update_item_data",response,g_ugtext.updating_item_data,function(responseUpdate){
				var htmlItem = responseUpdate.html_item;
				g_objItems.replaceItemHtml(objItem, htmlItem);
			});
			
		}, data);
				
	}
	
	
	/**
	 * edit item operation. open quick edit dialog
	 */
	function editItem(){
		
		//get selected item
		var arrItems = g_objItems.getSelectedItems();
		if(arrItems.length != 1)
			return(false);
		
		var objItem = jQuery(arrItems[0]);
		var itemType = objItem.data("type");
		
		
		switch(itemType){
			case "image":
				editItem_image(objItem);
			break;
			default:		//edit media item
				editItem_media(objItem);
			break;
		}
		
		
	}
	
	
	
	
	/**
	 * duplicate items
	 */
	function duplicateItems(){
		
		var arrIDs = g_objItems.getSelectedItemIDs();
		if(arrIDs.length == 0)
			return(false);
		
		var selectedCatID = 0;
		
		if(g_objCats)
			selectedCatID = g_objCats.getSelectedCatID();
		
		if(selectedCatID == -1)
			return(false);
		
		var data = {
				arrIDs: arrIDs,
				catID: selectedCatID
		};
		
		g_manager.ajaxRequestManager("duplicate_items",data,g_ugtext.duplicating_items,function(response){
			setHtmlListCombo(response);
		});	
	}
	
	
	/**
	 * update items order in server
	 */
	function updateItemsOrder(){
		
		var catID = g_objCats.getSelectedCatID();
		var arrIDs = g_objItems.getArrItemIDs();
		
		//clear sortby, set to category
		g_manager.clearItemsSortby();	//the value will be changed in db automatically
		var currentSortby = g_objItemsSortby.val();
		g_objCats.updateCatSortby(catID, currentSortby);
		
		trace(currentSortby);
		
		var data = {
					items_order:arrIDs,
					catid:catID
				};
		g_manager.ajaxRequestManager("update_items_order",data,g_ugtext.updating_items_order);
	}
	
	
	/**
	 * set items html, enable objects
	 */
	function setItemsHTML(itemsHtml){
		
		g_objItems.setHtmlListItems(itemsHtml);
		g_objItems.checkSelectRelatedItems();
	}
	
	
	/**
	 * init sortby
	 */
	this.onSortbyChange = function(){
		
			
			var catID = g_objCats.getSelectedCatID();
			if(catID == -1)
				throw new Error("There should be selected category");
			
			var newSortby = g_objItemsSortby.val();
			
			var data = {};
			data.catid = catID;
			data.sortby = newSortby;
			
			g_objItems.setItemsLoaderState();
			
			g_manager.ajaxRequestManager("change_cat_sortby", data, null, function(response){
				
				g_objCats.updateCatSortbyData(catID, newSortby);
				
				setItemsHTML(response.itemsHtml);
				
			});
			
		
	}
	
	/**
	 * init sortby
	 */
	function initSortby(){
		g_objItemsSortby = jQuery("#um_select_sortby_items");
	}
	
	/**
	 * init the actions
	 */
	this.init = function(objManager){
		g_manager = objManager;
		
		g_objCats = g_manager.getObjCats();
		g_objItems = g_manager.getObjItems();
		
		g_objItems.setSpacesBetween(15,15);
		
		g_manager.initItems();
		
		initItems();
		
		initSortby();
	}
	
	
}