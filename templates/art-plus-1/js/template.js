


jQuery(function(f){
    var element = f('.jakor');
    f(window).scroll(function(){
        element['fade'+ (f(this).scrollTop() > 200 ? 'In': 'Out')](500);           
    });
});


// 08.11
jQuery(document).ready(function($) {
    $('a.maximenuck ').click(function(event) {
      $('.mobilemaximenuckclose').trigger('click')
    });
});
// magnifig srart для заголовка формы во всплывающем окне
(function($){
     $('.amp-link').each(function(index, el) {
          if (typeof  $(el).data('text')!=="undefined") {
               //удаляем все классы и удаляются обработчики
               $(el).removeAttr('class');
               var text=$(el).data('text');
               $(el).click(function(event) {
                 event.preventDefault();
                 var href=$(el).attr('href');
                 $.magnificPopup.open({
                   items: {
                     src: href
                   },
                   type: 'iframe',
                   callbacks: {
                       beforeAppend: function() {
                          var self=this;
                          this.content.find('iframe').on('load', function() {
                            var iframe = self.content.find('iframe');
                            var $inp=$('[name="form[Email]"]', iframe.contents()),
                                $title_form=$('.rsform-block-bistrii_zagolovok', iframe.contents());
                            //записуем заголовок в скрытое поле form[Email]
                            $inp.val(text);
                            //устанавливаем заговок
                            $title_form.text(text);
                          });
                       }
                   }
                 });
               });
          }else{
             $(el).click(function(event) {
                //пишем название  на странице  товара
                if($(this).parents('.productdetails-view.productdetails').length){
                    var tovar_nazvanie=$(this).parents('.productdetails-view.productdetails').find('.tovar_nazvanie').text();
                    // console.log(tovar_nazvanie +" название категории");
                    $('[name="form[nazvanie]"]').val(tovar_nazvanie)
                    $('[name="form[ssilka]"]').val(location.href)
                }
                //пишем название  если категория ///прочее
                if($(this).parents('.wmvo_vnewnaa_granica').length){
                     var tovar_nazvanie=$(this).parents('.wmvo_vnewnaa_granica').find('.wmvo_nazvanie_kategoria').text();
                     var link=$(this).parents('.wmvo_vnewnaa_granica').find('.wmvo_nazvanie_kategoria a').attr('href');
                     console.log(link +"  link");
                     $('[name="form[nazvanie]"]').val(tovar_nazvanie)
                     $('[name="form[ssilka]"]').val(link);
                }

             });

          }
     });
})(jQuery);
// magnifig end


/**
 * Created by aleks on 17.08.2016.
 */
// /*
(function($){
   if ($('#limit').length) {
      $('#limit').css('display','none');
   }
})(jQuery);

jQuery(document).ready(function($) {
    if ($('#limit').length) {
        var $html_par = $('<div class="orderlistcontainerlimit"></div>'),
            $html_child = $('<div class="orderlist"></div>'),
            $html_active;
        $('#limit option').each(function(index, el) {
            //$html_child.append('<div ><a  title="' + $(el).text() + '" href="'+globalLinklimit + "&"+$(el).val() + '">' + $(el).text() + '</a></div>');
			$html_child.append('<div ><a  title="' + $(el).text() + '" href="'+$(el).val() + '">' + $(el).text() + '</a></div>');
            if ($(el).is(':selected')) {
                $html_active = '<div class="activeOrder"><a title="' + $(el).text() + '" onclick="return false;" href="' + $(el).val() + '">' + $(el).text() + '</a></div>';
            }
        });
        $('#limit').remove();
         setTimeout(function(){
            $html_par.append($html_active);
            $html_par.append($html_child);
            $('.display-number').append($html_par);
            //клик
            $html_par.find('.activeOrder').click(function(event) {
              event.preventDefault();
              $html_par.find('.orderlist').stop().toggle();
            });  
            $('body').click(function(event) {
                let $target=$(event.target);
                if($('.orderlist').length){
                   if(($target.is('a')&&$target.parents('.activeOrder').length>0)||($target.is('div')&&$target.hasClass('activeOrder'))){
                      return true;
                   }
                   let style=$('.orderlist').attr('style');
                   if(style=='display: block;'){
                       $('.orderlist').stop().toggle();
                   }
                }
            });  
            $html_par.find('.activeOrder a').click(function(event) {
              event.preventDefault();
            });

         },100)
    }
	 $('.moduletabletitle_filtr').click(function(){
      $(this).parent().find('.paramfilter.ver160').slideToggle();
    });
});
//*/

