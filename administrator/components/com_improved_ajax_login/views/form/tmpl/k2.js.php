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
// no direct access
defined('_JEXEC') or die;
?>
JBackend = true;
JURI = "<?php echo JURI::root() ?>";
Theme = "<?php echo $this->theme ?>";
PredefinedElems = {
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
    prefix: {value: "jform"},
    label: {value: "", defaultValue: "COM_USERS_REGISTER_NAME_LABEL",
      placeholder: "<?php echo addslashes(JText::_('COM_USERS_REGISTER_NAME_LABEL'))?>"},
    placeholder: {value: "", placeholder: ""},
    title: {value: "", defaultValue: "COM_USERS_REGISTER_NAME_DESC",
      placeholder: "<?php echo addslashes(JText::_('COM_USERS_REGISTER_NAME_DESC'))?>"},
    pattern: {value: ""},
    error: {value: "", defaultValue: "JLIB_FORM_VALIDATE_FIELD_INVALID",
      placeholder: "<?php echo addslashes(JText::sprintf('JLIB_FORM_VALIDATE_FIELD_INVALID', ''))?>"}
  },
  username: {
    type: {value: "textfield", defaultValue: "text", predefined: "username",
      readonly: true, button: "Username", icon: "icon-user"},
    required: {checked: true, disabled: true},
    wide: {checked: false},
    "class": {value: "ial-username"},
    name: {value: "username", readonly: true},
    prefix: {value: "jform"},
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
    prefix: {value: "jform"},
    label: {value: "", defaultValue: "COM_USERS_REGISTER_PASSWORD1_LABEL",
      placeholder: "<?php echo addslashes(JText::_('COM_USERS_REGISTER_PASSWORD1_LABEL'))?>"},
    placeholder: {value: "", placeholder: ""},
    title: {value: "", defaultValue: "COM_USERS_DESIRED_PASSWORD",
      placeholder: "<?php echo addslashes(JText::_('COM_USERS_DESIRED_PASSWORD'))?>"},
    error: {value: "", defaultValue: "JLIB_FORM_VALIDATE_FIELD_INVALID",
      placeholder: "<?php echo addslashes(JText::sprintf('JLIB_FORM_VALIDATE_FIELD_INVALID', ''))?>"}
  },
  password2: {
    type: {value: "password2", defaultValue: "password", predefined: "password2",
      readonly: true, button: "Password again", icon: "icon-lock"},
    required: {checked: true, disabled: true},
    wide: {checked: false},
    "class": {value: "ial-password2"},
    name: {value: "password2", readonly: true},
    prefix: {value: "jform"},
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
    prefix: {value: "jform"},
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
    "class": {value: ""},
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
    error: {value: "", defaultValue: "JLIB_FORM_VALIDATE_FIELD_INVALID",
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
  // Profile fields
  gender: {
    type: {value: "select", predefined: "gender", readonly: true, button: "Gender", icon: "icon-chevron-down"},
    required: {checked: false, disabled: false},
    wide: {checked: false},
    "class": {value: ""},
    name: {value: "gender", readonly: true},
    prefix: {value: ""},
    label: {value: "", defaultValue: "K2_GENDER", placeholder: "<?php echo addslashes(JText::_('K2_GENDER')) ?>"},
    select: {value: "<option value=\"m\"><?php echo addslashes(JText::_('K2_MALE')) ?></option><option value=\"f\"><?php echo addslashes(JText::_('K2_FEMALE')) ?></option>"},
    title: {value: "", placeholder: ""}
  },
  description: {
    type: {value: "textarea", predefined: "description", readonly: true, button: "Description", icon: "icon-pencil-2 icon-pencil"},
    required: {checked: false, disabled: false},
    wide: {checked: false},
    "class": {value: "ial-aboutme"},
    name: {value: "description", readonly: true},
    prefix: {value: ""},
    label: {value: "", defaultValue: "K2_DESCRIPTION", placeholder: "<?php echo addslashes(JText::_('K2_DESCRIPTION')) ?>"},
    value: {value: ""},
    placeholder: {value: "", placeholder: ""},
    title: {value: "", placeholder: ""},
    error: {value: "", defaultValue: "JLIB_FORM_VALIDATE_FIELD_INVALID",
      placeholder: "<?php echo addslashes(JText::sprintf('JLIB_FORM_VALIDATE_FIELD_INVALID', ''))?>"},
    pattern: {value: ""}
  },
  url: {
    type: {value: "textfield", defaultValue: "text", predefined: "url",
      readonly: true, button: "URL", icon: "icon-file"},
    required: {checked: false, disabled: false},
    wide: {checked: false},
    "class": {value: "ial-website"},
    name: {value: "url", readonly: true},
    prefix: {value: ""},
    label: {value: "", defaultValue: "K2_URL",
      placeholder: "<?php echo addslashes(JText::_('K2_URL'))?>"},
    placeholder: {value: "", placeholder: ""},
    value: {value: "http://"},
    title: {value: "", placeholder: ""},
    pattern: {value: ""},
    error: {value: "", defaultValue: "JLIB_FORM_VALIDATE_FIELD_INVALID",
      placeholder: "<?php echo addslashes(JText::sprintf('JLIB_FORM_VALIDATE_FIELD_INVALID', ''))?>"}
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
};