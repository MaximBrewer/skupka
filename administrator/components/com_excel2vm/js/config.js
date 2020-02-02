jQuery(function() {

    if(typeof Joomla != 'undefined'){
         Joomla.submitbutton = function(pressbutton) {
				if(pressbutton == 'extra' || pressbutton == 'extra_price' || pressbutton == 'price'|| pressbutton == 'cherry_field'  || pressbutton == 'multi'|| pressbutton == 'user_field')
	                extend_field(pressbutton);
				else
					eval(pressbutton+'()');
		 }
     }
     else{
          window['submitbutton'] = function (pressbutton) {
				if(pressbutton == 'extra' || pressbutton == 'extra_price' || pressbutton == 'price'|| pressbutton == 'cherry_field'  || pressbutton == 'multi' || pressbutton == 'user_field')
	                extend_field(pressbutton);
				else
					eval(pressbutton+'()');
		 }
     }

     function sync(){
         jQuery.ajax({
                     url:'index.php?option=com_excel2vm&view=config',
					 type:'GET',
					 data:{task:'sync',rand:Math.random()},
					 dataType: 'json',
					 success:function(data){
                         jQuery("#response").html(data.html);
            			 jQuery("#response_div").css({height:'180px',width:'340px'}).show($notify_show,{},200);
                         var $ecf=data.insert.ecf;
                         var $ep=data.insert.ep;
                         var $ef=data.insert.ef;
                         var $ef=data.insert.ef;
                         $ecf.forEach(function(item){
                             add_field(item.id,item.title,'extra-cart');
                         });
                         $ep.forEach(function(item){
                             add_field(item.id,item.title,'extra-price');
                         });
                         $ef.forEach(function(item){
                             add_field(item.id,item.title,'extra');
                         });
                         data.del.forEach(function(id){
                             jQuery("#"+id).hide();
                         });
					 }
           });
     }

	 function save_config(options){
            if(!options){
               var options = {
			  	target:"#response",
				data:{task:'save_config'},
			    success: response,
			    timeout: 3000
		    	};
            }

			jQuery("#fields_list").val(jQuery("#active").sortable('toArray'));
			jQuery("#new_profile_name").val();
            jQuery('#adminform').ajaxSubmit(options);

		    return false;
     }

     function save_profile(){
            jQuery.ajax({
                     url:'index.php?option=com_excel2vm&view=config',
					 type:'GET',
					 data:{task:'profile_list',rand:Math.random()},
					 dataType: 'html',
					 success:function(data){
                         jQuery("#response").html(data);
            			 jQuery("#response_div").css({height:'180px',width:'340px'}).show($notify_show,{},200);
					 }
           });
     }

        //Отображаем ответ сервера
		function response(text){
			if(text)
				jQuery("#response").html(text);
            //jQuery("#response_div").css({height:'50px',width:'400px'}).show($notify_show,{},500).delay(3000).hide($notify_hide,{},500);
            jQuery("#response_div").css({height:'50px',width:'400px'}).show($notify_show);
			setTimeout(function(){
                 jQuery("#response_div").hide($notify_hide);
			},3000);

		}



		//Добавить пустой столбец
		function empty_field(){
			jQuery.ajax({
                     url:'index.php?option=com_excel2vm&view=config&task=empty_field&rand='+Math.random(),
					 type:'GET',
					 dataType: 'text',
					 error: function(){response($jtext_DATA_NOT_SAVED)},
					 success:function(data){
                         add_field(data,$jtext_EMPTY_COLUMN,"empty");
						 response($jtext_EMPTY_COLUMN+" "+$jtext_ADDED);
					 }
            });
		}

        function custom_field(){
			jQuery.ajax({
                     url:'index.php?option=com_excel2vm&view=config&task=custom_field&rand='+Math.random(),
					 type:'GET',
					 dataType: 'json',
					 error: function(){response($jtext_DATA_NOT_SAVED)},
					 success:function(data){
                         add_field(data.title,$jtext_CUSTOM_COLUMN+" ("+data.title+") - "+$jtext_CUSTOM_COLUMN_TITLE,"custom");
                         add_field(data.units,$jtext_CUSTOM_COLUMN+" ("+data.title+") - "+$jtext_CUSTOM_COLUMN_UNITS,"custom");
                         add_field(data.value,$jtext_CUSTOM_COLUMN+" ("+data.title+") - "+$jtext_CUSTOM_COLUMN_VALUE,"custom");
						 response("Произвольное поле добавлено");
					 }
            });
		}



		//Добавить доп. поле
		function extend_field(field_type){
			jQuery.ajax({
                     url:'index.php?option=com_excel2vm&view=config',
					 type:'GET',
					 data:{task:field_type,rand:Math.random()},
					 dataType: 'html',
					 success:function(data){
                         jQuery("#response").html(data);
            			 jQuery("#response_div").css({height:'180px',width:'320px'}).show($notify_show,{},200);
					 }
           });
		}






    function price_template_change(){
             var val = jQuery("#price_template :selected").val();
			 if(val!=2 && val!=3){
				if(jQuery("#simbol_li").is(':visible'))
	            	jQuery("#simbol_li").hide('drop');
			 }
			 else if(!jQuery("#simbol_li").is(':visible'))
	            jQuery("#simbol_li").show('drop');

			 if(val!=5){
             if(jQuery("#extra_category").is(':visible'))
            	jQuery("#extra_category").hide('drop');
			 }
	         else if(!jQuery("#extra_category").is(':visible'))
	            jQuery("#extra_category").show('drop');

             if(val==7){
                 if(!jQuery(".delimiters_li").is(':visible')){
                     jQuery(".delimiters_li").show('drop');
                 }
             }
             else{
                 if(jQuery(".delimiters_li").is(':visible')){
                     jQuery(".delimiters_li").hide('drop');
                 }
             }

             if(val==6){
                 if(!jQuery(".cat_id_col").is(':visible')){
                     jQuery(".cat_id_col").show('drop');
                 }
             }
             else{
                 if(jQuery(".cat_id_col").is(':visible')){
                     jQuery(".cat_id_col").hide('drop');
                 }
             }
    }

    function auto_unpublish_change(){
        var val = jQuery("input[name=unpublish]:checked").val();
        if(val==1){
            jQuery(".unpublish_cats").show($notify_show);
        }
        else{
            jQuery(".unpublish_cats").hide($notify_hide);
        }
    }

    function auto_reset_change(){
        var val = jQuery("input[name=reset_stock]:checked").val();
        if(val==1){
            jQuery(".reset_cats").show($notify_show);
        }
        else{
            jQuery(".reset_cats").hide($notify_hide);
        }
    }


		jQuery( "#active" ).sortable({
			connectWith: "ul",

			revert: true,

			placeholder: 'ui-state-highlight',
			forcePlaceholderSize: true,
			containment: '#order_table'
		});

		jQuery( "#inactive" ).sortable({
			connectWith: "ul",
			revert: true,
			placeholder: 'ui-state-highlight',
			forcePlaceholderSize: true,
			containment: '#order_table'


		});

		jQuery( "#inactive, #active" ).disableSelection();

        //Сохраняем настройки

        jQuery("#create_profile_form").live("click",function(){
              var options = {
				dataType: 'html',
				data:{task:'create_profile'},
			    success: function(data){
					jQuery("#response").html(data).show($notify_show,500);
					jQuery("#response_div").delay(3000).hide($notify_hide,{},500).delay(3000);
            	}

		    };
            var profile_name=jQuery('#ajax_form :selected').text();
            if(profile_name == $jtext_ADD_NEW){
               profile_name = jQuery("#profile").val();
			   jQuery("#new_profile_name").val(profile_name);
			   jQuery("#profile_id2").val(0);
            }
			else{
			   //alert(jQuery('#ajax_form :selected').val());
               jQuery("#profile_id2").val(jQuery('#ajax_form :selected').val());
			   jQuery("#new_profile_name").val();
			}
			if(!profile_name){
				alert($jtext_INPUT_THE_NAME_OF_THE_NEW_PROFILE);
                return false;
			}

			jQuery("#response").hide($notify_hide,{},300);
			jQuery("#response_div").animate({height:'60px',width:'400px'},500);
            //jQuery('#ajax_form').ajaxSubmit(options);
			save_config(options);
			jQuery("#current_profile").html(profile_name).css({background:'yellow'});

			return false;
        });

        jQuery("#profile_id").live("change",function(){
             if(jQuery("#profile_id :selected").val()){
              	 if(jQuery("#create_new_profile").is(':visible'))
                  jQuery("#create_new_profile").hide($notify_hide);
    		  }
    		  else{
                  jQuery("#create_new_profile").show($notify_show);
    		  }
        });

        //удалить поле
        jQuery(".ui-icon-trash").live("click",function(){
             var parent = jQuery(this).parent('li');
             parent.hide($notify_hide,{},1000);
			 jQuery.ajax({
                     url:'index.php?option=com_excel2vm&view=config&task=delete_field',
					 type:'GET',
					 data:{id : parent.attr('id'),rand:Math.random()},
					 dataType: 'text',
					 error: function(){response($jtext_ERROR_COLUMN_DELETE)},
					 success:function(data){
						 response($jtext_COLUMN_DELETED);
					 }
             });

        });


        jQuery("#select_field_type").live("change",function(){
            if(jQuery("#select_field_type").val()=='clabels'){
                jQuery("#clabel_wrapper").show($notify_show);
            }
            else if(jQuery("#clabel_wrapper").is(':visible')){
                jQuery("#clabel_wrapper").hide($notify_hide);
            }
        });


		//Кнопка закрытия уведомления
		jQuery("#close").click(function(){
             jQuery("#response_div").hide($notify_hide,{},1000);
		});

		jQuery("#price_template").change(function(){
             price_template_change();
		});
        jQuery("input[name=unpublish]").change(function(){
             auto_unpublish_change();
		});
        jQuery("input[name=reset_stock]").change(function(){
             auto_reset_change();
		});
		price_template_change();
        auto_unpublish_change();
        auto_reset_change();
	});

