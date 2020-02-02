/**
 * Support for editable admin lists.
 */

var editableRows = new Class ({

	addNewRow: function(table) //add new empty row (clone last one) with array input names incremented by 1
	{
		lastRow = $(table).getElement('tbody').getLast('tr'); //get model row from last row in table
		
		var footer = $(table).getElement('tfoot');
		var modelRow=null;
		
		if (footer!=null)
			modelRow = footer.getElement('tr');
		
		if (modelRow!=null) //get new row from special "model row" in tfooter
			newRow = modelRow.clone();
		else{
			if (lastRow==null)
				return false;
			else
				newRow = lastRow.clone(); //get model row from last row in table
		}
		
		var index = 0;
		
		//get index of new input names
		if (lastRow==null)
			index = 0; //1.st row
		else{
			lastRow.getElements('input').each( function(el){ //get inputs index of last row
				parts = el.name.match(/^(.+)\[(.+)\]$/);
				if (parts!=null)
					index = parts[2]*1+1;
			});
		}
		
		newRow.getElements('input,select,textarea').each( //incerement inputs keys indexes
			function(el){
				parts = el.name.match(/^(.+)\[(.+)\]$/); //already have indexes (not model) = rewrite it
				if (parts!=null){
					el.name=parts[1]+'['+index+']';
	
					if (el.type!=undefined && el.type=="text")
						el.value="";
				}
				else { //from "model" row
					parts = el.name.match(/^(.+)_model$/);
					if (parts!=null)
						el.name=parts[1]+'['+index+']';
				}
			}
		);
		
		newRow.inject($(table).getElement('tbody'),'bottom');
	}
});

vmiRows = new editableRows();