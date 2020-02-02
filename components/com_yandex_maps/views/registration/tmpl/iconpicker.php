<?php defined("_JEXEC") or die;?>
<?php if($params->get('registration_organization_icon', 1)) { ?>
<fieldset>
	<legend><span>Иконка</span></legend>
	<div>
		<div class="iconpicker">
			<div class="iconpickervariants control-group">
				<input class="validate <?php echo $params->get('registration_organization_icon', 2)==2 ? 'required' : ''?>" name="jform[organization_icon]" id="jform_organization_icon" type="hidden" value=""/>
				<img id="iconpickermirror" src="<?php echo JURI::root();?><?php echo jHtml::_('xdwork.thumb', 'media/com_yandex_maps/images/nophoto.png', 48, 48, 1);?>"><span></span>
				<div class="iconpickersuggestions">
					<?php
						$dir = opendir(JPATH_ROOT.'/media/com_yandex_maps/images/organization_icons/');
						while ($file = readdir($dir)) {
							if ($file!='.' and $file!='..' and !preg_match('#\.(html)$#iu', $file)) { ?>
								<a href="#" data-file="<?php echo $file?>"><img src="<?php echo jURI::root().'media/com_yandex_maps/images/organization_icons/'.$file?>"></a>
							<?php }
						}
						closedir($dir);
					?>
					<?php if($params->get('registration_organization_icon_some_file', 1)) { ?>
					<a href="#">Другое</a>
					<?php }?>
				</div>
				<div class="xdsoft_tooltip ">Выберите изображение иконки</div>
			</div>
			<?php if($params->get('registration_organization_icon_some_file', 1)) { ?>
			<div class="iconpickerfile">
				<div class="control-group">
					<label>Иконка</label>
					<input class="span6 validate <?php echo $params->get('registration_organization_icon', 2)==2 ? 'required' : ''?>" data-rules="file" name="jform[organization_icon_file]" id="jform_organization_icon_file" type="file"/>
					<div class="xdsoft_tooltip">Выберите файл</div>
				</div>
			</div>
			<?php }?>
		</div>
		Выберите изображение для иконки
	</div>
</fieldset>
<script>
(function($){
	function hideIconPicker(){
		$('.iconpickervariants')
			.addClass('xdsoft_i_closed');
		setTimeout(function () {
			$('.iconpickervariants')
				.removeClass('xdsoft_i_closed');
		}, 100);
	}
	$('#jform_organization_icon').on('change', function (){
		if (!this.value) {
			$('#iconpickermirror').attr('src', '<?php echo jURI::root().jHtml::_('xdwork.thumb', 'media/com_yandex_maps/images/nophoto.png', 48, 48, 1);?>');
			return;
		}
		$('#iconpickermirror').attr('src', '<?php echo jURI::root().'media/com_yandex_maps/images/organization_icons/'?>' + this.value);
	});
	$('.iconpicker .iconpickersuggestions a').on('click', function (){
		if ($(this).data('file')) {
			$('#jform_organization_icon').val($(this).data('file')).trigger('change');
			$('.iconpickerfile').hide();
		} else {
			$('#jform_organization_icon').val('').trigger('change');
			$('.iconpickerfile').show();
		}
		hideIconPicker();
		return false;
	});
}(jQuery))
</script>
<?php } ?>