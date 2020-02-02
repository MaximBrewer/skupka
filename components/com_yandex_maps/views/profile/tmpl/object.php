<div class="accordion-heading">
	<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion3" href="#myobjects<?php echo $object->id?>">
		<?php echo $object->title?>
	</a>
</div>
<div id="myobjects<?php echo $object->id?>" class="accordion-body collapse">
	<div class="accordion-inner">
		<?php 
		jhtml::_('xdwork.organization', $object);
		if (isset($object->organization_compile)) {
			echo $object->organization_compile;
		}
		if (isset($object->description)) {
			echo $object->description;
		}
		$slug = $object->alias!='' ? $object->id.':'.$object->alias : $object->id;
		?>
		<a class="btn btn-link" href="<?php echo jRoute::_('index.php?task=object&id='.$slug)?>">Подробнее</a>
	</div>
</div>