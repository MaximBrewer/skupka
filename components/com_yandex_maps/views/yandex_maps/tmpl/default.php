<?php
defined("_JEXEC") or die("Access deny");
?>
<div class="xdsoft_yandex_maps xdsoft_items_list">
	<?php
		if (count($this->items)) {
			foreach ($this->items as $item) {
				?>
				<div class="xdsoft_item_intro">
					<h2><a href="<?=JRoute::_('index.php?option=com_yandex_maps&task=map&id='.$item->slug.'')?>"><?=$item->title?></a></h2>
					<div class="map_description">
						<?=$item->intro?>
					</div>
					<div class="xdsoft_more_link">
						<a class="btn btn-link" href="<?=JRoute::_('index.php?option=com_yandex_maps&task=map&id='.$item->slug.'')?>">Подробнее</a>
					</div>
				</div>
				<?
			}
		}
	?>
</div>