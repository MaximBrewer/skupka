jQuery(document).ready(function(){
    function str2num(val){
        val = '0' + val;
        val = parseFloat(val);
        return val;
    }

    jQuery(".nb_start_compare").click(function(){
        var count_ss_dk = jQuery('.nb_product_compare').length;
        console.log(count_ss_dk);
        if(count_ss_dk >=1){
            var link=jQuery(this).attr('data-link');
            location.href=link;
        }else{
            alert('Добавьте минимум 1 товара');
        }
    });
    if(position_compare != 'none'){
    //chwck again
        jQuery('.nb_list_compare').hide();
        jQuery('.nb_sort_compare span').click(function(){
            jQuery(this).hide();
            jQuery('.nb_list_compare').show();
        });
        jQuery('.nb_hide_list').click(function(){
            jQuery('.nb_list_compare').hide();
            jQuery('.nb_sort_compare span').show(1000);
        });
    //end
    }
    jQuery("i.nb_remove_product").live("click", function(){
        if(!jQuery('i.nb_remove_product').hasClass('remove_loading')){
            jQuery(this).addClass('remove_loading');
            jQuery(this).removeClass('fa fa-times');
            var product_id_remove = jQuery(this).parent().find('.product_id_remove').val();
            jQuery.ajax({
        		url: 'index.php?compare=1&tack=remove',
        		cache: false,
        		type: 'POST',
                data:{product_id_remove:product_id_remove},
        		success: function(html) {
                      jQuery('span.compa'+product_id_remove).closest('.btn-compare').removeClass('in-comparison');
        		      var count_ss = jQuery('.nb_product_compare').length;
                      if(count_ss == 0){
                        jQuery('.nb_list').html('<div class="nb_no-item">Нет продуктов для сравнения</div>');
                      }
                      var total = jQuery('.nb_product_compare').length;
                      jQuery('.toltal-compare').text(total);
                      jQuery('.remove_loading').parent().remove();
        		}
          });
        }
    });
    //clear all
    jQuery('.nb_compare_clear_all').click(function(){
        if(!jQuery('.nb_compare_clear_all').hasClass('remove_all_loading')){
            jQuery(this).addClass('remove_all_loading');
            jQuery(this).children('i').removeClass('fa fa-trash-o');
            jQuery.ajax({
            	url: 'index.php?compare=1&tack=remove_all',
            	cache: false,
            	type: 'POST',
                data:{clear_all:1},
            	success: function(html) {
                    jQuery('.btn-compare').removeClass('in-comparison');
                    jQuery('.nb_list').html('<div class="nb_no-item">Нет продуктов для сравнения</div>');
                    jQuery('.nb_product_compare').remove();
                    jQuery('.toltal-compare').text(0);
                    jQuery('.remove_all_loading').children('i').addClass('fa fa-trash-o');
                    jQuery('.remove_all_loading').removeClass('remove_all_loading');
            	}
            });
        }
    });
});