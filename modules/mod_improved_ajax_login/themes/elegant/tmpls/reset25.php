<?php
/*-------------------------------------------------------------------------
# mod_improved_ajax_login - Improved AJAX Login and Register
# -------------------------------------------------------------------------
# @ author    Balint Polgarfi
# @ copyright Copyright (C) 2017 Offlajn.com  All Rights Reserved.
# @ license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @ website   http://www.offlajn.com
-------------------------------------------------------------------------*/
defined('_JEXEC') or die('Restricted access');
?>
<form action="<?php echo JRoute::_('index.php?option=com_users&task=reset.request'); ?>" method="post" class="ial-login ialResetForm" name="ialLoginReset">
  <div class="gi-elem">
    <h3 class="loginH3"><?php echo JText::_('COM_USERS_RESET')?></h3>
  </div>
  <div class="gi-elem">
    <span class="smallTxt"><?php echo JText::_('COM_USERS_RESET_REQUEST_LABEL') ?><br/><br/></span>
  </div>
  <div class="gi-elem ial-email1">
    <input id="resetEmail" class="loginTxt" name="jform[email]" type="text" placeholder="<?php echo JText::_('COM_USERS_FIELD_PASSWORD_RESET_LABEL') ?>" />
  </div>
<?php if ($reCaptcha) : ?>
  <div class="gi-elem">
    <div id="resetCaptcha" class="ial-recaptcha" data-sitekey="<?php echo $reCaptcha ?>"></div>
  </div>
<?php endif ?>
  <div class="gi-elem">
    <button class="loginBtn ial-reset" id="resetBtn" style="width: 100%; margin-bottom: 10px"><span><i class="ial-load"></i><?php echo JText::_('JSUBMIT')?></span></button>
  </div>
  <input id="ialReset" name="ialReset" type="hidden" value="1" />
  <?php echo JHTML::_('form.token') ?>
</form>
<br style="clear:both" />
