<?php 

defined('_JEXEC') or die('Restricted access'); ?>

<?php 

	$numSliders = count($this->arrSliders);

	if($numSliders == 0){	//error output
		?>
			<h2>Please add some slider before operating slides</h2>
		<?php 
	}else
		echo $this->loadTemplate("slide");
	
	HelperNivoPro::includeView("sliders/tmpl/footer.php");	 
		
?>



