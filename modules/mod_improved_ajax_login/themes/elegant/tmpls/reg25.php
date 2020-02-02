<?php
/*-------------------------------------------------------------------------
# mod_improved_ajax_login - Improved AJAX Login and Register
# -------------------------------------------------------------------------
# @ author    Balint Polgarfi
# @ copyright Copyright (C) 2013 Offlajn.com  All Rights Reserved.
# @ license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @ website   http://www.offlajn.com
-------------------------------------------------------------------------*/
?><?php defined('_JEXEC') or die('Restricted access'); ?>

<?php if (count($modules = JModuleHelper::getModules($params->get('reg_top', 'reg-top')))): // REG-TOP MODULEPOS ?>
  <?php foreach ($modules as $m): ?>
    <?php echo JModuleHelper::renderModule($m) ?>
  <?php endforeach ?>
  <div class="loginBrd"></div>
<?php endif ?>

<?php if (@$oauth_list && $socialpos=='top') require dirname(__FILE__).'/social.php' // TOP SOCIALPOS ?>

<?php
$registration = 'registration';
if ($regp[0] != 'joomla') $registration.= '/'.$regp[0];

switch ($regp[0]) {
  case 'joomla': $lang->load('plg_user_profile', JPATH_ADMINISTRATOR); break;
  case 'virtuemart': $lang->load('com_virtuemart'); break;
}

// get language code
$lang_code = $lang->setLanguage('');
$lang->setLanguage($lang_code);

$db = JFactory::getDBo();
$db->setQuery(defined('DEMO')?
  "SELECT fields, props FROM #__offlajn_forms WHERE id = {$params->get('regform', 1)}":
  "SELECT fields, props FROM #__offlajn_forms WHERE state=1 AND type='$registration' AND (id < 99 OR language = '$lang_code') ORDER BY id" );
$res = $db->loadObjectList();
$elems = isset($res[1]) ? @json_decode($res[1]->fields)->page[0]->elem : null;
$fields = json_decode($res[0]->fields);
foreach ($fields->page[0]->elem as $id => &$elem) {
  foreach ($elem as $name => &$prop) {
    switch ($name) {
      case 'jform[elem_name]':
      case 'jform[elem_type]':
        $prop->value = $prop->value ? $prop->value : $prop->placeholder;
        unset($prop->placeholder);
        break;
      case 'jform[elem_label]':
      case 'jform[elem_subtitle]':
      case 'jform[elem_placeholder]':
      case 'jform[elem_title]':
      case 'jform[elem_error]':
      case 'jform[elem_select]':
      case 'jform[elem_article]':
      case 'jform[elem_article_name]':
        if ($elems && isset($elems->{$id}) && $elems->{$id}->{$name}->value != $prop->value) {
          $prop->value = $elems->{$id}->{$name}->value;
        }
        if (!$prop->value) {
          if (isset($prop->defaultValue) && $prop->defaultValue == 'JLIB_DATABASE_ERROR_VALID_AZ09') {
            $prop->value = html_entity_decode(@JText::sprintf($prop->defaultValue, 2), ENT_COMPAT, 'UTF-8');
          } else {
            $prop->value = isset($prop->defaultValue) ? @JText::_($prop->defaultValue) : @$prop->placeholder;
          }
        }
        unset($prop->placeholder);
        unset($prop->defaultValue);
        break;
    }
  }
}
?>
<script>var ialFields = <?php echo json_encode($fields) ?>, ialProps = <?php echo $res[0]->props ?>;</script>
<?php if ($regp[0] == 'joomla' || $regp[0] == 'jomsocial'): ?>
<form action="<?php echo JRoute::_('index.php?option=com_users', true) ?>" method="post" name="ialRegister" class="ial-form">
  <input type="hidden" value="com_users" name="option" />
  <input type="hidden" value="registration.register" name="task" />
  <input type="hidden" name="lang" value="<?php echo JFactory::getLanguage()->getTag() ?>" />
  <?php echo JHTML::_('form.token') ?>
<?php elseif ($regp[0] == 'k2'): ?>
<form action="<?php echo JRoute::_('index.php?option=com_users', true) ?>" method="post" name="ialRegister" class="ial-form">
  <input type="hidden" value="com_users" name="option" />
  <input type="hidden" value="registration.register" name="task" />
  <input type="hidden" value="0" name="id" />
  <input type="hidden" value="0" name="gid" />
  <input type="hidden" value="1" name="K2UserForm">
  <?php echo JHTML::_('form.token') ?>
<?php elseif ($regp[0] == 'hikashop'): ?>
<form action="<?php echo JRoute::_('index.php?option=com_hikashop&ctrl=user&task=register', true) ?>" method="post" name="ialRegister" class="ial-form">
  <input type="hidden" value="com_hikashop" name="option" />
  <input type="hidden" value="user" name="ctrl" />
  <input type="hidden" value="register" name="task" />
  <input type="hidden" value="0" name="data[register][id]" />
  <input type="hidden" value="0" name="data[register][gid]" />
  <input type="hidden" value="1" name="acylistsdisplayed_dispall" />
<?php elseif ($regp[0] == 'virtuemart'): ?>
<form action="<?php echo JRoute::_('index.php?option=com_virtuemart', true) ?>" method="post" name="ialRegister" class="ial-form">
  <input type="hidden" value="com_virtuemart" name="option" />
  <input type="hidden" value="user" name="controller" />
  <input type="hidden" value="saveUser" name="task" />
  <input type="hidden" value="BT" name="address_type" />
  <?php echo JHTML::_('form.token'); ?>
<?php endif; ?>
</form>

<br style="clear:both" />
<?php if (@$oauth_list && $socialpos=='bottom') require dirname(__FILE__).'/social.php' // BOTTOM SOCIALPOS ?>

<?php if (count($modules = JModuleHelper::getModules($params->get('reg_bottom', 'reg-bottom')))): // REG-BOTTOM MODULEPOS ?>
  <div class="loginBrd"></div>
  <?php foreach ($modules as $m): ?>
    <?php echo JModuleHelper::renderModule($m) ?>
  <?php endforeach ?>
<?php endif ?>
