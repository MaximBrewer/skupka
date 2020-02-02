/**
 * Created by aleks on 17.08.2016.
 */
// /*
(function($){
   if ($('#limit').length) {
      $('#limit').css('display','none');
   }
})(jQuery)

jQuery(document).ready(function($) {
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

         },100)
    }
    if ($('#kommentarii_field').length) {
        $('#kommentarii_field').attr('placeholder', 'Комментарии');
    }
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

// Разный фон для разных страниц
/*var urls = {
	'kat1.html' : 'about-background',
	'/project/' : 'project-background'
};

for (var urlMapping in urls) {
	if (location.href.indexOf(urlMapping) !== -1) {
		document.body.className += (' ' + urls[urlMapping]);
	};
}*/