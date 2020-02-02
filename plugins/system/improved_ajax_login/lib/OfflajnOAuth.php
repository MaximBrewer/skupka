<?php
/*------------------------------------------------------------------------
# plg_improved_ajax_login - Improved AJAX Login & Register
# ------------------------------------------------------------------------
# author    Balint Polgarfi
# copyright Copyright (C) 2013 Offlajn.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.offlajn.com
-------------------------------------------------------------------------*/
?><?php
defined('_JEXEC') or die('Restricted access');

class OfflajnOAuth
{
	// variables
  var $oauth;
  var $user;

	function OfflajnOAuth($oauth)
	{
    $db = JFactory::getDBO();
    $db->setQuery("SELECT * FROM #__offlajn_oauths WHERE published = 1 AND alias = '$oauth'");
    $this->oauth = $db->loadObject();
    if (!$this->oauth) die("Error: can't find $oauth authentication!");
	}

  function getRedirectURI($alias) {
    $root = JURI::root(true);
    if ($root != '/') $root.= '/';
    $domain = ($_SERVER['SERVER_PORT']==443? 'https://':'http://').$_SERVER['SERVER_NAME'];
    return $alias == "windows" || $alias == 'twitter' || $alias == 'vk' ? $domain.$root.'index.php' :
      $domain.$root.'index.php?option=com_improved_ajax_login&task='.$alias;
  }

  function getToken()
  {
    $class = $this->oauth->alias.'oauth';
    require_once(dirname(__FILE__)."/{$class}.php");

    $oa = new $class($this->oauth->app_id, $this->oauth->app_secret);

    $rt = $oa->getRequestToken(self::getRedirectURI($this->oauth->alias));

    ${'_SESSION'}['oauth'] = $rt;

    if (200 == $oa->http_code) {
      header('Location: '.$oa->getAuthorizeURL($rt['oauth_token']));
      exit;
    } else die("HTTP code: {$oa->http_code}<br>Could not connect to Twitter. Refresh the page or try again later.");
  }

  function getUser()
  {
    if ($this->user) return $this->user;

    $class = $this->oauth->alias.'oauth';
    require_once(dirname(__FILE__)."/{$class}.php");

    $rt = ${'_SESSION'}['oauth'];
    $oa = new $class($this->oauth->app_id, $this->oauth->app_secret, $rt['oauth_token'], $rt['oauth_token_secret']);
    $at = $oa->getAccessToken($_REQUEST['oauth_verifier']);

    if (200 == $oa->http_code) {
      $oa = new $class($this->oauth->app_id, $this->oauth->app_secret, $at['oauth_token'], $at['oauth_token_secret']);
      $user = $oa->get('account/verify_credentials', array('include_email' => 'true', 'skip_status' => 'true'));
      $this->user = new OfflajnOAuthUser($user, $this->oauth->alias);
      return $this->user;
    } else echo("HTTP code: {$oa->http_code}<br>Could not connect to Twitter. Refresh the page or try again later.");

    return null;
  }

}

class OfflajnOAuth2 extends OfflajnOAuth
{
	// variables
  var $code;
  var $use_curl;
  var $token;

	// constructor
	function OfflajnOAuth2($oauth, $code, $use_curl = true)
	{
    $this->OfflajnOAuth($oauth);
    $this->code = $code;
    $this->use_curl = $use_curl;

    $this->getToken();
	}

  function getToken()
  {
    if (!$this->token)
    {
      $params = http_build_query(array(
        'client_id' => $this->oauth->app_id,
        'client_secret' => $this->oauth->app_secret,
        'redirect_uri' => self::getRedirectURI($this->oauth->alias),
        'code' => $this->code,
        'grant_type' => 'authorization_code'
      ));
      if ($this->use_curl)
      {
        $params = str_replace("&amp;", "&", $params);
        $ch = curl_init($this->oauth->token);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $token = curl_exec($ch);
        curl_close($ch);
      }
      else
      {
        $context = stream_context_create(array(
          'http' => array(
            'method' => 'POST',
            'header' => array('Content-type: application/x-www-form-urlencoded;charset=UTF-8'),
            'content' => $params
          )
        ));
        $token = file_get_contents($this->oauth->token, false, $context);
      }

      $this->token = json_decode($token);
      if (!is_object($this->token))
      {
        parse_str($token, $token);
        $this->token = (object) $token;
      }
      if (isset($this->token->error)) {
        echo '<pre>';
        print_r($this->token->error);
        exit;
      }
    }

    return $this->token;
  }

