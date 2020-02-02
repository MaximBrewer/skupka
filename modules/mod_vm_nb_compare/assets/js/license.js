$(document).ready(function(){
   
   $('#jform_params_license_key_active').hide();
   
    
   $('.hidden-new').hide();
   $('#license-hidden-view-more').hide();
   $('#license-view-more').click(function(){
        $('.hidden-new').slideDown();
        $(this).hide();
        $('#license-hidden-view-more').show();
    
   });
   
   $('#license-hidden-view-more').click(function(){
        $('.hidden-new').slideUp();
        $(this).hide();
        $('#license-view-more').show();
    
   })
   
   
    
});


$(window).load(function(){
    
    var root = location.protocol + '//' + location.host;
    //$("[name='jform[params][domain]']").val(root);     
    var action_url = 'http://cmsmart.net/index.php?option=com_license&task=active';
    var product_sku = $("[name='jform[params][product_sku]']").val();
    var license_key = $("[name='jform[params][license_key]']").val();
    var domain = $("[name='jform[params][domain]']").val();
    
    //block();
    
    $.ajax({
        type: 'POST',
        url: action_url,
        data: 'license[product_sku]='+product_sku+'&license[license_key]='+license_key+'&license[domain]='+domain,       
        dataType: 'json',
        beforeSend: function(){
                    $('#ajax-loader').fadeIn("fast");
            },
        success: function(html){
                    $('#ajax-loader').fadeOut("fast");
                    $('#license-messages').text('');
                    
                    //$('#license-messages').append(html.data);
                    $str = '';
                    if(html.result==true)
                    {
                        $str = '<span class="license-mstrue"><span class="icon-checkmark-circle fs32"></span><span class="license-msdes">'+html.data+'</span></span>';
                        
                        //active();
                    }
                    else
                    {
                        $str = '<span class="license-msfalse"><span class="icon-checkmark-circle fs32"></span><span class="license-msdes">'+html.data+'</span></span>';
                        
                        //block();                        
                    }
                    $('#license-messages').append($str);
            },
            error:function()
            {
                $('#ajax-loader').fadeOut("fast");
                $str = '<span class="license-msfalse"><span class="icon-checkmark-circle fs32"></span><span class="license-msdes">No connect server cmsmart.net</span></span>';
                $('#license-messages').text('');
                $('#license-messages').append($str);
            }
        
        
    });       
        
  
   $('#license_key_active').click(function(){
    
        var product_sku = $("[name='jform[params][product_sku]']").val();
        var license_key = $("[name='jform[params][license_key]']").val();
        var domain = $("[name='jform[params][domain]']").val();
    
         $.ajax({
            type: 'POST',
            url: action_url,
            data: 'license[product_sku]='+product_sku+'&license[license_key]='+license_key+'&license[domain]='+domain,
            dataType:'json',
            beforeSend: function(){
                        $('#ajax-loader').fadeIn("fast");
                },
            success: function(html){
                    $('#ajax-loader').fadeOut("fast");
                    $('#license-messages').text('');
                    
                    //$('#license-messages').append(html.data);
                    $str = '';
                    if(html.result==true)
                    {
                        $str = '<span class="license-mstrue"><span class="icon-checkmark-circle fs32"></span><span class="license-msdes">'+html.data+'</span></span>';
                        
                        //active();
                    }
                    else
                    {
                        $str = '<span class="license-msfalse"><span class="icon-checkmark-circle fs32"></span><span class="license-msdes">'+html.data+'</span></span>';
                        
                        //block();                        
                    }
                    $('#license-messages').append($str);
            },
            error:function()
            {
                $('#ajax-loader').fadeOut("fast");
                $str = '<span class="license-msfalse"><span class="icon-checkmark-circle fs32"></span><span class="license-msdes">No connect server cmsmart.net</span></span>';
                $('#license-messages').text('');
                $('#license-messages').append($str);
            }
            
        });       
   
   });
   
   
    
   
});



