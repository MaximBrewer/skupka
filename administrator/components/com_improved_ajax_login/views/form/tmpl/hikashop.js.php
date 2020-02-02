<?php
/*-------------------------------------------------------------------------
# com_improved_ajax_login - com_improved_ajax_login
# -------------------------------------------------------------------------
# @ author    Balint Polgarfi
# @ copyright Copyright (C) 2013 Offlajn.com  All Rights Reserved.
# @ license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @ website   http://www.offlajn.com
-------------------------------------------------------------------------*/
?><?php
defined('_JEXEC') or die;

if (!file_exists(JPATH_ADMINISTRATOR.'/components/com_hikashop/helpers/helper.php'))
  die('</script>HikaShop is not installed!');

require_once JPATH_ADMINISTRATOR.'/components/com_hikashop/helpers/helper.php';
$fieldsClass = hikashop_get('class.field');
$address = @$fieldsClass->getFields('frontcomp', @$address, 'address');

if (isset($address['address_state'])) {
  $address['address_state']->field_value = '';
  $address['address_state']->field_type = 'singledropdown';
}

$name = array( 'value' => '', 'readonly' => false );
$empty = array( 'value' => '' );
$empty2 = array( 'value' => '', 'placeholder' => '' );
$prefix = array( 'value' => 'data[register]' );
$pattern = array( 'value' => '.+', 'placeholder' => '.+' );
$checked = array( 'checked' => false );
$required = array( 'checked' => false, 'disabled' => false );

$custom = array(
  'header' => array(
    'type' => array( 'value' => 'header', 'readonly' => true, 'button' => 'Header', 'icon' => 'icon-quote icon-font' ),
    'wide' => array( 'checked' => true),
    'label' => array( 'value' => '', 'placeholder' => 'Header text' ),
    'subtitle' => $empty2,
  ),
  'label' => array(
    'type' => array( 'value' => 'label', 'readonly' => true, 'button' => 'Label', 'icon' => 'icon-quote icon-align-justify' ),
    'wide' => $checked,
    'label' => array( 'value' => '', 'placeholder' => 'Label text' ),
  ),
  'textfield' => array(
    'type' => array( 'value' => 'textfield', 'defaultValue' => 'text', 'readonly' => true, 'button' => 'Textfield', 'icon' => 'icon-pencil' ),
    'required' => $required,
    'wide' => $checked,
    'class' => $empty,
    'name' => $name,
    'prefix' => $prefix,
    'label' => array( 'value' => '', 'placeholder' => 'Textfield:' ),
    'value' => $empty,
    'placeholder' => $empty2,
    'title' => $empty2,
    'error' => $empty2,
    'pattern' => $pattern,
  ),
  'password' => array(
    'type' => array( 'value' => 'password2', 'defaultValue' => 'password', 'readonly' => true, 'button' => 'Password', 'icon' => 'icon-lock' ),
    'required' => array( 'checked' => true, 'disabled' => false ),
    'wide' => $checked,
    'class' => $empty,
    'name' => $name,
    'prefix' => $prefix,
    'label' => array( 'value' => '', 'placeholder' => 'Secret:' ),
    'placeholder' => $empty2,
    'title' => $empty2,
    'error' => $empty2,
    'pattern' => $pattern,
  ),
  'textarea' => array(
    'type' => array( 'value' => 'textarea', 'readonly' => true, 'button' => 'Textarea', 'icon' => 'icon-pencil-2 icon-pencil' ),
    'required' => $required,
    'wide' => $checked,
    'class' => $empty,
    'name' => $name,
    'prefix' => $prefix,
    'label' => array( 'value' => '', 'placeholder' => 'Textarea:' ),
    'value' => $empty,
    'placeholder' => $empty2,
    'title' => $empty2,
    'error' => $empty2,
    'pattern' => $pattern,
  ),
  'checkbox' => array(
    'type' => array( 'value' => 'checkbox', 'readonly' => true, 'button' => 'Checkbox', 'icon' => 'icon-checkbox icon-ok-circle' ),
    'required' => $required,
    'checked' => array( 'checked' => false, 'disabled' => false ),
    'wide' => $checked,
    'class' => $empty,
    'name' => $name,
    'prefix' => $prefix,
    'label' => array( 'value' => '', 'placeholder' => 'Label text' ),
    'title' => $empty2,
  ),
  'article' => array(
    'type' => array( 'value' => 'tos', 'readonly' => true, 'button' => 'Agree', 'icon' => 'icon-checkbox icon-ok-circle' ),
    'required' => $required,
    'checked' => array( 'checked' => false, 'disabled' => false ),
    'wide' => $checked,
    'class' => $empty,
    'name' => $name,
    'prefix' => $prefix,
    'label' => array( 'value' => '', 'defaultValue' => 'PLG_USER_PROFILE_OPTION_AGREE', 'placeholder' => '' ),
    'title' => array( 'value' => '', 'defaultValue' => '', 'placeholder' => '' ),
    'article' => $empty,
    'article_name' => array( 'value' => '', 'placeholder' => 'Select an Article' ),
  ),
  'select' => array(
    'type' => array( 'value' => 'select', 'readonly' => true, 'button' => 'Select', 'icon' => 'icon-chevron-down' ),
    'required' => $required,
    'wide' => $checked,
    'class' => $empty,
    'name' => $name,
    'prefix' => $prefix,
    'label' => array( 'value' => '', 'placeholder' => 'Select:' ),
    'select' => $empty,
    'title' => $empty2
  )
);

