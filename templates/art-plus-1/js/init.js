// ======= Ajax Submit Form Plugin =======
(function($) {
    jQuery.fn.sendForm = function(options) {
        options = $.extend({
            successTitle: "Ваше сообщение успешно отправлено!",
            successText: "Мы свяжемся с Вами в самое ближайшее время"
        }, options);

        var make = function() {
            var optionsForm = {
                beforeSubmit: showRequest,
                success: showResponseAll,
                //clearForm: true,
                //resetForm: true
            };
            // bind to the form's submit event 
            $(this).submit(function() {
                $(this).ajaxSubmit(optionsForm);
                return false;
            });
            var $this = $(this);
            // pre-submit callback 
            function showRequest(formData, jqForm, options) {
                $this.find('.rsform-submit-button').addClass('sending').text("Отправка....");
            }
            // post-submit callback
            function showResponseAll(responseText, statusText, xhr, $form) {
                if ($this.find('.formError').is(":visible")) {
                    $this.find('.rsform-submit-button').removeClass('sending').text("Отправить");
                } else {
					$this[0].reset();
                    $this.find('.hide-form-success').slideUp().delay(4000).slideDown();
                    $this.find('.rsform-submit-button').removeClass('sending').text("Отправить");
                    $this.find('.hide-form-success').after('<div class="sys-messages"></div>');
                    $this.find('.sys-messages').html('<h4 class="success-title">' + options.successTitle + '</h4><p class = "success-text" >' + options.successText + '</p>');
                    setTimeout(function() {
                        $this.find('.sys-messages').fadeOut().delay(2000).remove();
                    }, 4000);
                }
            }
        }
        return this.each(make);
    };
})(jQuery);

jQuery(document).ready(function($) {
    $('#userForm3').sendForm({
       
    });
});
jQuery(document).ready(function($) {
    $('#userForm4').sendForm({
       
    });
});// end ready
jQuery(document).ready(function($) {
    $('#userForm5').sendForm({
       
    });
});// end ready
jQuery(document).ready(function($) {
    $('#userForm6').sendForm({
       
    });
});// end ready
jQuery(document).ready(function($) {
    $('#userForm7').sendForm({
       
    });
});// end ready
jQuery(document).ready(function($) {
    $('#userForm8').sendForm({
       
    });
});// end ready
/* =============================================================
    
                            Ajax Submit Form Plugin
 C собственной валидацией (валидация RsForm должна быть отключена) для версии - 1.52++
 Обязательное поле input надо обязательно обернуть родительским блоком с классом - required-field

============================================================================================================== */

(function($) {
    jQuery.fn.sendForm = function(options) {
        options = $.extend({
            successTitle: "Ваше сообщение успешно отправлено!",
            successText: "Мы свяжемся с Вами в самое ближайшее время"
        }, options);

        var make = function() {
            var optionsForm = {
                beforeSubmit: showRequest,
                success: showResponseAll
            };
            // bind to the form's submit event 
            $(this).submit(function() {
                $(this).ajaxSubmit(optionsForm);
                return false;
            });
            var $this = $(this),
                btn = $this.find('.rsform-submit-button');

            // pre-submit callback 
            function showRequest(formData, jqForm, options) {
                btn.addClass('sending').text("Отправка....");
            }
            // post-submit callback
            function showResponseAll(responseText, statusText, xhr, $form) {
                var requiredField = $this.find('.required-field input, .required-field textarea');

                // Удаляем подсветку ошибки при заполнении поля
                requiredField.change(function() {
                    $(this).next().hide();
                    $(this).removeClass('rsform-error');
                });

                // Удаляем сласс sending у кнопки, если поля не заполнены
                if ($this.find('.formError').is(":visible")) {
                    btn.removeClass('sending').text("Отправить");
                }

                $this.find(requiredField).addClass('rsform-error');

                // Проверки полей формы
                $this.find(requiredField).each(function(){
                    if($(this).val() != ''){
                        $(this).next().removeClass('formError').hide();
                        $(this).removeClass('rsform-error');
                    } else {
                        $(this).next().addClass('formError').show();
                        $(this).addClass('rsform-error');
                    }
                });

                var errorField = $this.find('.rsform-error').size();
                if(errorField > 0){
                    btn.removeClass('sending').text("Отправить");
                    return false
                }

                if(requiredField.hasClass('rsform-error')){
                    return false
                } else {
                    $this[0].reset();
                    $this.find('.hide-form-success').slideUp().delay(4000).slideDown();
                    btn.removeClass('sending').text("Отправить");
                    $this.find(requiredField).removeClass('rsform-error');
                    $this.find('.hide-form-success').after('<div class="sys-messages"></div>');
                    $this.find('.sys-messages').html('<h5 class="success-title">' + options.successTitle + '</h5><p class = "success-text" >' + options.successText + '</p>');
                    setTimeout(function() {
                        $this.find('.sys-messages').fadeOut().delay(2000).remove();
                    }, 4000);
                }
            }
        };
        return this.each(make);
    };
})(jQuery);

/** =====================

Для того, чтобы форма работала нужно обрамить поля формы тегом с классом - hide-form-success:

Пример формы:

<h2 class="zayavka-service-title">{global:formtitle}</h2>
    {error}
    <div class="hide-form-success">

        <div class="form-row">
            <input value="" size="20" name="form[fio]" id="fio" placeholder="Ф.И.О*" class="inputbox rsform-input-box" type="text">
            <span id="component28" class="formNoError">Введите ваше Ф.И.О</span>
        </div>

        <div class="form-row">
            <input value="" size="20" name="form[email]" id="email" placeholder="E-mail*" class="inputbox rsform-input-box" type="text">
            <span id="component29" class="formNoError">Введите ваш e-mail</span>
        </div>

        .....   
        
    </div>
    
===================================== **/
