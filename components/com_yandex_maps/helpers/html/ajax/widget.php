<?php defined('_JEXEC') or die; ob_start();?>
<?php
	$app = jFactory::getApplication();
	$point = $app->input->get('center', array($map->lat, $map->lan), 'ARRAY');
	$bound = $app->input->get('bound', array(), 'ARRAY');
	$limit = $app->input->get('limit', 10, 'INTEGER');
	$offset = $app->input->get('offset', 0, 'INTEGER');
	$data = $map->getObjectsByBound($bound, $offset, $limit, array(), false, 0, false, array(), $point);
	?>
	<?php if (!$offset and $map->settings->get('show_wdm_result_counts', 1))  { ?>
	<div class="xdm_result_count">Найдено <?php echo $data['count']?> результата</div>
	<?php } ?>
	<?php foreach($data['objects'] as $object) { ?>
	<div class="xdm_wg_item_view">
		<a onclick="return map<?php echo $map->id?>.goTo('<?php echo $object->id?>')" href="<?php echo $object->link?>">
			<?php
			$meta = new stdClass();
			if (isset($object->metadata)) {
				$meta = is_string($object->metadata) ? (json_decode($object->metadata)?:new stdClass()) : $object->metadata;
			}
			if ($map->settings->get('show_wdm_image', 1) and isset($meta->image) and trim($meta->image)!='') { ?>
			<img src="<?php echo jhtml::_('xdwork.thumb', $meta->image, 332);?>" alt="">
			<?php } ?>
			<?php if ($map->settings->get('show_wdm_object_title', 1))  { ?>
			<div class="xdm_wg_object_name">
				<?php echo $object->title?>
			</div>
			<?php } ?>
			<?php if ($map->settings->get('show_wdm_object_descrition', 1))  { ?>
			<div class="xdm_wg_description">
				<?php switch($map->settings->get('show_wdm_object_descrition', 1)) {
					case 3:
						if (!empty($meta->metadesc)) {
							echo $meta->metadesc;
						} else {
							echo $object->description;
						}
					break;
					case 2:
						if (!empty($meta->metadesc)) {
							echo $meta->metadesc;
						}
					break;
					default:
						echo $object->description;
				} ?>
			</div>
			<?php } ?>
			<?php if ($map->settings->get('show_wdm_read_more', 1))  { ?>
			<a onclick="return map<?php echo $map->id?>.goTo('<?php echo $object->id?>')" href="<?php echo $object->link?>" class="xdm_wg_more">подробнее</a>
			<?php } ?>
		</a>
	</div>
	<?php } ?>
<?php 
return ob_get_clean();