  function getUser()
  {
    if (!$this->user)
    {
      $url = $this->oauth->userinfo.$this->token->access_token."&fields=name,email,id";
      if (isset($this->token->user_id)) $url.= '&user_id='.$this->token->user_id;
      if ($this->use_curl)
      {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $user = curl_exec($ch);
        curl_close($ch);
      }
      else $user = file_get_contents($url);
      $u = json_decode($user);
      if (isset($u->response) && isset($u->response[0])) $u = &$u->response[0];
      if (isset($this->token->user_id)) $u->id = $this->token->user_id;
      if (isset($this->token->email)) $u->email = $this->token->email;
      $this->user = new OfflajnOAuthUser($u, $this->oauth->alias);
    }

    return $this->user;
  }

}

class OfflajnOAuthUser
{
	// variables
  var $type;
  var $juser;

	// constructor
  function OfflajnOAuthUser($user, $type)
  {
    $this->type = $type;
    foreach ($user as $key => $value) {
      $this->{$key} = $value;
    }
  }

  function getEmail()
  {
    $email = '';
    if (!empty($this->email)) {
      $email = $this->email;
    } elseif (!empty($this->emailAddress)) {
      $email = $this->emailAddress;
    } elseif (!empty($this->emails->preferred)) {
      $email = $this->emails->preferred;
    } elseif (!empty($this->emails)) {
      foreach ($this->emails as $em) {
        $email = $em;
        break;
      }
    }
    return $email;
  }

  function getUserNames()
  {
    $email = $this->getEmail();
    if ($GLOBALS['username=email'] && $email) return array($email);
    $username = array();
    if (!empty($this->username)) $username[] = trim($this->username);
    if (!empty($this->screen_name)) $username[] = trim($this->screen_name);
    if ($email) {
      $username[] = strtolower(preg_replace('/@.*/', '', $email));
    } elseif ($name = $this->getName()) {
      $username[] = strtolower(preg_replace('/\W/', '', $name));
    }

    return $username;
  }

  function getUserName()
  {
    $username = $this->getUserNames();
    if (OUserHelper::getId($username[0])) {
      $username[0].= OUserHelper::getNewId();
    }
    return $username[0];
  }

  function getName()
  {
    $name = '';
    if (!empty($this->name)) $name = $this->name;
    elseif (!empty($this->firstName)) $name = $this->firstName.' '.$this->lastName;
    elseif (!empty($this->first_name)) $name = $this->first_name.' '.$this->last_name;
    return $name;
  }

  function getJUser()
  {
    if (!$this->juser)
    {
      $db = JFactory::getDBO();
      $db->setQuery("SELECT user_id FROM #__offlajn_users WHERE {$this->type}_id = '{$this->id}'");
      if ($id = $db->loadRow())
      {
        $this->juser = JUser::getInstance($id[0]);
        // if user was deleted, but still exists in #__offlajn_users
        if (!$this->juser->id)
        {
          $db->setQuery("DELETE FROM #__offlajn_users WHERE user_id = {$id[0]}");
          $db->query();
          $this->juser = null;
        }
      }
      else $this->juser = $this->updateOAuthByEmail();
    }

    return $this->juser;
  }

  function saveUser($user_id, $new = true)
  {
    $db = JFactory::getDBO();
    if ($new) $db->setQuery("INSERT INTO #__offlajn_users(user_id, {$this->type}_id) VALUES($user_id, '{$this->id}')");
    // If user is already registered with other OAuth
    else $db->setQuery("UPDATE #__offlajn_users SET {$this->type}_id = '{$this->id}' WHERE user_id = $user_id");
    $db->query();
  }

  function updateOAuthByEmail()
  {
    if ($email = $this->getEmail())
    {
      $db = JFactory::getDBO();
      $db->setQuery("SELECT u.id, ou.user_id FROM #__users AS u LEFT JOIN #__offlajn_users AS ou ON u.id = ou.user_id WHERE u.email = '$email'");
      // If user is already registered with this e-mail address
      if ($id = $db->loadRow())
      {
        $this->saveUser($id[0], !isset($id[1]));
        return JUser::getInstance($id[0]);
      }
    }

    return null;
  }


