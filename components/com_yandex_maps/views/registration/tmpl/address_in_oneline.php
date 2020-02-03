<?php 
$address = "organization_address$type";

if (@$organization->$address and is_string($organization->$address)) {
	$organization->$address = json_decode($organization->$address);
}

if (is_array($organization->$address)) {
	$organization->$address = (object)$organization->$address;
}
?>
<div>
	<div class="control-group">
		<?php echo htmlspecialchars($organization->$address->full)?>
		<input type="text" style="display:block" class="xdsoft_search_input span12 validate required" data-rules="short_string" id="xdsoft_search<?php echo $type?>_address" placeholder="Начните вводить адрес" value="<?php echo htmlspecialchars($organization->$address->full)?>"/>
		<div class="xdsoft_tooltip">Введено неверно. Возможно слишком короткое название</div>
	</div>
</div>
<?php
jhtml::_('xdwork.autocomplete');
?>
<script>
(function($){
	$(function(){
		if (window.initAutoComplete) {
			window.initAutoComplete();
		}
		$("#xdsoft_search<?php echo $type?>_address")
			.on('selected.xdsoft keydown.xdsoft',function(e, datum){
				if ((e.keyCode===13 || e.type!=='keydown') && datum) {
					$('#jform_organization_address<?php echo $type?>_full').val(datum.GeoObject.metaDataProperty.GeocoderMetaData.text)
					$('#jform_organization_address<?php echo $type?>_zoom').val(18)
					$('#jform_organization_address<?php echo $type?>_lat').val(datum.GeoObject.Point.pos.split(' ')[1])
					$('#jform_organization_address<?php echo $type?>_lan').val(datum.GeoObject.Point.pos.split(' ')[0])
				}
			});
	})
}(window.XDjQuery || window.jQ || window.jQuery));
</script>
<input type="hidden" id="jform_organization_address<?php echo $type?>_full" name="jform[organization_address<?php echo $type?>][full]"  value="<?php echo htmlspecialchars($organization->$address->full)?>">
<input type="hidden" id="jform_organization_address<?php echo $type?>_zoom" name="jform[organization_address<?php echo $type?>][zoom]" value="12">
<input type="hidden" id="jform_organization_address<?php echo $type?>_lat" name="jform[organization_address<?php echo $type?>][lat]" value="54">
<input type="hidden" id="jform_organization_address<?php echo $type?>_lan" name="jform[organization_address<?php echo $type?>][lan]" value="34">