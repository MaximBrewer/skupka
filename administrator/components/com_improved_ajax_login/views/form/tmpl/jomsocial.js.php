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

//echo "</script>"; // !!!
is_dir(JPATH_ADMINISTRATOR.'/components/com_community') or die('</script>JomSocial is not installed!');

$db = JFactory::getDBO();
$db->setQuery("SELECT * FROM #__community_fields WHERE registration = 1");
$flds = $db->loadObjectList();

$name = array( 'value' => '', 'readonly' => false );
$empty = array( 'value' => '' );
$empty2 = array( 'value' => '', 'placeholder' => '' );
$prefix = array( 'value' => 'jomsocial' );
$pattern = array( 'value' => '', 'placeholder' => '' );
$checked = array( 'checked' => false );
$required = array( 'checked' => false, 'disabled' => false );

$custom = array(
  'header' => array(
    'type' => array( 'value' => 'header', 'readonly' => true, 'button' => 'Header', 'icon' => 'icon-quote icon-font' ),
    'wide' => array( 'checked' => true),
    'class' => $empty,
    'label' => array( 'value' => '', 'placeholder' => 'Header text' ),
    'subtitle' => $empty2,
  ),
  'label' => array(
    'type' => array( 'value' => 'label', 'readonly' => true, 'button' => 'Label', 'icon' => 'icon-quote icon-align-justify' ),
    'wide' => $checked,
    'class' => $empty,
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
  'date' => array(
    'type' => array( 'value' => 'textfield', 'defaultValue' => 'text', 'readonly' => true, 'button' => 'Date', 'icon' => 'icon-calendar' ),
    'required' => $required,
    'wide' => $checked,
    'class' => $empty,
    'name' => $name,
    'prefix' => $prefix,
    'label' => array( 'value' => '', 'placeholder' => 'Date:' ),
    'value' => $empty,
    'placeholder' => array( 'value' => 'YYYY-MM-DD', 'placeholder' => '' ),
    'title' => $empty2,
    'error' => array( 'value' => '', 'defaultValue' => 'JLIB_FORM_VALIDATE_FIELD_INVALID', 'placeholder' => JText::sprintf('JLIB_FORM_VALIDATE_FIELD_INVALID', '') ),
    'pattern' => array( 'value' => '^\\d{4}\\-\\d\\d\\-\\d\\d$', 'placeholder' => '' ),
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
  ),
  'gender' => array(
    'type' => array( 'value' => 'select', 'readonly' => true, 'button' => 'Select', 'icon' => 'icon-chevron-down' ),
    'required' => $required,
    'wide' => $checked,
    'class' => $empty,
    'name' => $name,
    'prefix' => $prefix,
    'label' => $empty2,
    'select' => array( 'value' => '<option value="COM_COMMUNITY_MALE">Male</option><option value="COM_COMMUNITY_FEMALE">Female</option>' ),
    'title' => $empty2
  )
);
$custom['country'] = $custom['select'];
$custom['country']['select']['value'] = '<option value=""></option>';
JFactory::getLanguage()->load('com_community.country', JPATH_SITE);
$xml = JFactory::getXML(JPATH_SITE.'/components/com_community/libraries/fields/countries.xml');
foreach ($xml->countries->country as $c) {
  $custom['country']['select']['value'].= '<option value="'.$c->name.'">'.JText::_($c->name).'</option>';
}
$custom['website'] = $custom['textfield'];
$custom['website']['value']['value'] = 'http://';

$field = array();
$types = array(
  'group' => 'header',
  'gender' => 'gender',
  'birthdate' => 'date',
  'text' => 'textfield',
  'country' => 'country',
  'checkbox' => 'checkbox',
  'email' => 'textfield',
  'url' => 'website',
  'time' => 'textfield',
  'label' => 'label',
  'email' => 'textfield',
  'select' => 'select',
  'textarea' => 'textarea');
$class = array(
  'FIELD_ABOUTME' => 'ial-aboutme',
  'FIELD_BIRTHDATE' => 'ial-dob',
  'FIELD_LANDPHONE' => 'ial-phone',
  'FIELD_MOBILE' => 'ial-phone',
  'FIELD_ADDRESS' => 'ial-address1',
  'FIELD_STATE' => 'ial-region',
  'FIELD_CITY' => 'ial-city',
  'FIELD_COUNTRY' => 'ial-country',
  'FIELD_WEBSITE' => 'ial-website',
  'FIELD_COLLEGE' => 'ial-favoritebook',
  'FIELD_GRADUATION' => 'ial-dob');
foreach ($flds as $key => $v) {
  if (!isset($types[$v->type])) {
    unset($flds[$key]);
    continue;
  }
  $k = $v->type.$v->id;
  $field[$k] = $custom[$types[$v->type]];

  $field[$k]['type']['button'] = $v->name;
  $field[$k]['type']['predefined'] = $k;
  $field[$k]['class']['value'] = @$class[$v->fieldcode];
  if (isset($field[$k]['name'])) {
    $field[$k]['name']['value'] = 'field'.$v->id;
    $field[$k]['name']['readonly'] = true;
  }
  $field[$k]['label']['value'] = $v->name;
  if (isset($field[$k]['required']))
    $field[$k]['required']['checked'] = $v->required == 1;
  if (isset($field[$k]['title']))
    $field[$k]['title']['value'] = $v->tips;
  if ($v->type == 'select' && $v->options)
    foreach (explode("\n", $v->options) as $opt) {
    	$val = strtoupper(preg_replace('/\W/', '', function_exists('iconv') ? iconv("UTF-8", "Windows-1252", $opt) : $opt ));
      $field[$k]['select']['value'].= '<option value="'.$val.'">'.$opt.'</option>';
    }
}

$prefix = 'jform';
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
    "class": {value: ""},
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
    name: {value: "password1", readonly: true},
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
    name: {value: "email1", readonly: true},
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
    name: {value: "email2", readonly: true},
    prefix: {value: "jform"},
    label: {value: "", defaultValue: "COM_USERS_REGISTER_EMAIL2_LABEL",
      placeholder: "<?php echo addslashes(JText::_('COM_USERS_REGISTER_EMAIL2_LABEL'))?>"},
    placeholder: {value: "", placeholder: ""},
    title: {value: "", defaultValue: "COM_USERS_REGISTER_EMAIL2_DESC",
      placeholder: "<?php echo addslashes(JText::_('COM_USERS_REGISTER_EMAIL2_DESC'))?>"},
    error: {value: "", defaultValue: "COM_USERS_REGISTER_EMAIL2_MESSAGE",
      placeholder: "<?php echo addslashes(JText::_('COM_USERS_REGISTER_EMAIL2_MESSAGE'))?>"}
  },
  captcha: {
    type: {value: "captcha", predefined: "captcha",
      readonly: true, button: "Captcha image", icon: "icon-picture"},
    wide: {checked: true},
    "class": {value: ""},
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
  },
  submit: {
    type: {value: "button", predefined: "submit",
      readonly: true, button: "Submit", icon: "icon-arrow-right"},
    wide: {checked: false},
    "class": {value: ""},
    label: {value: "", defaultValue: "JREGISTER",
      placeholder: "<?php echo JText::_('JREGISTER') ?>"},
    subtitle: {value: "&nbsp", placeholder:""}
  },
  // Custom fields
  header: {
    type: {value: "header", readonly: true, button: "Header", icon: "icon-quote icon-font"},
    wide: {checked: true},
    "class": {value: ""},
    label: {value: "", placeholder: "Header text"},
    subtitle: {value: "", placeholder: ""}
  },
  label: {
    type: {value: "label", readonly: true, button: "Label", icon: "icon-quote icon-align-justify"},
    wide: {checked: false},
    "class": {value: ""},
    label: {value: "", placeholder: "Label text"}
  }
});