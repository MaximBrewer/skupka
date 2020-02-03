<?php
/**
 * @package         Regular Labs Library
 * @version         20.1.23725
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            http://www.regularlabs.com
 * @copyright       Copyright © 2020 Regular Labs All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('JPATH_BASE') or die;

/**
 * Layout variables
 * -----------------
 * @var array   $displayData
 * @var string  $autocomplete   Autocomplete attribute for the field.
 * @var boolean $autofocus      Is autofocus enabled?
 * @var string  $class          Classes for the input.
 * @var string  $description    Description of the field.
 * @var boolean $disabled       Is this field disabled?
 * @var string  $group          Group the field belongs to. <fields> section in form XML.
 * @var boolean $hidden         Is this field hidden in the form?
 * @var string  $hint           Placeholder for the field.
 * @var string  $id             DOM id of the field.
 * @var string  $label          Label of the field.
 * @var string  $labelclass     Classes to apply to the label.
 * @var boolean $multiple       Does this field support multiple values?
 * @var string  $name           Name of the input field.
 * @var string  $onchange       Onchange attribute for the field.
 * @var string  $onclick        Onclick attribute for the field.
 * @var string  $pattern        Pattern (Reg Ex) of value of the form field.
 * @var boolean $readonly       Is this field read only?
 * @var boolean $repeat         Allows extensions to duplicate elements.
 * @var boolean $required       Is this field required?
 * @var integer $size           Size attribute of the input.
 * @var boolean $spellcheck     Spellcheck state for the form field.
 * @var string  $validate       Validation rules to apply.
 * @var string  $value          Value attribute of the field.
 * @var array   $checkedOptions Options that will be set as checked.
 * @var boolean $hasValue       Has this field a value assigned?
 * @var array   $options        Options available for this field.
 * @var array   $inputType      Options available for this field.
 * @var string  $accept         File types that are accepted.
 */

extract($displayData);

// Including fallback code for HTML5 non supported browsers.
JHtml::_('jquery.framework');
JHtml::_('script', 'system/html5fallback.js', ['version' => 'auto', 'relative' => true, 'conditional' => 'lt IE 9']);

// Initialize some field attributes.
$attributes_range = [
	$class ? 'class="' . $class . '"' : '',
	$disabled ? 'disabled' : '',
	$readonly ? 'readonly' : '',
	! empty($onchange) ? 'onchange="' . $onchange . '"' : '',
	! empty($max) ? 'max="' . $max . '"' : '',
	! empty($step) ? 'step="' . $step . '"' : '',
	! empty($min) ? 'min="' . $min . '"' : '',
	$autofocus ? 'autofocus' : '',
];

$attributes_number = [
	'class="input-mini text-right"',
	! empty($size) ? 'size="' . $size . '"' : '',
	$disabled ? 'disabled' : '',
	$readonly ? 'readonly' : '',
	strlen($hint) ? 'placeholder="' . htmlspecialchars($hint, ENT_COMPAT, 'UTF-8') . '"' : '',
	! empty($onchange) ? 'onchange="' . $onchange . '"' : '',
	isset($max) ? 'max="' . $max . '"' : '',
	! empty($step) ? 'step="' . $step . '"' : '',
	isset($min) ? 'min="' . $min . '"' : '',
	$required ? 'required aria-required="true"' : '',
	$autocomplete,
	$autofocus ? 'autofocus' : '',
];

$chars = strlen($max) ?: $size ?: 4;
$width = $chars * 8;
?>

<input type="number" name="<?php echo $name; ?>" id="<?php echo $id; ?>" value="<?php echo $value; ?>"
       oninput="document.querySelector('input[data-for=\'<?php echo $name; ?>\']').value=this.value;"
	<?php echo implode(' ', $attributes_number); ?> />
<input type="range" data-for="<?php echo $name; ?>" value="<?php echo $value; ?>"
       oninput="document.querySelector('input[name=\'<?php echo $name; ?>\']').value=this.value;"
	<?php echo implode(' ', $attributes_range); ?> />