  function login() {
    if (!$this->getJUser()) return false;
    if ($this->juser->block) return false;

    $app = JFactory::getApplication();
    $db = JFactory::getDBO();

    $tempPass = 'b67a031bb5fc0d92b89cdcc450540bec:rp4n8EYYrYcaqWbHB7YjHxk6J9UEKnw0';

    $query = "SELECT password FROM #__users WHERE id={$this->juser->id}";
    $db->setQuery($query);
    $pass = $db->loadResult();

    $query = "UPDATE #__users SET password='{$tempPass}' WHERE id={$this->juser->id}";
    $db->setQuery($query);
    $db->query();

    $result = $app->login( array( 'username' => $this->juser->username , 'password' => 'magiclogin' ) );

    $query = "UPDATE #__users SET password='{$pass}' WHERE id={$this->juser->id}";
    $db->setQuery($query);
    $db->query();

    return $result;
  }

  function register()
  {
    if ($this->getJUser()) return false;

    jimport('joomla.user.helper');
    $usersConfig = JComponentHelper::getParams('com_users');
    if ($usersConfig->get('allowUserRegistration') == '0')
      die("Registration isn't allowed!");

    $this->juser = new JUser();
    $usertype = $usersConfig->get('new_usertype');
    if (!$usertype) $usertype = 'Registered';

    $userdata = array();
    $userdata['name'] = $this->getName();
    $userdata['username'] = $this->searchUserName();
    $pass = substr(md5(rand()), 0, 8);
    $userdata['password'] = $userdata['password2'] = $pass;
    $userdata['email'] = $this->getEmail();
    // if useractivation is self, don't block the user
    $userdata['block'] = $usersConfig->get('useractivation', 1) > 1? 1 : 0;
    if ($usersConfig->get('useractivation') < 2)
    {
      $this->juser->setParam('activate', 1);
      $userdata['activation'] = '';
    }
    else $userdata['activation'] = JApplication::getHash(JUserHelper::genRandomPassword());
    if (version_compare(JVERSION, '1.6.0', 'lt'))
    {
      $userdata['gid'] = JFactory::getACL()->get_group_id('', $usertype, 'ARO');
      $userdata['usertype'] = $usertype;
    }
    else
    {
      $userdata['gid'] = $usertype;
      $userdata['groups'] = array($usertype);
    }
    if (!$this->juser->bind($userdata)) die(JText::_($this->juser->getError()));
    if (!$this->juser->save()) die(JText::_($this->juser->getError()));

    $this->saveUser($this->juser->id);

    // send mail to user
    JFactory::getLanguage()->load('com_users');
    $config = JFactory::getConfig();
		$emailSubject	= JText::sprintf('COM_USERS_EMAIL_ACCOUNT_DETAILS',
			$this->getName(), $config->get('sitename'));
    $body = version_compare(JVERSION, '3.0.0', 'ge')?
      'COM_USERS_EMAIL_REGISTERED_BODY_NOPW' : 'COM_USERS_EMAIL_REGISTERED_BODY';
  	$emailBody = JText::sprintf($body, $this->getName(),
      $config->get('sitename'), JUri::root())."\n\n";
    if ($usersConfig->get('useractivation') == 2)  // admin approval
    {
      $emailBody.= JText::_('COM_USERS_REGISTRATION_VERIFY_SUCCESS')."\n\n";
    }
    $emailBody.= JText::_('JGLOBAL_USERNAME').": ".$userdata['username']."\n";
    $emailBody.= JText::_('JGLOBAL_PASSWORD').": ".$pass;
    JFactory::getMailer()->sendMail($config->get('mailfrom'),
      $config->get('fromname'), $userdata['email'], $emailSubject, $emailBody);

    return $userdata['activation'];
  }

  function searchUserName()
  {
    $db = JFactory::getDBO();
    $names = $this->getUserNames();
    foreach ($names as $username) {
      $db->setQuery("SELECT id FROM #__users WHERE username LIKE '$username'");
      if (!$db->loadRow()) return $username;
    }

    return $names[0].rand(0, 99);
  }

}
