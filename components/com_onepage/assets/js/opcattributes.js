var OPCCart = {
	setproducttype2: function(id) {
		
		
		var cart_key = id.split('atr_').join(''); 
		var d = document.getElementById('atr_switch_'+cart_key); 
		if (d != null)
		{
			d.value = 1; 
		}
		
		
		//OPCCart.setproducttype(d, product_id); 
		var datas = jQuery('.opc_atr_'+cart_key); 
		var query = '&'; 
		datas.each(function() { 
				 
				  if ((typeof this.name != 'undefined') && (typeof this.value != 'undefined'))
				  {
				    //stAn - no other characters then & have to be encoded here, all are handled by apache and other systems
				    query += '&'+this.name+'='+this.value.split("&").join("%26");
					
					if (this.name == 'cart_virtuemart_product_id') cart_id = datas[i].value; 
				  }
		}); 
				
				console.log(query); 
				Onepage.updateProductAttributes(query); 
		
	},
			setproducttype : function (form, id) {
				form.view = null;
				
				//orignal: datas = form.serialize();
				
				var datas = form.serializeArray(); 
				console.log(datas); 
				var query = ''; 
				var cart_id = id; 
				query += '&option=com_onepage&nosef=1&task=opc&view=opc&controller=opc&cmd=updateattributes&tmpl=component&virtuemart_product_id[0]='+id+'&format=opchtml';
				for (var i=0; i<datas.length; i++)
				{
				  if (datas[i].name != 'undefined')
				  {
				    //stAn - no other characters then & have to be encoded here, all are handled by apache and other systems
				    query += '&'+datas[i].name+'='+datas[i].value.split("&").join("%26");
					
					if (datas[i].name == 'cart_virtuemart_product_id') cart_id = datas[i].value; 
				  }
				}
				
				cart_id = cart_id.split('::').join('___').split(';').join('__').split(':').join('_'); 
				
			   
				
				
				
				
				
				
				Onepage.op_log(query); 
				
				Onepage.op_runSS(null, null, true, 'updateattributes'+query, true); 
				
				return true; 
				
				
				
				
			},


};			

