<?php
/*-------------------------------------------------------------------------
# plg_offlajnjoomla3compat - Offlajn Joomla 3 Compatibility
# -------------------------------------------------------------------------
# @ author    Jeno Kovacs
# @ copyright Copyright (C) 2014 Offlajn.com  All Rights Reserved.
# @ license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @ website   http://www.offlajn.com
-------------------------------------------------------------------------*/
?><?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

/* Loading only for Joomla 3 and greater */
if(version_compare(JVERSION,'3.0.0','l')){
  if(!function_exists('Offlajnjimport')){
    function Offlajnjimport($key, $base = null){
      return jimport($key);
    }
  }
  if(!function_exists('OfflajnnameQuote')){
    function OfflajnnameQuote(&$db, $s){
      return $db->nameQuote($s);
    }
  }
  if(!function_exists('Offlajnescape')){
    function Offlajnescape(&$db, $s){
      return $db->getEscaped($s);
    }
  }
  if(!function_exists('OfflajngetAttribute')){
    function OfflajngetAttribute(&$xml, $s){
      return $xml->attributes($s);
    }
  }
  return;
}else{
  if(!function_exists('OfflajnnameQuote')){
    function OfflajnnameQuote(&$db, $s){
      return $db->quoteName($s);
    }
  }
  if(!function_exists('Offlajnescape')){
    function Offlajnescape(&$db, $s){
      return $db->escape($s);
    }
  }
  if(!function_exists('OfflajngetAttribute')){
    function OfflajngetAttribute(&$xml, $attr){
      return (string)$xml->attributes()->{$attr};
    }
  }
}

defined('DS') or define( 'DS', DIRECTORY_SEPARATOR );
defined('OfflajnCOMPAT') or define( 'OfflajnCOMPAT', dirname(__FILE__).'/compat/libraries');

if(version_compare(JVERSION,'3.0.0','ge')){
  function OfflajnJoomla3CompatFixArray($a){
    if (is_array($a) || is_object($a)) {
      foreach($a AS $k => $v){
        if(is_array($v)){
          $a[$k] = OfflajnJoomla3CompatFixArray($v);
        }elseif(isset($a[$k][0]) && $a[$k][0] == '{'){
          $a[$k] = str_replace('\\"', '"', $a[$k]);
        }
      }
    }
    return $a;
  }

  $jpost = JFactory::getApplication()->input->post;
  $jform = $jpost->get('jform', array(), null);

  if ($jpost->getCmd('task') == 'module.apply' && isset($jform['params']) && isset($jform['params']['moduleparametersTab'])) {
    ${'_POST'} = OfflajnJoomla3CompatFixArray(${'_POST'});
  }

  function Offlajnjimport($path){
    defined('OFFLAJNCOMPAT') or define( 'OFFLAJNCOMPAT', dirname(__FILE__).'/compat/libraries');
    $path = str_replace('joomla', 'coomla', $path);
    return JLoader::import($path, OfflajnCOMPAT);
  }
}

class plgSystemOfflajnJoomla3compat extends JPlugin {

  var $cache = 0;

  function __construct(& $subject) {
		parent::__construct($subject);
 	}

  function onAfterInitialise(){

  }

}