//ajax отправка формы
jQuery(function($) {
    $(document).ready(function() {
        var options = {
            beforeSubmit: showRequest,
            success: showResponseAll
        };
        $('.userFormClass').each(function(index, el) {
            $(this).submit(function(event) {
                event.preventDefault();
                $(this).ajaxSubmit(options);
                return false;
            });
        });
    });
    // pre-submit callback
    function showRequest(formData, jqForm, options) {
        $('.my-message_send').removeClass('hide');
        return true;
    }
    // post-submit callback
    function showResponseAll(responseText, statusText, xhr, $form) {
        $('.my-message_send').addClass('hide');
        var $response = $(responseText);
        var comon = $response;
        var id=$form.attr('id');
        var dane = comon.find('.'+id);
        var form_m=comon.find('#'+id);
        if (form_m.length>0) {
        } else {
            $('.my-message', $form).html(dane).css('display', 'block');;
            $form.find('.form2LinesLayout').css('display', 'none');
            setTimeout(function(){
                $('.my-message', $form).hide('slow/400/fast', function() {
                    $form.find('.form2LinesLayout').css('display', 'block');
                });
            },5000)
        }
    }
});
//подсказки
/*jQuery(document).ready(function($) {
    $('input.addtocart-button').tooltipster({
        theme: 'tooltipster-shadow',
        content: "Купить",
        side:'bottom',
    });
	$('a.modal.tovar_zakaz').tooltipster({
        theme: 'tooltipster-shadow',
        content: "Заказать",
        side:'bottom',
    });
    $(window).load(function() {
        $('input.quicktocart-button').tooltipster({
            theme: 'tooltipster-shadow',
            content: "Быстрый заказ",
            side:'bottom'
        });
    });
     var nocompare_="Убрать из избранного",
         yescompare_="Добавить в избранное";
    $('.btn-compare').each(function(index, el) {
        var str="";
        if ($(el).hasClass('in-comparison')) {
           str=nocompare_ ;
        }else{
          str=yescompare_;
        }
        $(el).tooltipster({
            theme: 'tooltipster-shadow',
            content: str,
            side:'bottom'
        });
    });
});*/

//заказ
jQuery(document).ready(function($) {
    $('.tovar_zakaz').click(function(event) {
        var name=$(this).data('name'),
            link=$(this).data('link');
            if ($('#sbox-window').length) {
                var counter_=1;
                var int=setInterval(function(){
                    var iframe = $('#sbox-window').find('iframe');
                    if ($('[name="form[nazvanie]"]', iframe.contents()).length) {
                        $('[name="form[nazvanie]"]', iframe.contents()).val(name);
                        $('[name="form[ssilka]"]', iframe.contents()).val(link);
                        clearInterval(int);
                    }
                    if (counter_==300) {
                       clearInterval(int);
                    }
                    counter_=counter_+1;
                },10)
            }
            if($('.mfp-iframe').length){
                var counter_=1;
                var int=setInterval(function(){
                    var iframe = $('.mfp-iframe');
                    if ($('[name="form[nazvanie]"]', iframe.contents()).length) {
                        $('[name="form[nazvanie]"]', iframe.contents()).val(name);
                        $('[name="form[ssilka]"]', iframe.contents()).val(link);
                        clearInterval(int);
                    }
                    if (counter_==300) {
                       clearInterval(int);
                    }
                    counter_=counter_+1;
                },10)
            }
    });

});

// подключить класс на разных страницах
var urls = {
	'/korzina.html' : 'displai_none',
	'/izmenit-zapis.html' : 'displai_none',
	'/uchetnaya-zapis.html' : 'displai_none',
	'/uchetnaya-zapis/edit.html' : 'displai_none'
};

for (var urlMapping in urls) {
	if (location.href.indexOf(urlMapping) !== -1) {
		document.body.className += (' ' + urls[urlMapping]);
	};
}


/*

if(location.href=="http://a.art-plus-test.ru/vse-ceni/lending-pejdzh.html"){
             jQuery('.wapka').addClass('wapka_vn_internet_magazin')
        }
        else{
            for (var urlMapping in urls) {
                            if (location.href.indexOf(urlMapping) !== -1) {
                                           jQuery('.wapka').addClass('class_name')
                            };
            }
        }
*/
/*// убираем клик по ссылке
(function($){
    // контеер меню ид
    let $this_menu=$('#maximenuck199');
    $('a[href="/kategorii.html"],a[ href="/pomoshch.html"]',$this_menu).click(function(event) {
         event.preventDefault();
    });
})(jQuery);*/