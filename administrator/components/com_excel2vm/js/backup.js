jQuery(function(){
    if(typeof Joomla != 'undefined') {
      Joomla.submitbutton = function(pressbutton) {
        if(pressbutton == 'extra' || pressbutton == 'extra_price' || pressbutton == 'price')
          extend_field(pressbutton);
        else
          eval(pressbutton+'()');
      }
    }
    else {
      window['submitbutton'] = function (pressbutton) {
        eval(pressbutton+'()');
      }
    }

    function new_backup() {
      jQuery("#loader").show();
      jQuery.ajax(
        {
        url: 'index.php?option=com_excel2vm&view=backup&rand='+Math.random(),
        type: 'GET',
        data: {task: 'new_backup'},
        dataType: 'json',
        error: function(data) {jQuery("#loader").hide(); notify(0, data.statusText)},
        success: function(data) {
            jQuery("#loader").hide();
            if(data.status=='ok') {
              notify(1, $jtext_BACKUP_SUCCESSFULL+"<br>Время на создание: "+data.time+" с");
              jQuery(".table-striped tbody").prepend(data.html);
              jQuery(".table-striped tbody > tr:first").fadeIn('slow');
            }
            else {
              notify(0, data.html);
            }


          }
        }
      );
    }

    function notify(title, text) {
      if(title)
        jQuery("#ui-dialog-title-dialog").html($jtext_THE_OPERATION_WAS_SUCCESSFUL);
      else
        jQuery("#ui-dialog-title-dialog").html($jtext_ERROR_OCCURED);
      jQuery("#dialog").html(text);
      jQuery("#dialog").dialog("open");
      setTimeout(function (){jQuery("#dialog").dialog("close");},4000);
    }

    function clear() {
      jQuery("#dialog-form").dialog("open");
    }


    jQuery.fx.speeds._default = 1000;
    jQuery("#dialog").dialog(
      {
      autoOpen: false,
      show: $notify_show,
      hide: $notify_hide
      }
    );

    //Удаление бэкапа
    jQuery(".ui-icon-circle-close").live("click", function()
      {
        var backup_id = jQuery(this).attr("rel");

        jQuery.ajax(
          {
          url: 'index.php?option=com_excel2vm&view=backup&rand='+Math.random(),
          type: 'GET',
          data: {task: 'delete_backup', id: backup_id},
          dataType: 'text',
          error: function(data) {notify(0, data.statusText)},
          success: function(data) {
              jQuery("#"+backup_id).fadeOut('slow');
              notify(1, data);
            }
          }
        );
      }
    );

    jQuery(".ui-icon-arrowreturnthick-1-w").live("click", function()
      {
        var backup_id = jQuery(this).attr("rel");
        jQuery("#loader").show();
        jQuery.ajax(
          {
          url: 'index.php?option=com_excel2vm&view=backup&rand='+Math.random(),
          type: 'GET',
          data: {task: 'restore', id: backup_id},
          dataType: 'text',
          error: function(data) {jQuery("#loader").hide(); notify(0, data.statusText)},
          success: function(data) {
              jQuery("#loader").hide();
              notify(1, data);
            }
          }
        );

      }
    );

    jQuery("#dialog-form").dialog(
      {
      autoOpen: false,
      show: $notify_show,
      hide: $notify_hide,
      modal: false,
      buttons: {
          "Очистить выбранное": function() {
            if(!confirm("Вы уверены, что хотите произвести очистку на сайте ("+$SERVER_NAME+")???")) {
              jQuery(this).dialog("close");
              return false;
            }
            var $products = jQuery("#products").is(':checked');
            var $cats = jQuery("#cats").is(':checked');
            var $images = jQuery("#images").is(':checked');
            var $manufacturers = jQuery("#manufacturers").is(':checked');
            var $customs = jQuery("#customs").is(':checked');
            var $customs_profile = jQuery("#customs_profile").is(':checked');
            var $empty_profile = jQuery("#empty_profile").is(':checked');
            var $backups = jQuery("#backups").is(':checked');
            var $loaded = jQuery("#loaded").is(':checked');
            var $exported = jQuery("#exported").is(':checked');
            if(!$products && !$cats && !$images && !$manufacturers && !$customs && !$customs_profile && !$backups && !$loaded && !$empty_profile & !$exported ) {
              alert("Выберите параметры очистки");
              return false;
            }
            jQuery(this).dialog("close");
            jQuery.ajax(
              {
              url: 'index.php?option=com_excel2vm&view=backup&rand='+Math.random(),
              type: 'GET',
              data: {task: 'clear', products: $products, cats: $cats, images: $images, manufacturers: $manufacturers, customs: $customs, customs_profile: $customs_profile, empty_profile: $empty_profile, backups: $backups, loaded: $loaded,exported: $exported},
              dataType: 'text',
              error: function(data) {notify(0, data.statusText);},
              success: function(data) {notify(1, data);}
              }
            );
          },
        "Отмена": function() {
            jQuery(this).dialog("close");
          }
        }
      }
    );



    //hover states on the static widgets
    jQuery('.ui-state-default').hover(
      function() {jQuery(this).addClass('ui-state-hover');},
      function() {jQuery(this).removeClass('ui-state-hover');}
    );

    //Переопределяем функцию нажатия на кнопки панели
    Joomla.submitbutton = function(pressbutton) {
      eval(pressbutton+'()');
    }
  }
);
