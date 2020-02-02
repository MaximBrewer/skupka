<?php if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );

?>
<div class="everything">
<div id="google-modal">
<div class="wrapper" id="coolrunner_map" style="display:none;">
        <noscript>
        <div class="global-site-notice noscript">
            <div class="notice-inner">
                <p><strong><?php echo JText::_('MOD_COOLRUNNER_NOTICE1'); ?></strong><br>
                <?php echo JText::_('MOD_COOLRUNNER_NOTICE2'); ?></p>
            </div>
        </div></noscript>

		
		<div class="op_inside"  style="width: 100%;">
<div class="op_rounded">
	<div><div><div><div>
	   <div class="op_rounded_fix" style="width: 100%;">  
                             <h3>
                                <span class="col-module_header_r">
                                <span class="col-module_header_l">
                                <span class="col-module_header_arrow">
                                    <span class="col-module_header_color"><?php echo JText::_('MOD_COOLRUNNER_FIND_ADDRESS'); ?></span>                        		
                                </span>
                                </span>
                             	</span>  
                        	</h3>
                        	<div class="op_rounded_content">
 <p class="p1"><span class="s1"><?php echo JText::_('MOD_COOLRUNNER_DESC1'); ?>
 <?php $desc = JText::_('MOD_COOLRUNNER_DESC2'); 
 $desc = trim($desc); if (!empty($desc)) { ?>
 <br></span><span class="s1">
   <?php echo $desc; ?>
 </span>
 <?php } ?>
 </p>




 <div class="narrow-droppoints">
                            <div id="carrier-selection">
                                <div class="droppoint-filters">
                                    <span style="float: left; clear: left;" class="preword"><?php echo JText::_('MOD_COOLRUNNER_VIS'); ?></span>

                                    <div style="float: left; clear: none;"  class="droppoint-filter">
                                        <input id="dao-droppoints" type="checkbox" value="1" checked="checked" /> <label for="dao-droppoints"><?php echo JText::_('MOD_COOLRUNNER_DAO'); ?></label>
                                    </div>

                                    <div style="float: left; clear: none;" class="droppoint-filter">
									<input id="pdk-droppoints" type="checkbox" value="1" checked="checked" />
                                        <label for="pdk-droppoints"><?php echo JText::_('MOD_COOLRUNNER_POST'); ?></label>
                                    </div>
									
                                    <div style="float: left; clear: right; " class="droppoint-filter">
									<input id="gls-droppoints" type="checkbox" value="1" checked="checked" /> 
                                        <label for="gls-droppoints"><?php echo JText::_('MOD_COOLRUNNER_GLS'); ?></label>
                                    </div>
									
                                </div>
                            </div>

                            <div class="search-droppoints">
                                <input id="search-country" type="hidden" name="country" value="DK"> <input id="search-zipcode" type="text" name="zipcode" value="" placeholder="<?php echo addslashes(JText::_('MOD_COOLRUNNER_ZIP')); ?>"> <input id="search-street" type="text" name="street" value="" placeholder="<?php echo addslashes(JText::_('MOD_COOLRUNNER_STREET')); ?>"> <button type="button" class="btn btn-primary"><?php echo JText::_('MOD_COOLRUNNER_SEARCH'); ?></button>
                            </div>
                        </div>

                        <div class="droppoint-map-sidebar">
                            <div id="all-droppoint-map">
                                <div id="all-droppoint-map-canvas" style="width:70%; min-height:300px; float: left; clear:left;margin:0;padding:0;" >&nbsp;</div>
								
								<div id="closest_points" style="float: left; clear: none; width:30%; margin:0;padding:0;"></div>
								
								
<div class="selected_drop_address" id="selected_drop_address" style="display: none;">
 <div class="send-to-adr">
   <span class="selected_addres_span"><?php echo JText::_('MOD_COOLRUNNER_SELECTED_ADDRESS'); ?></span>
   <span id="address_1_html">&nbsp;</span>,
   <span id="address_2_html">&nbsp;</span>,
   <span id="zip_html">&nbsp;</span> 
   <span id="city_html">&nbsp;</span>
   </div>
   <div class="send-to-btn">
   <button class="btn btn-primary btn-store" onclick="return AllDroppoints.storeAddress(this);"><?php echo JText::_('JSAVE'); ?></button>
   </div>
 </div>
								
								
                            </div>
                        </div>

                        <div id="all-droppoint-opening-hours-weekday-container" style="display: none;">
                            <span id="all-droppoint-opening-hours-weekday-mo"><?php JText::_('MONDAY'); ?></span>
                            <span id="all-droppoint-opening-hours-weekday-tu"><?php JText::_('TUESDAY'); ?></span>
                            <span id="all-droppoint-opening-hours-weekday-we"><?php JText::_('WEDNESDAY'); ?></span>
                            <span id="all-droppoint-opening-hours-weekday-th"><?php JText::_('THURSDAY'); ?></span>
                            <span id="all-droppoint-opening-hours-weekday-fr"><?php JText::_('FRIDAY'); ?></span>
                            <span id="all-droppoint-opening-hours-weekday-sa"><?php JText::_('SATURDAY'); ?></span>
                            <span id="all-droppoint-opening-hours-weekday-su"><?php JText::_('SUNDAY'); ?></span>
                        </div>

                        

								<!-- end shipping methodd -->

							</div>
							
							 

<br style="clear: both;"/>

	  </div>
	 </div></div></div></div>
</div>
</div>
	
    </div>
</div>	

<div style="display: none;">
<div class="shpping_address_display" id="shpping_address_display">
<?php
   $root = Juri::root(); 
		if (substr($root, -1)!=='/') $root .= '/'; 
?>
<div style="display: none;" class="shipping_address_wrap shipping_address_wrap_{id}" >

   <span class="selected_addres_span"><?php echo JText::_('MOD_COOLRUNNER_SELECTED_ADDRESS'); ?></span>
   
   <span class="img_comes_here">{address_type_name_shipping}</span>
   <span class="name_shipping">&nbsp;</span>,
   <span class="address_1_shipping">&nbsp;</span>,
   <span class="address_2_shipping"></span>
   <span class="zip_shipping">&nbsp;</span>
   <span class="city_shipping">&nbsp;</span>
   
</div>
<div class="shipping_link"><a href="#" onclick="return displayGoogleMap2({id})"><?php echo JText::_('MOD_COOLRUNNER_CHOOSE'); ?></a></div>   
   
  
  </div>
  
  <!-- ONLY INNERHTML: wrapper that is shown at the right side of the map -->  
  <div class="select_display" id="select_display_id">
  <fieldset>
	{lines_display}
  </fieldset>
  </div>
  <!-- end wrapper that is shown at the right side of the map -->
  
  <!-- ONLY INNERHTML: wrapper of individual lines that are shown at the right side of the map, make sure the default state is checked="checked" -->  
  <div class="line_display" id="line_display_id">
  <input type="radio" value="{droppoint_id}" id="point_{droppoint_id}" name="currently_selected_droppoint" checked="checked" />
  <label for="point_{droppoint_id}"><span class="carrier">{carrier}</span><span class="point_name">{name}</span><span class="point_street">{address.street}</span>, <span class="point_zip">{address.postal_code}</span> <span class="point_city">{address.city}</span></label>
  </div>
  
  <!-- end wrapper of individual lines that are shown at the right side of the map -->  
  
  
 </div>

</div>
	
<?php

