jQuery(function() {
      jQuery(".chosen-select").chosen();
      function update_files(){
        jQuery.ajax({
                                   url:'index.php?option=com_excel2vm&view=export&rand='+Math.random(),
                					 type:'GET',
                					 data:{task:'update_files'},
                					 dataType: 'html',
                					 success:function(data){
                                        jQuery("#uploaded_files_tbody").html(data);
                					 }
        });
      }
      jQuery(".delete").live('click',function(){
                 var $id=jQuery(this).attr('rel');
                 var $file=jQuery(this).attr('file');
                 jQuery.ajax({
                                   url:'index.php?option=com_excel2vm&view=export&rand='+Math.random(),
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
                 if(!confirm("Уверены, что хотите удалить все файлы экспорта?")){
                      return false;
                 }
                 jQuery.ajax({
                                   url:'index.php?option=com_excel2vm&view=export&rand='+Math.random(),
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


		  var end_of_export = false;
		  var intrval_id;
		  var part=0;
		  var timeouted=0;
		  var memory_limit=jQuery("#memory_limit").val();

          jQuery("#results").hide();
          jQuery("#statistics").hide();
              //Продолжение экспорта через CSV
          jQuery("#re_export_button").click(function(){
              end_of_export = false;
			  jQuery("#export_button").hide('explode',{},1500);
			  jQuery("#re_export_button").hide('explode',{},1500);
              jQuery("#links").html('');

              re_export();

              setTimeout(function(){
                  jQuery("#import_started").slideUp();
                  jQuery("#statistics").slideDown();
	              intrval_id = setInterval(function(){
	              	 if(end_of_export){
		               clearInterval(intrval_id);
		        	 }
					 else
                     	getStat();
		          },2000);
			  },2000);
			  return false;
          });


		  jQuery('#export_button').click(function() {
		  	  end_of_export = false;
			  jQuery("#export_button").hide('explode',{},1500);
			  jQuery("#re_export_button").hide('explode',{},1500);
              part = 0;
              jQuery("#part").val(0);
			  jQuery("#links").html('');
			  jQuery("#import_started").slideDown();
              jQuery('#export_form').ajaxSubmit({
				  	url: 'index.php?option=com_excel2vm&task=export&view=export&rand='+Math.random(),
				    success: showResponse,
				    method: 'post',
		            timeout: 600000,
					dataType:'json',
					error:if_error
			  });


              jQuery("#row").html(0);
              jQuery("#cat").html(0);
              jQuery("#product").html(0);
              jQuery("#current_cat").html('');
              jQuery("#current_product").html('');
              jQuery("#duration").html("0 c.");
              jQuery("#memory").html("0");
              jQuery("#status").html("");
			  setTimeout(function(){
                  jQuery("#import_started").slideUp();
                  jQuery("#statistics").slideDown();
	              intrval_id = setInterval(function(){
	              	 if(end_of_export){
		               clearInterval(intrval_id);
		        	 }
					 else
                     	getStat();
		          },2000);
			  },2000);
			  return false;
		  });
        function if_error(data,textStatus){
           update_files();
           if(data.statusText=='OK'){
           	  if(data.responseText.indexOf("Out of memory")>-1)out_of_memory();
           	  else{
           	      jQuery("#links").append("<div><font color='#FF0000'>"+$jtext_ERROR_OCCURED+":<br>"+textStatus+"<br>"+data.responseText+"</font></div>").show('bounce');

                  jQuery("#export_button").show('explode',{},1500);
                  if(jQuery("#csv").val()==1){
                    jQuery("#re_export_button").show('slide',{},1000);
                  }
                  setTimeout(function(){
                      jQuery("#statistics").slideUp();
                  },2000);
           	  }

           }
		   else if(data.statusText=='Gateway Time-out' ||data.statusText== 'timeout'){
                timeouted=1;
		   }
		   else{
		   		end_of_export = true;
		        clearInterval(intrval_id);
				jQuery("#links").append('<b><font color="#FF0000">'+$jtext_ERROR_OCCURED+'</font></b><br />'+data.statusText+'<br>'+data.responseText).show('bounce');
				jQuery("#export_button").show('slide',{},1000);
                if(jQuery("#csv").val()==1){
                    jQuery("#re_export_button").show('slide',{},1000);
                }

		   }
        }

        function out_of_memory(){
            var mem_limit = jQuery("#memory_limit").val();
			  if(mem_limit > 0.2){
			  	 mem_limit=(parseFloat(mem_limit) - 0.1).toFixed(1);
			  	 alert($jtext_OUT_OF_RAM1+" "+(mem_limit*100)+$jtext_OUT_OF_RAM2);
                 jQuery("#memory_limit").val(mem_limit);
                 jQuery('#export_form').ajaxSubmit({
				  	url: 'index.php?option=com_excel2vm&task=export&view=export&rand='+Math.random(),
				    success: showResponse,
				    method: 'post',
		            timeout: 600000,
					dataType:'json',
					error:if_error
			     });
				 return false;
			  }
			  else{
              	  jQuery("#links").append($jtext_OUT_OF_RAM3);
	              end_of_export = true;
	              clearInterval(intrval_id);
              }
        }
        function re_export(){
            jQuery('#export_form').ajaxSubmit({
				  	url: 'index.php?option=com_excel2vm&task=export&view=export&rand='+Math.random(),
				    success: showResponse,
				    method: 'post',
		            timeout: 600000,
					dataType:'json',
					error:if_error
			  });

              jQuery("#status").html("Перезапуск");
              var now = new Date();
              console.log("Перезапуск. "+now.getHours()+":"+now.getMinutes()+":"+now.getSeconds());
        }
        function getStat(){

            jQuery.ajax({
                 url:'index.php?option=com_excel2vm&view=export&rand='+Math.random(),
                 type:'GET',
                 data:{task:'get_export_stat'},
                 dataType: 'json',
                 success:function(data){
                   /* Если требуется перезапуск */
                    if(jQuery("#csv").val()==1 && data.status=='timeout'){
                          re_export();
                          return false;
                    }

                 	if(data.row){
                      jQuery("#row").html(data.row);
                      jQuery("#cat").html(data.cat);
                      jQuery("#product").html(data.product);
                      jQuery("#current_cat").html(data.current_cat);
                      jQuery("#current_product").html(data.current_product);
                      jQuery("#duration").html(data.time+" c.");
                      jQuery("#memory").html(data.mem);
                      jQuery("#status").html(data.status);
                    }
                       if(jQuery("#csv").val()==1 && data.notmodified){
                           re_export();
                           return false;
                       }
					   if(timeouted && data.notmodified){
								jQuery.ajax({
								  	url: 'index.php?option=com_excel2vm&task=get_export_file&view=export&rand='+Math.random(),
								    success: showResponse,
								    method: 'post',
						            timeout: 600000,
									dataType:'json',
									data:{csv:jQuery("#csv").val(),part:part},
									error:if_error
								});
					   }

                  }
           });
        }

		function showResponse(responseText, statusText)  {
			if(responseText.text=='No')
				return false;
			if(!responseText){
               out_of_memory();
			   return false;
			}
            timeouted=0;
            jQuery("#links").append('<a href="'+responseText.link+'">'+responseText.text+'</a><br />');
			getStat();
            update_files();
			if(responseText.finish!=1){
                part++;
				jQuery("#part").val(part);
                jQuery('#export_form').ajaxSubmit({
				  	url: 'index.php?option=com_excel2vm&task=export&view=export&filename='+responseText.filename+'&rand='+Math.random(),

				    success: showResponse,
				    method: 'post',
		            timeout: 600000,
					dataType:'json',
					error:if_error
			    });

				return false;
			}
            else if(part>1){
                var $file_type=jQuery("#csv").val();
                jQuery("#links").append('<a href="index.php?option=com_excel2vm&view=export&task=zip&filename='+responseText.filename+'&file_type='+$file_type+'&parts='+part+'">'+$jtext_DOWNLOAD_ALL_PARTS+'</a><br />');
            }
			end_of_export = true;
            clearInterval(intrval_id);
			jQuery("#statistics").slideDown();
            jQuery("#export_button").show('slide',{},1000);
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
	});