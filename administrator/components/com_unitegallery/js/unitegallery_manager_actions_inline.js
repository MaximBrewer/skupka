function UGManagerActionsInline(){
	 
	var t = this;
	var g_objCats, g_objItems, g_manager, g_objDialogEdit;
	var g_objWrapper, g_objSettings, g_objSettingsWrapper;
	var g_imageField = null;		//field that set to be image for html output
	
	
	/**
	 * on item button click
	 */
	this.runItemAction = function(action, data){
		
		switch(action){
			case "add_images":
				onAddImagesClick();
			break;
			case "add_item":
				openAddEditItemDialog();
			break;
			case "edit_item":
				onEditItemClick();
			break;
			case "update_order":	//do nothing
			break;
			case "remove_items":
				g_objItems.removeSelectedItems();
			break;
			case "duplicate_items":
				g_objItems.duplicateSelectedItems();
			break;
			default:
				trace("wrong action: "+action)
			break;
		}
	}
	
	
	/**
	 * get items data
	 */
	this.getItemsData = function(){
		var objItems = g_objItems.getObjItems();
		
		var arrItems = [];
		jQuery.each(objItems, function(index, item){
			var objItem = jQuery(item);
			var params = objItem.data("params");
			arrItems.push(params);
		});
		
		return(arrItems);
	}
	
	
	/**
	 * set items from data
	 */
	this.setItemsFromData = function(arrItems){
		
		g_objItems.removeAllItems(true);
		
		if(typeof arrItems != "object")
			return(false);
		
		jQuery.each(arrItems, function(index, itemData){
			appendItem(itemData, true);
		});
		
		g_objItems.updateAfterHtmlListChange();
	}
	
	
	/**
	 * on add images click
	 */
	function onAddImagesClick(){
		
		g_ugAdmin.openAddImageDialog("Add Images",function(response){
			
			jQuery.each(response, function(index, item){
				var urlImage = item.url;
				urlImage = g_ugAdmin.urlToRelative(urlImage);
				
				addItemFromImage(urlImage);
			});
			
		},true);
		
	}
	
	
	/**
	 * open edit item dialog
	 */
	function onEditItemClick(){
		
		var objItem = g_objItems.getSelectedItem();
		if(!objItem)
			throw new Error("No items found");
				
		openAddEditItemDialog(true, objItem);
	}
	
	
	/**
	 * open add item dialog
	 */
	function openAddEditItemDialog(isEdit, objItem){
		
		if(!isEdit)
			var isEdit = false;
		
		var buttonText = g_ugtext.add_item;
		var titleText = g_ugtext.add_item;
		
		if(isEdit){
			var params = objItem.data("params");
			g_objDialogEdit.data("item", objItem);
			
			buttonText = g_ugtext.update_item;
			titleText = g_ugtext.edit_item;
		}
		
		var buttonOpts = {};
		
		buttonOpts[g_ugtext.cancel] = function(){
			g_objDialogEdit.dialog("close");
		};

		buttonOpts[buttonText] = function(){
			
			if(isEdit == false)
				addItemFromDialog();
			else{
				var objItem = g_objDialogEdit.data("item");
				updateItemFromDialog(objItem);
			}
			
			g_objDialogEdit.dialog("close");
		};
		
		g_objDialogEdit.dialog({
			dialogClass:"unite-ui",			
			buttons:buttonOpts,
			title: titleText,
			minWidth:600,
			modal:true,
			open:function(){
				
				if(isEdit == false)	//add
					g_objSettings.clearSettings();
				else				//edit
					g_objSettings.setValues(params);
			}
			
		});
		
	}
	
	
	
	/**
	 * generate item title
	 */
	function generateItemTitle(){
		var numItems = g_objItems.getNumItems()+1;
		var title = "Item " + numItems;
		return(title);
	}
	
	/**
	 * get title from params
	 * @param params
	 */
	function getTitleFromParams(params){
		
		if(params.hasOwnProperty("title") == false)
			return(null);
		
		var title = params["title"];
		if(!title)
			return(null);
		
		return(title);
	}
	
	
	/**
	 * generate item html
	 */
	function generateItemHtml(params, id){
		
		//set title
		var title = generateItemTitle();
		var altTitle = getTitleFromParams(params);
		
		if(altTitle)
			title = altTitle;
		
		var description = "";
		
		//set description style
		var urlImage = null;
		
		if(params.hasOwnProperty("thumb"))
			urlImage = jQuery.trim(params.thumb);
		
		if(!urlImage && g_imageField && params.hasOwnProperty(g_imageField))
			urlImage = jQuery.trim(params[g_imageField]);
		
		var descStyle = "";
		if(urlImage){
			urlImage = g_ugAdmin.urlToFull(urlImage);
			descStyle = "style=\"background-image:url('"+urlImage+"')\"";
		}
		
		//generatet id
		if(id){
			var itemID = g_objItems.getItemIDFromID(id);
		}else{
			var objID = g_objItems.getObjNewID();
			var id = objID.id;
			var itemID = objID.itemID;
		}
		
		
		var $htmlItem = "";
		$htmlItem += "<li id='" + itemID + "' data-id='"+id+"' data-title="+title+" >";
		$htmlItem += "	<div class=\"ug-item-title unselectable\" unselectable=\"on\">"+title+"</div>";
		$htmlItem += "	<div class=\"ug-item-description unselectable\" unselectable=\"on\" "+descStyle+">"+description+"</div>";
		$htmlItem += "	<div class=\"ug-item-icon unselectable\" unselectable=\"on\"></div>";
		$htmlItem += "</li>";
		
		return($htmlItem);
	}
	
	
	/**
	 * update item html from params
	 */
	function updateItemHtml(objItem, params){
		
		var id = objItem.data("id");
		
		var html = generateItemHtml(params, id);
		
		var objNewItem = g_objItems.replaceItemHtml(objItem, html);
		
		objNewItem.data("params", params);
		
	}
	
	
	/**
	 * append item from values
	 * @param objValues
	 */
	function appendItem(objValues, noUpdate){
		var htmlItem = generateItemHtml(objValues);
		var objItem = g_objItems.appendItem(htmlItem, noUpdate);
		objItem.data("params", objValues);
	}
	
	
	/**
	 * add item from dialog
	 */
	function addItemFromDialog(){
		var objValues = g_objSettings.getSettingsValues();
		appendItem(objValues);
	}
	
	
	/**
	 * add item from image
	 */
	function addItemFromImage(urlImage){
		var objInfo = g_ugAdmin.pathinfo(urlImage);
		var params = {};
		params.title = objInfo.filename;
		params.image = urlImage;
		
		appendItem(params);
				
	}
	
	
	/**
	 * update item from dialog
	 */
	function updateItemFromDialog(objItem){
		
		var params = g_objSettings.getSettingsValues();
		objItem.data("params", params);
		updateItemHtml(objItem, params);
		
	}
	
	
	/**
	 * set thumb field for viewing the thumb
	 */
	function init_setImageField(){
		
		var arrFieldNames = g_objSettings.getFieldNamesByType("image");
		if(arrFieldNames.length == 0)
			return(false);

		g_imageField = arrFieldNames[0];
		
		if(arrFieldNames.length > 1){
			if(jQuery.inArray("image",arrFieldNames) != -1)
				g_imageField == "image";
		}
		
	}
	
	
	/**
	 * init the actions
	 */
	this.init = function(objManager){
		g_manager = objManager;
		
		g_manager.initItems();
		
		g_objCats = g_manager.getObjCats();
		g_objItems = g_manager.getObjItems();
		g_objWrapper = g_manager.getObjWrapper();
		
		g_objDialogEdit = g_objWrapper.find(".ug-dialog-edit-item");
		g_objSettingsWrapper = g_objWrapper.find(".ug-item-config-settings");
		
		g_objSettings = new UniteSettingsUC();
		g_objSettings.init(g_objSettingsWrapper);
		
		init_setImageField();
		
		//init from data
		var arrInitItems = g_objWrapper.data("init-items");
		
		if(arrInitItems && typeof arrInitItems == "object")
			t.setItemsFromData(arrInitItems);
		
	}
	
}