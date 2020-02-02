<?php
/**
 * @name		Maximenu CK params
 * @package		com_maximenuck
 * @copyright	Copyright (C) 2014. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author		Cedric Keiflin - http://www.template-creator.com - http://www.joomlack.fr
 */

defined('_JEXEC') or die;
$input = new JInput();
$popupclass = ($input->get('layout', '', 'string') === 'modal') ? 'ckpopupwizard' : '';
$preview_width = ($this->params->get('orientation', 'horizontal') == 'vertical') ? 'width:200px;' : '';
?>
<div id="ckpopupstyleswizard" class="<?php echo $popupclass; ?>">
	<?php if ($input->get('layout', '', 'string') === 'modal') {
		echo $this->loadTemplate('mainmenu'); 
	} ?>
	<?php
	// detection for IE
	if(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== FALSE || strpos($_SERVER['HTTP_USER_AGENT'], 'Trident/7.0; rv:11.0') !== FALSE ) { ?>
	<div class="errorck" style="margin:0 10px;">
		<?php echo JText::_('CK_PLEASE_DO_NOT_USE_IE'); ?>
	</div>
	<?php } ?>
	<div id="ckpopupstyleswizard_preview">
		<div class="ckstylesheet">
			<div class="ckstylesheet">
				<link type="text/css" href="<?php echo JUri::root(true); ?>/modules/mod_maximenuck/themes/<?php echo $this->params->get('theme'); ?>/css/maximenuck.php?monid=maximenuck_previewmodule" rel="stylesheet">
			</div>
		</div>
		<div class="ckgfontstylesheet"></div>
		<div class="ckstyle"></div>
		<div class="inner" style="<?php echo $preview_width; ?>">
			<?php echo $this->loadTemplate('render_menu_module'); ?>
		</div>
	</div>
	<div id="ckpopupstyleswizard_options">
		<div class="menulink current" tab="tab_mainmenu"><?php echo JText::_('CK_MAINMENU'); ?></div>
		<div class="menulink" tab="tab_submenu"><?php echo JText::_('CK_SUBMENU'); ?></div>
		<div class="menulink" tab="tab_layout"><?php echo JText::_('CK_LAYOUT'); ?></div>
		<div class="menulink" tab="tab_themes"><?php echo JText::_('CK_THEMES'); ?></div>
		<div class="clr"></div>
		<div class="tab current hascol" id="tab_mainmenu">
			<div class="ckpopupstyleswizard_col_left">
				<div class="menulink2 current" tab="tab_menustyles"><?php echo JText::_('CK_MENUBAR'); ?></div>
				<div class="menulink2" tab="tab_level1itemnormalstyles"><?php echo JText::_('CK_MENULINK'); ?></div>
				<div class="menulink2" tab="tab_level1itemhoverstyles"><?php echo JText::_('CK_MENULINK_HOVER'); ?></div>
				<div class="menulink2" tab="tab_level1itemactivestyles"><?php echo JText::_('CK_MENULINK_ACTIVE'); ?></div>
				<div class="menulink2" tab="tab_level1itemparentarrow"><?php echo JText::_('CK_PARENT_ARROW'); ?></div>
				<div class="menulink2" tab="tab_level1itemicon"><?php echo JText::_('CK_ITEM_ICON'); ?></div>
			</div>
			<div class="ckpopupstyleswizard_col_right">
				<div class="tab2 current" id="tab_menustyles">
					<?php echo $this->loadTemplate('render_tab_menustyles'); ?>
				</div>
				<div class="tab2" id="tab_level1itemnormalstyles">
					<?php echo $this->loadTemplate('render_tab_level1itemnormalstyles'); ?>
				</div>
				<div class="tab2" id="tab_level1itemhoverstyles">
					<?php echo $this->loadTemplate('render_tab_level1itemhoverstyles'); ?>
				</div>
				<div class="tab2" id="tab_level1itemactivestyles">
					<?php echo $this->loadTemplate('render_tab_level1itemactivestyles'); ?>
				</div>
				<div class="tab2" id="tab_level1itemparentarrow">
					<?php echo $this->loadTemplate('render_tab_level1itemparentarrow'); ?>
				</div>
				<div class="tab2" id="tab_level1itemicon">
					<?php echo $this->loadTemplate('render_tab_level1itemicon'); ?>
				</div>
			</div>
			<div style="clear:both;"></div>
		</div>
		<div class="tab hascol" id="tab_submenu">
			<div class="ckpopupstyleswizard_col_left">
				<div class="menulink2 current" tab="tab_level2menustyles"><?php echo JText::_('CK_SUBMENU'); ?></div>
				<div class="menulink2" tab="tab_level2itemnormalstyles"><?php echo JText::_('CK_SUBMENULINK'); ?></div>
				<div class="menulink2" tab="tab_level2itemhoverstyles"><?php echo JText::_('CK_SUBMENULINK_HOVER'); ?></div>
				<div class="menulink2" tab="tab_level2itemactivestyles"><?php echo JText::_('CK_SUBMENULINK_ACTIVE'); ?></div>
				<div class="menulink2" tab="tab_level2itemparentarrow"><?php echo JText::_('CK_PARENT_ARROW'); ?></div>
				<div class="menulink2" tab="tab_level2heading"><?php echo JText::_('CK_COLUMN_HEADING'); ?></div>
				<div class="menulink2" tab="tab_level2itemicon"><?php echo JText::_('CK_ITEM_ICON'); ?></div>
			</div>
			<div class="ckpopupstyleswizard_col_right">
				<div class="tab2 current" id="tab_level2menustyles">
					<?php echo $this->loadTemplate('render_tab_level2menustyles'); ?>
				</div>
				<div class="tab2" id="tab_level2itemnormalstyles">
					<?php echo $this->loadTemplate('render_tab_level2itemnormalstyles'); ?>
				</div>
				<div class="tab2" id="tab_level2itemhoverstyles">
					<?php echo $this->loadTemplate('render_tab_level2itemhoverstyles'); ?>
				</div>
				<div class="tab2" id="tab_level2itemactivestyles">
					<?php echo $this->loadTemplate('render_tab_level2itemactivestyles'); ?>
				</div>
				<div class="tab2" id="tab_level2itemparentarrow">
					<?php echo $this->loadTemplate('render_tab_level2itemparentarrow'); ?>
				</div>
				<div class="tab2" id="tab_level2heading">
					<?php echo $this->loadTemplate('render_tab_heading'); ?>
				</div>
				<div class="tab2" id="tab_level2itemicon">
					<?php echo $this->loadTemplate('render_tab_level2itemicon'); ?>
				</div>
			</div>
			<div style="clear:both;"></div>
		</div>
		<div class="tab" id="tab_layout">
			<input type="hidden" id="theme" name="theme" value="" />
			<?php echo $this->loadTemplate('layout'); ?>
		</div>
		<div class="tab" id="tab_themes">
			<input type="hidden" id="theme" name="theme" value="" />
			<?php echo $this->loadTemplate('themes'); ?>
		</div>
	</div>
	<div style="clear:both;"></div>
