<?php
/*------------------------------------------------------------------------
# com_improved_ajax_login - Improved AJAX Login
# ------------------------------------------------------------------------
# author    Balint Polgarfi
# copyright Copyright (C) 2012 Offlajn.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.offlajn.com
-------------------------------------------------------------------------*/
?>
<?php
defined('_JEXEC') or die( 'Restricted access' );
require_once(dirname(__FILE__).'/lib/OfflajnOAuth.php');

$user = $newuser = 0;

// OAuth 1.0
if (JRequest::getVar('redirect')) {
  $oa = new OfflajnOAuth($task);
  $oa->getToken();  // redirection
}
if (JRequest::getVar('oauth_verifier')) {
  $oa = new OfflajnOAuth($task);
  $user = $oa->getUser();
}

// OAuth 2.0
if ($code = JRequest::getVar('code', 0)) {
  $cURL = !ini_get('allow_url_fopen') && ini_set('allow_url_fopen', 1) === false;
  $oa = new OfflajnOAuth2($task, $code, $cURL);
  $user = $oa->getUser();
} elseif ($errormsg = JRequest::getVar('error_message')) die($errormsg);

if ($user) {
  if (!$user->getJUser()) {
    $newuser = 1;
    $email = $user->getEmail();
    // manual registration
    if (${'_SESSION'}['ologin']['regpage'] == 'joomla' || !$email) {
      ${'_SESSION'}['oauth'] = array('type' => $task, 'id' => $user->id);
      oexit("
        var $ = opener.jq183 || opener.jQuery,
            btn = $('.regBtn'),
            reg = $('form[name=ialRegister]');
        reg.find('[name=\"jform[name]\"]')
          .val('{$user->getName()}').addClass('ial-correct');
        reg.find('[name=\"jform[username]\"]')
          .val('{$user->searchUserName()}').addClass('ial-correct');
        if ('$email')
          reg.find('[name=\"jform[email1]\"], [name=\"jform[email2]\"]')
            .val('$email').addClass('ial-correct');
        $('<input name=\"social_id\" value=\"{$user->id}\" type=\"hidden\">')
          .appendTo(reg);
        if (!btn.hasClass('ial-active')) btn.click();
        window.close();
      ");
    }
    // auto registration
    $token = $user->register();
    if ($token && $user->juser->block) oexit('
      opener.location.href = "'.JRoute::_('index.php?option=com_users&task=registration.activate&token='.$token).'";
      window.close();
    ');
  }
  elseif ($user->juser->block) oexit('
    opener.ologin.$oauthBtn.ialErrorMsg({
      msg: "'.JText::_('JERROR_NOLOGIN_BLOCKED').'"
  	});
    window.close();
  ');
  //get the return URL from module params
  $mod = plgSystemImproved_Ajax_Login::getModule();
  $mparams = json_decode($mod->params);
  $socialProfile = $mparams->socialTab->socialprofil;
  if ($newuser && $socialProfile) {
    // if new user is redirected to profile page after first social login then get profile page
    $myp = explode('|*|', $mparams->moduleparametersTab->mypage ? $mparams->moduleparametersTab->mypage : "joomla|*|");
    $mypage = array(
      'joomla' => 'index.php?option=com_users&view=profile&layout=edit',
      'virtuemart' => 'index.php?option=com_virtuemart&view=user');
    $mypage['hikashop'] = 'index.php?option=com_hikashop&view=user&layout=cpanel';
    $mypage['community'] = 'index.php?option=com_comprofiler';
    $mypage['jomsocial'] = 'index.php?option=com_community&view=profile';
    $mypage['k2'] = 'index.php?option=com_users&view=registration';
    $mypage['custom'] = @$myp[1];
    $returnUrl = JRoute::_($mypage[$myp[0]]);
  } else {
    // else get redirect URL from itemId
    $returnUrl = '';
    $itemid = ($mparams->moduleparametersTab->login) ? $mparams->moduleparametersTab->login : 0;
    if ($itemid) {
      $menu = JApplication::getMenu('site');
      $item = $menu->getItem($itemid);
      if ($item) $returnUrl = JRoute::_($item->link.(strpos($item->link, '?')?'&':'?').'Itemid='.$itemid, false);
    }
  }

  ${'_SESSION'}['ialMessage'] = JFactory::getApplication()->getMessageQueue();
  if ($user->login()) oexit("
    var returnUrl = '$returnUrl';
    var cacheFix = '{$mparams->advancedTab->cache_fix}';
    if (returnUrl) {
      if(cacheFix) {
        returnUrl = returnUrl.replace(/[&\?]_=\d+/, '');
        opener.location.href = returnUrl + (returnUrl.match(/\?/)? '&' : '?') +
        '_=' + new Date().getTime();
      } else
          opener.location.href = returnUrl
    } else if(cacheFix)
        opener.location.href = opener.location.href + (opener.location.href.match(/\?/)? '&' : '?') +
          '_=' + new Date().getTime();
      else
        opener.location.href = opener.location.href
    window.close();
  ");
}
oexit('window.close();');

function oexit($script) {
  @ob_clean(); ?>
  <!DOCTYPE html>
  <html>
    <head>
      <meta http-equiv="content-type" content="text/html; charset=utf-8" />
      <title>Login</title>
      <script type="text/javascript">
        <?php echo $script ?>
      </script>
    </head>
    <body>
    </body>
  </html>
  <?php
  exit;
}