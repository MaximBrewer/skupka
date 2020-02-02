<?php
defined('JPATH_BASE') or die;
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');
class JFormFieldClusterIcons extends JFormField{
	protected function getInput(){
		$sizes = explode(',', '0,100,1000,10000');
		if (!is_array($this->value)) {
			$this->value = array();
		}
		ob_start();
		?>
		<input type="hidden" value="<?php echo $this->value?>" name="<?php echo $this->name ?>" id="<?php echo $this->id?>"/>
		<table class="table table-striped table-bordered table-condensed">
			<tr>
				<th>Количество иконок</th>
				<th>Иконка</th>
				<th>Размеры иконки</th>
				<th>Смещение иконки</th>
			</tr>
		<?php 
		foreach ($sizes as $size){ ?>
			<tr>
				<td><label><?php echo $size ? 'после ' : ''?> <input class="input-mini"  min="1" type="<?php echo $size ? 'number' : 'hidden'?>" name="<?php echo $this->name?>[<?php echo $size?>][count]" value="<?php echo @$this->value[$size]['count']?:$size;?>"/></label></td>
				<td>
					<?php 
						$href = JFormHelper::loadFieldType('media', true);
						$href->setForm($this->form);
						$href->setup(simplexml_load_string('<field/>'), null);
						$href->name = $this->name.'['.$size.'][icon]';
						$href->id = $this->id.'_'.$size.'_icon';
						$href->value = @$this->value[$size]['icon'];
						echo $href->getInput(); 
					?>
				</td>
				<td>
					<?php 
						$msize = JFormHelper::loadFieldType('xy', true);
						$msize->setForm($this->form);
						$msize->setup(simplexml_load_string('<field/>'), null);
						$msize->name = $this->name.'['.$size.'][size]';
						$msize->id = $this->id.'_'.$size.'_size';
						$msize->value = @$this->value[$size]['size'];
						echo $msize->getInput();
					?>
				</td>
				<td>
					<?php 
						$offset = JFormHelper::loadFieldType('xy', true);
						$offset->setForm($this->form);
						$offset->setup(simplexml_load_string('<field/>'), null);
						$offset->name = $this->name.'['.$size.'][offset]';
						$offset->value = @$this->value[$size]['offset'];
						echo $offset->getInput(); 
						
						$media2 = JFormHelper::loadFieldType('script', true);
						$media2->setForm($this->form);
						$media2->setup(simplexml_load_string('<field forid="'.$href->id.'" forsize="'.$msize->id.'" foroffset="'.$offset->id.'"/>'), null);
						echo $media2->getInput(); 
					?>
				</td>
			</tr>
		<?php }
		?></table>
		<a target="_blank" href="https://tech.yandex.ru/maps/doc/jsapi/2.1/ref/reference/ClusterPlacemark-docpage/#param-options.icons">Подробнее про иконки кластеров</a>
		<?php
		return ob_get_clean();
	}
	public function attr($attr_name, $default = null){
		if (!empty($this->element[$attr_name])) {
			return $this->element[$attr_name];
		} else {
			return $default;
		}
	}
}
