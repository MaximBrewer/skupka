// jQuery cookie plugin
if(!('cookie' in jQuery)){
var pluses = /\+/g; function raw(s) { return s; } function decoded(s) { return decodeURIComponent(s.replace(pluses, ' ')); } var config = jQuery.cookie = function (key, value, options) { if (value !== undefined) { options = jQuery.extend({}, config.defaults, options); if (value === null) { options.expires = -1; } if (typeof options.expires === 'number') { var days = options.expires, t = options.expires = new Date(); t.setDate(t.getDate() + days); } value = config.json ? JSON.stringify(value) : String(value); return (document.cookie = [ encodeURIComponent(key), '=', config.raw ? value : encodeURIComponent(value), options.expires ? '; expires=' + options.expires.toUTCString() : '', options.path    ? '; path=' + options.path : '', options.domain  ? '; domain=' + options.domain : '', options.secure  ? '; secure' : '' ].join('')); } var decode = config.raw ? raw : decoded; var cookies = document.cookie.split('; '); for (var i = 0, l = cookies.length; i < l; i++) { var parts = cookies[i].split('='); if (decode(parts.shift()) === key) { var cookie = decode(parts.join('=')); return config.json ? JSON.parse(cookie) : cookie; } } return null; }; config.defaults = {}; jQuery.removeCookie = function (key, options) { if (jQuery.cookie(key) !== null) { jQuery.cookie(key, null, options); return true; } return false; };
}

