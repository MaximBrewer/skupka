<?php
/*-------------------------------------------------------------------------
# plg_offlajnparams - Offlajn Params
# -------------------------------------------------------------------------
# @ author    Balint Polgarfi
# @ copyright Copyright (C) 2016 Offlajn.com  All Rights Reserved.
# @ license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @ website   http://www.offlajn.com
-------------------------------------------------------------------------*/
?><?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

if(version_compare(JVERSION,'3.0.0','l')) {
  function offlajn_jimport($key, $base = null) {
    return jimport($key);
  }
} else {
  defined('DS') or define('DS', DIRECTORY_SEPARATOR);
  define('OFFLAJN_COMPAT', dirname(__FILE__).'/compat/libraries');

  function OfflajnJoomlaCompatFixArray($a) {
    if (is_array($a) || is_object($a)) {
      foreach($a AS $k => $v){
        if(is_array($v)){
          $a[$k] = OfflajnJoomlaCompatFixArray($v);
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
    ${'_POST'} = OfflajnJoomlaCompatFixArray(${'_POST'});
  }

  function offlajn_jimport($path) {
    $path = str_replace('joomla', 'coomla', $path);
    return JLoader::import($path, OFFLAJN_COMPAT);
  }
}
