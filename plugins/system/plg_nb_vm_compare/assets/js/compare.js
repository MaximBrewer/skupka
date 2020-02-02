jQuery(document).ready(function () {
    jQuery(blook_compare).each(function () {
        var product_link_compare = jQuery(this).find(link_compare).attr('href');
        var check_product = product_link_compare;
        product_link_compare = product_link_compare + request_compare;
        var button = '<span></span>';
        if (check_product != undefined) {
            //jQuery(button).attr('data-link',product_link_compare).addClass('btn-compare fa fa-random').appendTo(this);
        } else {
            var id_ = jQuery(this).find('input[name=\'virtuemart_product_id[]\']').val();
            if (id_ != undefined) {
                var link_ = http_compare + '/index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' + id_
                link_ = link_ + '&compare=1&tack=add';
                //jQuery(button).attr('data-link',link_).addClass('btn-compare fa fa-random').appendTo(this);
            }
        }
    });

    jQuery('.btn-compare').click(function () {
        if (!jQuery('.btn-wishlist').hasClass('fly')) {
            var self_ = jQuery(this);
            jQuery(this).removeClass('fa fa-random');
            var url_save = jQuery(this).attr('data-link');
            var id_compa=jQuery(this).attr('data-id');
            jQuery.ajax({
                url: url_save,
                cache: false,
                type: 'POST',
                data: {add: 1},
                success: function (html) {
                    // eсли продукт в сравнении то удаляем
                    if (html == 2){
                        var id = self_.data('id');
                        jQuery.ajax({
                            url: 'index.php?compare=1&tack=remove',
                            cache: false,
                            type: 'POST',
                            data: {product_id_remove: id},
                            success: function (html) {
                                //ищем все кнопки добавить в избранное
                                //меняем класс
                                jQuery('.compa'+id_compa).removeClass('in-comparison')
                                jQuery('.compa'+id_compa).addClass('fa fa-random');
                                Virtuemart.set_response('template_compare_remove');

                                jQuery('.nb_list_compare').find('.product_id_remove').each(function (index, el) {
                                    if (jQuery(el).val() == id) {
                                        //с листа удаляем
                                        jQuery(el).parents('.nb_product_compare').remove();
                                        var total = 0;
                                        jQuery('.nb_list').each(function (index, el) {
                                            total = jQuery(el).find('.nb_product_compare').length;
                                        });
                                        jQuery('.toltal-compare').text(total);
                                    }
                                });
                            }
                        });

                    }
                    //если добавлен
                    else{
                        jQuery('.nb_no-item').hide();
                        jQuery('.nb_list').prepend(html);
                        var total = 0;
                        jQuery('.nb_list').each(function (index, el) {
                            total = jQuery(el).find('.nb_product_compare').length;
                        });
                        jQuery('.toltal-compare').text(total);
                        //ищем все кнопки добавить в избранное
                        //меняем класс
                        jQuery('.compa'+id_compa).addClass('in-comparison fa fa-random');

                        Virtuemart.set_response('template_compare_add');
                    }
                }
            });
        }
    });
});
