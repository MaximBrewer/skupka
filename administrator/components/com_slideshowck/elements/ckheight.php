<?php

/**
 * @copyright	Copyright (C) 2011-2019 Cedric KEIFLIN alias ced1870
 * https://www.joomlack.fr
 * @license		GNU/GPL
 * */
defined('JPATH_PLATFORM') or die;

require_once 'ckformfield.php';

class JFormFieldCkheight extends CKFormField {

	/**
	 * The form field type.
	 *
	 * @var    string
	 *
	 * @since  11.1
	 */
	protected $type = 'ckheight';

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   11.1
	 */
	protected function getInput() {
		// Initialize some field attributes.
		$icon = $this->element['icon'];
		$suffix = $this->element['suffix'];
		$size = $this->element['size'] ? ' size="' . (int) $this->element['size'] . '"' : '';
		$maxLength = $this->element['maxlength'] ? ' maxlength="' . (int) $this->element['maxlength'] . '"' : '';
		$class = $this->element['class'] ? ' class="' . (string) $this->element['class'] . '"' : '';
		$readonly = ((string) $this->element['readonly'] == 'true') ? ' readonly="readonly"' : '';
		$disabled = ((string) $this->element['disabled'] == 'true') ? ' disabled="disabled"' : '';
		$defautlwidth = $suffix ? '128px' : '150px';
		$styles = ' style="width:' . $defautlwidth . ';' . $this->element['styles'] . '"';

		// Initialize JavaScript field attributes.
		$onchange = $this->element['onchange'] ? ' onchange="' . (string) $this->element['onchange'] . '"' : '';
		$html = $icon ? '<div style="display:inline-block;vertical-align:top;margin-top:4px;width:20px;"><img src="' . $this->mediaPath . $icon . '" style="margin-right:5px;" /></div>' : '<div style="display:inline-block;width:20px;"></div>';
		$html .= '<div class="ckbutton-group"><input type="text" name="' . $this->name . '" id="' . $this->id . '"' . ' value="'
				. htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '"' . $class . $size . $disabled . $readonly . $onchange . $maxLength . $styles . '/>';
		$html .= '<span class="ckbutton" onclick="CKBox.open({handler: \'inline\', content: \'ckheightfieldhelp\', style: {padding: \'10px\'}, size: {x:  \'800px\', y: \'550px\'}})"><i class="fas fa-info"></i></span></div>';
		$html .= '<div id="ckheightfieldhelp" style="display: none;"><h3>' . JText::_('SLIDESHOWCK_HEIGHT_FIELD_HELP_TITLE') . '</h3>
		<p>' . JText::_('SLIDESHOWCK_HEIGHT_FIELD_HELP_1') . '</p>
		<p><b>' . JText::_('SLIDESHOWCK_HEIGHT_FIELD_HELP_2') . '</b></p>
		<p>' . JText::_('SLIDESHOWCK_HEIGHT_FIELD_HELP_3') . '</p>
		<p>' . JText::_('SLIDESHOWCK_HEIGHT_FIELD_HELP_4') . '</p>
		<p style="text-align:center;padding:10px;font-size: 18px;">1280 x 800 px</p>
		<p>' . JText::_('SLIDESHOWCK_HEIGHT_FIELD_HELP_5') . '</p>
		<p><b>' . JText::_('SLIDESHOWCK_CALCULATOR') . '</b></p>
		<p><label for="ckheightfieldhelpheight">' . JText::_('SLIDESHOWCK_HEIGHT_LABEL') . '</label><input type="text" id="ckheightfieldhelpheight" onchange="ckHeightFieldHelpCalculator()"/></p>
		<p><label for="ckheightfieldhelpwidth">' . JText::_('SLIDESHOWCK_WIDTH_LABEL') . '</label><input type="text" id="ckheightfieldhelpwidth" onchange="ckHeightFieldHelpCalculator()" /></p>
		<p><label for="ckheightfieldhelpratio">' . JText::_('SLIDESHOWCK_RATIO_LABEL') . '</label><input type="text" id="ckheightfieldhelpratio" style="font-size: 18px;" /></p>
		<script>function ckHeightFieldHelpCalculator() {
			document.getElementById("ckheightfieldhelpratio").value = parseFloat(document.getElementById("ckheightfieldhelpheight").value) / parseFloat(document.getElementById("ckheightfieldhelpwidth").value) * 100;
		}</script>
		</div>';
		return $html;
	}

}
