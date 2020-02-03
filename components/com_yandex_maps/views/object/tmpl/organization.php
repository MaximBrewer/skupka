<?php
defined("_JEXEC") or die("Access deny");

if (!isset($organization)) {
	$organization = new stdClass();
}
?>
<div class="xdsoft_organization">
	<?php 
	$sizes = array_map('intval', explode(',', $params->get('registration_organization_image_size', '128,128')));
	?>
	<?php if ($organization->organization_image) {?>
	<div class="image_organization" style="width:<?php echo $sizes[0]?>px">
		<img src="<?=jURI::root()?><?=jhtml::_('xdwork.thumb', jhtml::_('xdwork.imageurl').$organization->organization_image, $sizes[0], $sizes[1], 1)?>" alt="<?=htmlspecialchars($organization->organization_name)?>">
	</div>
	<?php } ?>
	<div class="description_organization" style="width:calc(100% - <?php echo ($sizes[0]+20)?>px)">
		<table class="xdsoft_organization table">
			<?php if ($organization->organization_name) { ?>
				<tr>
					<th>Название</th>
					<td><?=htmlspecialchars($organization->organization_name)?></td>
				</tr>
			<?php } ?>

			<?php if ($organization->organization_phone) { ?>
				<tr>
					<th>Контактный телефон</th>
					<td><?=htmlspecialchars($organization->organization_phone)?></td>
				</tr>
			<?php } ?>

			<?php
			if ($organization->organization_address) { 
				if (is_string($organization->organization_address)) {
					$organization->organization_address = json_decode($organization->organization_address);
					$organization->organization_address_legal = json_decode($organization->organization_address_legal);
				}
					if ($organization->organization_address->full) {
				?>
					<tr>
						<th>Адрес</th>
						<td><?=htmlspecialchars($organization->organization_address->full)?>
						<?=($organization->organization_address_legal->full and $organization->organization_address->full!=$organization->organization_address_legal->full)? '('.htmlspecialchars($organization->organization_address_legal->full).')' : ''?></td>
					</tr>
				<?php } ?>
			<?php } ?>

			<?php if ($organization->organization_self_schedule_text) { ?>
				<tr>
					<th>Режим работы</th>
					<td><?=nl2br(htmlspecialchars($organization->organization_self_schedule_text));?></td>
				</tr>
			<?php } else { ?>
				<?php if ($organization->organization_shedule_24 or  $organization->organization_start_in or $organization->organization_end_in) { ?>
					<tr>
						<th>Режим работы</th>
						<td><?php
							if ($organization->organization_shedule_24) { ?>
								<span>Круглосуточно</span>
							<? } else { ?>
								с <?=htmlspecialchars($organization->organization_start_in) ?> по с <?php echo htmlspecialchars($organization->organization_end_in) ?>
							<?php } ?>
							<?php if ($organization->organization_shedule_days) { ?>
								<strong><?=htmlspecialchars($organization->organization_shedule_days) ?><strong>
							<? }
						?></td>
					</tr>
				<?php } ?>
			<?php } ?>
			<?php if ($organization->organization_info) { ?>
				<tr class="xdsoft_organization_info">
					<td colspan="2"><?= htmlspecialchars($organization->organization_info) ?></td>
				</tr>
			<?php } ?>
		</table>
	</div>
</div>
