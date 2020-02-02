<?php
/**
 * @name		Accordeon Menu CK params
 * @package		com_accordeonck
 * @copyright	Copyright (C) 2014. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author		Cedric Keiflin - http://www.template-creator.com - http://www.joomlack.fr
 */

defined('_JEXEC') or die;
$input = new JInput();
$id = $input->get('id', '', 'int');
$popupclass = ($input->get('layout', '', 'string') === 'modal') ? 'ckpopupwizard' : '';
$preview_width = ($this->params->get('orientation', 'horizontal') == 'vertical') ? 'width:200px;' : '';
?>
<input id="checkfirstuse" type="hidden" value="<?php echo (int)($this->params->get('level1itemnormalstyles') != '[]') ?>" />
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
		<div class="ckgfontstylesheet"></div>
		<div class="ckstyle"></div>
		<div class="inner" style="<?php echo $preview_width; ?>">
			<?php echo $this->loadTemplate('render_menu_module'); ?>
		</div>
	</div>
	<div id="ckpopupstyleswizard_options">
		<div class="menulink current" tab="tab_mainmenu"><?php echo JText::_('CK_MAINMENU'); ?></div>
		<div class="menulink" tab="tab_submenu"><?php echo JText::_('CK_SUBMENU'); ?> - <span><?php echo JText::_('CK_LEVEL'); ?> 2</span></div>
		<div class="menulink" tab="tab_subsubmenu"><?php echo JText::_('CK_SUBMENU'); ?> - <span><?php echo JText::_('CK_LEVEL'); ?> 3+</span></div>
		<div class="menulink" tab="tab_customcss"><?php echo JText::_('CK_CUSTOM_CSS'); ?></div>
		<div class="menulink" tab="tab_presets"><?php echo JText::_('CK_PRESETS'); ?></div>
		<div class="clr"></div>
		<div class="tab current hascol" id="tab_mainmenu">
			<div class="ckpopupstyleswizard_col_left">
				<div class="menulink2 current" tab="tab_menustyles"><?php echo JText::_('CK_MENUBAR'); ?></div>
				<div class="menulink2" tab="tab_level1itemgroup"><?php echo JText::_('CK_LINKS_GROUP'); ?></div>
				<div class="menulink2" tab="tab_level1itemnormalstyles"><?php echo JText::_('CK_MENULINK'); ?></div>
				<div class="menulink2" tab="tab_level1itemhoverstyles"><?php echo JText::_('CK_MENULINK_HOVER'); ?></div>
				<div class="menulink2" tab="tab_level1itemparentarrow"><?php echo JText::_('CK_PARENT_ARROW'); ?></div>
				
			</div>
			<div class="ckpopupstyleswizard_col_right">
				<div class="tab2 current" id="tab_menustyles">
					<?php echo $this->loadTemplate('render_tab_menustyles'); ?>
				</div>
				<div class="tab2" id="tab_level1itemgroup">
					<?php echo $this->loadTemplate('render_tab_level1itemgroup'); ?>
				</div>
				<div class="tab2" id="tab_level1itemnormalstyles">
					<?php echo $this->loadTemplate('render_tab_level1itemnormalstyles'); ?>
				</div>
				<div class="tab2" id="tab_level1itemhoverstyles">
					<?php echo $this->loadTemplate('render_tab_level1itemhoverstyles'); ?>
				</div>
				<div class="tab2" id="tab_level1itemparentarrow">
					<?php echo $this->loadTemplate('render_tab_level1itemparentarrow'); ?>
				</div>
			</div>
			<div style="clear:both;"></div>
		</div>
		<div class="tab hascol" id="tab_submenu">
			<div class="ckpopupstyleswizard_col_left">
				<div class="menulink2 current" tab="tab_level2menustyles"><?php echo JText::_('CK_SUBMENU'); ?></div>
				<div class="menulink2" tab="tab_level2itemgroup"><?php echo JText::_('CK_LINKS_GROUP'); ?></div>
				<div class="menulink2" tab="tab_level2itemnormalstyles"><?php echo JText::_('CK_SUBMENULINK'); ?></div>
				<div class="menulink2" tab="tab_level2itemhoverstyles"><?php echo JText::_('CK_SUBMENULINK_HOVER'); ?></div>
				<div class="menulink2" tab="tab_level2itemparentarrow"><?php echo JText::_('CK_PARENT_ARROW'); ?></div>
				
			</div>
			<div class="ckpopupstyleswizard_col_right">
				<div class="tab2 current" id="tab_level2menustyles">
					<?php echo $this->loadTemplate('render_tab_level2menustyles'); ?>
				</div>
				<div class="tab2" id="tab_level2itemgroup">
					<?php echo $this->loadTemplate('render_tab_level2itemgroup'); ?>
				</div>
				<div class="tab2" id="tab_level2itemnormalstyles">
					<?php echo $this->loadTemplate('render_tab_level2itemnormalstyles'); ?>
				</div>
				<div class="tab2" id="tab_level2itemhoverstyles">
					<?php echo $this->loadTemplate('render_tab_level2itemhoverstyles'); ?>
				</div>
				<div class="tab2" id="tab_level2itemparentarrow">
					<?php echo $this->loadTemplate('render_tab_level2itemparentarrow'); ?>
				</div>
			</div>
			<div style="clear:both;"></div>
		</div>
		<div class="tab hascol" id="tab_subsubmenu">
			<div class="ckpopupstyleswizard_col_left">
				<div class="menulink2 current" tab="tab_level3menustyles"><?php echo JText::_('CK_SUBMENU'); ?></div>
				<div class="menulink2" tab="tab_level3itemgroup"><?php echo JText::_('CK_LINKS_GROUP'); ?></div>
				<div class="menulink2" tab="tab_level3itemnormalstyles"><?php echo JText::_('CK_SUBMENULINK'); ?></div>
				<div class="menulink2" tab="tab_level3itemhoverstyles"><?php echo JText::_('CK_SUBMENULINK_HOVER'); ?></div>
				<div class="menulink2" tab="tab_level3itemparentarrow"><?php echo JText::_('CK_PARENT_ARROW'); ?></div>
				
			</div>
			<div class="ckpopupstyleswizard_col_right">
				<div class="tab2 current" id="tab_level3menustyles">
					<?php echo $this->loadTemplate('render_tab_level3menustyles'); ?>
				</div>
				<div class="tab2" id="tab_level3itemgroup">
					<?php echo $this->loadTemplate('render_tab_level3itemgroup'); ?>
				</div>
				<div class="tab2" id="tab_level3itemnormalstyles">
					<?php echo $this->loadTemplate('render_tab_level3itemnormalstyles'); ?>
				</div>
				<div class="tab2" id="tab_level3itemhoverstyles">
					<?php echo $this->loadTemplate('render_tab_level3itemhoverstyles'); ?>
				</div>
				<div class="tab2" id="tab_level3itemparentarrow">
					<?php echo $this->loadTemplate('render_tab_level3itemparentarrow'); ?>
				</div>
			</div>
			<div style="clear:both;"></div>
		</div>
		<div class="tab" id="tab_presets">
			<?php echo $this->loadTemplate('themes'); ?>
		</div>
		<div class="tab" id="tab_customcss">
			<textarea id="customcss" style="width: 100%;min-height:500px;box-sizing:border-box;"></textarea>
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
		// if first time, ask to import styles from module
		if ($ck('#checkfirstuse').val() == '0') {
			importParamsFirstUse('<?php echo $input->get('id',0,'int'); ?>');
		}
	});
</script>
