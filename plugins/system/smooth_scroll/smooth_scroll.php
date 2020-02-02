<?php
// No direct access
defined( '_JEXEC' ) or die;

/**
 *
 * @package     Joomla.Plugin
 * @subpackage  System.Smooth_scroll
 * @since       2.5+, 3.0+
 * @copyright	Copyright (c)2013 ELLE / JoomExt.ru
 * @license	GNU GPLv3 http://www.gnu.org/licenses/gpl.html or later
 * @version 	1.0 Stable
 */

class plgSystemSmooth_scroll extends JPlugin
{
	/**
	 * Class Constructor
	 * @param object $subject
	 * @param array $config
	 */
	public function __construct( & $subject, $config )
	{
		parent::__construct( $subject, $config );
		$this->loadLanguage();
	}

	public function onBeforeCompileHead(){

    	$app = JFactory::getApplication();
		$doc = JFactory::getDocument();

		if ($app->isAdmin()) return true;

        if (version_compare(JVERSION, '3', 'ge')) JHtml::_('jquery.framework');
        $doc->addScript(JURI::base().'media/smooth_scroll/js/jquery.smooth-scroll.min.js');

	}
}
