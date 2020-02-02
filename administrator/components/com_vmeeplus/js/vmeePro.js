dojo.require("dojo.data.ItemFileReadStore");
dojo.require("dojox.data.QueryReadStore");
dojo.require("dojox.grid.EnhancedGrid");
dojo.require("dojo.parser");
dojo.require("dojox.data.dom");

var conditionsStore;
var conditionsGrid;
var reportsAndPresets;
var conditionsData = new Array();


function refreshGrid() {
	conditionsGrid.store.close();
	conditionsGrid.sort();
}

function showDetailsForCurrentCondition() {
	var items 	= conditionsGrid.selection.getSelected();
	if (items != null && items.length > 0 && items[0] != null) {
		dojo.byId('deleteBtn').disabled = false;
	} 
}

function handleRowSelect(e) {
	showDetailsForCurrentCondition();
}

function deleteCondition(id){
	doDelete = confirm("Are you sure you want to delete this condition?");
	if (doDelete) {
		dojo.xhrGet({
			handleAs:	'json',
			url:	vmeeProURL+"&controller=rule&task=deleteCondition&format=raw&conditionId="+id,
			handle:	function(data) {
				if(data.error != ''){
					setErrorMsg(data.error);
				}else{
					resetMessages("Condition deleted!", 'message');
				}
			},
			error:	function() {
				resetMessages("An error occured while contacting the server.", 'error');
			}
		});
	}
}

function doDeleteCondition(id) {
	dojo.xhrGet({
		handleAs:	'json',
		url:	vmeeProURL+"&controller=rule&task=deleteCondition&format=raw&conditionId="+id,
		handle:	function(data) {
			if(data.error != ''){
				resetMessages(data.error, 'error');
			}else{
				resetMessages("Condition deleted", 'message');
			}
			refreshGrid();
		},
		error:	function() {
			resetMessages("An error occured while contacting the server.", 'error');
		}
	});
}

function deleteSelected() {
	var items 	= conditionsGrid.selection.getSelected();
	if (items.length > 0) {
		var item 	= items[0];
		var id 		= conditionsGrid.store.getValue(item, 'conditionId');
		var conditionName	= conditionsGrid.store.getValue(item, 'conditionName');
		doDelete = confirm("Are you sure you want to delete condition "+conditionName+"?");
		if (doDelete) {
			doDeleteCondition(id);
		}
	}
}

function showOperatorsForConditions(widget) {
	selectedIdx = jQuery('#newConditionType')[0].selectedIndex;
	var operators = conditionsData[selectedIdx].operators;
	length = operators.length;
	jQuery('#newConditionOperator').children().remove();
	for( i=0; i < length; i++){
		jQuery('#newConditionOperator').append(jQuery('<option>', { value : operators[i] }).text(operators[i])); 
	}

	jQuery("#newConditionOperator").dropdownchecklist("destroy");
	jQuery("#newConditionOperator").dropdownchecklist({ closeRadioOnClick: true });
	if('values' in conditionsData[selectedIdx]){
		values = conditionsData[selectedIdx].values;
		jQuery("#condvalueplaceholder").html(values);
		if(values.search(/type="text"/) == -1){
			jQuery("#valuesselect").dropdownchecklist({ emptyText: "Please select ...",maxDropHeight: 150, width: 250 });
		}
	}
	else{
		getPossibleValues(selectedIdx, conditionsData[selectedIdx].type);
	}
}

function addNewCondition() {
	var values = jQuery("#valuesselect").val();
	var textvalues = '';
	
	if(typeof(values) == 'undefined' || jQuery.trim(values) == ''){
		resetMessages('Please set the condition value', 'notice');
		return;
	}
	if(jQuery("#valuesselect").is("select")){
		var textvaluesArr = [];
		jQuery('#valuesselect :selected').each(function(i, selected) {
			textvaluesArr[i] = jQuery(selected).text();
		});
		 textvalues = textvaluesArr.join(',');
	}
	else{
		textvalues = values;

	}
	jQuery("#loading").css("display","block");
	dojo.xhrPost({
		handleAs:	'json',
		url:	vmeeProURL+"&controller=rule&task=addNewCondition&format=raw&values="+encodeURIComponent(values)+"&textvalues="+encodeURIComponent(textvalues),
		form:	'newConditionForm',
		handle:	function(data) {
			if(typeof(data.errors) != "undefined"){
				resetMessages(data.errors, 'error');
			}
			else{
				resetMessages("New condition added!", "message");
			}
			refreshGrid();
			jQuery("#loading").css("display","none");
		},
		error:	function() {
			resetMessages("An error occured while contacting the server.", 'error');
		}
	});
}

function populateSelect(selectId, jsonValues) {
	var select_box  = dojo.byId(selectId);
	select_box.options.length = 0;
	dojo.forEach(jsonValues, function(item, i) {
		var j = select_box.options.length++;
		select_box.options[j].value  = item.type;
		select_box.options[j].text   = item.displayName;
    });
	showOperatorsForConditions(select_box);
}


