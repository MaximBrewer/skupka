jQuery(document).ready(function(){
     /* Экспорт */
     var reimport_counter=1;
     jQuery("#create_yml").click(function(){
           jQuery("#response_link").html('');
           jQuery("#create_yml").hide('explode',{},1500);
           setTimeout(function(){
	              export_intrval_id = setInterval(function(){
                     getExportStat();
		          },2000);
		   },1000);
           jQuery('#export_form').ajaxSubmit({
                url: 'index.php?option=com_excel2vm&view=yml&task=yml_export&rand='+Math.random(),
                dataType:'json',
			    success: export_success,
	            timeout: 600000,
				error:function(data){
					alert("Произошла ошибка: "+data.statusText+"\n"+data.responseText);

                    jQuery("#create_yml").show('explode',{},1500);
				}
           });

     });
     /* Импорт */
     jQuery("#import_yml").click(function(){
           window.end_of_import=0;
           jQuery("#import_yml").hide('explode',{},1000);
           jQuery("#abort_button").show('slide',{},1000);
           jQuery("#errors").html("");
           yml_import(0);

           setTimeout(function(){
	              intrval_id = setInterval(function(){
                     getStat();
		          },2000);
		   },1000);
     });

     jQuery("#abort_button").click(function() {
            jQuery.ajax({
		        type: "HEAD",
		        async: true,
		        url:'index.php?option=com_excel2vm&view=excel2vm&task=abort_yml&rand='+Math.random(),
		        success: function(){
		        	clearInterval(intrval_id);
                    jQuery("#abort_button").hide('explode',{},1500);
		        }
		    });
        });


});

function export_success(data){
    if(data.status=='ok'){
        jQuery("#response_link").hide();
        jQuery("#response_link").css({'backgroundColor':'#FFCC00'});
        jQuery("#response_link").html(data.msg).show('slide',{},1000).animate({'backgroundColor':'#FFFFFF'},1500);
        jQuery("#create_yml").show('explode',{},1500);
        var end_of_export=true;
    }
    else if(data.status=='timeout'){
        jQuery('#export_form').ajaxSubmit({
                url: 'index.php?option=com_excel2vm&view=yml&task=yml_export&row='+data.row+'&rand='+Math.random(),
                dataType:'json',
			    success: export_success,
	            timeout: 600000,
				error:function(data){
					alert("Произошла ошибка: "+data.statusText+"\n"+data.responseText);

                    jQuery("#create_yml").show('explode',{},1500);
				}
        });
    }
    else{
        alert("Ошибка: "+data.msg);
        jQuery("#create_yml").show('explode',{},1500);
    }
}

function getStat(){
            if(window.end_of_import){
		    	clearInterval(intrval_id);
				return;
			}
            jQuery.ajax({
                 url:'index.php?option=com_excel2vm&view=excel2vm',
                 type:'GET',
                 data:{task:'get_yml_stat',rand:Math.random()},
                 async:false,
                 dataType: 'json',
                 success:function(data){

					  if(data != null){
	                  	  jQuery("#statistics").slideDown();
                 	  }
                      else{
                        return;
                      }

                      jQuery("#row").html(data.cur_row);
                      jQuery("#total_row").html(data.num_row);
                      jQuery("#new").html(data.pn);
                      jQuery("#up").html(data.pu);
                      jQuery("#new_cat").html(data.cn);
                      jQuery("#up_cat").html(data.cu);
                      jQuery("#duration").html(data.time+" c.");

                      jQuery("#product").html(data.cur_prod);
                      jQuery("#last_response").html("<center>"+$jtext_SERVER_LAST_RESPONSE+" "+data.last_response+ $jtext_SECONDS_AGO+"</center>");

                       var speed = parseInt(data.cur_row / data.time);
                       //var speed2 = parseInt(data.cur_row -last_cur_row);
                       var last_cur_row=data.cur_row;
					   var progress = Math.round(100*data.cur_row/data.num_row);
					   var left_time = Math.round(100*data.time/progress)-data.time;
                       jQuery( "#progressbar" ).progressbar({value: progress});
                       if(progress==100){
                          jQuery("#progresspercent").html("<b>Импорт завершен</b>");
                       }
                       else{
                          jQuery("#progresspercent").html("<b>"+progress+"%</b>");
                       	   jQuery("#time_left :hidden").show();
						   jQuery("#speed :hidden").show();
	                       jQuery("#time_left").html("<b>"+$jtext_TIME_LEFT+": "+left_time+" "+$jtext_SECONDS+"</b>");
	                       jQuery("#speed").html("<b>"+$jtext_RATE+": "+speed+" "+$jtext_ROWS_PER_SECOND+"</b>");
	                       //jQuery("#step").html("<b>Строки: +"+speed2+"</b>");
                       }

                       jQuery("#memory").html("<b>"+$jtext_MEMORY_USAGE+": "+data.mem+$jtext_MB+" "+$jtext_FROM+" "+data.mem_total+$jtext_MB+" ("+Math.round(100*data.mem/data.mem_total)+"%)</b>");

					   if(parseInt(data.cur_row) >= parseInt(data.num_row)){
                             clearInterval(intrval_id);
                             var $show_results=jQuery("#show_results:checked").val();
                             jQuery("#results").load("index.php",{option : 'com_excel2vm',task : 'response',show_results:$show_results}).delay(1000);
							 jQuery("#results").show('slide',{},1000);
							 //jQuery("#import_button").show('slide',{},1000);
					   }

                       if(data.timeout){

                          if(window.reimport_counter>0){
                             window.reimport_counter--;
                          }
                          else{
                             yml_import(1);
                             window.reimport_counter=3;
                          }
                       }
                       else if(data.last_response>15){
                          if(window.reimport_counter>0){
                             window.reimport_counter--;
                          }
                          else{
                             yml_import(1);
                             window.reimport_counter=3;
                          }
                       }
                       else{
                           window.reimport_counter=1;
                       }
                  }
           });
        }

