<?php

/**
 * @copyright	Copyright (C) 2011 Cedric KEIFLIN alias ced1870
 * http://www.joomlack.fr
 * @license		GNU/GPL
 * */
// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.form');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
// error_reporting(0);

class JFormFieldCkmaximenuwizard extends JFormField {

	protected $type = 'ckmaximenuwizard';

	protected function createFields($form) {
		// if (!count($fields))
			// return false;
		$identifier = 'ckmaximenuwizard';
		?>
		<div id="ckpopupwizard" style="display:none;" class="ckpopupwizard">
			<div class="ck_buttons_right">
				<div class="ck_button big">
					<a href="javascript:void(0);" id="ckpopupstyleswizard_save" class="cksave black" onclick="javascript:saveWizardCK('#ckpopupwizard', '<?php echo $this->name ?>', 'ckmaximenuwizard');">
						<?php echo JText::_('SAVE') ?>
					</a>
				</div>
				<div class="ck_button big">
					<a href="javascript:void(0)" class="ckcancel" onclick="window.parent.CKBox.close()">
						<?php echo JText::_('CLOSE') ?>
					</a>
				</div>
			</div>
<div id="ckpopupwizard_title" class="ckpopupwizard_title"><?php echo JText::_('MAXIMENUCK_WIZARD') ?></div>
<div style="clear:both;"></div>
<div class="ckpopupwizard_index active">1</div>
<div class="ckpopupwizard_index">2</div>
<div class="ckpopupwizard_index">3</div>
<div class="ckpopupwizard_index">4</div>
<div class="ckpopupwizard_index">5</div>
<div class="ckpopupwizard_indexline"></div>
<div class="ckpopupwizard_indexline active"></div>
<div id="ckpopupwizard_slider" data-index="0">
	<div class="inner">
		<div class="ckpopupwizard_slider">
			<div class="ckpopupwizard_heading"><?php echo JText::_('MAXIMENUCK_WIZARD_MENU_TO_RENDER') ?></div>
			<div class="ckpopupwizard_optionsselect_area">
				<div class="ckpopupwizard_select active" data-type="none" data-target="ckpopupwizard_menutorenderoptions_joomla" onclick="changeFieldValue('ckmaximenuwizard_thirdparty','none');showActiveOptions(this)"><?php echo JText::_('MAXIMENUCK_WIZARD_JOOMLA_MENU') ?>
					<div class="ckpopupwizard_select_desc"><?php echo JText::_('MAXIMENUCK_WIZARD_JOOMLA_MENU_DESC') ?></div>
				</div>
				<div class="ckpopupwizard_select" data-type="k2" data-target="ckpopupwizard_menutorenderoptions_k2" onclick="changeFieldValue('ckmaximenuwizard_thirdparty','k2');showActiveOptions(this)"><?php echo JText::_('MAXIMENUCK_WIZARD_K2_MENU') ?>
					<div class="ckpopupwizard_select_desc"><?php echo JText::_('MAXIMENUCK_WIZARD_K2_MENU_DESC') ?></div>
				</div>
				<div class="ckpopupwizard_select" data-type="hikashop" data-target="ckpopupwizard_menutorenderoptions_hikashop" onclick="changeFieldValue('ckmaximenuwizard_thirdparty','hikashop');showActiveOptions(this)"><?php echo JText::_('MAXIMENUCK_WIZARD_HIKASHOP_MENU') ?>
					<div class="ckpopupwizard_select_desc"><?php echo JText::_('MAXIMENUCK_WIZARD_HIKASHOP_MENU_DESC') ?></div>
				</div>
				<div class="ckpopupwizard_select" data-type="joomshopping" data-target="ckpopupwizard_menutorenderoptions_joomshopping" onclick="changeFieldValue('ckmaximenuwizard_thirdparty','joomshopping');showActiveOptions(this)"><?php echo JText::_('MAXIMENUCK_WIZARD_JOOMSHOPPING_MENU') ?>
					<div class="ckpopupwizard_select_desc"><?php echo JText::_('MAXIMENUCK_WIZARD_JOOMSHOPPING_MENU_DESC') ?></div>
				</div>
				<input type="hidden" id="ckmaximenuwizard_thirdparty" class="ckmaximenuwizard_inputbox" />
				<br />
				<div id="ckpopupwizard_select_desc_area"></div>
			</div>
			<div class="ckpopupwizard_options_area">
				<div id="ckpopupwizard_menutorenderoptions_joomla">
				<?php foreach ($form->getFieldset('ckmaximenuwizard_menujoomlaoptions') as $key => $field) : ?>
					<div class="ckpopupwizard_row">
						<div class="ckpopupwizard_label"><?php echo $form->getLabel(str_replace($identifier . "_", "", $key), $identifier); ?></div>
						<div class="ckpopupwizard_input"><?php echo $form->getInput(str_replace($identifier . "_", "", $key), $identifier); ?></div>
					</div>
				<?php endforeach; ?>
				</div>
				<div id="ckpopupwizard_menutorenderoptions_k2">
				<?php
				if (JFolder::exists(JPATH_ROOT . '/administrator/components/com_k2')
					AND JFile::exists(JPATH_ROOT . '/modules/mod_maximenuck/helper_k2.php')) {
				foreach ($form->getFieldset('ckmaximenuwizard_menuk2options') as $key => $field)  : ?> 
					<div class="ckpopupwizard_row">
						<div class="ckpopupwizard_label"><?php echo $form->getLabel(str_replace($identifier . "_", "", $key), $identifier); ?></div>
						<div class="ckpopupwizard_input"><?php echo $form->getInput(str_replace($identifier . "_", "", $key), $identifier); ?></div>
					</div>
				<?php endforeach; 
				} else {
					if (!JFile::exists(JPATH_ROOT . '/modules/mod_maximenuck/helper_k2.php'))
						echo '<p>' . JText::_('MOD_MAXIMENUCK_SPACER_K2_PATCH') . '</p>';
					if (!JFolder::exists(JPATH_ROOT . '/administrator/components/com_k2'))
						echo '<p>' . JText::_('MOD_MAXIMENUCK_K2_NOTFOUND') . '</p>';
				}?>
				</div>
				<div id="ckpopupwizard_menutorenderoptions_hikashop">
				<?php
				if (JFolder::exists(JPATH_ROOT . '/administrator/components/com_hikashop')
					AND JFile::exists(JPATH_ROOT . '/modules/mod_maximenuck/helper_hikashop.php')) {
				foreach ($form->getFieldset('ckmaximenuwizard_menuhikashopoptions') as $key => $field)  : ?> 
					<div class="ckpopupwizard_row">
						<div class="ckpopupwizard_label"><?php echo $form->getLabel(str_replace($identifier . "_", "", $key), $identifier); ?></div>
						<div class="ckpopupwizard_input"><?php echo $form->getInput(str_replace($identifier . "_", "", $key), $identifier); ?></div>
					</div>
				<?php endforeach; 
				} else {
					if (!JFile::exists(JPATH_ROOT . '/modules/mod_maximenuck/helper_hikashop.php'))
						echo '<p>' . JText::_('MOD_MAXIMENUCK_SPACER_HIKASHOP_PATCH') . '</p>';
					if (!JFolder::exists(JPATH_ROOT . '/administrator/components/com_hikashop'))
						echo '<p>' . JText::_('MOD_MAXIMENUCK_HIKASHOP_NOTFOUND') . '</p>';
				}?>
				</div>
				<div id="ckpopupwizard_menutorenderoptions_joomshopping">
				<?php
				if (JFolder::exists(JPATH_ROOT . '/administrator/components/com_jshopping')
					AND JFile::exists(JPATH_ROOT . '/modules/mod_maximenuck/helper_joomshopping.php')) {
				foreach ($form->getFieldset('ckmaximenuwizard_menujoomshoppingoptions') as $key => $field)  : ?> 
					<div class="ckpopupwizard_row">
						<div class="ckpopupwizard_label"><?php echo $form->getLabel(str_replace($identifier . "_", "", $key), $identifier); ?></div>
						<div class="ckpopupwizard_input"><?php echo $form->getInput(str_replace($identifier . "_", "", $key), $identifier); ?></div>
					</div>
				<?php endforeach; 
				} else {
					if (!JFile::exists(JPATH_ROOT . '/modules/mod_maximenuck/helper_joomshopping.php'))
						echo '<p>' . JText::_('MOD_MAXIMENUCK_SPACER_JOOMSHOPPING_PATCH') . '</p>';
					if (!JFolder::exists(JPATH_ROOT . '/administrator/components/com_jshopping'))
						echo '<p>' . JText::_('MOD_MAXIMENUCK_JOOMSHOPPING_NOTFOUND') . '</p>';
				}?>
				</div>
			</div>
		</div>
		<div class="ckpopupwizard_slider">
			<div class="ckpopupwizard_heading"><?php echo JText::_('MAXIMENUCK_WIZARD_TYPE_OF_LAYOUT') ?></div>
			<div class="ckpopupwizard_optionsselect_area">
				<div class="ckpopupwizard_select active" data-type="_:default" data-target="ckpopupwizard_typeoflayout_default" onclick="changeFieldValue('ckmaximenuwizard_layout','_:default');showActiveOptions(this)"><?php echo JText::_('MAXIMENUCK_WIZARD_LAYOUT_DEFAULT') ?>
					<div class="ckpopupwizard_select_desc"><?php echo JText::_('MAXIMENUCK_WIZARD_LAYOUT_DEFAULT_DESC') ?></div>
				</div>
				<div class="ckpopupwizard_select" data-type="_:pushdown" data-target="ckpopupwizard_typeoflayout_pushdown" onclick="changeFieldValue('ckmaximenuwizard_layout','_:pushdown');showActiveOptions(this)"><?php echo JText::_('MAXIMENUCK_WIZARD_LAYOUT_PUSHDOWN') ?>
					<div class="ckpopupwizard_select_desc"><?php echo JText::_('MAXIMENUCK_WIZARD_LAYOUT_PUSHDOWN_DESC') ?></div>
				</div>
				<div class="ckpopupwizard_select" data-type="_:nativejoomla" data-target="ckpopupwizard_typeoflayout_nativejoomla" onclick="changeFieldValue('ckmaximenuwizard_layout','_:nativejoomla');showActiveOptions(this)"><?php echo JText::_('MAXIMENUCK_WIZARD_LAYOUT_NATIVEJOOMLA') ?>
					<div class="ckpopupwizard_select_desc"><?php echo JText::_('MAXIMENUCK_WIZARD_LAYOUT_NATIVEJOOMLA_DESC') ?></div>
				</div>
				<div class="ckpopupwizard_select" data-type="_:dropselect" data-target="ckpopupwizard_typeoflayout_dropselect" onclick="changeFieldValue('ckmaximenuwizard_layout','_:dropselect');showActiveOptions(this)"><?php echo JText::_('MAXIMENUCK_WIZARD_LAYOUT_DROPSELECT') ?>
					<div class="ckpopupwizard_select_desc"><?php echo JText::_('MAXIMENUCK_WIZARD_LAYOUT_DROPSELECT_DESC') ?></div>
				</div>
				<div class="ckpopupwizard_select" data-type="_:flatlist" data-target="ckpopupwizard_typeoflayout_flatlist" onclick="changeFieldValue('ckmaximenuwizard_layout','_:flatlist');showActiveOptions(this)"><?php echo JText::_('MAXIMENUCK_WIZARD_LAYOUT_FLATLIST') ?>
					<div class="ckpopupwizard_select_desc"><?php echo JText::_('MAXIMENUCK_WIZARD_LAYOUT_FLATLIST_DESC') ?></div>
				</div>
				<div class="ckpopupwizard_select" data-type="_:fullwidth" data-target="ckpopupwizard_typeoflayout_fullwidth" onclick="changeFieldValue('ckmaximenuwizard_layout','_:fullwidth');showActiveOptions(this)"><?php echo JText::_('MAXIMENUCK_WIZARD_LAYOUT_FULLWIDTH') ?>
					<div class="ckpopupwizard_select_desc"><?php echo JText::_('MAXIMENUCK_WIZARD_LAYOUT_FULLWIDTH_DESC') ?></div>
				</div>
				<input type="hidden" id="ckmaximenuwizard_layout" class="ckmaximenuwizard_inputbox" />
				<br />
				<div id="ckpopupwizard_select_desc_area"></div>
			</div>
			<div class="ckpopupwizard_options_area">
				<div id="ckpopupwizard_typeoflayout_default">
					<img src="<?php echo $this->getPathToElements() ?>/ckmaximenuwizard/default_layout.jpg" width="450" height="142" />
				</div>
				<div id="ckpopupwizard_typeoflayout_pushdown">
					<img src="<?php echo $this->getPathToElements() ?>/ckmaximenuwizard/pushdown_layout.jpg" width="450" height="142" />
				</div>
				<div id="ckpopupwizard_typeoflayout_nativejoomla">
					<img src="<?php echo $this->getPathToElements() ?>/ckmaximenuwizard/nativejoomla_layout.jpg" width="450" height="198" />
				</div>
				<div id="ckpopupwizard_typeoflayout_dropselect">
					<img src="<?php echo $this->getPathToElements() ?>/ckmaximenuwizard/dropselect_layout.jpg" width="400" height="142" />
				</div>
				<div id="ckpopupwizard_typeoflayout_flatlist">
					<img src="<?php echo $this->getPathToElements() ?>/ckmaximenuwizard/flatlist_layout.jpg" width="450" height="142" />
				</div>
				<div id="ckpopupwizard_typeoflayout_fullwidth">
					<img src="<?php echo $this->getPathToElements() ?>/ckmaximenuwizard/fullwidth_layout.jpg" width="450" height="142" />
				</div>
			</div>
		</div>
		<div class="ckpopupwizard_slider">
			<div class="ckpopupwizard_optionsselect_area">
				<div class="ckpopupwizard_heading"><?php echo JText::_('MAXIMENUCK_WIZARD_MENU_POSITION') ?></div>
				<div class="ckpopupwizard_page">
					<div class="ckpopupwizard_select" data-type="topfixed" data-target="ckpopupwizard_menuposition_topfixed" onclick="changeFieldValue('ckmaximenuwizard_menuposition','topfixed');showActiveOptions(this)"><?php echo JText::_('MAXIMENUCK_WIZARD_POSITION_TOPFIXED') ?>
						<div class="ckpopupwizard_select_desc"><?php echo JText::_('MAXIMENUCK_WIZARD_POSITION_TOPFIXED_DESC') ?></div>
					</div>
					<div style="clear:both;"></div>
					<img src="<?php echo $this->getPathToElements() ?>/images/logo_fake.png" height="24.5%" width="40%" style="float:left;margin-top: 20px;"/> 
					<div class="ckpopupwizard_block ckpopupwizard_logomodule"></div>
					<div style="clear:both;"></div>
					<div class="ckpopupwizard_select active" data-type="0" data-target="ckpopupwizard_menuposition_normal" onclick="changeFieldValue('ckmaximenuwizard_menuposition','0');showActiveOptions(this)"><?php echo JText::_('MAXIMENUCK_WIZARD_POSITION_NORMAL') ?>
						<div class="ckpopupwizard_select_desc"><?php echo JText::_('MAXIMENUCK_WIZARD_POSITION_NORMAL_DESC') ?></div>
					</div>
					<div style="clear:both;"></div>
					<div class="ckpopupwizard_block ckpopupwizard_leftcol"></div>
					<div class="ckpopupwizard_block ckpopupwizard_centercol"></div>
					<div class="ckpopupwizard_block ckpopupwizard_right"></div>
					<div style="clear:both;"></div>
					<div class="ckpopupwizard_select" data-type="bottomfixed" data-target="ckpopupwizard_menuposition_bottomfixed" onclick="changeFieldValue('ckmaximenuwizard_menuposition','bottomfixed');showActiveOptions(this)"><?php echo JText::_('MAXIMENUCK_WIZARD_POSITION_BOTTOMFIXED') ?>
						<div class="ckpopupwizard_select_desc"><?php echo JText::_('MAXIMENUCK_WIZARD_POSITION_BOTTOMFIXED_DESC') ?></div>
					</div>
				</div>
				<input type="hidden" id="ckmaximenuwizard_menuposition" class="ckmaximenuwizard_inputbox" />
				<br />
				<div id="ckpopupwizard_select_desc_area"></div>
			</div>
			<div class="ckpopupwizard_optionsselect_area">
				<div class="ckpopupwizard_heading"><?php echo JText::_('MAXIMENUCK_WIZARD_MENU_EFFECT') ?></div>
				<?php foreach ($form->getFieldset('ckmaximenuwizard_effectoptions') as $key => $field) : ?>
					<div class="ckpopupwizard_row">
						<div class="ckpopupwizard_label"><?php echo $form->getLabel(str_replace($identifier . "_", "", $key), $identifier); ?></div>
						<div class="ckpopupwizard_input"><?php echo $form->getInput(str_replace($identifier . "_", "", $key), $identifier); ?></div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
		<div class="ckpopupwizard_slider">
			<div class="ckpopupwizard_optionsselect_area">
				<div class="ckpopupwizard_heading"><?php echo JText::_('MAXIMENUCK_WIZARD_MENU_STYLES') ?></div>
				<?php foreach ($form->getFieldset('ckmaximenuwizard_stylesoptions') as $key => $field) : ?>
					<div class="ckpopupwizard_row">
						<div class="ckpopupwizard_label"><?php echo $form->getLabel(str_replace($identifier . "_", "", $key), $identifier); ?></div>
						<div class="ckpopupwizard_input"><?php echo $form->getInput(str_replace($identifier . "_", "", $key), $identifier); ?></div>
					</div>
				<?php endforeach; ?>
			</div>
			<div class="ckpopupwizard_optionsselect_area">
				<?php
				JFormHelper::loadFieldClass('Ckstyleswizard');
				$styleswizardClass = new JFormFieldCkstyleswizard();
				echo $styleswizardClass->getLabel();
				?>
			</div>
			<br style="clear:both;"/>
			<div id="ckpopupwizard_select_desc_area"><?php echo JText::_('MAXIMENUCK_WIZARD_MENU_DOWNLOAD_THEMES') ?></div>
		</div>
		<div class="ckpopupwizard_slider">
			
			<?php if (JFile::exists(JPATH_ROOT . '/plugins/system/maximenuckmobile/maximenuckmobile.php')
                && JPluginHelper::isEnabled('system','maximenuckmobile'))  : ?>
			
			<div class="ckpopupwizard_optionsselect_area">
				<div class="ckpopupwizard_heading"><?php echo JText::_('MAXIMENUCK_WIZARD_MOBILE') ?></div>
				<?php foreach ($form->getFieldset('ckmaximenuwizard_mobileoptions') as $key => $field) : ?>
					<div class="ckpopupwizard_row">
						<div class="ckpopupwizard_label"><?php echo $form->getLabel(str_replace($identifier . "_", "", $key), $identifier); ?></div>
						<div class="ckpopupwizard_input"><?php echo $form->getInput(str_replace($identifier . "_", "", $key), $identifier); ?></div>
					</div>
				<?php endforeach; ?>
			</div>
			<div class="ckpopupwizard_optionsselect_area">
				<div class="ckpopupwizard_heading"><?php echo JText::_('MAXIMENUCK_WIZARD_MOBILE_EFFECT') ?></div>
				<div style="width:150px;float:left;">
					<div class="ckpopupwizard_select active" data-type="normal" data-target="ckpopupwizard_mobileoptions_normal" onclick="changeFieldValue('ckmaximenuwizard_maximenumobile_displayeffect','normal');showActiveOptions(this)"><?php echo JText::_('MAXIMENUCK_WIZARD_MOBILE_NORMAL') ?>
						<div class="ckpopupwizard_select_desc"><?php echo JText::_('MAXIMENUCK_WIZARD_MOBILE_NORMAL_DESC') ?></div>
					</div>
					<div class="ckpopupwizard_select" data-type="slideleft" data-target="ckpopupwizard_mobileoptions_slideleft" onclick="changeFieldValue('ckmaximenuwizard_maximenumobile_displayeffect','slideleft');showActiveOptions(this)"><?php echo JText::_('MAXIMENUCK_WIZARD_MOBILE_SLIDELEFT') ?>
						<div class="ckpopupwizard_select_desc"><?php echo JText::_('MAXIMENUCK_WIZARD_MOBILE_SLIDELEFT_DESC') ?></div>
					</div>
					<div class="ckpopupwizard_select" data-type="slideright" data-target="ckpopupwizard_mobileoptions_slideright" onclick="changeFieldValue('ckmaximenuwizard_maximenumobile_displayeffect','slideright');showActiveOptions(this)"><?php echo JText::_('MAXIMENUCK_WIZARD_MOBILE_SLIDERIGHT') ?>
						<div class="ckpopupwizard_select_desc"><?php echo JText::_('MAXIMENUCK_WIZARD_MOBILE_SLIDERIGHT_DESC') ?></div>
					</div>
					<div class="ckpopupwizard_select" data-type="topfixed" data-target="ckpopupwizard_mobileoptions_topfixed" onclick="changeFieldValue('ckmaximenuwizard_maximenumobile_displayeffect','topfixed');showActiveOptions(this)"><?php echo JText::_('MAXIMENUCK_WIZARD_MOBILE_TOPFIXED') ?>
						<div class="ckpopupwizard_select_desc"><?php echo JText::_('MAXIMENUCK_WIZARD_MOBILE_TOPFIXED_DESC') ?></div>
					</div>
					<input type="hidden" id="ckmaximenuwizard_maximenumobile_displayeffect" class="ckmaximenuwizard_inputbox" />
					<br />
					<div id="ckpopupwizard_select_desc_area"></div>
				</div>
				<div  class="ckpopupwizard_options_area" style="width:200px;float: left;margin-left:20px;">
					<div id="ckpopupwizard_mobileoptions_normal">
						<img src="<?php echo $this->getPathToElements() ?>/ckmaximenuwizard/mobilemenu_normal.jpg" width="200" height="auto" />
					</div>
					<div id="ckpopupwizard_mobileoptions_slideleft">
						<img src="<?php echo $this->getPathToElements() ?>/ckmaximenuwizard/mobilemenu_slideleft.jpg" width="200" height="auto" />
					</div>
					<div id="ckpopupwizard_mobileoptions_slideright">
						<img src="<?php echo $this->getPathToElements() ?>/ckmaximenuwizard/mobilemenu_slideright.jpg" width="200" height="auto" />
					</div>
					<div id="ckpopupwizard_mobileoptions_topfixed">
						<img src="<?php echo $this->getPathToElements() ?>/ckmaximenuwizard/mobilemenu_topfixed.jpg" width="200" height="auto" />
					</div>
				</div>
			</div>
			<?php else: ?>
				<?php echo '<p>' . JText::_('MOD_MAXIMENUCK_CHECKPLUGINMOBILE') . '</p>'; ?>
			<?php endif; ?>
		</div>
	</div>
</div>
<div style="clear:both;"></div>
<div id="ckpopupwizard_next" class="ckboxmodal-button" onclick="ckpopupwizard_next()">>></div>
<div id="ckpopupwizard_prev" class="ckboxmodal-button" onclick="ckpopupwizard_prev()"><<</div>

<?php
		echo '</div>';
	}

