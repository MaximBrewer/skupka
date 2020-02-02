<?php
defined('_JEXEC') or die('Restricted access');
/**
* Param: Virtuemart 2 customfield plugin
* Version: 3.0.6 (2015.11.23)
* Author: Dmitriy Usov
* Copyright: Copyright (C) 2012-2015 usovdm
* License GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
* http://myext.eu
**/

$path = str_replace(DIRECTORY_SEPARATOR .'administrator','',JPATH_BASE);
$path = empty($path)? DIRECTORY_SEPARATOR : $path;
$site_key = substr(base64_encode(gzdeflate(md5('M'.$path.'E'))),0,16);
echo '<br/><a href="http://myext.eu/en/vmcustom-param-vip?hardware3='.$site_key.'#key" title="Get activation key" target="_blank">Get activation key</a>';