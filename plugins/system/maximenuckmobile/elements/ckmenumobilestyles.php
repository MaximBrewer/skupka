<?php

/**
 * @copyright	Copyright (C) 2015 Cedric KEIFLIN alias ced1870
 * http://www.joomlack.fr
 * @license		GNU/GPL
 * */
// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.form');

class JFormFieldCkmenumobilestyles extends JFormField {

	protected $type = 'ckmenumobilestyles';

	protected $path;
	
	protected $imagespath;

	protected $colorpicker_class;

	function __construct() {
		$this->path = '/plugins/system/maximenuckmobile/elements/ckmenumobilestyles/';
		$this->imagespath = $this->getPathToElements();
		$this->colorpicker_class = 'color {required:false,pickerPosition:\'top\',pickerBorder:2,pickerInset:3,hash:true}';
	}

	protected function createFields() {
		$input = JFactory::getApplication()->input;
		// retrieve the option values from the plugin params
		// if empty, we load the default data
		if ($this->value === '') {
			$this->value = '{|qq|menubarheight|qq|:|qq|35|qq|,|qq|menubarbgcolor1|qq|:|qq|#444444|qq|,|qq|menubarbgcolor2|qq|:|qq||qq|,|qq|menubarbgopacity|qq|:|qq||qq|,|qq|menubarbgimage|qq|:|qq||qq|,|qq|menubarbgpositionx|qq|:|qq||qq|,|qq|menubarbgpositiony|qq|:|qq||qq|,|qq|menubarmargintop|qq|:|qq||qq|,|qq|menubarmarginright|qq|:|qq||qq|,|qq|menubarmarginbottom|qq|:|qq||qq|,|qq|menubarmarginleft|qq|:|qq||qq|,|qq|menubarpaddingtop|qq|:|qq||qq|,|qq|menubarpaddingright|qq|:|qq||qq|,|qq|menubarpaddingbottom|qq|:|qq||qq|,|qq|menubarpaddingleft|qq|:|qq|20|qq|,|qq|menubarbordercolor|qq|:|qq||qq|,|qq|menubarbordertopwidth|qq|:|qq||qq|,|qq|menubarborderrightwidth|qq|:|qq||qq|,|qq|menubarborderbottomwidth|qq|:|qq||qq|,|qq|menubarborderleftwidth|qq|:|qq||qq|,|qq|menubarborderstyle|qq|:|qq|solid|qq|,|qq|menubarroundedcornerstl|qq|:|qq||qq|,|qq|menubarroundedcornerstr|qq|:|qq||qq|,|qq|menubarroundedcornersbr|qq|:|qq||qq|,|qq|menubarroundedcornersbl|qq|:|qq||qq|,|qq|menubarshadowcolor|qq|:|qq||qq|,|qq|menubarshadowblur|qq|:|qq||qq|,|qq|menubarshadowspread|qq|:|qq||qq|,|qq|menubarshadowoffsetx|qq|:|qq||qq|,|qq|menubarshadowoffsety|qq|:|qq||qq|,|qq|menubarfontsize|qq|:|qq|16|qq|,|qq|menubarfontcolor|qq|:|qq|#EEEEEE|qq|,|qq|menubarlineheight|qq|:|qq|35|qq|,|qq|menubartextindent|qq|:|qq||qq|,|qq|menubarfontfamily|qq|:|qq||qq|,|qq|menubarbuttoncontentcustomtext|qq|:|qq|Open|qq|,|qq|menubarbuttoncontent|qq|:|qq|none|qq|,|qq|menubarbuttonheight|qq|:|qq|24|qq|,|qq|menubarbuttonwidth|qq|:|qq|35|qq|,|qq|menubarbuttonbgcolor1|qq|:|qq||qq|,|qq|menubarbuttonbgcolor2|qq|:|qq||qq|,|qq|menubarbuttonbgopacity|qq|:|qq||qq|,|qq|menubarbuttonbgimage|qq|:|qq|/plugins/system/maximenuckmobile/presets/default/icon_launch.jpg|qq|,|qq|menubarbuttonbgpositionx|qq|:|qq|left|qq|,|qq|menubarbuttonbgpositiony|qq|:|qq|center|qq|,|qq|menubarbuttonbgimagerepeat|qq|:|qq|no-repeat|qq|,|qq|menubarbuttonmargintop|qq|:|qq|5|qq|,|qq|menubarbuttonmarginright|qq|:|qq|10|qq|,|qq|menubarbuttonmarginbottom|qq|:|qq|5|qq|,|qq|menubarbuttonmarginleft|qq|:|qq|5|qq|,|qq|menubarbuttonpaddingtop|qq|:|qq||qq|,|qq|menubarbuttonpaddingright|qq|:|qq||qq|,|qq|menubarbuttonpaddingbottom|qq|:|qq||qq|,|qq|menubarbuttonpaddingleft|qq|:|qq||qq|,|qq|menubarbuttonbordercolor|qq|:|qq||qq|,|qq|menubarbuttonbordertopwidth|qq|:|qq||qq|,|qq|menubarbuttonborderrightwidth|qq|:|qq||qq|,|qq|menubarbuttonborderbottomwidth|qq|:|qq||qq|,|qq|menubarbuttonborderleftwidth|qq|:|qq||qq|,|qq|menubarbuttonborderstyle|qq|:|qq|solid|qq|,|qq|menubarbuttonroundedcornerstl|qq|:|qq||qq|,|qq|menubarbuttonroundedcornerstr|qq|:|qq||qq|,|qq|menubarbuttonroundedcornersbr|qq|:|qq||qq|,|qq|menubarbuttonroundedcornersbl|qq|:|qq||qq|,|qq|menubarbuttonshadowcolor|qq|:|qq||qq|,|qq|menubarbuttonshadowblur|qq|:|qq||qq|,|qq|menubarbuttonshadowspread|qq|:|qq||qq|,|qq|menubarbuttonshadowoffsetx|qq|:|qq||qq|,|qq|menubarbuttonshadowoffsety|qq|:|qq||qq|,|qq|menubarbuttonfontsize|qq|:|qq|20|qq|,|qq|menubarbuttonfontcolor|qq|:|qq||qq|,|qq|menubarbuttonlineheight|qq|:|qq||qq|,|qq|topbarheight|qq|:|qq|40|qq|,|qq|topbarbgcolor1|qq|:|qq||qq|,|qq|topbarbgcolor2|qq|:|qq||qq|,|qq|topbarbgopacity|qq|:|qq||qq|,|qq|topbarbgimage|qq|:|qq|/plugins/system/maximenuckmobile/presets/default/fond_haut.jpg|qq|,|qq|topbarbgpositionx|qq|:|qq|left|qq|,|qq|topbarbgpositiony|qq|:|qq|top|qq|,|qq|topbarbgimagerepeat|qq|:|qq|repeat-x|qq|,|qq|topbarmargintop|qq|:|qq||qq|,|qq|topbarmarginright|qq|:|qq||qq|,|qq|topbarmarginbottom|qq|:|qq||qq|,|qq|topbarmarginleft|qq|:|qq||qq|,|qq|topbarpaddingtop|qq|:|qq|10|qq|,|qq|topbarpaddingright|qq|:|qq|10|qq|,|qq|topbarpaddingbottom|qq|:|qq|10|qq|,|qq|topbarpaddingleft|qq|:|qq|10|qq|,|qq|topbarbordercolor|qq|:|qq||qq|,|qq|topbarbordertopwidth|qq|:|qq||qq|,|qq|topbarborderrightwidth|qq|:|qq||qq|,|qq|topbarborderbottomwidth|qq|:|qq||qq|,|qq|topbarborderleftwidth|qq|:|qq||qq|,|qq|topbarborderstyle|qq|:|qq|solid|qq|,|qq|topbarroundedcornerstl|qq|:|qq||qq|,|qq|topbarroundedcornerstr|qq|:|qq||qq|,|qq|topbarroundedcornersbr|qq|:|qq||qq|,|qq|topbarroundedcornersbl|qq|:|qq||qq|,|qq|topbarshadowcolor|qq|:|qq||qq|,|qq|topbarshadowblur|qq|:|qq||qq|,|qq|topbarshadowspread|qq|:|qq||qq|,|qq|topbarshadowoffsetx|qq|:|qq||qq|,|qq|topbarshadowoffsety|qq|:|qq||qq|,|qq|topbarfontsize|qq|:|qq|20|qq|,|qq|topbarfontcolor|qq|:|qq||qq|,|qq|topbarlineheight|qq|:|qq|20|qq|,|qq|topbartextindent|qq|:|qq|20|qq|,|qq|topbarfontfamily|qq|:|qq||qq|,|qq|topbarbuttoncontentcustomtext|qq|:|qq||qq|,|qq|topbarbuttoncontent|qq|:|qq|none|qq|,|qq|topbarbuttonheight|qq|:|qq|31|qq|,|qq|topbarbuttonwidth|qq|:|qq|31|qq|,|qq|topbarbuttonbgcolor1|qq|:|qq||qq|,|qq|topbarbuttonbgcolor2|qq|:|qq||qq|,|qq|topbarbuttonbgopacity|qq|:|qq||qq|,|qq|topbarbuttonbgimage|qq|:|qq|/plugins/system/maximenuckmobile/presets/default/close.jpg|qq|,|qq|topbarbuttonbgpositionx|qq|:|qq|center|qq|,|qq|topbarbuttonbgpositiony|qq|:|qq|center|qq|,|qq|topbarbuttonmargintop|qq|:|qq|5|qq|,|qq|topbarbuttonmarginright|qq|:|qq|10|qq|,|qq|topbarbuttonmarginbottom|qq|:|qq|5|qq|,|qq|topbarbuttonmarginleft|qq|:|qq|10|qq|,|qq|topbarbuttonpaddingtop|qq|:|qq||qq|,|qq|topbarbuttonpaddingright|qq|:|qq||qq|,|qq|topbarbuttonpaddingbottom|qq|:|qq||qq|,|qq|topbarbuttonpaddingleft|qq|:|qq||qq|,|qq|topbarbuttonbordercolor|qq|:|qq||qq|,|qq|topbarbuttonbordertopwidth|qq|:|qq||qq|,|qq|topbarbuttonborderrightwidth|qq|:|qq||qq|,|qq|topbarbuttonborderbottomwidth|qq|:|qq||qq|,|qq|topbarbuttonborderleftwidth|qq|:|qq||qq|,|qq|topbarbuttonborderstyle|qq|:|qq|solid|qq|,|qq|topbarbuttonroundedcornerstl|qq|:|qq||qq|,|qq|topbarbuttonroundedcornerstr|qq|:|qq||qq|,|qq|topbarbuttonroundedcornersbr|qq|:|qq||qq|,|qq|topbarbuttonroundedcornersbl|qq|:|qq||qq|,|qq|topbarbuttonshadowcolor|qq|:|qq||qq|,|qq|topbarbuttonshadowblur|qq|:|qq||qq|,|qq|topbarbuttonshadowspread|qq|:|qq||qq|,|qq|topbarbuttonshadowoffsetx|qq|:|qq||qq|,|qq|topbarbuttonshadowoffsety|qq|:|qq||qq|,|qq|topbarbuttonfontsize|qq|:|qq||qq|,|qq|topbarbuttonfontcolor|qq|:|qq||qq|,|qq|topbarbuttonlineheight|qq|:|qq||qq|,|qq|topbarbuttontextindent|qq|:|qq||qq|,|qq|menubgcolor1|qq|:|qq|#32373B|qq|,|qq|menubgcolor2|qq|:|qq||qq|,|qq|menubgopacity|qq|:|qq||qq|,|qq|menubgimage|qq|:|qq||qq|,|qq|menubgpositionx|qq|:|qq||qq|,|qq|menubgpositiony|qq|:|qq||qq|,|qq|menumargintop|qq|:|qq||qq|,|qq|menumarginright|qq|:|qq||qq|,|qq|menumarginbottom|qq|:|qq||qq|,|qq|menumarginleft|qq|:|qq||qq|,|qq|menupaddingtop|qq|:|qq||qq|,|qq|menupaddingright|qq|:|qq||qq|,|qq|menupaddingbottom|qq|:|qq||qq|,|qq|menupaddingleft|qq|:|qq||qq|,|qq|menubordercolor|qq|:|qq||qq|,|qq|menubordertopwidth|qq|:|qq||qq|,|qq|menuborderrightwidth|qq|:|qq||qq|,|qq|menuborderbottomwidth|qq|:|qq||qq|,|qq|menuborderleftwidth|qq|:|qq||qq|,|qq|menuborderstyle|qq|:|qq|solid|qq|,|qq|menuroundedcornerstl|qq|:|qq||qq|,|qq|menuroundedcornerstr|qq|:|qq||qq|,|qq|menuroundedcornersbr|qq|:|qq||qq|,|qq|menuroundedcornersbl|qq|:|qq||qq|,|qq|menushadowcolor|qq|:|qq||qq|,|qq|menushadowblur|qq|:|qq||qq|,|qq|menushadowspread|qq|:|qq||qq|,|qq|menushadowoffsetx|qq|:|qq||qq|,|qq|menushadowoffsety|qq|:|qq||qq|,|qq|menufontsize|qq|:|qq|15|qq|,|qq|menufontcolor|qq|:|qq|#FFFFFF|qq|,|qq|menulineheight|qq|:|qq||qq|,|qq|menutextindent|qq|:|qq||qq|,|qq|menufontfamily|qq|:|qq|Arial|qq|,|qq|menufontweight|qq|:|qq|normal|qq|,|qq|level1menuitemheight|qq|:|qq|35|qq|,|qq|level1menuitembgcolor1|qq|:|qq||qq|,|qq|level1menuitembgcolor2|qq|:|qq||qq|,|qq|level1menuitembgopacity|qq|:|qq||qq|,|qq|level1menuitembgimage|qq|:|qq|/plugins/system/maximenuckmobile/presets/default/arrow.jpg|qq|,|qq|level1menuitembgpositionx|qq|:|qq|left |qq|,|qq|level1menuitembgpositiony|qq|:|qq|bottom|qq|,|qq|level1menuitembgimagerepeat|qq|:|qq|no-repeat|qq|,|qq|level1menuitemmargintop|qq|:|qq||qq|,|qq|level1menuitemmarginright|qq|:|qq||qq|,|qq|level1menuitemmarginbottom|qq|:|qq||qq|,|qq|level1menuitemmarginleft|qq|:|qq||qq|,|qq|level1menuitempaddingtop|qq|:|qq||qq|,|qq|level1menuitempaddingright|qq|:|qq||qq|,|qq|level1menuitempaddingbottom|qq|:|qq||qq|,|qq|level1menuitempaddingleft|qq|:|qq|45|qq|,|qq|level1menuitembordercolor|qq|:|qq||qq|,|qq|level1menuitembordertopwidth|qq|:|qq||qq|,|qq|level1menuitemborderrightwidth|qq|:|qq||qq|,|qq|level1menuitemborderbottomwidth|qq|:|qq||qq|,|qq|level1menuitemborderleftwidth|qq|:|qq||qq|,|qq|level1menuitemborderstyle|qq|:|qq|solid|qq|,|qq|level1menuitemroundedcornerstl|qq|:|qq||qq|,|qq|level1menuitemroundedcornerstr|qq|:|qq||qq|,|qq|level1menuitemroundedcornersbr|qq|:|qq||qq|,|qq|level1menuitemroundedcornersbl|qq|:|qq||qq|,|qq|level1menuitemshadowcolor|qq|:|qq||qq|,|qq|level1menuitemshadowblur|qq|:|qq||qq|,|qq|level1menuitemshadowspread|qq|:|qq||qq|,|qq|level1menuitemshadowoffsetx|qq|:|qq||qq|,|qq|level1menuitemshadowoffsety|qq|:|qq||qq|,|qq|level1menuitemfontsize|qq|:|qq||qq|,|qq|level1menuitemfontcolor|qq|:|qq|#FFFFFF|qq|,|qq|level1menuitemlineheight|qq|:|qq|35|qq|,|qq|level1menuitemtextindent|qq|:|qq||qq|,|qq|level1menuitemfontfamily|qq|:|qq||qq|,|qq|level2menuitemheight|qq|:|qq|35|qq|,|qq|level2menuitembgcolor1|qq|:|qq||qq|,|qq|level2menuitembgcolor2|qq|:|qq||qq|,|qq|level2menuitembgopacity|qq|:|qq||qq|,|qq|level2menuitembgimage|qq|:|qq|/plugins/system/maximenuckmobile/presets/default/arrow2.jpg|qq|,|qq|level2menuitembgpositionx|qq|:|qq|left|qq|,|qq|level2menuitembgpositiony|qq|:|qq|center|qq|,|qq|level2menuitembgimagerepeat|qq|:|qq|no-repeat|qq|,|qq|level2menuitemmargintop|qq|:|qq||qq|,|qq|level2menuitemmarginright|qq|:|qq||qq|,|qq|level2menuitemmarginbottom|qq|:|qq||qq|,|qq|level2menuitemmarginleft|qq|:|qq||qq|,|qq|level2menuitempaddingtop|qq|:|qq||qq|,|qq|level2menuitempaddingright|qq|:|qq||qq|,|qq|level2menuitempaddingbottom|qq|:|qq||qq|,|qq|level2menuitempaddingleft|qq|:|qq|55|qq|,|qq|level2menuitembordercolor|qq|:|qq||qq|,|qq|level2menuitembordertopwidth|qq|:|qq||qq|,|qq|level2menuitemborderrightwidth|qq|:|qq||qq|,|qq|level2menuitemborderbottomwidth|qq|:|qq||qq|,|qq|level2menuitemborderleftwidth|qq|:|qq||qq|,|qq|level2menuitemborderstyle|qq|:|qq|solid|qq|,|qq|level2menuitemroundedcornerstl|qq|:|qq||qq|,|qq|level2menuitemroundedcornerstr|qq|:|qq||qq|,|qq|level2menuitemroundedcornersbr|qq|:|qq||qq|,|qq|level2menuitemroundedcornersbl|qq|:|qq||qq|,|qq|level2menuitemshadowcolor|qq|:|qq||qq|,|qq|level2menuitemshadowblur|qq|:|qq||qq|,|qq|level2menuitemshadowspread|qq|:|qq||qq|,|qq|level2menuitemshadowoffsetx|qq|:|qq||qq|,|qq|level2menuitemshadowoffsety|qq|:|qq||qq|,|qq|level2menuitemfontsize|qq|:|qq||qq|,|qq|level2menuitemfontcolor|qq|:|qq||qq|,|qq|level2menuitemlineheight|qq|:|qq|35|qq|,|qq|level2menuitemtextindent|qq|:|qq||qq|,|qq|level2menuitemfontfamily|qq|:|qq||qq|,|qq|level3menuitemheight|qq|:|qq|35|qq|,|qq|level3menuitembgcolor1|qq|:|qq||qq|,|qq|level3menuitembgcolor2|qq|:|qq||qq|,|qq|level3menuitembgopacity|qq|:|qq||qq|,|qq|level3menuitembgimage|qq|:|qq|/plugins/system/maximenuckmobile/presets/default/arrow3.png|qq|,|qq|level3menuitembgpositionx|qq|:|qq|20px|qq|,|qq|level3menuitembgpositiony|qq|:|qq|center|qq|,|qq|level3menuitembgimagerepeat|qq|:|qq|no-repeat|qq|,|qq|level3menuitemmargintop|qq|:|qq||qq|,|qq|level3menuitemmarginright|qq|:|qq||qq|,|qq|level3menuitemmarginbottom|qq|:|qq||qq|,|qq|level3menuitemmarginleft|qq|:|qq||qq|,|qq|level3menuitempaddingtop|qq|:|qq||qq|,|qq|level3menuitempaddingright|qq|:|qq||qq|,|qq|level3menuitempaddingbottom|qq|:|qq||qq|,|qq|level3menuitempaddingleft|qq|:|qq|65|qq|,|qq|level3menuitembordercolor|qq|:|qq||qq|,|qq|level3menuitembordertopwidth|qq|:|qq||qq|,|qq|level3menuitemborderrightwidth|qq|:|qq||qq|,|qq|level3menuitemborderbottomwidth|qq|:|qq||qq|,|qq|level3menuitemborderleftwidth|qq|:|qq||qq|,|qq|level3menuitemborderstyle|qq|:|qq|solid|qq|,|qq|level3menuitemroundedcornerstl|qq|:|qq||qq|,|qq|level3menuitemroundedcornerstr|qq|:|qq||qq|,|qq|level3menuitemroundedcornersbr|qq|:|qq||qq|,|qq|level3menuitemroundedcornersbl|qq|:|qq||qq|,|qq|level3menuitemshadowcolor|qq|:|qq||qq|,|qq|level3menuitemshadowblur|qq|:|qq||qq|,|qq|level3menuitemshadowspread|qq|:|qq||qq|,|qq|level3menuitemshadowoffsetx|qq|:|qq||qq|,|qq|level3menuitemshadowoffsety|qq|:|qq||qq|,|qq|level3menuitemfontsize|qq|:|qq||qq|,|qq|level3menuitemfontcolor|qq|:|qq||qq|,|qq|level3menuitemlineheight|qq|:|qq|35|qq|,|qq|level3menuitemtextindent|qq|:|qq||qq|,|qq|level3menuitemfontfamily|qq|:|qq||qq|,|qq|togglericoncontentclosedcustomtext|qq|:|qq||qq|,|qq|togglericoncontentclosed|qq|:|qq||qq|,|qq|togglericoncontentopenedcustomtext|qq|:|qq||qq|,|qq|togglericoncontentopened|qq|:|qq||qq|,|qq|togglericonheight|qq|:|qq|35|qq|,|qq|togglericonwidth|qq|:|qq|35|qq|,|qq|togglericonbgcolor1|qq|:|qq||qq|,|qq|togglericonbgcolor2|qq|:|qq||qq|,|qq|togglericonbgopacity|qq|:|qq||qq|,|qq|togglericonbgimage|qq|:|qq|/plugins/system/maximenuckmobile/presets/default/plus.jpg|qq|,|qq|togglericonbgpositionx|qq|:|qq|center|qq|,|qq|togglericonbgpositiony|qq|:|qq|center|qq|,|qq|togglericonbgimagerepeat|qq|:|qq|no-repeat|qq|,|qq|togglericonmargintop|qq|:|qq||qq|,|qq|togglericonmarginright|qq|:|qq||qq|,|qq|togglericonmarginbottom|qq|:|qq||qq|,|qq|togglericonmarginleft|qq|:|qq||qq|,|qq|togglericonpaddingtop|qq|:|qq||qq|,|qq|togglericonpaddingright|qq|:|qq||qq|,|qq|togglericonpaddingbottom|qq|:|qq||qq|,|qq|togglericonpaddingleft|qq|:|qq||qq|,|qq|togglericonbordercolor|qq|:|qq||qq|,|qq|togglericonbordertopwidth|qq|:|qq||qq|,|qq|togglericonborderrightwidth|qq|:|qq||qq|,|qq|togglericonborderbottomwidth|qq|:|qq||qq|,|qq|togglericonborderleftwidth|qq|:|qq||qq|,|qq|togglericonborderstyle|qq|:null,|qq|togglericonroundedcornerstl|qq|:|qq||qq|,|qq|togglericonroundedcornerstr|qq|:|qq||qq|,|qq|togglericonroundedcornersbr|qq|:|qq||qq|,|qq|togglericonroundedcornersbl|qq|:|qq||qq|,|qq|togglericonshadowcolor|qq|:|qq||qq|,|qq|togglericonshadowblur|qq|:|qq||qq|,|qq|togglericonshadowspread|qq|:|qq||qq|,|qq|togglericonshadowoffsetx|qq|:|qq||qq|,|qq|togglericonshadowoffsety|qq|:|qq||qq|,|qq|togglericonfontsize|qq|:|qq||qq|,|qq|togglericonfontcolor|qq|:|qq||qq|,|qq|togglericonlineheight|qq|:|qq||qq|,|qq|togglericonfontfamily|qq|:|qq||qq|}';
		}
		$fields = str_replace('|qq|', '"', $this->value);
		?>
		<div id="ckgfontstylesheet"></div>
		<div id="ckstyles_menubar"></div>
		<div id="ckmenumobilestyles_alert">
			<div class="alert alert-warning">
				<h4 class="alert-heading"><?php echo JText::_('CK_MESSAGE'); ?></h4>
				<p><?php echo JText::_('CK_CHOOSE_CUSTOM_THEME'); ?></p>
				<p><a href="javascript:void(0)" onclick="set_custom_theme()"><?php echo JText::_('CK_ACTIVATE_CUSTOM_THEME'); ?></a></p>
			</div>
		</div>
		<div id="ckmenumobilestyles_container">
			<div id="preview_area">
				<?php $this->render_previewmenu() ?>
			</div>
			<div id="ckpopupstyleswizard" style="margin-top:10px;">
				<div class="menulink current" tab="tab_menubar"><?php echo JText::_('CK_MENUBAR'); ?></div>
				<div class="menulink" tab="tab_mobilemenu"><?php echo JText::_('CK_MOBILE_MENU'); ?></div>
				<div class="menulink" tab="tab_customcss"><?php echo JText::_('CK_CUSTOM_CSS'); ?></div>
				<div class="menulink" tab="tab_preset"><?php echo JText::_('CK_PRESET'); ?></div>
				<div class="btn-group pull-right">
					<div class="btn" onclick="reset_styles()"><?php echo JText::_('CK_RESET_STYLES'); ?></div>
					<div id="ckpopupstyleswizard_makepreview" class="btn" onclick="preview_stylesparams()" style="height:18px;"><span class="ckwaiticon"></span><?php echo JText::_('CK_PREVIEW'); ?></div>
				</div>
				
				<div class="clr"></div>
				<div class="tab current hascol" id="tab_menubar">
					<div class="ckpopupstyleswizard_col_left">
						<div class="menulink2 current" tab="tab_bar"><?php echo JText::_('PLG_MAXIMENUCK_BAR'); ?></div>
						<div class="menulink2" tab="tab_button"><?php echo JText::_('PLG_MAXIMENUCK_BUTTON'); ?></div>

						<div class="clr"></div>
						
					</div>
					<div class="ckpopupstyleswizard_col_right">
						<div class="tab2 current" id="tab_bar">
							<?php include_once(JPATH_ROOT . $this->path . 'default_render_tab_menubar.php'); ?>
						</div>
						<div class="tab2 hascol" id="tab_button">
							<?php include_once(JPATH_ROOT . $this->path . 'default_render_tab_button.php'); ?>
						</div>
					</div>
					<div style="clear:both;"></div>
				</div>
				<div class="tab hascol" id="tab_mobilemenu">
					<div class="ckpopupstyleswizard_col_left">
						<div class="menulink2 current" tab="tab_menu"><?php echo JText::_('PLG_MAXIMENUCK_MENU'); ?></div>
						<div class="menulink2" tab="tab_topbar"><?php echo JText::_('PLG_MAXIMENUCK_BAR'); ?></div>
						<div class="menulink2" tab="tab_topbutton"><?php echo JText::_('PLG_MAXIMENUCK_BUTTON'); ?></div>
						<div class="menulink2" tab="tab_level1menuitem"><?php echo JText::_('PLG_MAXIMENUCK_LEVEL1_MENUITEMS'); ?></div>
						<div class="menulink2" tab="tab_level2menuitem"><?php echo JText::_('PLG_MAXIMENUCK_LEVEL2_MENUITEMS'); ?></div>
						<div class="menulink2" tab="tab_level3menuitem"><?php echo JText::_('PLG_MAXIMENUCK_LEVEL3_MENUITEMS'); ?></div>
						<div class="menulink2" tab="tab_togglericon"><?php echo JText::_('PLG_MAXIMENUCK_ACCORDION_ICONS'); ?></div>

						<div class="clr"></div>
						
					</div>
					<div class="ckpopupstyleswizard_col_right">
						<div class="tab2 current" id="tab_menu">
							<?php include_once(JPATH_ROOT . $this->path . 'default_render_tab_menu.php'); ?>
						</div>
						<div class="tab2 hascol" id="tab_topbar">
							<?php include_once(JPATH_ROOT . $this->path . 'default_render_tab_topbar.php'); ?>
						</div>
						<div class="tab2 hascol" id="tab_topbutton">
							<?php include_once(JPATH_ROOT . $this->path . 'default_render_tab_topbutton.php'); ?>
						</div>
						<div class="tab2 hascol" id="tab_level1menuitem">
							<?php include_once(JPATH_ROOT . $this->path . 'default_render_tab_level1menuitem.php'); ?>
						</div>
						<div class="tab2 hascol" id="tab_level2menuitem">
							<?php include_once(JPATH_ROOT . $this->path . 'default_render_tab_level2menuitem.php'); ?>
						</div>
						<div class="tab2 hascol" id="tab_level3menuitem">
							<?php include_once(JPATH_ROOT . $this->path . 'default_render_tab_level3menuitem.php'); ?>
						</div>
						<div class="tab2 hascol" id="tab_togglericon">
							<?php include_once(JPATH_ROOT . $this->path . 'default_render_tab_togglericon.php'); ?>
						</div>
					</div>
					<div style="clear:both;"></div>
				</div>
				<div class="tab" id="tab_customcss">
					<textarea id="customcss_area" style="width: 100%;min-height:500px;box-sizing:border-box;"></textarea>
				</div>
				<div class="tab" id="tab_preset">
					<?php include_once(JPATH_ROOT . $this->path . 'default_render_tab_preset.php'); ?>
				</div>
			</div>
		</div>
		<script language="javascript" type="text/javascript">
			jQuery('#ckpopupstyleswizard div.tab:not(.current)').hide();
			jQuery('.menulink', jQuery('#ckpopupstyleswizard')).each(function(i, tab) {
				jQuery(tab).click(function() {
					jQuery('#ckpopupstyleswizard div.tab').hide();
					jQuery('.menulink', jQuery('#ckpopupstyleswizard')).removeClass('current');
					if (jQuery('#' + jQuery(tab).attr('tab')).length)
						jQuery('#' + jQuery(tab).attr('tab')).show();
					this.addClass('current');
				});
			});
			$ck('#ckpopupstyleswizard div.tab2:not(.current)').hide();
			$ck('.menulink2', $ck('#ckpopupstyleswizard')).each(function(i, tab) {
				$ck(tab).click(function() {
					var parent_cont = $ck(tab).parents('.tab')[0];
					$ck('.tab2', parent_cont).hide();
					$ck('.menulink2', parent_cont).removeClass('current');
					if ($ck('#' + $ck(tab).attr('tab')).length)
						$ck('#' + $ck(tab).attr('tab')).show();
					this.addClass('current');
				});
			});
			jQuery(document).ready(function()
			{
				jQuery('.hasTip').tooltip({"html": true,"container": "body"});
//				 window.setInterval("keepAlive()", 600000);
				// loop through the fields to set the value
				set_fields_value('<?php echo $fields ?>');
				
				// set the value for the custom css
				$ck('#customcss_area').val($ck('#jform_params_maximenumobile_customcss').val());
				// preview automatically the menu on page load
				if ($ck('#jform_params_maximenumobile_theme').val() == 'custom') preview_stylesparams();
				
				$ck('#ckpopupstyleswizard input,#ckpopupstyleswizard select').change(function() {
					// launch the preview
					preview_stylesparams();
				});
				$ck('#customcss_area').blur(function() {
					// launch the preview
					preview_stylesparams();
				});
				// check that the theme selected is custom to show the fields
				switch_fields_area();
				$ck('#jform_params_maximenumobile_theme').change(function() {
					switch_fields_area();
				});
			});
			function jModalClose() {
				SqueezeBox.close();
			}
			jscolor.init();
		</script>
		<?php
	}
	
