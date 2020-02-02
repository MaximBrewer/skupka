<?php

define('_JEXEC', 1);

// No direct access.
defined('_JEXEC') or die;

define( 'DS', DIRECTORY_SEPARATOR );

define('JPATH_BASE', dirname(__FILE__).DS.'..'.DS.'..'.DS.'..'.DS.'..' );

require_once ( JPATH_BASE .DS.'includes'.DS.'defines.php' );
require_once ( JPATH_BASE .DS.'includes'.DS.'framework.php' );

jimport('joomla.database.database');
jimport('joomla.database.table');

$app = JFactory::getApplication('site');
$app->initialise();

$user = JFactory::getUser();

$plugin	= JPluginHelper::getPlugin('content', 'vrvote');

$params = new JRegistry;
$params->loadString($plugin->params);

$db =& JFactory::getDBO();

$user_rating = JRequest::getInt('user_rating');
$cid = JRequest::getInt('cid');

if ($user_rating >= 1 && $user_rating <= 5) {
		$currip = $_SERVER['REMOTE_ADDR'];
		$query = "SELECT * FROM #__content_vrvote WHERE content_id=".$cid;
		$db->setQuery( $query );
		$votesdb = $db->loadObject();
			if ( !$votesdb ) {
				$query = "INSERT INTO #__content_vrvote  (content_id,extra_id,lastip,rating_count,rating_sum)"
				. "\n VALUES (".$cid.",".$cid.",".$db->Quote($currip).",1,".$user_rating.")";
				$db->setQuery( $query );
				$db->query() or die( $db->getErrorMsg() );
			} else {
				if ($currip != ($votesdb->lastip)) {
					$query = "UPDATE #__content_vrvote"
					. "\n SET rating_count = rating_count + 1, rating_sum = rating_sum + " .  $user_rating . ", lastip = " . $db->Quote( $currip )
					. "\n WHERE content_id=".$cid;
					$db->setQuery( $query );
					$db->query() or die( $db->getErrorMsg() );
				} else {
					echo 'voted';
					exit();
				}
			}
		}
		echo 'thanks';

?>