<?php
/**
 * @author     mediahof, Kiel-Germany
 * @link       http://www.mediahof.de
 * @copyright  Copyright (C) 2013 - 2014 mediahof. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die;

class plgSystemFail2Ban extends JPlugin
{
   
    public function onUserLoginFailure(array $response)
    {
	  	 
		 jimport( 'joomla.application.application' );
	     $app = JFactory::getApplication(); 
		 $a = $app->isAdmin(); 
		 if ($this->params->get('only_admin', false))
		 if (!$a)
		 return; 

	    //[Mon Mar 31 10:13:58 2014] [error] [client 212.109.14.203] user mywebsite authentication failure
		$msg = '[error] [client '.$_SERVER['REMOTE_ADDR'].'] user '.$response['username'].' '.$_SERVER['SERVER_NAME'].' authentication failure';
		$param = $this->params->get('use_syslog', true);
	    if (!empty($param))
		{
		   openlog('joomla', LOG_NDELAY, LOG_USER);
		   syslog(LOG_ERR, sprintf($msg));
		   error_log(sprintf($this->params->get('message'), $response['username']));
		}
		else
        error_log(sprintf($this->params->get('message'), $response['username']));
    }
}