<?php
/**
 * @package         Regular Labs Library
 * @version         18.10.22077
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            http://www.regularlabs.com
 * @copyright       Copyright © 2018 Regular Labs All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

use Joomla\CMS\Form\FormField as JFormField;
use RegularLabs\Library\Document as RL_Document;

if ( ! is_file(JPATH_LIBRARIES . '/regularlabs/autoload.php'))
{
	return;
}

require_once JPATH_LIBRARIES . '/regularlabs/autoload.php';

class JFormFieldRL_HR extends JFormField
{
	public $type = 'HR';

	protected function getLabel()
	{
		return '';
	}

	protected function getInput()
	{
		RL_Document::stylesheet('regularlabs/style.min.css');

		return '<div class="rl_panel rl_hr"></div>';
	}
}