$field = array();
$types = array(
  'text' => 'textfield',
  'singledropdown' => 'select',
  'zone' => 'select'
);
$class = array(
  'address_title' => 'ial-title',
  'address_telephone' => 'ial-phone',
  'address_firstname' => 'ial-name',
  'address_lastname' => 'ial-name',
  'address_street' => 'ial-address1',
  'address_city' => 'ial-city',
  'address_post_code' => 'ial-postal_code',
  'address_country' => 'ial-country',
  'address_state' => 'ial-region'
);
foreach ($address as $k=>$v) {
  $field[$k] = $custom[$types[$v->field_type]];
  $field[$k]['type']['button'] = $v->field_realname;
  $field[$k]['type']['predefined'] = $k;
  $field[$k]['class']['value'] = @$class[$k];
  $field[$k]['prefix']['value'] = 'data[address]';
  $field[$k]['name']['value'] = $v->field_namekey;
  $field[$k]['name']['readonly'] = true;
  $field[$k]['label']['value'] = $v->field_realname;
  $field[$k]['required']['checked'] = $v->field_required == 1;
  if (is_array($v->field_value)) {
    if (!$v->field_default) $v->field_default = key($v->field_value);
    foreach ($v->field_value as $val=>$opt) {
      $field[$k]['select']['value'].= '<option '.($v->field_default == $val? 'selected="selected" ':'')
        .'value="'.$val.'">'.JText::_($opt->value).'</option>';
    }
  }
}