//Отправляем данные для добавления нового столбца
		function add_field_form(){
			var options = {
				dataType: 'json',
			    success: function(obj){
			        if(obj.title=='error'){
                        jQuery("#response").html("<span style='color: #CC0000'>Возникла ошибка при создании поля</span>:<br>"+obj.msg).show($notify_show,500);
					    jQuery("#response_div").delay(3000).hide($notify_hide,{},500);
			        }
                    else{
                        add_field(obj.id,obj.title,obj.type);
    					jQuery("#response").html($jtext_COLUMN_CREATED+" - "+obj.title).show($notify_show,500);
    					jQuery("#response_div").delay(3000).hide($notify_hide,{},500);
                    }

            	},
                error:function(){
                     jQuery("#response").html("<span style='color: #CC0000'>Возникла ошибка при создании поля</span>").show($notify_show,500);
					 jQuery("#response_div").delay(3000).hide($notify_hide,{},500);
                }

		    };
			//jQuery("#response").hide('scale',{},300);
			//jQuery("#response_div").animate({height:'60px',width:'400px'},500);
            jQuery('#ajax_form').ajaxSubmit(options);
			return false;
		}

        //Добавить поле
		function add_field(id,title,type){

            jQuery("#active").append("<li class='"+type+"' id='"+id+"'>"+title+" "+$trash_link+"</li>");
		}