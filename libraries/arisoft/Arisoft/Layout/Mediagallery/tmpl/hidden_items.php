<?php
/*
 * ARI Framework
 *
 * @package		ARI Framework
 * @version		1.0.0
 * @author		ARI Soft
 * @copyright	Copyright (c) 2009 www.ari-soft.com. All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
 *
 */

defined('_JEXEC') or die;

$items = $data['items'];
?>
<?php
	if ($items):
		foreach ($items as $item):
			echo $item['__content'];
		endforeach;
	endif;
?>