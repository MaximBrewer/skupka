<?php
defined('JPATH_BASE') or die;
jimport('joomla.form.helper');
class JFormFieldIconPicker extends JFormField{
	public function attr($attr_name, $default = null){
		if (isset($this->element[$attr_name])) {
			return $this->element[$attr_name];
		} else {
			return $default;
		}
	}
	function getInput() {
		ob_start();
		JHtml::addIncludePath(JPATH_ROOT.'/components/com_yandex_maps/helpers');
		?>
<div>
	<div>
		<div id="<?php echo $this->id?>-box" class="iconpicker">
			<label><span>Иконка</span></label>
			<div class="iconpickervariants control-group">
				<input name="<?php echo $this->name?>" id="<?php echo $this->id?>" type="hidden" value="<?php echo $this->value?>"/>
				<img id="<?php echo $this->id?>iconpickermirror" src="<?php echo JURI::root();?><?php echo jHtml::_('xdwork.thumb', 'media/com_yandex_maps/images/nophoto.png', 48, 48, 1);?>"><span></span>
				<div class="iconpickersuggestions">
					<?php
						$dir = opendir(JPATH_ROOT.'/media/com_yandex_maps/images/organization_icons/');
						while ($file = readdir($dir)) {
							if ($file!='.' and $file!='..' and !preg_match('#\.(html)$#iu', $file)) { ?>
								<a href="#" data-file="media/com_yandex_maps/images/organization_icons/<?php echo $file?>"><img src="<?php echo jURI::root().'media/com_yandex_maps/images/organization_icons/'.$file?>"></a>
							<?php }
						}
						closedir($dir);
					?>
					<a href="#">Другое</a>
				</div>
			</div>
			<div class="iconpickerfile">
				<div class="control-group">
					<label>Иконка</label>
					<?php
						$media = JFormHelper::loadFieldType('media', true);
						$media->setForm($this->form); // $this->form можно заменить на new JForm('name')
						$media->setup(simplexml_load_string('<field/>'), null);
						$media->id = $this->id.'media-picker-straight';
						echo $media->getInput();
					?>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
(function($){
	var id = '#<?php echo $this->id?>',
		root = '<?php echo jURI::root()?>';
	function setMirror(val) {
		if (!val) {
			val = '<?php echo jHtml::_('xdwork.thumb', 'media/com_yandex_maps/images/nophoto.png', 48, 48, 1);?>';
		}
		$(id + 'iconpickermirror').attr('src', root + val);
		$(id).val(val);
	} 

	$(id)
		.on('change', function (){
			setMirror(this.value);
		})
		.trigger('change');
	$(id + '-box  .iconpickersuggestions a').on('click', function (){
		if ($(this).data('file')) {
			setMirror($(this).data('file'));
			$(id + '-box  .iconpickerfile').hide();
		} else {
			setMirror('');
			$(id + '-box  .iconpickerfile').show();
		}
		$(id + '-box .iconpickervariants')
			.addClass('xdsoft_i_closed');
		setTimeout(function () {
			$(id + '-box .iconpickervariants')
				.removeClass('xdsoft_i_closed');
		}, 100);
		return false;
	});
	$(id + 'media-picker-straight').on('change', function (){
		setMirror(this.value);
	});
}(jQuery))
</script>
<style>
.iconpicker,.iconpicker *{
	-moz-box-sizing:border-box;
	box-sizing:border-box;
}
.iconpicker label{
font-weight:600;
display:block !important;
}
.iconpicker{
	display:inline-block;
	margin-right:10px;
	margin-top:10px;
}
.iconpicker .iconpickerfile{
	display:none;
}
.iconpicker .iconpickersuggestions{
	display:none;
	position:absolute;
	z-index:20;
	background:#fff;
	border:1px solid #ccc;
	padding:10px;
	width:auto;
	min-width:400px;
    bottom: -1px;
    left: 57px;
}
.iconpicker .iconpickersuggestions a{
	cursor:pointer;
	border:1px solid transparent;
	display:inline-block;
	padding:5px;
}
.iconpicker .iconpickersuggestions a:hover{
border:1px solid #ccc;
}
.iconpicker .iconpickersuggestions img{
	max-width:<?php echo (int)$this->attr('icon-width-preview', 24);?>px;
}
.iconpicker .iconpickervariants{
	position:relative;
	display:inline-block;
	width:50px;
	height:50px;
	border:1px solid #ccc;
	padding:5px;
}
.iconpicker .iconpickervariants > span{
	display:block;
	width:15px;
	position:absolute;
	right:-10px;
	top:-1px;
	bottom:-1px;
	border:1px solid #ccc;
	border-width:1px 1px 1px 0px;
}
.iconpicker .iconpickervariants > span:after{
	display:inline-block;
	content:"";
	width: 0;
	height: 0;
	border-style: solid;
	border-width: 4px 0 4px 8px;
	border-color: transparent transparent transparent #999999;
	top:50%;
	left:7px;
	margin-top:-4px;
	position:absolute;
}
.iconpicker .iconpickervariants:not(.xdsoft_i_closed):hover .iconpickersuggestions{
	display:block;
}
.iconpicker .field-media-wrapper .input-append>*{
height:28px;
}
</style>
		<?php
		return ob_get_clean();
	}
}