function populateNewConditionSelects() {
	var ruleId = jQuery("#rule_id").val();
	dojo.xhrGet({
		handleAs:	'json',
		url:	vmeeProURL+"&controller=rule&task=getAllCondtionsConf&rule_id="+ruleId,
		handle:	function(data) {
			conditionsData = data;
			
			/*dojo.forEach(conditions, function(item, i) {
				var conditionNames = new Object();
				conditionNames.name = item.name;
				conditionNames.displayName = item.displayName;
				conditionNames.operators = item.operators;
				
				conditionNameArr[i] = conditionNames;
		    });*/
			populateSelect('newConditionType', conditionsData);
			jQuery("#newConditionType").dropdownchecklist({ icon: {},closeRadioOnClick: true, width: 265});
		},
		error:	function() {
			resetMessages("An error occured while contacting the server.", 'error');
		}
	});	
}

dojo.addOnLoad(function() {
	var ruleId =  dojo.byId("rule_id").value;
	conditionsStore = new dojo.data.ItemFileReadStore({
		url: vmeeProURL+"&controller=rule&task=getConditionsStore&rule_id="+ruleId,
		clearOnClose: true
    });
	
	var conditionsLayout = [
	{
		field:	'counter',
		name:	'#',
		width:	'20px'
	},
	{
		field:	'conditionName',
		name:	'Name',
		width:	'150px'
	},
	/*{
		field:	'conditionClass',
		name:	'Class',
		width:	'150px'
	},*/
	{
		field:	'conditionOperator',
		name:	'Operator',
		width:	'150px'
	},
	{
		field:	'conditionValue',
		name:	'Value',
		width:	'auto',
		hidden: true	
	},
	{
		field:	'conditionTextValue',
		name:	'textValue',
		width:	'auto'
	}
	];
	
	conditionsGrid = new dojox.grid.DataGrid({
        query: {
			conditionId: '*'
        },
        store: conditionsStore,
        clientSort: true,
        structure: conditionsLayout,
        selectionMode: 'single'
    },
    
    document.createElement('div'));
    dojo.byId("conditionsGrid").appendChild(conditionsGrid.domNode);

    dojo.connect(conditionsGrid, "onRowClick", handleRowSelect);
    dojo.connect(conditionsStore, "onFetchComplete", showDetailsForCurrentCondition);
    // Call startup, in order to render the grid:
    conditionsGrid.startup();
    
    // Populate the report select boxes
    populateNewConditionSelects();
    jQuery("#newConditionType").change(showOperatorsForConditions);
});

/*function activatePlugin() {
	dojo.xhrGet({
		handleAs:	'json',
		url:	automailURL+"&task=activatePlugin",
		handle:	function(data) {
			pluginStatus = dojo.byId('pluginStatus');
			if (data.success == true) {
				setSuccessMsg("Plugin enabled!");
				clearErrorMessage();
			} else {
				clearSuccessMessage();
				setErrorMsg("Problems enabling plugin.");
			}
		},
		error:	function() {
			setErrorMsg("An error occured while contacting the server.");
		}
	});
}*/

function getPossibleValues(idx,type){
	jQuery("#loading").css("display","block");
	dojo.xhrGet({
		handleAs:	'json',
		url:	vmeeProURL+"&controller=rule&task=getConditionValuesFormatted&conditionType="+type,
		handle:	function(data) {
			conditionsData[selectedIdx].values = data;
			jQuery("#condvalueplaceholder").html(data);
			if(data.search(/type="text"/) == -1){
				jQuery("#valuesselect").dropdownchecklist({ emptyText: "Please select ...",maxDropHeight: 150, width: 250 });
			}
			jQuery("#loading").css("display","none");
		},
		error:	function() {
			resetMessages("An error occured while contacting the server.", 'error');
		}
	});	
}

function resetMessages(msg, type){
	//check if there is not already a system message dive
	if (jQuery("#system-message").length == 0){
		  // create the div
		jQuery('<dl/>', {
		    id: 'system-message'
		}).insertAfter('#toolbar-box');
	}
	else{
		//div already exists, make sure it is empty
		jQuery("#system-message").empty();
	}
	html = '<dt class="message">Message</dt>';
	switch(type){
	case 'message':
		html += '<dd class="message"><ul><li>'+ msg  +'</li></ul></dd>';
		break;
	case 'notice':
		html += '<dd class="notice"><ul><li>'+ msg  +'</li></ul></dd>';
		break;
	case 'error':
		html += '<dd class="error"><ul><li>'+ msg  +'</li></ul></dd>';
		break;
	default:
		html += '<dd class="message"><ul><li>'+ msg  +'</li></ul></dd>';
	}
	jQuery("#system-message").append(html);
}