function getExportStat(){
            if(window.end_of_export){
		    	clearInterval(window.export_intrval_id);
				return;
			}
            jQuery.ajax({
                 url:'index.php?option=com_excel2vm&view=excel2vm',
                 type:'GET',
                 data:{task:'get_yml_export_stat',rand:Math.random()},
                 async:false,
                 dataType: 'json',
                 success:function(data){

					  if(data != null){
	                  	  jQuery("#export_statistics").slideDown();
                 	  }
                      else{
                        return;
                      }

                      jQuery("#export_row").html(data.cur_row);
                      jQuery("#export_total_row").html(data.num_row);
                      jQuery("#export_duration").html(data.time+" c.");

                       var speed = parseInt(data.cur_row / data.time);
                       //var speed2 = parseInt(data.cur_row -last_cur_row);
                       var last_cur_row=data.cur_row;
                       var last_total_row=data.num_row;
					   var progress = Math.round(100*data.cur_row/data.num_row);
					   var left_time = Math.round(100*data.time/progress)-data.time;
                       jQuery( "#export_progressbar" ).progressbar({value: progress});
                       if(progress==100){
                          jQuery("#export_progresspercent").html("<b>Экспорт завершен</b>");
                          jQuery("#export_time_left").html("<b>"+$jtext_TIME_LEFT+": "+left_time+" "+$jtext_SECONDS+"</b>");
                       }
                       else{
                          jQuery("#export_progresspercent").html("<b>"+progress+"%</b>");
                       	   jQuery("#export_time_left :hidden").show();
						   jQuery("#export_speed :hidden").show();
	                       jQuery("#export_time_left").html("<b>"+$jtext_TIME_LEFT+": "+left_time+" "+$jtext_SECONDS+"</b>");
	                       jQuery("#export_speed").html("<b>"+$jtext_RATE+": "+speed+" "+$jtext_ROWS_PER_SECOND+"</b>");
	                       //jQuery("#step").html("<b>Строки: +"+speed2+"</b>");
                       }
                       if(data.is_end==1){
                            clearInterval(window.export_intrval_id);
                       }
                  }
           });
        }

        function yml_import($reimport){
            jQuery('#import_form').ajaxSubmit({
                url: 'index.php?option=com_excel2vm&view=yml&task=yml_import&rand='+Math.random(),
                dataType:'json',
                data:{reimport:$reimport},
			    success: function (data){
                    if(data.status=='ok'){
                        jQuery("#abort_button").hide('slide',{},1000);
                        jQuery("#import_yml").show('explode',{},1000);
                        getStat();
                        window.end_of_import=1;

                        //alert(data.msg);
                    }
                    else if(data.status=='timeout'){
                        yml_import(1);
                        if(data.errors){
                             jQuery("#errors").html("<h3>Ошибки:</h3>"+data.errors);
                        }
                    }
                    else{
                        window.end_of_import=1;
                        jQuery("#abort_button").hide('slide',{},1000);
                        jQuery("#import_yml").show('explode',{},1000);
                        alert("Ошибка: "+data.msg);

                    }

			    },
	            timeout: 600000,
				error:function(data){
                    if(data.statusText!='Gateway Time-out'){
                        window.end_of_import=1;
                        jQuery("#abort_button").hide('slide',{},1000);
                        jQuery("#import_yml").show('explode',{},1000);
                        alert("Произошла ошибка: "+data.statusText);
                        jQuery("#errors").html(data.responseText);
    				}
				}
           });
        }