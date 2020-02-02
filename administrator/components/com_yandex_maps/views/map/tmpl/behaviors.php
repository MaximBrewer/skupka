<?php
	JHtml::_('btngroup._', 'drag','Перемещение карты при нажатой <br>левой  кнопке мыши либо одиночным касанием',in_array('drag', $this->item->behaviors),'behaviors','behavior');
	JHtml::_('btngroup._', 'scrollZoom','Изменение масштаба колесом мыши',in_array('scrollZoom', $this->item->behaviors),'behaviors','behavior');
	JHtml::_('btngroup._', 'dblClickZoom','Масштабирование карты  двойным щелчком кнопки мыши',in_array('dblClickZoom', $this->item->behaviors),'behaviors','behavior');
	JHtml::_('btngroup._', 'multiTouch','Масштабирование карты двойным касанием <br>(например, пальцами на  сенсорном экране)',in_array('multiTouch', $this->item->behaviors),'behaviors','behavior');
	JHtml::_('btngroup._', 'rightMouseButtonMagnifier','Увеличение области,  выделенной правой <br>кнопкой мыши (только для настольных браузеров),',in_array('rightMouseButtonMagnifier', $this->item->behaviors),'behaviors','behavior');
	JHtml::_('btngroup._', 'leftMouseButtonMagnifier','Увеличение области, выделенной левой <br>кнопкой мыши либо одиночным касанием',in_array('rightMouseButtonMagnifier', $this->item->behaviors),'behaviors','behavior');
	JHtml::_('btngroup._', 'ruler','Измерение расстояния',in_array('ruler', $this->item->behaviors),'behaviors','behavior');
	JHtml::_('btngroup._', 'routeEditor','Редактор маршрутов',in_array('routeEditor', $this->item->behaviors),'behaviors','behavior');
?>
<legend>Наборы</legend>
<?php
	JHtml::_('btngroup._', 'default','Набор поведений  карты по умолчанию',$default = in_array('default', $this->item->behaviors),'behaviors','behaviors_default');
?>
<script>
	jQuery('.control-group.behavior input').on('change', function() {
		jQuery('.control-group.behaviors_default input').val(0).trigger('update');
		jQuery(this).trigger('updateValue');
	});
	jQuery('.control-group.behaviors_default input').on('change', function() {
		var val = parseInt(this.value);
		jQuery('.control-group.behaviors_default input,.control-group.behavior input').val(0).trigger('update');
		jQuery(this).val(val).trigger('update').trigger('updateValue');
	});
</script>
<a target="_blank" href="https://tech.yandex.ru/maps/doc/jsapi/2.1/ref/reference/map.behavior.Manager-docpage/" class="btn btn-link">Подробнее про поведения</a>
