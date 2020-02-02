<?php
/*-------------------------------------------------------------------------
# com_improved_ajax_login - com_improved_ajax_login
# -------------------------------------------------------------------------
# @ author    Balint Polgarfi
# @ copyright Copyright (C) 2013 Offlajn.com  All Rights Reserved.
# @ license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @ website   http://www.offlajn.com
-------------------------------------------------------------------------*/
?><?php defined('_JEXEC') or die; ?>
<?php
// get reCaptcha plugin
$db = JFactory::getDbo();
$db->setQuery("SELECT params FROM `#__extensions` WHERE name = 'plg_captcha_recaptcha' LIMIT 1");
$res = $db->loadResult();
$captcha = $res ? json_decode($res) : null;
?>
<div class="ui-accordion">
  <h3>Default fields</h3>
  <div class="default-fields">
    <div class="ui-draggable" data-elem="title"></div>
    <div class="ui-draggable" data-elem="name"></div>
    <div class="ui-draggable" data-elem="username"></div>
    <div class="ui-draggable" data-elem="password1"></div>
    <div class="ui-draggable" data-elem="password2"></div>
    <div class="ui-draggable" data-elem="email"></div>
    <div class="ui-draggable" data-elem="email2"></div>
  <?php if ($captcha) : ?>
    <?php if (empty($captcha->version) || 1 == (int) $captcha->version) : ?>
    <div class="ui-draggable" data-elem="captcha"></div>
    <div class="ui-draggable" data-elem="captcha2"></div>
    <?php else : ?>
    <?php JFactory::getDocument()->addCustomTag("<script>
      ologin.captchaVer = '{$captcha->version}';
      jQuery(function($) {
        $('.ui-draggable[data-elem=captcha] .gi-elem-name').html(\"<i class='icon-checkbox icon-ok-circle'></i> I'm not a robot\");
      });
    </script>") ?>
    <div class="ui-draggable" data-elem="captcha"></div>
    <?php endif ?>
  <?php endif ?>
    <div class="ui-draggable" data-elem="submit"></div>
  </div>

  <h3>Profile fields</h3>
  <div class="profile-fields">
    <div class="ui-draggable" data-elem="address1"></div>
    <div class="ui-draggable" data-elem="address2"></div>
    <div class="ui-draggable" data-elem="city"></div>
    <div class="ui-draggable" data-elem="region"></div>
    <div class="ui-draggable" data-elem="country"></div>
    <div class="ui-draggable" data-elem="postalcode"></div>
    <div class="ui-draggable" data-elem="phone"></div>
    <div class="ui-draggable" data-elem="website"></div>
    <div class="ui-draggable" data-elem="favoritebook"></div>
    <div class="ui-draggable" data-elem="aboutme"></div>
    <div class="ui-draggable" data-elem="dob"></div>
    <div class="ui-draggable" data-elem="tos"></div>
  </div>

  <h3>Custom fields</h3>
  <div class="custom-fields">
    <div class="ui-draggable" data-elem="header"></div>
    <div class="ui-draggable" data-elem="label"></div>
    <div class="ui-draggable" data-elem="textfield"></div>
    <div class="ui-draggable" data-elem="password"></div>
    <div class="ui-draggable" data-elem="textarea"></div>
    <div class="ui-draggable" data-elem="checkbox"></div>
    <div class="ui-draggable" data-elem="article"></div>
    <div class="ui-draggable" data-elem="select"></div>
  </div>
</div>