$prefix = 'data[register]';
?>
JBackend = true;
JURI = "<?php echo JURI::root() ?>";
Theme = "<?php echo $this->theme ?>";
PredefinedElems = jQuery.extend(<?php echo json_encode($field) ?>, {
  // Default fields
  title: {
    type: {value: "header", predefined: "title",
      readonly: true, button: "Title", icon: "icon-quote icon-font"},
    wide: {checked: true},
    label: {value: "", defaultValue: "MOD_LOGIN_REGISTER",
      placeholder: "<?php echo addslashes(JText::_('MOD_LOGIN_REGISTER')) ?>"},
    subtitle: {value: "", defaultValue: "COM_USERS_REGISTER_REQUIRED",
      placeholder: "<?php echo addslashes(JText::_('COM_USERS_REGISTER_REQUIRED')) ?>"}
  },
  name: {
    type: {value: "textfield", defaultValue: "text", predefined: "name",
      readonly: true, button: "Name", icon: "icon-user"},
    required: {checked: true, disabled: false},
    wide: {checked: false},
    "class": {value: "ial-name"},
    name: {value: "name", readonly: true},
    prefix: {value: "<?php echo $prefix ?>"},
    label: {value: "", defaultValue: "COM_USERS_REGISTER_NAME_LABEL",
      placeholder: "<?php echo addslashes(JText::_('COM_USERS_REGISTER_NAME_LABEL'))?>"},
    placeholder: {value: "", placeholder: ""},
    title: {value: "", defaultValue: "COM_USERS_REGISTER_NAME_DESC",
      placeholder: "<?php echo addslashes(JText::_('COM_USERS_REGISTER_NAME_DESC'))?>"},
    pattern: {value: ".+", placeholder: ".+"},
    error: {value: "", defaultValue: "JLIB_FORM_VALIDATE_FIELD_INVALID",
      placeholder: "<?php echo addslashes(JText::sprintf('JLIB_FORM_VALIDATE_FIELD_INVALID', ''))?>"},
  },
  username: {
    type: {value: "textfield", defaultValue: "text", predefined: "username",
      readonly: true, button: "Username", icon: "icon-user"},
    required: {checked: true, disabled: true},
    wide: {checked: false},
    "class": {value: "ial-username"},
    name: {value: "username", readonly: true},
    prefix: {value: "<?php echo $prefix ?>"},
    label: {value: "", defaultValue: "COM_USERS_REGISTER_USERNAME_LABEL",
      placeholder: "<?php echo addslashes(JText::_('COM_USERS_REGISTER_USERNAME_LABEL'))?>"},
    placeholder: {value: "", placeholder: ""},
    title: {value: "", defaultValue: "COM_USERS_REGISTER_USERNAME_DESC",
      placeholder: "<?php echo addslashes(JText::_('COM_USERS_REGISTER_USERNAME_DESC'))?>"},
    error: {value: "", defaultValue: "JLIB_DATABASE_ERROR_VALID_AZ09",
      placeholder: "<?php echo addslashes(@JText::sprintf('JLIB_DATABASE_ERROR_VALID_AZ09', 2))?>"},
    pattern: {value: "^[^<>\\\\&%'\";\\(\\)]{2,}$", placeholder: "^[^<>\\\\&%'\";\\(\\)]{2,}$"},
    ajax: "username"
  },
  password1: {
    type: {value: "password1", defaultValue: "password", predefined: "password1",
      readonly: true, button: "Password", icon: "icon-lock"},
    required: {checked: true, disabled: true},
    wide: {checked: false},
    "class": {value: "ial-password1"},
    name: {value: "password", readonly: true},
    prefix: {value: "<?php echo $prefix ?>"},
    label: {value: "", defaultValue: "COM_USERS_REGISTER_PASSWORD1_LABEL",
      placeholder: "<?php echo addslashes(JText::_('COM_USERS_REGISTER_PASSWORD1_LABEL'))?>"},
    placeholder: {value: "", placeholder: ""},
    title: {value: "", defaultValue: "COM_USERS_DESIRED_PASSWORD",
      placeholder: "<?php echo addslashes(JText::_('COM_USERS_DESIRED_PASSWORD'))?>"},
    error: {value: "", defaultValue: "JLIB_FORM_FIELD_INVALID",
      placeholder: "<?php echo addslashes(JText::sprintf('JLIB_FORM_VALIDATE_FIELD_INVALID', ''))?>"},
  },
  password2: {
    type: {value: "password2", defaultValue: "password", predefined: "password2",
      readonly: true, button: "Password again", icon: "icon-lock"},
    required: {checked: true, disabled: true},
    wide: {checked: false},
    "class": {value: "ial-password2"},
    name: {value: "password2", readonly: true},
    prefix: {value: "<?php echo $prefix ?>"},
    label: {value: "", defaultValue: "COM_USERS_REGISTER_PASSWORD2_LABEL",
      placeholder: "<?php echo addslashes(JText::_('COM_USERS_REGISTER_PASSWORD2_LABEL'))?>"},
    placeholder: {value: "", placeholder: ""},
    title: {value: "", defaultValue: "COM_USERS_REGISTER_PASSWORD2_DESC",
      placeholder: "<?php echo addslashes(JText::_('COM_USERS_REGISTER_PASSWORD2_DESC'))?>"},
    error: {value: "", defaultValue: "COM_USERS_REGISTER_PASSWORD1_MESSAGE",
      placeholder: "<?php echo addslashes(JText::_('COM_USERS_REGISTER_PASSWORD1_MESSAGE'))?>"}
  },
  email: {
    type: {value: "textfield", defaultValue: "text", predefined: "email",
      readonly: true, button: "Email", icon: "icon-mail-2 icon-envelope"},
    required: {checked: true, disabled: true},
    wide: {checked: false},
    "class": {value: "ial-email1"},
    name: {value: "email", readonly: true},
    prefix: {value: "<?php echo $prefix ?>"},
    label: {value: "", defaultValue: "COM_USERS_REGISTER_EMAIL1_LABEL",
      placeholder: "<?php echo addslashes(JText::_('COM_USERS_REGISTER_EMAIL1_LABEL'))?>"},
    placeholder: {value: "", placeholder: ""},
    title: {value: "", defaultValue: "COM_USERS_REGISTER_EMAIL1_DESC",
      placeholder: "<?php echo addslashes(JText::_('COM_USERS_REGISTER_EMAIL1_DESC'))?>"},
    error: {value: "", defaultValue: "COM_USERS_INVALID_EMAIL",
      placeholder: "<?php echo addslashes(JText::_('COM_USERS_INVALID_EMAIL'))?>"},
    pattern: {value: "^([\\w0-9\\.\\-])+\\@(([a-zA-Z0-9\\-])+\\.)+[a-zA-Z]{2,4}$",
      placeholder: "^([\\w0-9\\.\\-])+\\@(([a-zA-Z0-9\\-])+\\.)+[a-zA-Z]{2,4}$"},
    ajax: "email"
  },
  email2: {
    type: {value: "textfield", defaultValue: "text", predefined: "email2",
      readonly: true, button: "Email again", icon: "icon-mail-2 icon-envelope"},
    required: {checked: true, disabled: true},
    wide: {checked: false},
    "class": {value: "ial-email2"},
    name: {value: "email_confirm", readonly: true},
    prefix: {value: "<?php echo $prefix ?>"},
    label: {value: "", defaultValue: "COM_USERS_REGISTER_EMAIL2_LABEL",
      placeholder: "<?php echo addslashes(JText::_('COM_USERS_REGISTER_EMAIL2_LABEL'))?>"},
    placeholder: {value: "", placeholder: ""},
    title: {value: "", defaultValue: "COM_USERS_REGISTER_EMAIL2_DESC",
      placeholder: "<?php echo addslashes(JText::_('COM_USERS_REGISTER_EMAIL2_DESC'))?>"},
    error: {value: "", defaultValue: "COM_USERS_REGISTER_EMAIL2_MESSAGE",
      placeholder: "<?php echo addslashes(JText::_('COM_USERS_REGISTER_EMAIL2_MESSAGE'))?>"}
  },/*
  captcha: {
    type: {value: "captcha", predefined: "captcha",
      readonly: true, button: "Captcha image", icon: "icon-picture"},
    wide: {checked: true}
  },
  captcha2: {
    type: {value: "textfield", defaultValue: "text", predefined: "captcha2",
      readonly: true, button: "Captcha textfield", icon: "icon-pencil"},
    required: {checked: true, disabled: true},
    clear: {checked: false},
    wide: {checked: false},
    "class": {value: "ial-captcha"},
    name: {value: "recaptcha_response_field", readonly: true},
    prefix: {value: ""},
    label: {value: "", defaultValue: "COM_USERS_CAPTCHA_LABEL",
      placeholder: "<?php echo addslashes(JText::_('COM_USERS_CAPTCHA_LABEL'))?>:"},
    placeholder: {value: "", placeholder: ""},
    title: {value: "", defaultValue: "COM_USERS_CAPTCHA_DESC",
      placeholder: "<?php echo addslashes(JText::_('COM_USERS_CAPTCHA_DESC'))?>"},
    error: {value: "", defaultValue: "JLIB_FORM_FIELD_INVALID",
      placeholder: "<?php echo addslashes(JText::sprintf('JLIB_FORM_VALIDATE_FIELD_INVALID', ''))?>"},
    pattern: {value: "", placeholder: ""},
    autoCompOff: true
  },*/
  submit: {
    type: {value: "button", predefined: "submit",
      readonly: true, button: "Submit", icon: "icon-arrow-right"},
    wide: {checked: false},
    label: {value: "", defaultValue: "JREGISTER",
      placeholder: "<?php echo JText::_('JREGISTER') ?>"},
    subtitle: {value: "&nbsp", placeholder:""}
  },
  // Custom fields
  header: {
    type: {value: "header", readonly: true, button: "Header", icon: "icon-quote icon-font"},
    wide: {checked: true},
    label: {value: "", placeholder: "Header text"},
    subtitle: {value: "", placeholder: ""}
  },
  label: {
    type: {value: "label", readonly: true, button: "Label", icon: "icon-quote icon-align-justify"},
    wide: {checked: false},
    label: {value: "", placeholder: "Label text"}
  }
});