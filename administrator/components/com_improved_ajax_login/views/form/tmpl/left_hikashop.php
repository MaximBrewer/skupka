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
    <div class="ui-draggable" data-elem="submit"></div>
  </div>

  <h3>Hikashop fields</h3>
  <div class="profile-fields">
    <a href="<?php echo JURI::root() ?>administrator/index.php?option=com_hikashop&ctrl=field" target="_blank">[ Manage HikaShop fields ]</a>
    <?php foreach ($address as $k=>$v): ?>
    <div class="ui-draggable" data-elem="<?php echo $k ?>"><?php echo $v->field_realname ?></div>
    <?php endforeach ?>
    <div class="ui-draggable" data-elem="acymail"></div>
  </div>

  <h3>Custom fields</h3>
  <div class="custom-fields">
    <div class="ui-draggable" data-elem="header"></div>
    <div class="ui-draggable" data-elem="label"></div>
  </div>
</div>