var mcf_reinit = {};
var loader_img = {};
var mcf_toggle = new Array();
(function($){
	mcf_toggle = $.cookie('mcf_toggle');
	if(mcf_toggle){
		mcf_toggle = mcf_toggle.split(',');
	}else{
		mcf_toggle = new Array();
	}
	init();
	
	function init(){
		$(document).ready(function(){
			$('.paramfilter').each(function(){

				mcf_reinit['#'+$(this).attr('id')+'-start']();
				ajaxFilterReload('#'+$(this).attr('id'));
				$('.values',$(this)).each(function(){
					var values = ''+$(this).data('id');
					var index = $.inArray(values,mcf_toggle);
					if(index != -1){
						$(this).hide(0).siblings('.heading').addClass('closed');
					}
				});
			});
			function ajaxParamFilter(obj){
				var form = obj.closest('form');
				var change_obj = obj.closest('.values');
				if(change_obj.hasClass('assign_val') || change_obj.hasClass('assign_id')){ 
					// Очистка связных полей перед отправкой изменений, т.к. блок может визуально исчезнуть, а на выборку (запрос) будет влиять
					customChildClear(obj,change_obj,selector);
				}
				var ajax_m = form.hasClass('mcf_mod_ajax');
				var ajax_d = form.hasClass('mcf_mod_ajax_div');
				var preload = 0;
				if(form.find('input[name=preload_virtuemart_category_id]').length && ajax_m){
					preload = 1;
				}
				var selector_id = obj.closest('.paramfilter').attr('id')
				var selector = '#'+selector_id;
				if(!ajax_m && !ajax_d)
					return false;
				var url = (ajax_m && !ajax_d) ? $('.ajax_url',form).text() : form.attr('action');
				// $('input[name=limit]',form).remove();
				var query_string = form.serialize();
				query_string += '&mcf_ajax=1';
				if(!$('input[name=virtuemart_category_id]',form).length && form.find('#mcf_vmlastvisitedcategoryid').length && ajax_m && (!ajax_d || preload)){
					query_string = query_string + '&virtuemart_category_id=' + form.find('#mcf_vmlastvisitedcategoryid').text();
				}
				var aload = $('<img src="'+mcf_uri+'/modules/mod_virtuemart_param_filter/assets/'+loader_img[selector_id]+'" alt="AJAX loader" />');
				// var change_obj = obj.closest('.values');
				if(change_obj.length > 0){
					var change_div = '.'+obj.closest('.values').parent('div').attr('class').replace(' ','.')+' .values';
				}
				obj.closest('.values').siblings('.heading').append(aload);
				form.find('input[name=preload_virtuemart_category_id]').remove();
				if(ajax_d && !preload){
						var body_selector = form.find('#ajax_div').text();
						$(body_selector).parent().height($(body_selector).parent().height());
						$(body_selector).stop(1,1).fadeOut();
					
					}
				$.ajax({
					url: url+'?'+query_string,
					dataType: 'html',
					success: function(data){

						if(ajax_d && !preload){
							$(body_selector).html($(data).find(body_selector).html()).stop(1,1).fadeIn();
							$(body_selector).parent().height('auto');
							mcf_reinit[selector+'-div']();

							aload.remove();



						}
						if(ajax_m){
							var form = $(data).find(selector+' form');
							if($('input[type=checkbox],input[type=radio],select',change_obj).length > 0 
							&& $('input[type=checkbox]:checked,input[type=radio]:checked,option:selected',change_obj).length > 0 
							&& change_obj.length > 0 
							&& $('select',change_obj).val() != ''
							&& !change_obj.hasClass('sliderbox')){
								$(form).find(change_div).before(change_obj).remove();
							}

							$('form',selector).before(form).remove();
							mcf_reinit[selector+'-mod']();
						
							ajaxFilterReload(selector);
							aload.remove();
						}
						//добавить вызов ф-ции фильтра start
						$(".orderlistcontainer").vmsorting("select");
						if ($('#limit').length) {
						    var $html_par = $('<div class="orderlistcontainerlimit"></div>'),
						        $html_child = $('<div class="orderlist"></div>'),
						        $html_active;
						    $('#limit option').each(function(index, el) {
						        $html_child.append('<div ><a  title="' + $(el).text() + '" href="' + $(el).val() + '">' + $(el).text() + '</a></div>');
						        if ($(el).is(':selected')) {
						            $html_active = '<div class="activeOrder"><a title="' + $(el).text() + '" href="' + $(el).val() + '">' + $(el).text() + '</a></div>';
						        }
						    });
						    $('#limit').remove();
						     setTimeout(function(){
						        $html_par.append($html_active);
						        $html_par.append($html_child);
						        $('.display-number').append($html_par);
						        $html_par.hover(function() {
						            jQuery(this).find('.orderlist').stop().show()
						        }, function() {
						            jQuery(this).find('.orderlist').stop().hide()
						        })

						     },150)
						}
						//******************************************** end
					}
				});
				
			}
			function customChildClear(obj,valbox,selector){
				// Рекурсивная очистка дочерних связанных полей с делением на "связное значение" assign_pval и "связное поле" assign_pid
				/* class:
					values 
					cv-ХХ : id текущего поля
					custom_child : флаг "дочерний связный"
					custom_child-XX : id родительского связного поля
					assign_id : флаг "родительский связный по всему полю"
					assign_pid : флаг "дочерний связный по всему полю"
					assign_val : флаг "родительский связный по значению"
					assign_pval : флаг "дочерний связный по значению"
				*/
				var data = valbox.data();
				if(valbox.hasClass('assign_val')){
					if($('input.assign_val:not(:checked),option.assign_val:not(:selected)',valbox).length > 0){
						$('option:selected',$('.custom_child-'+data['id']+'.assign_pval',selector)).prop('selected',false);
						$('input:checked',$('.custom_child-'+data['id']+'.assign_pval',selector)).prop('checked',false);
						$('input[type=text]',$('.custom_child-'+data['id']+'.assign_pval',selector)).val('');
						var customChild = $('.custom_child-'+data['id']+' select',selector);
						customChildClear(customChild,customChild.closest('.values'),selector);
					}
				}
				if(valbox.hasClass('assign_id')){
					if($('input:checked',valbox).length == 0 || $('select',valbox).val() != ''){
						$('option:selected',$('.custom_child-'+data['id']+'.assign_pid',selector)).prop('selected',false);
						$('input:checked',$('.custom_child-'+data['id']+'.assign_pid',selector)).prop('checked',false);
						$('input[type=text]',$('.custom_child-'+data['id']+'.assign_pval',selector)).val('');
						var customChild = $('.custom_child-'+data['id']+' select',selector);
						customChildClear(customChild,customChild.closest('.values'),selector);
					}
				}
				
			}
			function ajaxFilterReload(selector){
				if(typeof Tips != 'undefined'){
					var JTooltips = new Tips($$('.hasTip'), { width: 100, fixed: false});
				}
				$('.heading:visible',selector).each(function(){
					$(this).height($(this).height());
				})
				$(selector + ' .filter_category input:checked').each(function(){
					$(this).parents('ul:hidden').show();
					$(this).closest('li').find('ul input[type=checkbox]').prop('checked',true);
				});
				$('.mcf_mod_ajax input[name=preload_virtuemart_category_id]',selector).each(function(){
					ajaxParamFilter($(this));
				});
				$('.mcf_mod_ajax .price input[type=text], .values input[type=text]',selector).unbind('blur').blur(function(){
					ajaxParamFilter($(this));
				});
				$('label.filter input,.check input,.values select,select.values',selector).unbind('change').change(function(){
					ajaxParamFilter($(this));
				});
				$('.values-named li', selector).hover(function(){
					$(this).addClass('hover');
				},function(){
					$(this).removeClass('hover');
				});
				$('.values-named input',selector).each(function(){
					if($(this).prop('checked')){
						$(this).closest('label').addClass('checked');
					}else{
						$(this).closest('label').removeClass('checked');
					}
				});
				$('.values',selector).each(function(){
					var values = $(this).data('id');
					var index = $.inArray(values,mcf_toggle);
					if(index != -1){
						$(this).hide(0).siblings('.heading').addClass('closed');
					}
				});
				$('.heading',selector).click(function(){
					var values = ''+$(this).siblings('.values').data('id');
					var index = $.inArray(values,mcf_toggle);
					if(index != -1){
						mcf_toggle.splice(index,1);
						$(this).removeClass('closed').siblings('.values').stop(1,1).slideDown(300);
					}else{
						mcf_toggle.push(''+values);
						$(this).addClass('closed');
						$(this).siblings('.values').stop(1,1).slideUp(300);
					}
					$.cookie('mcf_toggle',mcf_toggle,{path: '/'});
				});
				$(selector + ' .filter_category input').unbind('change').change(function(){
					var checked = $(this).prop('checked');
					$(this).closest('li').find('ul input[type=checkbox]').prop('checked',checked);
					if(!checked){
						$(this).parents('ul').siblings('label').find('input[type=checkbox]').prop('checked',checked);
					}
					ajaxParamFilter($(this));
				});
				$('.custom_child',selector).each(function(){
					var rel = $(this).attr('rel');
					var data = $(this).data();
					if(data['pval']){
						data['pval'] = (''+data['pval']).split(';');
						$('.values.cv-'+data['pid'],selector).addClass('assign_val');
						$(this).addClass('assign_pval');
						$('input,option',$('.values.cv-'+data['pid'],selector)).each(function(){
							if($.inArray($(this).val(),data['pval']) != -1){
								$(this).addClass('assign_val');
							}
						});
					}else{
						$('.values.cv-'+data['pid'],selector).addClass('assign_id');
						$(this).addClass('assign_pid');
					}
					// $('.custom_params-'+rel).not('custom_parent').addClass('custom_parent');
				});
				$(selector + ' .filter_category a.next_depth').unbind('click').click(function(){
					$(this).siblings('ul').toggle();
					return false;
				});
				if($(selector + ' .chosen').length > 0){
					$(selector + ' .chosen').chosen({
						allow_single_deselect: true
					});
				}
				if($(selector + ' div.sliderbox').length > 0){
					// Single slider for handle and list values
					$(selector + ' .slider-single-handle .slider-line,' + selector + ' .slider-single-list .slider-line').prop('slide',null).each(function(){
						var parent = $(this).closest('.sliderbox')
						var max = parent.find('input').length - 1;
						var value = parent.find('input').index(parent.find('input:checked'));
						if(parent.find('input:checked').length == 1)
							parent.find('.slider-msg').html(parent.find('input:checked').siblings('span').text());
						$(this).empty().slider({
							range: false,
							min: 0,
							max: max,
							values: [value],
							slide: function( event, ui ) {
								var input = parent.find('input:eq('+ui.values[0]+')');
								parent.find('.slider-msg').html(input.siblings('span').text());
								parent.find('input').prop('checked',false)
								input.prop('checked',true);
							},
							stop: function( event, ui ) { 
								ajaxParamFilter(parent);
							}
								
						});
					});
					// Double slider for manual int values
					$(selector + ' .slider-double-handle .slider-line').prop('slide',null).each(function(){
						var parent = $(this).closest('.sliderbox')
						var min = parseFloat(parent.find('input.slider-range-gt').attr('rel'));
						var max = parseFloat(parent.find('input.slider-range-lt').attr('rel'));
						var value_1 = parent.find('input.slider-range-gt').val() == '' ? min : parent.find('input.slider-range-gt').val();
						var value_2 = parent.find('input.slider-range-lt').val() == '' ? max : parent.find('input.slider-range-lt').val();
						var step = parent.attr('rel') ? parent.attr('rel') : 1;
						$(this).empty().slider({
							range: true,
							min: min,
							max: max,
							step: step,
							round: false,
							values: [value_1,value_2],
							create: function(event, ui){
								var slider_count = parent.find('.slider-range-lt').attr('rel') - parent.find('.slider-range-gt').attr('rel');
								var slider_left = parent.find('.slider-range-gt').attr('rev') - parent.find('.slider-range-gt').attr('rel');
								var slider_right = parent.find('.slider-range-lt').attr('rev') - parent.find('.slider-range-gt').attr('rel');
								var count = slider_left/slider_count > 0 ? 1 : 0;
								$(event.target).append('<div class="slider_active count-'+count+'" style="left:'+slider_left/slider_count*100+'%; width:'+(slider_right-slider_left)/slider_count*100+'%;"></div>');
							},
							slide: function( event, ui ) {
								parent.find('input.slider-range-gt').val(ui.values[0]);
								parent.find('input.slider-range-lt').val(ui.values[1]);
							},
							stop: function( event, ui ) { 
								ajaxParamFilter(parent);
							}
						});
					});
					// Double slider for manual text and list values
					$(selector + ' .slider-double-list .slider-line').prop('slide',null).each(function(){
						var parent = $(this).closest('.sliderbox')
						var max = parent.find('input').length - 1;
						var value_1 = parent.find('input').index(parent.find('input:checked:first'));
						var value_2 = parent.find('input').index(parent.find('input:checked:last'));
						value_1 = value_1==-1 ? 0 : value_1;
						value_2 = value_2==-1 ? max : value_2;
						parent.find('div.slider-range-gt').text(parent.find('input:eq('+value_1+')').siblings('span').text());
						parent.find('div.slider-range-lt').text(parent.find('input:eq('+value_2+')').siblings('span').text());
						$(this).empty().slider({
							range: true,
							min: 0,
							max: max,
							values: [value_1,value_2],
							create: function(event, ui){
								var slider_count = parent.find('input[type=checkbox]').length - 1;
								var slider_left = parent.find('input[type=checkbox]').index(parent.find('input[type=checkbox].slider_visible:first'));
								var slider_right = parent.find('input[type=checkbox]').index(parent.find('input[type=checkbox].slider_visible:last'));
								var slider_active_count = parent.find('input[type=checkbox].slider_visible').length;
								$(event.target).append('<div class="slider_active count-'+(slider_active_count)+'" style="left:'+slider_left/slider_count*100+'%; width:'+(slider_right-slider_left)/slider_count*100+'%;"></div>');
							},
							slide: function( event, ui ) {
								parent.find('input').prop('checked',true)
								parent.find('input:lt('+ui.values[ 0 ]+')').prop('checked',false);
								parent.find('input:gt('+ui.values[ 1 ]+')').prop('checked',false);
								parent.find('div.slider-range-gt').text(parent.find('input:eq('+ui.values[0]+')').siblings('span').text());
								parent.find('div.slider-range-lt').text(parent.find('input:eq('+ui.values[1]+')').siblings('span').text());
							},
							stop: function( event, ui ) { 
								ajaxParamFilter(parent);
							}
						});
					});
				}
				$(selector + ' a.reset').unbind('click').click(function(){
					var values = $(this).parent().siblings('.values');
					values.find('input[type=text]').val('');
					values.find('input[type=checkbox]:checked,input[type=radio]:checked').prop('checked',false);
					values.find('select option:selected').prop('selected',false);
					values.find('select option:first').prop('selected',true);
					values.find('.slider-line').each(function(){
						$(this).slider('values',0,0);
					})
					ajaxParamFilter($(this).closest('.heading').siblings('.values').children());
					return false;
				});
				$(selector + ' a.fullreset').unbind('click').click(function(){
					$('input[type=text]',$(selector)).val('');
					$('input[type=checkbox],input[type=radio]',$(selector)).prop('checked',false);
					$('select option:selected',$(selector)).prop('selected',false);
					$('select option:first',$(selector)).prop('selected',true);
					$('.slider-line',$(selector)).each(function(){
						$(this).slider('values',0,0);
					})
					// ajaxParamFilter($(selector + ' input[name=mcf_id]'));
					ajaxParamFilter($(this));
					return false;
				});
				
				return true;
			}
		})
	}
})(jQuery)