<?php 
$address = "organization_address$type";
if (@$organization->$address and is_string($organization->$address)) {
	$organization->$address = json_decode($organization->$address);
}
if (is_array($organization->$address)) {
	$organization->$address = (object)$organization->$address;
}
?>
<div class="row-fluid" style="position:relative">
	<div class="span6">
		<?php if ($params->get('registration_organization_address_zip', 2)) {?>
		<div class="control-group">
			<label>Индекс</label>
			<input tabindex="100" autocomplete="off" class="span12 address validate <?php echo $params->get('registration_organization_address_zip', 2)==2 ? 'required' : ''?>" data-rules="number" data-type="zip" name="jform[organization_address<?php echo $type?>][zip]" id="jform_organization_address<?php echo $type?>_zip" type="text" placeholder="Например: 450000"  value="<?php echo htmlspecialchars($organization->$address->zip)?>"/>
			<div class="xdsoft_tooltip">Введено неверно. Допустимо только числовое значение</div>
		</div>
		<?php } ?>
		<?php if ($params->get('registration_organization_address_street', 2)) {?>
		<div class="control-group">
			<label>Улица</label>
			<input tabindex="102" autocomplete="off" class="span12 address validate <?php echo $params->get('registration_organization_address_street', 2)==2 ? 'required' : ''?>" data-rules="short_name" data-type="street"  data-parent="city" name="jform[organization_address<?php echo $type?>][street]" id="jform_organization_address<?php echo $type?>_street" type="text" placeholder="Например: Ленина"  value="<?php echo htmlspecialchars($organization->$address->street)?>"/>
			<div class="xdsoft_tooltip">Введено неверно. Возможно слишком короткое название</div>
		</div>
		<?php } ?>						
	</div>
	<div class="span6">
		<?php if ($params->get('registration_organization_address_city', 2)) {?>
		<div class="control-group">
			<label>Населенный пункт</label>
			<input tabindex="101" autocomplete="off" class="span12 address validate <?php echo $params->get('registration_organization_address_city', 2)==2 ? 'required' : ''?>" data-rules="short_name" data-type="city" data-parent="zip" name="jform[organization_address<?php echo $type?>][city]" id="jform_organization_address<?php echo $type?>_city" type="text" placeholder="Например: Москва"   value="<?php echo htmlspecialchars($organization->$address->city)?>"/>
			<div class="xdsoft_tooltip">Введено неверно. Возможно слишком короткое название</div>
		</div>
		<?php } ?>
		<?php if ($params->get('registration_organization_address_building', 2)) {?>
		<div class="control-group">
			<label>дом/корпус/строение</label>
			<input tabindex="103" autocomplete="off" class="span12 address validate <?php echo $params->get('registration_organization_address_building', 2)==2 ? 'required' : ''?>" data-rules="short_name" data-type="building"  data-parent="street" name="jform[organization_address<?php echo $type?>][building]" id="jform_organization_address<?php echo $type?>_building" type="text" placeholder="Например: 12/5"  value="<?php echo htmlspecialchars($organization->$address->building)?>"/>
			<div class="xdsoft_tooltip">Введено неверно. Возможно слишком короткое название</div>
		</div>
		<?php } ?>
	</div>
	<input type="hidden" id="jform_organization_address<?php echo $type?>_full" name="jform[organization_address<?php echo $type?>][full]"  value="<?php echo htmlspecialchars($organization->$address->full)?>">
	<input type="hidden" id="jform_organization_address<?php echo $type?>_zoom" name="jform[organization_address<?php echo $type?>][zoom]" value="12">
	<input type="hidden" id="jform_organization_address<?php echo $type?>_lat" name="jform[organization_address<?php echo $type?>][lat]" value="54">
	<input type="hidden" id="jform_organization_address<?php echo $type?>_lan" name="jform[organization_address<?php echo $type?>][lan]" value="34">
</div>