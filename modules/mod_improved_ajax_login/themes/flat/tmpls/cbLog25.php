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

<?php
global $_CB_framework, $ueConfig, $_CB_PMS, $cbSpecialReturnAfterLogin, $cbSpecialReturnAfterLogout;

if ( ( ! file_exists( JPATH_SITE . '/libraries/CBLib/CBLib/Core/CBLib.php' ) ) || ( ! file_exists( JPATH_ADMINISTRATOR . '/components/com_comprofiler/plugin.foundation.php' ) ) ) {
	echo 'CB not installed'; return;
}

include_once( JPATH_ADMINISTRATOR . '/components/com_comprofiler/plugin.foundation.php' );

cbimport( 'cb.html' );
cbimport( 'language.front' );

outputCbTemplate();

require_once( JPATH_SITE . '/modules/mod_cblogin/helper.php' );
?>

<?php if (count($modules = JModuleHelper::getModules($params->get('top_module', 'login-top')))): // LOGIN-TOP MODULEPOS ?>
  <?php foreach ($modules as $m): ?>
    <?php echo JModuleHelper::renderModule($m) ?>
  <?php endforeach ?>
  <div class="loginBrd"></div>
<?php endif ?>

<?php if (@$oauth_list && $socialpos=='top') require dirname(__FILE__).'/social.php' // TOP SOCIALPOS ?>
<?php
$type = modCBLoginHelper::getType();
 echo modCBLoginHelper::getPlugins( $params, $type, 'beforeForm' ); ?>
<form action="<?php echo $_CB_framework->viewUrl( 'login', true, null, 'html', 0 ); ?>" method="post" name="ialLogin" class="ial-login <?php if (!$loginpopup) echo 'fullWidth' ?>">
  <?php if (!$module->showtitle && !$loginpopup || $loginpopup): ?>
  <div class="gi-elem gi-wide">
    <h3 class="loginH3"><?php echo JText::_($params->get('header', 'Login to your account')) ?></h3>
  </div>
  <?php endif ?>

  <div class="gi-elem">
    <?php $icon = $params->get('username', 1)? 'gi-user' : 'gi-ial-email1'; ?>
    <div class="gi-field-out"><div class="gi-field-icon <?php echo $icon ?>"><div class="gi-field-icon-hover <?php echo $icon ?>"></div></div></div>
    <input id="userTxt" class="loginTxt" name="username" type="text" placeholder="<?php echo $auth ?>" autocomplete="off" />
  </div>
  <div class="gi-elem">
    <div class="gi-field-out"><div class="gi-field-icon gi-passw"><div class="gi-field-icon-hover gi-passw"></div></div></div>
    <input id="passTxt" class="loginTxt" name="passwd" type="password" placeholder="<?php echo $password ?>" autocomplete="off" />
  </div>
  <?php //if ($twofactorauth): ?>
    <!--<div class="gi-elem">
      <div class="gi-field-out"><div class="gi-field-icon gi-key"><div class="gi-field-icon-hover gi-key"></div></div></div>
      <input id="secretTxt" class="loginTxt" name="secretkey" type="text" placeholder="<?php echo JText::_('JGLOBAL_SECRETKEY') ?>" autocomplete="off"
       title="<?php echo JText::_('JGLOBAL_SECRETKEY_HELP') ?>" />
    </div>           -->
  <?php //endif; ?>
  <div class="gi-elem">
    <button class="loginBtn ial-submit" id="submitBtn"><span><i class="ial-load"></i><?php echo JText::_('JLOGIN')?></span></button>
  </div>
  <div class="gi-elem">
    <?php if (JPluginHelper::isEnabled('system', 'remember')): // REMEMBER ME ?>
      <label class="ial-check-lbl smallTxt" for="keepSigned">
  		  <input id="keepSigned" name="remember" type="checkbox" class="ial-checkbox" <?php if ($params->get('rememberme', 0)) echo 'checked="checked"'?> />
  			<?php echo JText::_('MOD_LOGIN_REMEMBER_ME') ?>
  		</label>
  	<?php endif ?>
 <div class="forgetDiv">
      <?php if ($params->get('forgotpass', 1)):?>
  		<a class="forgetLnk" href="<?php echo $_CB_framework->viewUrl( 'lostpassword', true, null, 'html', 0 ); ?>"><?php echo JText::_('MOD_LOGIN_FORGOT_YOUR_PASSWORD') ?></a>
      <?php endif ?>
      <br />

  	</div>
  </div>
  <br style="clear:both" />
	<input type="hidden" name="option" value="com_comprofiler" />
	<input type="hidden" name="view" value="login" />
	<input type="hidden" name="op2" value="login" />
	<input type="hidden" name="message" value="" />
	<input type="hidden" name="loginfrom" value="<?php echo htmlspecialchars( ( defined( '_UE_LOGIN_FROM' ) ? _UE_LOGIN_FROM : 'loginmodule' ) ); ?>" />
  <?php echo cbGetSpoofInputTag( 'login' ); ?>
	<input type="hidden" name="return" value="B:<?php echo $return ?>" />
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