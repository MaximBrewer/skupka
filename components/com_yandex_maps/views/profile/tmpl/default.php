<?php defined("_JEXEC") or die;?>
<h1>Здравствуйте, <?php echo JFactory::getUser()->username?></h1>
<div class="accordion" id="accordion2">
	<div class="accordion-group">
		<div class="accordion-heading">
			<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#myobjects">
				Ваши объекты(<?php echo $this->myobjectstotal?>)
			</a>
		</div>
		<div id="myobjects" class="accordion-body collapse <?php echo isset($_REQUEST['start']) ? 'in' : ''?>">
			<div class="accordion-inner">
				<div class="accordion" id="accordion3">
					<?php echo $this->pagination->getListFooter(); ?>
					<div class="accordion-group">
						<?php 
							foreach ($this->myobjects as $object) {
								include "object.php";
							}
						?>
					</div>
					<?php echo $this->pagination->getListFooter(); ?>
				</div>
				
			</div>
		</div>
	</div>
</div>