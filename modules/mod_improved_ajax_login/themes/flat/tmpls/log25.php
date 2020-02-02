<?php
/*-------------------------------------------------------------------------
# mod_improved_ajax_login - Improved AJAX Login and Register
# -------------------------------------------------------------------------
# @ author    Balint Polgarfi
# @ copyright Copyright (C) 2013 Offlajn.com  All Rights Reserved.
# @ license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @ website   http://www.offlajn.com
-------------------------------------------------------------------------*/
?><?php defined('_JEXEC') or die('Restricted access') ?>

<?php if (count($modules = JModuleHelper::getModules($params->get('top_module', 'login-top')))): // LOGIN-TOP MODULEPOS ?>
  <?php foreach ($modules as $m): ?>
    <?php echo JModuleHelper::renderModule($m) ?>
  <?php endforeach ?>
  <div class="loginBrd"></div>
<?php endif ?>

<?php if (@$oauth_list && $socialpos=='top') require dirname(__FILE__).'/social.php' // TOP SOCIALPOS ?>

<form action="<?php echo JRoute::_('index.php') ?>" method="post" name="ialLogin" class="ial-login <?php if (!$loginpopup) echo 'fullWidth' ?> ialLoginBaseForm">
  <?php if ($loginH3 = trim($params->get('header', 'Login to your account'))): ?>
  <div class="gi-elem gi-wide">
    <h3 class="loginH3"><?php echo JText::_($loginH3) ?></h3>
  </div>
  <?php endif ?>

  <div class="gi-elem gi-wide">
    <?php $icon = $params->get('username', 1)? 'gi-user' : 'gi-ial-email1'; ?>
    <div class="gi-field-out"><div class="gi-field-icon <?php echo $icon ?>"><div class="gi-field-icon-hover <?php echo $icon ?>"></div></div></div>
    <input id="userTxt" class="loginTxt" name="<?php echo $params->get('username', 1)? 'username':'email'?>" type="text" placeholder="<?php echo $auth ?>" autocomplete="off" />
  </div>
  <div class="gi-elem gi-wide">
    <div class="gi-field-out"><div class="gi-field-icon gi-passw"><div class="gi-field-icon-hover gi-passw"></div></div></div>
    <input id="passTxt" class="loginTxt" name="password" type="password" placeholder="<?php echo $password ?>" autocomplete="off" />
  </div>
  <?php if (count($twofactormethods) > 1): ?>
    <div class="gi-elem gi-wide">
      <div class="gi-field-out"><div class="gi-field-icon gi-key"><div class="gi-field-icon-hover gi-key"></div></div></div>
      <input id="secretTxt" class="loginTxt" name="secretkey" type="text" placeholder="<?php echo JText::_('JGLOBAL_SECRETKEY') ?>" autocomplete="off"
       title="<?php echo JText::_('JGLOBAL_SECRETKEY_HELP') ?>" />
    </div>
  <?php endif; ?>
  <div class="gi-elem gi-wide">
    <button class="loginBtn ial-submit" id="submitBtn"><span><i class="ial-load"></i><?php echo JText::_('JLOGIN')?></span></button>
  </div>
  <div class="gi-elem gi-wide">
    <?php if (JPluginHelper::isEnabled('system', 'remember')): // REMEMBER ME ?>
      <label class="ial-check-lbl smallTxt" for="keepSigned">
  		  <input id="keepSigned" name="remember" type="checkbox" class="ial-checkbox" <?php if ($params->get('rememberme', 0)) echo 'checked="checked"'?> />
  			<?php echo JText::_('MOD_LOGIN_REMEMBER_ME') ?>
  		</label>
  	<?php endif ?>
  	<div class="forgetDiv">
      <?php if ($params->get('forgotpass', 1)):?>
  		<a class="forgetLnk frg_reset" href="<?php echo JRoute::_('index.php?option=com_users&view=reset') ?>"><?php echo JText::_('MOD_LOGIN_FORGOT_YOUR_PASSWORD') ?></a><br />
      <?php endif ?>
      <?php if ($params->get('forgotname', 0)):?>
      <a class="forgetLnk frg_remind" href="<?php echo JRoute::_('index.php?option=com_users&view=remind') ?>"><?php echo JText::_('MOD_LOGIN_FORGOT_YOUR_USERNAME') ?></a><br />
      <?php endif ?>
  	</div>
  </div>
  <br style="clear:both" />
	<input type="hidden" name="option" value="com_users" />
	<input type="hidden" name="task" value="user.login" />
	<input type="hidden" name="return" value="<?php echo $return ?>" />
  <input type="hidden" name="lang" value="<?php echo JFactory::getLanguage()->getTag() ?>" />
	<?php echo JHTML::_('form.token') ?>
</form>
<form name="saved" style="display:none">
  <?php $saveduser = $params->get('username', 1)? 'username':'email'?>
  <input type="text" name="<?php echo $saveduser ?>" id="saveduser" />
  <input type="password" name="password" id="savedpass" />
</form>


<?php
if (@$oauth_list && $socialpos=='bottom') require dirname(__FILE__).'/social.php'; // BOTTOM SOCIALPOS
else echo '<br />';
?>

<?php if (count($modules = JModuleHelper::getModules($params->get('bottom_module', 'login-bottom')))): // LOGIN-BOTTOM MODULEPOS ?>
  <div class="loginBrd"></div>
  <?php foreach ($modules as $m): ?>
    <?php echo JModuleHelper::renderModule($m) ?>
  <?php endforeach ?>
<?php endif ?>