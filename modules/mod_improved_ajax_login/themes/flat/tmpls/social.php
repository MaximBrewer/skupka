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


<?php if ($icontype == 'socialIco'):?>
  <div class="ial-socials">
    <?php foreach($oauth_list as $oauth):?>
    <div class="socialIco" data-oauth="<?php echo $oauth->alias ?>" title="<?php echo JText::_("IAL_LOGIN_WITH_".strtoupper($oauth->alias)) ?>">
      <div class="socialImg <?php echo $oauth->alias?>Img"><div class="socialImgHover <?php echo $oauth->alias?>Img"></div></div>
    </div>
    <?php endforeach;?>
  </div>
<?php else:?>
  <div class="ial-oauths">
  <?php foreach($oauth_list as $oauth):?>
  <button class="loginBtn ial-submit" data-oauth="<?php echo $oauth->alias?>"><span class="btnIco <?php echo $oauth->alias?>Ico">&nbsp;</span><?php echo JText::_("IAL_LOGIN_WITH_".strtoupper($oauth->alias)) ?></button>
  <?php endforeach;?>
  </div>
<?php endif;?>
