jQuery(document).ready(function($) {
    var test2=function(){
        $('body').on('click', '.uploadproductfile_imgs_item_wrap', function(event) {
            // event.preventDefault();
            var self = $(this),
                $conteer=self.parents('.uploadproductfile');

            if (self.parents('.uploadproductfile_imgs_item').hasClass('active')) {
                $('.uploadproductfile_imgs_item',$conteer).removeClass('active');
                $('.uploadproductfile_hidden',$conteer).val("");
            } else {
                $('.uploadproductfile_imgs_item').removeClass('active');
                self.parents('.uploadproductfile_imgs_item').addClass('active');
                var v = self.data('parenttitle') + ': ' + self.data('title')
                $('.uploadproductfile_hidden',$conteer).val(v);
            }
            formProduct = jQuery(this).parents("form.product");
            virtuemart_product_id = formProduct.find('input[name="virtuemart_product_id[]"]').val();
            Virtuemart.setproducttype(formProduct,virtuemart_product_id);
        });
    }
    jQuery("body").on("updateVirtueMartProductDetail", test2);
    test2();
});