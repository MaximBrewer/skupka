<ul class="nav nav-tabs">
	<li><a class="" href="<?php echo JRoute::_('index.php?option=com_yandex_maps')?>">Главная</a></li>
	<li class="<?php  echo $_GET['task']=='maps'? 'active' : ''?>"><a href="<?php echo JRoute::_('index.php?option=com_yandex_maps&task=maps')?>">Карты</a></li>
	<li class="<?php  echo $_GET['task']=='categories'? 'active' : ''?>"><a href="<?php echo JRoute::_('index.php?option=com_yandex_maps&task=categories')?>">Категории</a></li>
	<li class="<?php  echo $_GET['task']=='objects'? 'active' : ''?>"><a href="<?php echo JRoute::_('index.php?option=com_yandex_maps&task=objects')?>">Объекты</a></li>
</ul>