	protected function getInput() {

		$identifier = 'ckmaximenuwizard';

		$doc = JFactory::getDocument();
		$doc->addScript(JUri::root(true) . '/modules/mod_maximenuck/elements/ckbox/ckbox.js');
		$doc->addStylesheet(JUri::root(true) . '/modules/mod_maximenuck/elements/ckbox/ckbox.css');

		$form = new JForm('ckmaximenuwizard');
		JForm::addFormPath(JPATH_SITE . '/modules/mod_maximenuck/elements/ckmaximenuwizard');
		// $form->load('test.xml');
		if (!$formexists = $form->loadFile($identifier, false)) {
			echo '<p style="color:red">' . JText::_('Problem loading the file : ' . $identifier . '.xml') . '</p>';
			return '';
		}
		// $test = $form->getInput('usek2images', 'ckmaximenuwizard'); var_dump($test);// fonctionne
		$this->createFields($form);

		$doc->addScriptDeclaration("JURI='" . JURI::root() . "';");
		$path = 'modules/mod_maximenuck/elements/ckmaximenuwizard/';
		// JHtml::_('behavior.modal');
		// var_dump($path . 'ckmaximenuwizard.js');
		JHtml::_('script', $path . 'ckmaximenuwizard.js');
		JHtml::_('stylesheet', $path . 'ckmaximenuwizard.css');

		$html = '<input name="' . $this->name . '" id="' . $this->name . '" type="hidden" value="' . $this->value . '" />';
//		$html .= '<input name="' . $this->name . '_button" id="' . $this->name . '_button" class="ckpopupwizardmanager_button" type="button" value="' . JText::_('MAXIMENUCK_WIZARD') . '" onclick="javascript:showWizardPopupCK($(\'ckpopupwizard\'));"/>';
		//.'<input name="ckaddfromfolder" id="ckaddfromfolder" type="button" value="Import from a folder" onclick="javascript:addfromfolderck();"/>'
		//.'<input name="ckstoreslide" id="ckstoreslide" type="button" value="Save" onclick="javascript:storeslideck();"/>'

		$button = '<input name="' . $this->name . '_button2" id="' . $this->name . '_button2" class="ckpopupwizardmanager_button" type="button" value="' . JText::_((string) $this->element['label']) . '" onclick="CKBox.open({handler:\'inline\', size: {x: \'820px\', y: \'550px\'}, fullscreen: false, content: \'ckpopupwizard\', footerHtml: document.getElementById(\'ckpopupwizard_next\').outerHTML + document.getElementById(\'ckpopupwizard_prev\').outerHTML});showWizardPopupCK($(\'ckpopupwizard\'))"/>';
		$html .= $button;
		return $html;
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

	// protected function getArticlesList() {
		// $db = & JFactory::getDBO();

		// $query = "SELECT id, title FROM #__content WHERE state = 1 LIMIT 2;";
		// $db->setQuery($query);
		// $row = $db->loadObjectList('id');
		// var_dump($row);
		// return json_encode($row);
	// }

}

