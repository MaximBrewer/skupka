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

use \Arisoft\Template\Template as Template;
use \Joomla\Utilities\ArrayHelper as ArrayHelper;

$rootPath = dirname(__FILE__);
$items = $data['items'];
$hiddenItems = $data['hiddenItems'];
$params = $data['params'];
$textPrefix = $data['textPrefix'];
$paging = $params->paging;
$mainClass = $params->cssClass;
$theme = $params->theme;
$pagingPos = $paging->position;
$currentPage = $paging->currentPage;
$pageSize = $paging->enabled ? $paging->pageSize : 0;
$itemCount = count($items);
$pageCount = $paging->enabled && $pageSize > 0 ? ceil($itemCount / $pageSize) : 1;

if (!$paging->enabled || $currentPage >= $pageCount)
	$currentPage = $paging->currentPage = 0;
?>
<div id="<?php echo $data['id']; ?>" class="ari-media-gallery <?php if ($theme):?> <?php echo $theme; ?><?php endif; ?><?php if ($mainClass):?> <?php echo $mainClass; ?><?php endif; ?>">
<?php
	if ($itemCount > 0): 
?>
	<?php
		if ($paging->enabled && ($pagingPos == 'top' || $pagingPos == 'both')):
	?>
	<div class="ari-media-gallery-paging">
		<?php 
			Template::display(
				$rootPath . '/paging.php',
				array(
					'paging' => $paging,
					'pageCount' => $pageCount,
					'textPrefix' => $textPrefix
				)
			);
		?>
	</div>
	<?php
		endif; 
	?>
	<div class="ari-media-gallery-items">
		<?php
			for ($pageNum = 0; $pageNum < $pageCount; $pageNum++):
				$pageData = $pageSize > 0 ? array_slice($items, $pageNum * $pageSize, $pageSize) : $items;

				Template::display(
                    $rootPath . '/items.php',
					array(
						'items' => $pageData,
						'isCurrentPage' => ($currentPage == $pageNum)
					)
				);
			endfor; 
		?>
	</div>
	<?php
		if ($hiddenItems):
	?>
	<div class="ari-media-gallery-hidden-items">
	<?php
			Template::display(
                $rootPath . '/hidden_items.php',
				array('items' => $hiddenItems)
			);
	?>
	</div>
	<?php
		endif; 
	?>
	<br class="ari-media-gallery-break" />
	<?php
		if ($paging->enabled && ($pagingPos == 'bottom' || $pagingPos == 'both')):
	?>
	<div class="ari-media-gallery-paging">
		<?php 
			Template::display(
                $rootPath . '/paging.php',
				array(
					'paging' => $paging,
					'pageCount' => $pageCount,
					'textPrefix' => $textPrefix
				)
			);
		?>
	</div>
	<?php
		endif; 
	?>
<?php
	else: 
?>
	<div class="ari-media-gallery-nodata"><?php echo JText::_($textPrefix . '_MEDIAGALLERY_LABEL_NODATA'); ?></div>
<?php
	endif; 
?>
</div>