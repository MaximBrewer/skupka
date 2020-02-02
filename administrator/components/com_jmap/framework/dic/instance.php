<?php
// namespace components\com_jmap\libraries\framework\dic;
/**
 *
 * @package JMAP::administrator::components::com_jmap
 * @subpackage framework
 * @subpackage dic
 * @author Joomla! Extensions Store
 * @copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html  
 */
defined('_JEXEC') or die('Restricted access');

/**
 * Base controller class
 * 
 * @package JMAP::administrator::components::com_jmap
 * @subpackage framework
 * @subpackage dic
 * @since 2.0
 */
class JMapDicInstance {
	public $name;
	public function __construct($instance) {
		$this->name = $instance;
	}
}
