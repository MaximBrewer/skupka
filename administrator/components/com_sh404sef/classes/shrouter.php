<?php
/**
 * sh404SEF - SEO extension for Joomla!
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier - Weeblr llc - 2016
 * @package     sh404SEF
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     4.7.3.3292
 * @date		2016-03-15
 */

/** ensure this file is being included by a parent file */
defined( '_JEXEC' ) or die;

/**
 * Compatibility layer for 3rdt party plugins
 *
 *
 * @author yannick
 *
 */
class shRouter  {

  public static function &shGetConfig() {

    $config = & Sh404sefFactory::getConfig();

    return $config;
  }

  public static function &shPageInfo() {

    $config = & Sh404sefFactory::getPageInfo();

    return $config;
  }

}
