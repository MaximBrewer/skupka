jQuery(document).ready(function($) {
    
    $('body').on('submit', '.form_wishlist_products', function(event) {
    // $('.form_wishlist_products').submit(function(event) {
        var data = $(this).serialize(),
            $form = $(this);
            type=$form.find('[name="mode"]').val(),
            favorite_id=$form.find('[name="favorite_id"]').val();
        $(this).find('button').prop("disabled", true);
        $.ajax({
            type: 'POST',
            data: data,
            // dataType: 'default: Intelligent Guess (Other values: xml, json, script, or html)',
        }).done(function(res) {
            $('form.form_wishlist_products').each(function(index, el) {
                 var f_id=$(el).find('[name="favorite_id"]').val();
                 if(f_id==favorite_id){
                    $(el).find('button').html(res.data.but);
                     if(type=="fav_add"){
                         $(el).find('button').addClass('del')
                     }
                     if(type== "fav_del" ){
                         $(el).find('button').removeClass('del')
                     }
                    $(el).find('[name="mode"]').val(res.data.mode);
                    $('#mod_virtuemart_wishlist_products_cont').html(res.data.fav_products);
                 }
            });
            if(type=="fav_add"){
                Virtuemart.set_response('template_wishlist_add');
            }
            if(type== "fav_del" ){
                Virtuemart.set_response('template_wishlist_remove');
            }
        }).fail(function() {
            console.log("error");
        }).always(function() {
            $form.find('button').prop("disabled", false);
        });
        return false;
    });

});