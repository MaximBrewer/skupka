<?php
defined("_JEXEC") or die;
JHTML::_('behavior.modal');
?>
<div class="iconpicker">
	<div class="iconpickervariants control-group">
		<input id="iconpickericon" type="hidden" value=""/>
		<img id="iconpickermirror" data-default="<?php echo jURI::root().jHtml::_('xdwork.thumb', 'media/com_yandex_maps/images/nophoto.png', 48, 48, 1);?>"  src="<?php echo jURI::root()?><?php echo jHtml::_('xdwork.thumb', 'media/com_yandex_maps/images/nophoto.png', 48, 48, 1);?>"><span></span>
		<div class="iconpickersuggestions">
			<a id="point" href="#"><img src="<?php echo jURI::root()?>media/com_yandex_maps/images/placemark/islands-darkBlueDotIcon.png"></a>
			<a id="stretchy" href="#"><img src="<?php echo jURI::root()?>media/com_yandex_maps/images/placemark/islands-blueStretchyIcon.png"></a>
			<?php
				$dir = opendir(JPATH_ROOT.'/media/com_yandex_maps/images/organization_icons/');
				while ($file = readdir($dir)) {
					if ($file!='.' and $file!='..' and !preg_match('#\.(html)$#iu', $file)) { ?>
						<a href="#"><img src="<?php echo jURI::root().'media/com_yandex_maps/images/organization_icons/'.$file?>"></a>
					<?php }
				}
				closedir($dir);
			?>
			<a style="font-size:12px;" href="#">Другое</a>
		</div>
	</div>
	<div class="iconpickerfile">
		<div class="control-group">
			<label>Иконка</label>
			<?php 
//				$media = JFormHelper::loadFieldType('media', true);
//				$media->setForm($this->form);
//				$media->setup(simplexml_load_string('<field id="custom_icon_picker"/>'), null);
				echo JHTML::_('media._','iconImageHref','iconImageHref','images','','options', '');

            ?>
		</div>
	</div>
</div>