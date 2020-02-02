<?php

defined('_JEXEC') or die('Restricted access');

		require HelperGalleryUG::getPathHelperTemplate("header");

		$selectedGalleryTab = "settings";
		require HelperGalleryUG::getPathHelperTemplate("gallery_edit_tabs")
	?>

			<div class="settings_panel">
			
				<div class="settings_panel_left">
					<div class="settings_panel_left_inner settings_panel_box">
					<?php $outputMain->draw("form_gallery_main",true)?>
					
					<?php require HelperGalleryUG::getPathHelperTemplate("gallery_edit_buttons")?>
					</div>
				</div>
				<div class="settings_panel_right">
					<?php $outputParams->draw("form_gallery_params",true); ?>
				</div>
				
				<div class="unite-clear"></div>				
			</div>
			
	<?php 
	
		if(method_exists("UniteProviderFunctionsUG", "dialogShortcodeReplace")):
			 UniteProviderFunctionsUG::dialogShortcodeReplace();
		else:
	?>
			
			<div id="dialog_shortcode" class="unite-inputs" title="<?php _e("Generate Shortcode",UNITEGALLERY_TEXTDOMAIN)?>" style="display:none;">	
				<br><br>
				
				<div class="mbottom_5">
					<?php _e("Generated shortcode for using with other categories",UNITEGALLERY_TEXTDOMAIN)?>:
				</div>
								
				<input id="ds_shortcode" type="text" class="input-regular input-readonly">
				
				<div class="vert_sap20"></div>

				<div class="mbottom_5">				
				<?php _e("Select category below", UNITEGALLERY_TEXTDOMAIN)?>:
				</div>
				
				<?php echo $htmlSelectCats?>
				<div class="vert_sap20"></div>
				
			</div>
			
<?php endif?>


	<div id="ug_dialog_change_theme" class="dialog_new_gallery" title="<?php _e("Choose a gallery theme to change",UNITEGALLERY_TEXTDOMAIN)?>" style="display:none">
		<div class="unite-admin unite-dialog-inside">
			<ul id="ug_list_change_themes" class="list_galleries">
				<?php foreach($arrGalleryTypes as $gallery):					
					$themeType = UniteFunctionsUG::getVal($gallery, "name");
					$galleryTitle = UniteFunctionsUG::getVal($gallery, "title");
					
					$classAdd = "";
					if($themeType == $galleryType)
						$classAdd = " button-disabled";
						
				?>
				<li><a class="unite-button-secondary ug-link-theme <?php echo $classAdd?>" href="javascript:void(0)" data-name="<?php echo $themeType?>"><?php echo $galleryTitle?></a></li>
				<?php endforeach;?>
			</ul>
			<div class="unite-clear"></div>
			
			<br>		
			
			<div style="height:30px;">
				<span id="ug_list_change_themes_loader" class="loader_text" style="display:none">Changing theme...</span>
				<span id="ug_list_change_themes_error" class="unite-color-red" style="display:none"></span>
				<span id="ug_list_change_themes_success" class="unite_success_message" style="display:none"></span>
			</div>
		</div> 


	<script type="text/javascript">
	
		jQuery(document).ready(function(){
			var objAdmin = new UGAdmin();
			objAdmin.initCommonEditGalleryView();
		});
		
	</script>
	
