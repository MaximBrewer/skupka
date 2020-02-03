<?php
/**
 * @package         Modules Anywhere
 * @version         7.9.0
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            http://www.regularlabs.com
 * @copyright       Copyright © 2020 Regular Labs All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

namespace RegularLabs\Plugin\System\ModulesAnywhere;

defined('_JEXEC') or die;

use RegularLabs\Library\Protect as RL_Protect;

class Clean
{
	/**
	 * Just in case you can't figure the method name out: this cleans the left-over junk
	 */
	public static function cleanLeftoverJunk(&$string)
	{
		RL_Protect::removeAreaTags($string, 'MODA');

		$params = Params::get();

		Protect::unprotectTags($string);

		RL_Protect::removeFromHtmlTagContent($string, Params::getTags(true));
		RL_Protect::removeInlineComments($string, 'Modules Anywhere');

		if ( ! $params->place_comments)
		{
			RL_Protect::removeCommentTags($string, 'Modules Anywhere');
		}
	}
}
