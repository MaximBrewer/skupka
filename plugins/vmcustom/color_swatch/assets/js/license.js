/*------------------------------------------------------------------------
* Color Swatch Plugin for Virtuemart
* author    CMSMart Team
* copyright Copyright (C) 2012 Cmsmart Team. All Rights Reserved.
* @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* Websites: http://cmsmart.net
* Email: team@cmsmart.net
* Technical Support:  Forum - http://cmsmart.net/forum
* version 2.0.0
-------------------------------------------------------------------------*/

jQuery(document).ready(function(){
   jQuery('#jform_params_license_key_active').hide();
   
    
   jQuery('.hidden-new').hide();
   jQuery('#license-hidden-view-more').hide();
   jQuery('#license-view-more').click(function(){
        jQuery('.hidden-new').slideDown();
        jQuery(this).hide();
        jQuery('#license-hidden-view-more').show();
    
   });
   
   jQuery('#license-hidden-view-more').click(function(){
        jQuery('.hidden-new').slideUp();
        jQuery(this).hide();
        jQuery('#license-view-more').show();
    
   });
   
   
    
});

jQuery(window).load(function(){
    
    var root = location.protocol + '//' + location.host;
    //$("[name='jform[params][domain]']").val(root);     
    var action_url = 'http://cmsmart.net/index.php?option=com_license&task=active';
    var product_sku = jQuery("[name='jform[params][product_sku]']").val();
    var license_key = jQuery("[name='jform[params][license_key]']").val();
    var domain = jQuery("[name='jform[params][domain]']").val();
    
   // block();
    
    jQuery.ajax({
        type: 'POST',
        url: action_url,
        data: 'license[product_sku]='+product_sku+'&license[license_key]='+license_key+'&license[domain]='+domain,       
        dataType: 'json',
        beforeSend: function(){
                    jQuery('#ajax-loader').fadeIn("fast");
            },
        success: function(html){
                    jQuery('#ajax-loader').fadeOut("fast");
                    jQuery('#license-messages').text('');
                    
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
                        
                       // block();                        
                    }
                    jQuery('#license-messages').append($str);
            },
            error:function()
            {
                jQuery('#ajax-loader').fadeOut("fast");
                $str = '<span class="license-msfalse"><span class="icon-checkmark-circle fs32"></span><span class="license-msdes">No connect server cmsmart.net</span></span>';
                jQuery('#license-messages').text('');
                jQuery('#license-messages').append($str);
            }
        
        
    });       
        
  
   jQuery('#license_key_active').click(function(){
    
        var product_sku = jQuery("[name='jform[params][product_sku]']").val();
        var license_key = jQuery("[name='jform[params][license_key]']").val();
        var domain = jQuery("[name='jform[params][domain]']").val();
    
         jQuery.ajax({
            type: 'POST',
            url: action_url,
            data: 'license[product_sku]='+product_sku+'&license[license_key]='+license_key+'&license[domain]='+domain,
            dataType:'json',
            beforeSend: function(){
                        jQuery('#ajax-loader').fadeIn("fast");
                },
            success: function(html){
                    jQuery('#ajax-loader').fadeOut("fast");
                    jQuery('#license-messages').text('');
                    
                    //$('#license-messages').append(html.data);
                    $str = '';
                    if(html.result==true)
                    {
                        $str = '<span class="license-mstrue"><span class="icon-checkmark-circle fs32"></span><span class="license-msdes">'+html.data+'</span></span>';
                        
                       // active();
                    }
                    else
                    {
                        $str = '<span class="license-msfalse"><span class="icon-checkmark-circle fs32"></span><span class="license-msdes">'+html.data+'</span></span>';
                        
                       // block();                        
                    }
                    jQuery('#license-messages').append($str);
            },
            error:function()
            {
                jQuery('#ajax-loader').fadeOut("fast");
                $str = '<span class="license-msfalse"><span class="icon-checkmark-circle fs32"></span><span class="license-msdes">No connect server cmsmart.net</span></span>';
                jQuery('#license-messages').text('');
                jQuery('#license-messages').append($str);
            }
            
        });       
   
   });
   
   
    
   
});
