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

// Security check to ensure this file is being included by a parent file.
if (!defined('_JEXEC')) die('Direct Access to this location is not allowed.');

Class Sh404sefControllerAnalytics extends Sh404sefClassBasecontroller {

  protected $_context = 'com_sh404sef.analytics';
  protected $_defaultModel = 'analytics';
  protected $_defaultView = 'analytics';
  protected $_defaultController = 'analytics';
  protected $_defaultTask = '';
  protected $_defaultLayout = 'default';

  protected $_returnController = 'analytics';
  protected $_returnTask = '';
  protected $_returnView = 'analytics';
  protected $_returnLayout = 'default';

}
