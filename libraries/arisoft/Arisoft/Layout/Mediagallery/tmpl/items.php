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
$isCurrentPage = $data['isCurrentPage'];
?>
<div class="ari-media-gallery-page<?php if (!$isCurrentPage):?> page-hidden<?php endif; ?>">
<?php
	if ($items):
		foreach ($items as $item):
?>
	<div class="ari-media-gallery-item">
		<div class="item-outer">
			<div class="item-shadow">&nbsp;</div>
			<div class="item-inner">
				<div class="item-content">
					<?php echo $item['__content']; ?>
					<div class="item-marker"></div>
				</div>
			</div>
		</div>
		<div class="item-title"><?php echo isset($item['Title']) ? $item['Title'] : ''; ?></div>
	</div>
<?php
		endforeach;
	endif;
?>
</div>