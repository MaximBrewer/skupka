<?php
/**
 * @package         Advanced Module Manager
 * @version         7.9.3
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            http://www.regularlabs.com
 * @copyright       Copyright © 2018 Regular Labs All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

if ( ! is_file(JPATH_LIBRARIES . '/regularlabs/autoload.php'))
{
	return;
}

require_once JPATH_LIBRARIES . '/regularlabs/autoload.php';

/**
 * Plugin that logs User Actions
 */
class PlgActionlogAdvancedModules
	extends \RegularLabs\Library\ActionLogPlugin
{
	public $name  = 'ADVANCED_MODULE_MANAGER';
	public $alias = 'advancedmodules';

	public function __construct(&$subject, array $config = [])
	{
		parent::__construct($subject, $config);

		$this->items = [
			'module' => (object) [
				'title' => 'PLG_ACTIONLOG_JOOMLA_TYPE_MODULE',
			],
		];
	}
}
