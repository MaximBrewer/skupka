jQuery.fn.shiftSelectable = function() {
    var lastChecked,
        $boxes = this;

    $boxes.click(function(evt) {
        if(!lastChecked) {
            lastChecked = this;
            return;
        }

        if(evt.shiftKey) {
            var start = $boxes.index(this),
                end = $boxes.index(lastChecked);
            $boxes.slice(Math.min(start, end), Math.max(start, end) + 1)
                .attr('checked', lastChecked.checked)
                .trigger('change');
        }

        lastChecked = this;
    });
};

jQuery(document).ready(function(){

      if($uploaded_files >0){
          jQuery("#uploaded_files_table").tablesorter({sortList: [[3,1]],headers: {0:{sorter: false},4:{sorter: false},5:{sorter: false}}});
          jQuery('#uploaded_files_table input[type="checkbox"]').shiftSelectable();
      }
      else{
          jQuery("#uploaded_files_table").tablesorter();
      }

       jQuery(".delete").live('click',function(){
           var $id=jQuery(this).attr('rel');
           var $file=jQuery(this).attr('file');
           jQuery.ajax({
                             url:'index.php?option=com_excel2vm&rand='+Math.random(),
           					 type:'GET',
           					 data:{task:'delete',file:$file},
           					 dataType: 'text',
           					 success:function(data){
           					     if(data){
                                    alert(data);
           					     }
                                 else{
                                    jQuery("#row_"+$id).hide();
                                 }
           					 },
                             error:function(data){
                                 alert("Произошла ошибка:\n"+data.responseText);
                             }
                   });
       });

       jQuery(".delete_all").on('click',function(){
                 if(!confirm("Уверены, что хотите удалить все файлы импорта?")){
                      return false;
                 }
                 jQuery.ajax({
                                   url:'index.php?option=com_excel2vm&view=excel2vm&rand='+Math.random(),
                 					 type:'GET',
                 					 data:{task:'delete_all'},
                 					 dataType: 'text',
                 					 success:function(data){
                 					     if(data){
                                          alert(data);
                 					     }
                                       else{
                                          jQuery("#uploaded_files_tbody").html("");
                                       }
                 					 },
                                   error:function(data){
                                       alert("Произошла ошибка:\n"+data.responseText);
                                   }
                         });
       });



		  var end_of_import = false;
		  var intrval_id;
		  var interval_counter=0; //Количество циклов статистики
		  var buffer_counter= $reimport_time; //В это в ремя не страбатывает перезапуск
          var last_cur_row=0;
		  var timeouted=0;
          var reimport_counter=0;
          jQuery("#xls_file").val('');
          function reimport(){
          	 jQuery("#xls_file").attr("disabled","disabled");
             jQuery('#import_form').ajaxSubmit({
                url: 'index.php?option=com_excel2vm&task=import&reimport=1&rand='+Math.random(),
			    success: showResponse,
	            timeout: 300000,
				error:function(data){
					timeouted=1;
					if(data.statusText!='Gateway Time-out'){
						timeouted=1;
						reimport_counter++;
						if(reimport_counter<$reimport_num)
							reimport();

						else{
	                        clearInterval(intrval_id);
		                    options.url+='&reimport=1';
							jQuery("#results").html('<b><font color="#FF0000">'+$jtext_IMPORT_ERROR+'</font></b><br />'+data.responseText).show('bounce');
							jQuery("#import_button").show('slide',{},1000).val($jtext_IMPORT_CONTINUE);
						}

					}
				}

             });

          }

		  var options = {
		    url: 'index.php?option=com_excel2vm&task=import&rand='+Math.random(),
		    success: showResponse,
            timeout: 900000,
			error:function(data){
				timeouted=1;
				if(data.statusText!='Gateway Time-out'){
                    reimport();
				}
			}
		  };

          var upload_options = {
		    url: 'index.php?option=com_excel2vm&task=upload&rand='+Math.random(),
		    success: upload_complete,
            timeout: 900000,
            uploadProgress: OnProgress,
			error:upload_error
		  };

          function upload_error(data){
              jQuery("#import_button").show('explode',{},1500);
              jQuery("#results").html("<span style='color: #FF3333'>Ошибка при загрузке файла. Загрузите файл по ФТП в папку "+$JURI_root+"administrator/components/com_excel2vm/xls/</span>").show('slide',{},1000);
              jQuery("#import_started").html("Статус: <span style='color: #FF3333'>Ошибка при загрузке файла</span>");
          }

          jQuery("#results").hide();
          jQuery("#statistics").hide();

		  jQuery('#import_button').click(function(){  //Запуск закачки файлов

               jQuery("#results").hide();
               jQuery("#statistics").hide();
               jQuery("#last_response").hide();

               jQuery("#results").html('');
               jQuery("#import_button").hide('explode',{},1500);
               jQuery("#filename").html('');
               jQuery("#row").html(0);
               jQuery("#total_row").html(0);
               jQuery("#new").html(0);
               jQuery("#up").html(0);
               jQuery("#new_cat").html(0);
               jQuery("#up_cat").html(0);
               jQuery("#duration").html("0 c.");
               jQuery("#category").html("");
               jQuery("#product").html("");


               if(jQuery("#xls_file").val()){
                   jQuery('#upload_form').ajaxSubmit(upload_options);
                   jQuery("#import_started").html("Статус: Загрузка файла");
                   jQuery("#import_started").slideDown();
               }
               else{
                   start_import();
               }
		  });

        jQuery("#abort_button").click(function() {
            jQuery.ajax({
		        type: "HEAD",
		        async: true,
		        url:'index.php?option=com_excel2vm&view=excel2vm&task=abort&rand='+Math.random(),
		        success: function(){
		        	clearInterval(intrval_id);
                    jQuery("#abort_button").hide('explode',{},1500);
					if(timeouted){
						setTimeout(function(){
	                        jQuery("#results").load("index.php",{option : 'com_excel2vm',task : 'response'}).delay(1000);
							jQuery("#results").show('slide',{},1000);
						},5000);

					}
		        }
		    });
        });

        function upload_complete(data){ //Загрузка файлов завершена
             if(data!='Ok'){
                jQuery("#results").html(data).show('slide',{},1000);
                return false;
             }
             jQuery.ajax({
                               url:'index.php?option=com_excel2vm&view=excel2vm&rand='+Math.random(),
          					 type:'GET',
          					 data:{task:'update_files'},
          					 dataType: 'html',
          					 success:function(data){
                                       jQuery("#uploaded_files_tbody").html(data);
                                       var name;
                                        for (var i = 0; i < jQuery("#xls_file").get(0).files.length; ++i) {
                                            name=jQuery("#xls_file").get(0).files[i].name;
                                            jQuery("input[value='"+name+"']").prop('checked', true);
                                        }
                                        jQuery("#xls_file").val('');
                                        start_import();
                                        jQuery("#uploaded_files_table").trigger("update");


          					 }
             });
        }


        function start_import() {
		  	  reimport_counter=0;
			  end_of_import=false;

			  jQuery("#abort_button").show('slide',{},1500);
			  jQuery('#import_form').ajaxSubmit(options);
			  setTimeout(function(){
                  //jQuery("#import_started").slideUp();
                  //jQuery("#statistics").slideDown();
	              intrval_id = setInterval(function(){

                     getStat();
		          },2000);
			  },1000);

			  return false;
		  }

        function getStat(){
            if(buffer_counter< $reimport_time){
                jQuery("#reimport_counter").html("Перезапуск через: "+buffer_counter);
            }
            else{
                 jQuery("#reimport_counter").html('');
            }
            if(end_of_import){
		    	clearInterval(intrval_id);
				return;
			}
            jQuery.ajax({
                 url:'index.php?option=com_excel2vm&view=excel2vm',
                 type:'GET',
                 data:{task:'get_stat',rand:Math.random()},
                 dataType: 'json',
                 success:function(data){

					  if(data != null){
					  	  if(data.status){
                             jQuery("#import_started").html("Статус: "+data.status);
					  	  }
						  else{
	                          jQuery("#import_started:visible").slideUp();
	                  		  jQuery("#statistics").slideDown();
						  }

                 	  }
                      else{
                        return;
                      }

                      if(parseInt(data.last_response)>5 && parseInt(data.cur_row)!=-1){
                              buffer_counter--;
                              console.log("Перезапуск через "+buffer_counter+" с.");
                      }


                      if(parseInt(data.last_response)<=5){
                          buffer_counter=$reimport_time;
                      }
                      var file_num =parseInt(data.file_index)+1;
                      jQuery("#filename").html(data.filename+"("+file_num+$jtext_IMPORT_OF+" "+data.total_files+")");
                      jQuery("#row").html(data.cur_row);
                      jQuery("#total_row").html(data.num_row);
                      jQuery("#new").html(data.pn);
                      jQuery("#up").html(data.pu);
                      jQuery("#new_cat").html(data.cn);
                      jQuery("#up_cat").html(data.cu);
                      jQuery("#duration").html(data.time+" c.");
                      jQuery("#category").html(data.cur_cat);
                      jQuery("#product").html(data.cur_prod);
                      jQuery("#last_response").html("<center>"+$jtext_SERVER_LAST_RESPONSE+" "+data.last_response+ $jtext_SECONDS_AGO+"</center>");


                      jQuery("#file_index").val(data.file_index);


                       var speed = parseInt(data.cur_row / data.time);
                       var speed2 = parseInt(data.cur_row -last_cur_row);
                       last_cur_row=data.cur_row;
					   var progress = Math.round(100*data.cur_row/data.num_row);
					   var left_time = Math.round(100*data.time/progress)-data.time;
                       jQuery( "#progressbar" ).progressbar({value: progress});
                       if(progress==100){
                          jQuery("#progresspercent").html("<b>Импорт завершен</b>");
                       }
                       else
                          jQuery("#progresspercent").html("<b>"+progress+"%</b>");

                       if(data.total_files==1){
                       	   jQuery("#time_left :hidden").show();
						   jQuery("#speed :hidden").show();
	                       jQuery("#time_left").html("<b>"+$jtext_TIME_LEFT+": "+left_time+" "+$jtext_SECONDS+"</b>");
	                       jQuery("#speed").html("<b>"+$jtext_RATE+": "+speed+" "+$jtext_ROWS_PER_SECOND+"</b>");
	                       jQuery("#step").html("<b>Строки: +"+speed2+"</b>");
                       }
                       else{
                           jQuery("#time_left :visible").hide();
						   jQuery("#speed :visible").hide();
						   jQuery("#step :visible").hide();
                       }

                       jQuery("#memory").html("<b>"+$jtext_MEMORY_USAGE+": "+data.mem+$jtext_MB+" "+$jtext_FROM+" "+data.mem_total+$jtext_MB+" ("+Math.round(100*data.mem/data.mem_total)+"%)</b>");

					   if(parseInt(data.cur_row) >= parseInt(data.num_row) && timeouted){
                             clearInterval(intrval_id);
                             var $show_results=jQuery("#show_results:checked").val();
                             jQuery("#results").load("index.php",{option : 'com_excel2vm',task : 'response',show_results:$show_results}).delay(1000);
							 jQuery("#results").show('slide',{},1000);
							 //jQuery("#import_button").show('slide',{},1000);
					   }


                       if(data.last_response > 7  && buffer_counter <= 0){
                       	    buffer_counter=$reimport_time;
                            jQuery("#reimport_counter").html("");
							options.url='index.php?option=com_excel2vm&task=import&reimport=1';
                            console.log("Перезапуск №"+parseInt(reimport_counter)+1);
                            jQuery('#import_form').ajaxSubmit(options);

                       }
                  }
           });
        }

		function showResponse(responseText, statusText)  {
            if(responseText=='timeout'){
                reimport();
                return false;
            }

            jQuery("#results").html(responseText).show('slide',{},1000);
			clearInterval(intrval_id);
			setTimeout(function(){end_of_import = true;},1000);


            getStat();

			jQuery("#statistics").slideDown();
            jQuery("#import_button").show('slide',{},1000).val($jtext_START_IMPORT);
			jQuery("#abort_button").hide('explode',{},1500);
            reimport_counter=0;
			options.url='index.php?option=com_excel2vm&task=import';
			jQuery("#xls_file").attr("disabled",false);
            if(!$save_checked){
               jQuery("#uploaded_files_tbody input").prop('checked', false);
            }


		}

		jQuery("input[name=make_thumb]:radio").change(function(){
			if(jQuery(this).val()==1)
                 jQuery("#thumb_set").show('fold');
			else
                 jQuery("#thumb_set").hide('fold');
		});


		 if(jQuery("input[name=make_thumb]:checked").val()==1)
               jQuery("#thumb_set").show('fold');
		 else
               jQuery("#thumb_set").hide('fold');

         jQuery(".spoiler").live("click",function(){
			 jQuery("#spoiler_span").slideToggle("slow");
			  jQuery(this).toggleClass("active");
		 });
         function OnProgress(event, position, total, percentComplete){
            if(percentComplete==100 && position!=total){
               percentComplete=99;
            }
            jQuery("#import_started").html("Статус: Загрузка файла (" +percentComplete+"%)<br>Загружено: "+getSize(position)+" из "+getSize(total));
            if(!jQuery("#import_started").is(":visible") ){
                jQuery("#import_started").slideDown();
            }
            //console.log(percentComplete);
          }

          function getSize($bytes){
             if($bytes<1024)
             	  return $bytes+" B<br>";
             else if($bytes<1024*1024)
             	  return Math.round($bytes/1024)+" KB<br>";
             else
             	  return parseFloat($bytes/(1024*1024)).toFixed(2)+" MB<br>";
          }

    });