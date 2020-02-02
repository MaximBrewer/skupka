/*------------------------------------------------------------------------
# BIT Vituemart Product Badges
# ------------------------------------------------------------------------
# author:    Barg-IT
# copyright: Copyright (C) 2014 Barg-IT
# @license:  http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Website:   http://www.barg-it.de
-------------------------------------------------------------------------*/


function new_product_on_page(badge_div,badge_relevant,badge_img_new,badge_new_pos,badge_img_sale,badge_sale_pos,badge_img_discount,badge_discount_pos,badge_img_hot,badge_hot_pos,
                             badge_img_lowstock,badge_lowstock_pos,badge_img_product1,badge_img_product2,badge_img_product3,badge_img_product4,badge_img_product5,badge_product_pos,badge_img_category1,badge_img_category2,badge_img_category3,badge_img_category4,badge_img_category5,badge_category_pos ) {
	var badge_id=badge_div.attr('id');
	var product_id_arr=badge_id.split("_bvmpb");
	var product_id=product_id_arr[0];
	var found=badge_relevant[product_id];
	if(found){
		var inhalt = "";
		badge_div.css('display','block');
		if (found.search('new')!= -1 ) {inhalt = '<div class="bit_badge_new" style="'+ badge_new_pos +'"><img src="'+ badge_img_new +'" alt="badge_new" /></div>';  }
		if (found.search('sale')!= -1) {inhalt += '<div class="bit_badge_sale" style="'+ badge_sale_pos +'"><img src="'+ badge_img_sale +'" alt="badge_featured" /></div>';  }
		if (found.search('discount')!= -1) {inhalt += '<div class="bit_badge_discount" style="'+ badge_discount_pos +'"><img src="'+ badge_img_discount +'" alt="badge_discount" /></div>';  }
		if (found.search('hot')!= -1) {inhalt += '<div class="bit_badge_hot" style="'+ badge_hot_pos +'"><img src="'+ badge_img_hot +'" alt="badge_hot" /></div>';  }
		if (found.search('lowstock')!= -1) {inhalt += '<div class="bit_badge_lowstock" style="'+ badge_lowstock_pos +'"><img src="'+ badge_img_lowstock +'" alt="badge_lowstock" /></div>';  }
		if (found.search('product1')!= -1) {inhalt += '<div class="bit_badge_product" style="'+ badge_product_pos +'"><img src="'+ badge_img_product1 +'" alt="badge_product" /></div>';  }
		if (found.search('product2')!= -1) {inhalt += '<div class="bit_badge_product" style="'+ badge_product_pos +'"><img src="'+ badge_img_product2 +'" alt="badge_product" /></div>';  }
		if (found.search('product3')!= -1) {inhalt += '<div class="bit_badge_product" style="'+ badge_product_pos +'"><img src="'+ badge_img_product3 +'" alt="badge_product" /></div>';  }
		if (found.search('product4')!= -1) {inhalt += '<div class="bit_badge_product" style="'+ badge_product_pos +'"><img src="'+ badge_img_product4 +'" alt="badge_product" /></div>';  }
		if (found.search('product5')!= -1) {inhalt += '<div class="bit_badge_product" style="'+ badge_product_pos +'"><img src="'+ badge_img_product5 +'" alt="badge_product" /></div>';  }
		if (found.search('category1')!= -1) {inhalt += '<div class="bit_badge_category" style="'+ badge_category_pos +'"><img src="'+ badge_img_category1 +'" alt="badge_category" /></div>';  }
		if (found.search('category2')!= -1) {inhalt += '<div class="bit_badge_category" style="'+ badge_category_pos +'"><img src="'+ badge_img_category2 +'" alt="badge_category" /></div>';  }
		if (found.search('category3')!= -1) {inhalt += '<div class="bit_badge_category" style="'+ badge_category_pos +'"><img src="'+ badge_img_category3 +'" alt="badge_category" /></div>';  }
		if (found.search('category4')!= -1) {inhalt += '<div class="bit_badge_category" style="'+ badge_category_pos +'"><img src="'+ badge_img_category4 +'" alt="badge_category" /></div>';  }
		if (found.search('category5')!= -1) {inhalt += '<div class="bit_badge_category" style="'+ badge_category_pos +'"><img src="'+ badge_img_category5 +'" alt="badge_category" /></div>';  }
		
		badge_div.html(inhalt);    
    }
 
}