	protected function render_previewmenu() {
		?>
		<div id="preview-mobilebarmaximenuck" class="mobilebarmaximenuck" style="display:block;">
			<span class="mobilebarmenutitleck">Menu</span>
			<div class="mobilebuttonmaximenuck" onclick="jQuery('#preview-mobilebarmaximenuck').hide();jQuery('#testmaximenu-mobile').show();">&#x2261;</div>
		</div>
		<div id="testmaximenu-mobile" class="mobilemaximenuck" style="position: relative; z-index: 100000; display: none;">
				<div class="mobilemaximenucktopbar">
					<span class="mobilemaximenucktitle">Menu</span>
					<span class="mobilemaximenuckclose" onclick="jQuery('#testmaximenu-mobile').hide();jQuery('#preview-mobilebarmaximenuck').show();">×</span>
				</div>
				<div class="mobilemaximenuckitem">
					<div class="maximenuck first level1 ">
						<a href="javascript:void(0)">
							<span class="mobiletextck">Lorem</span>
						</a>
					</div>
				</div>
				<div class="mobilemaximenuckitem">
					<div class="maximenuck parent level1">
						<a href="javascript:void(0)">
							<span class="mobiletextck">Ipsum</span>
						</a>
					</div>
					<div class="mobilemaximenucksubmenu">
						<div class="mobilemaximenuckitem">
							<div class="maximenuck parent first level2 ">
								<a href="javascript:void(0)">
									<span class="mobiletextck">Dolor sit</span>
								</a>
							</div>
							<div class="mobilemaximenucksubmenu">
								<div class="mobilemaximenuckitem">
									<div class="maximenuck first level3 ">
										<a href="javascript:void(0)">
											<span class="mobiletextck">Consectetur</span>
										</a>
									</div>
								</div>
								<div class="mobilemaximenuckitem">
									<div class="maximenuck last level3 ">
										<a href="javascript:void(0)">
											<span class="mobiletextck">Adipiscing</span>
										</a>
									</div>
								</div>
							</div>
						</div>
						<div class="mobilemaximenuckitem">
							<div class="maximenuck parent level2 ">
								<a href="javascript:void(0)">
									<span class="mobiletextck">Sed maximus</span>
								</a>
							</div>
							<div class="mobilemaximenucksubmenu">
								<div class="mobilemaximenuckitem">
									<div class="maximenuck first level3 ">
										<a href="javascript:void(0)">
											<span class="mobiletextck">Vivamus</span>
										</a>
									</div>
								</div>
								<div class="mobilemaximenuckitem">
									<div class="maximenuck level3 ">
										<a href="javascript:void(0)">
											<span class="mobiletextck">Fusce porta</span>
										</a>
									</div>
								</div>
								<div class="mobilemaximenuckitem">
									<div class="maximenuck last level3 ">
										<a href="javascript:void(0)">
											<span class="mobiletextck">Pellentesque</span>
										</a>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				
				<div class="mobilemaximenuckitem">
					<div class="maximenuck parent level1 accordionmobileck">
						<a href="javascript:void(0)">
							<span class="mobiletextck">Maecenas</span>
						</a>
						<div class="mobilemaximenucktogglericon" onclick="jQuery(this).parent().toggleClass('open')"></div>
					</div>
					<div class="mobilemaximenucksubmenu">
						<div class="mobilemaximenuckitem">
							<div class="maximenuck parent first level2 accordionmobileck">
								<a href="javascript:void(0)">
									<span class="mobiletextck">Vel convallis</span>
								</a>
								<div class="mobilemaximenucktogglericon" onclick="jQuery(this).parent().toggleClass('open')"></div>
							</div>
							<div class="mobilemaximenucksubmenu">
								<div class="mobilemaximenuckitem">
									<div class="maximenuck first level3 ">
									<a href="javascript:void(0)">
									<span class="mobiletextck">Facilisis</span>
									</a>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php
	}

