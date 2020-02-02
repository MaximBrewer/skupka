<?php
	JHtml::_('btngroup._', 'fullscreenControl','Кнопка разворачивания карты на весь экран',in_array('fullscreenControl',$this->item->controls),'controls','control');
	JHtml::_('btngroup._', 'geolocationControl','Кнопка определения местоположения пользователя',in_array('geolocationControl',$this->item->controls),'controls','control');
	JHtml::_('btngroup._', 'routeEditor','Кнопка включения  и отключения поведения <br>&laquo;редактор маршрута&raquo;',in_array('routeEditor',$this->item->controls),'controls','control');
	JHtml::_('btngroup._', 'rulerControl','Кнопка включения  и отключения поведения &laquo;линейка&raquo;',in_array('rulerControl',$this->item->controls),'controls','control');
	JHtml::_('btngroup._', 'searchControl','Панель поиска',in_array('searchControl',$this->item->controls),'controls','control');
	JHtml::_('btngroup._', 'trafficControl','Панель пробок',in_array('trafficControl',$this->item->controls),'controls','control');
	JHtml::_('btngroup._', 'typeSelector','Панель переключения типа карты',in_array('typeSelector',$this->item->controls),'controls','control');
	JHtml::_('btngroup._', 'zoomControl','Ползунок масштаба',in_array('zoomControl',$this->item->controls),'controls','control');
	JHtml::_('btngroup._', 'categorySelector','Выбор категории',in_array('categorySelector',$this->item->controls),'controls','control');
	JHtml::_('btngroup._', 'addUserPoint','Добавление новых точек',in_array('addUserPoint',$this->item->controls),'controls','control');
?>
<script>
	jQuery('.control-group.control input').on('change', function() {
		jQuery('.control-group.default input').val(0).trigger('update');
		jQuery(this).trigger('updateValue');
	});
</script>
<legend>Наборы</legend>
<?php
	JHtml::_('btngroup._', 'largeMapDefaultSet','Набор элементов управления для карты большого размера',in_array('largeMapDefaultSet',$this->item->controls),'controls', 'default');
	JHtml::_('btngroup._', 'default','Набор элементов  управления для карты среднего размера',$default = in_array('default',$this->item->controls),'controls', 'default');
	JHtml::_('btngroup._', 'smallMapDefaultSet','Набор элементов управления для карты маленького размера',$default = in_array('smallMapDefaultSet',$this->item->controls),'controls', 'default');
?>
<script>
	jQuery('.control-group.default input').on('change', function() {
		var val = parseInt(this.value);
		jQuery('.control-group.default input,.control-group.control input').val(0).trigger('update');
		jQuery(this).val(val).trigger('update').trigger('updateValue');
	});
</script>
<!--https://tech.yandex.ru/maps/doc/jsapi/2.1/ref/reference/control.RouteEditor-docpage/-->
<a target="_blank" href="https://tech.yandex.ru/maps/doc/jsapi/2.1/ref/reference/control.Manager-docpage/" class="btn btn-link">Подробнее о элементах управления</a>
