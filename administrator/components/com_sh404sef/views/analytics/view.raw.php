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

jimport( 'joomla.application.component.view');

class Sh404sefViewAnalytics extends ShlMvcView_Base {

  public function display( $tpl = null) {

  	// version prefix
  	$this->joomlaVersionPrefix = Sh404sefHelperGeneral::getJoomlaVersionPrefix();

    // prepare the view, based on request
    // do we force reading updates from server ?
    $options = Sh404sefHelperAnalytics::getRequestOptions();

    // push display options into template
    $this->options = $options;

    // call report specific methods to get data
    $method = '_makeView' . ucfirst( $options['report']);
    if (is_callable( array( $this, $method))) {
      $this->$method( $tpl);
    }

    // flag to know if we should display placeholder for ajax fillin
    $this->isAjaxTemplate = false;

    parent::display( $this->joomlaVersionPrefix);
  }

  /**
   * Prepare and display the control panel
   * dashboard, which is a simplified view
   * of main analytics results
   *
   * @param string $tpl layout name
   */
  private function _makeViewDashboard( $tpl) {

    // get configuration object
    $sefConfig = & Sh404sefFactory::getConfig();

    // push it into to the view
    $this->sefConfig = $sefConfig;

    // get analytics data using helper, possibly from cache
    $analyticsData = Sh404sefHelperAnalytics::getData( $this->options);

    // push analytics stats into view
    $this->analytics = $analyticsData;

  }

}
