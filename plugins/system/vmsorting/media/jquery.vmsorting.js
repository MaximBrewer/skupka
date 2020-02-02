;
(function ($) {
    var methods = {
        block: function (options) {
            return this.each(function () {
                $('.orderlistcontainer').each(function(){
                    if($(this).find('.Order').length > 0){
                        $(this).remove();
                    }
                });
                $('.orderlist div').each(function () {
                    var name = $(this).find('a').text();
                    var link = $(this).find('a').attr('href');
                    $(this).replaceWith('<a class="btn btn-default" href="' + link + '">' + name + '</a>');     
                });
                $('.orderlist').each(function () {
                    $(this).replaceWith($(this).html());      
                });
                $('.activeOrder').each(function () {
                    var name = $(this).text().replace(/[+\/-]|[-\/+]/g, '').trim();
                    var link = $(this).find('a').attr('href');
                    if(name == "Выбрать производителя") {
                        $(this).hide();
                    } else {
                        var sortIcon;
                        if (link && link.indexOf('=DESC') != -1) {
                            sortIcon = '<img src="/plugins/system/vmsorting/media/sort-asc.svg" alt=""/>';
                        } else if (link && link.indexOf('dirDesc') != -1) {
                            sortIcon = '<img src="/plugins/system/vmsorting/media/sort-asc.svg" alt=""/>';
                        } else {
                            sortIcon = '<img src="/plugins/system/vmsorting/media/sort-desc.svg" alt=""/>';
                        }
                        if (link) {
                            $(this).after('<a class="btn btn-default icon" href="' + link + '">' + sortIcon + '</a>');
                        }
                        $(this).replaceWith('<a class="btn btn-default active" href="' + link + '">' + name + '</a>');
                    }  
                });
                $(this).replaceWith('<div class="vmsorting block"><div class="btn-group">' + $(this).html() + '</div></div>').show();
            });
        },
        select: function () {
            return this.each(function () {
                $('.orderlistcontainer').each(function(){
                    if($(this).find('.Order').length > 0){
                        $(this).remove();
                    }
                });
                $('.orderlist div').each(function () {
                    $(this).replaceWith('<li>' + $(this).html() + '</li>');
                });
                $('.orderlist').each(function () {
                    $(this).replaceWith('<ul class="dropdown-menu">' + $(this).html() + '</ul>');
                });
                $('.activeOrder').each(function () {
                    var name = $(this).text().replace(/[+\/-]|[-\/+]/g, '').trim();
                    var link = $(this).find('a').attr('href');
                    var sortIcon;
                    if (link && link.indexOf('=DESC') != -1) {
                        sortIcon = '<img src="/plugins/system/vmsorting/media/sort-asc.svg" alt=""/>';
                    } else if (link && link.indexOf('dirDesc') != -1) {
                        sortIcon = '<img src="/plugins/system/vmsorting/media/sort-asc.svg" alt=""/>';
                    } else {
                        sortIcon = '<img src="/plugins/system/vmsorting/media/sort-desc.svg" alt=""/>';
                    }
                    if (link) {
                        $(this).after('<a class="btn btn-default icon" href="' + link + '">' + sortIcon + '</a>');
                    }
                    $(this).replaceWith('<div class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">' + name + ' <span class="caret"></span></div>');
                });
                $(this).replaceWith('<div class="vmsorting select "><div class="btn-group" role="group">' + $(this).html() + '</div></div>').show();
            });
        }
    };

    $.fn.vmsorting = function (method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.select.apply(this, arguments);
        } else {
            $.error('error vmsorting');
        }
    };
})(jQuery);