	protected function getInput() {
		?>
		<input name="<?php echo $this->name ?>" id="<?php echo $this->id ?>" type="hidden" value="<?php echo $this->value ?>" />
		<?php
		// if the plugin is not enabled, we leave to avoir problems
		if (! JPluginHelper::isEnabled('system', 'maximenuckmobile')) {
			?>
			<div id="ckmenumobilestyles_alert">
				<div class="alert alert-error">
					<h4 class="alert-heading"><?php echo JText::_('CK_MESSAGE'); ?></h4>
					<p><?php echo JText::_('CK_PLUGIN_DISABLED'); ?></p>
				</div>
			</div>
			<?php
			return;
		}
		$document = JFactory::getDocument();
		$document->addStylesheet(JUri::root(true) . $this->path . 'ckmenumobilestyles.css');
		$document->addScript(JUri::root(true) . $this->path . '/jscolor/jscolor.js');
		$document->addScript(JUri::root(true) . $this->path . 'ckmenumobilestyles.js');

		// render the interface
		$this->createFields();

		$document->addScriptDeclaration("JURI='" . JURI::root() . "';");
		JHTML::_('behavior.modal');
	}

	protected function getPathToElements() {
		$localpath = dirname(__FILE__);
		$rootpath = JPATH_ROOT;
		$httppath = trim(JURI::root(), "/");
		$pathtoelements = str_replace("\\", "/", str_replace($rootpath, $httppath, $localpath));
		return $pathtoelements;
	}

	protected function getLabel() {

		return '';
	}
}