</div>
<script language="javascript" type="text/javascript">
	$ck('#ckpopupstyleswizard_options div.tab:not(.current)').hide();
	$ck('.menulink', $ck('#ckpopupstyleswizard_options')).each(function(i, tab) {
		$ck(tab).click(function() {
			$ck('#ckpopupstyleswizard_options div.tab').hide();
			$ck('.menulink', $ck('#ckpopupstyleswizard_options')).removeClass('current');
			if ($ck('#' + $ck(tab).attr('tab')).length)
				$ck('#' + $ck(tab).attr('tab')).show();
			this.addClass('current');
		});
	});
	
	$ck('#ckpopupstyleswizard_options div.tab2:not(.current)').hide();
	$ck('.menulink2', $ck('#ckpopupstyleswizard_options')).each(function(i, tab) {
		$ck(tab).click(function() {
			var parent_cont = $ck(tab).parents('.tab')[0];
			$ck('.tab2', parent_cont).hide();
			$ck('.menulink2', parent_cont).removeClass('current');
			if ($ck('#' + $ck(tab).attr('tab')).length)
				$ck('#' + $ck(tab).attr('tab')).show();
			this.addClass('current');
		});
	});

	jQuery(document).ready(function(){
		$ck('#ckpopupstyleswizard input,#ckpopupstyleswizard select').change(function() {
			// launch the preview
			preview_stylesparams('#ckpopupstyleswizard_makepreview');
		});
		load_module_theme('<?php echo $input->get('id',0,'int'); ?>');
		load_stylesparams('<?php echo $input->get('id',0,'int'); ?>');
	});
</script>
