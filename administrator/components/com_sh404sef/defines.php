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
if (!defined('_JEXEC'))
	die('Direct Access to this location is not allowed.');

if (!defined('sh404SEF_ADMIN_ABS_PATH'))
{
	define('sh404SEF_ADMIN_ABS_PATH', str_replace('\\', '/', dirname(__FILE__)) . '/');
}
if (!defined('sh404SEF_ABS_PATH'))
{
	define('sh404SEF_ABS_PATH', str_replace('/administrator/components/com_sh404sef', '', sh404SEF_ADMIN_ABS_PATH));
}
if (!defined('sh404SEF_FRONT_ABS_PATH'))
{
	define('sh404SEF_FRONT_ABS_PATH', sh404SEF_ABS_PATH . 'components/com_sh404sef/');
}

if (!defined('sh404SEF_FRONT_LAYOUTS'))
{
	define('sh404SEF_FRONT_LAYOUTS', sh404SEF_ABS_PATH . 'components/com_sh404sef/layouts/');
}

if (!defined('sh404SEF_LAYOUTS'))
{
	define('sh404SEF_LAYOUTS', sh404SEF_ADMIN_ABS_PATH . 'layouts/');
}

defined('SH404SEF_IS_INSTALLED') or define('SH404SEF_IS_INSTALLED', 1);

defined('sh404SEF_URLTYPE_404') or define('sh404SEF_URLTYPE_404', -2);
defined('sh404SEF_URLTYPE_NONE') or define('sh404SEF_URLTYPE_NONE', -1);
defined('sh404SEF_URLTYPE_AUTO') or define('sh404SEF_URLTYPE_AUTO', 0);
defined('sh404SEF_URLTYPE_CUSTOM') or define('sh404SEF_URLTYPE_CUSTOM', 1);
defined('sh404SEF_MAX_SEF_URL_LENGTH') or define('sh404SEF_MAX_SEF_URL_LENGTH', 200);

defined('sh404SEF_HOMEPAGE_CODE') or define('sh404SEF_HOMEPAGE_CODE', 'index.php?' . md5('sh404SEF Homepage url code'));

defined('SH404SEF_STANDARD_ADMIN') or define('SH404SEF_STANDARD_ADMIN', 1); // define possible levels for adminstration complexity
defined('SH404SEF_ADVANCED_ADMIN') or define('SH404SEF_ADVANCED_ADMIN', 2);

defined('SH404SEF_ANALYTICS_TIME_CUSTOM_VAR') or define('SH404SEF_ANALYTICS_TIME_CUSTOM_VAR', 1);
defined('SH404SEF_ANALYTICS_USER_CUSTOM_VAR') or define('SH404SEF_ANALYTICS_USER_CUSTOM_VAR', 2);

defined('SH404SEF_OPTION_VALUE_NO') or define('SH404SEF_OPTION_VALUE_NO', 0);
defined('SH404SEF_OPTION_VALUE_YES') or define('SH404SEF_OPTION_VALUE_YES', 1);
defined('SH404SEF_OPTION_VALUE_USE_DEFAULT') or define('SH404SEF_OPTION_VALUE_USE_DEFAULT', 2);
