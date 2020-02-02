<?php
defined ('_JEXEC') or     die();
// Here the plugin values

?>

<?php
$assetsPath = VMSOCOLISSIMOPLUGINWEBROOT . '/alatak_socolissimo/assets/css/socolissimo.css';
$document = JFactory::getDocument ();
$document->addStyleSheet ($assetsPath);


?>
<?php echo $viewData['shipment_name'] .$viewData['cost_display'] ?>
<?php
// SOCOLISSIMO NOT AVAILABLE
if (!($viewData['socolissimo_available'])) {
	echo JText::_ ('VMSHIPMENT_ALATAK_SOCOLISSIMO_NOT_AVAILABLE');

} else {
	// SOCOLISSIMO IS AVAILABLE

		
	?>
<?php
	if (empty($viewData['socolissimoResponseHtml'])) {
		if (!empty($viewData['shipment_description'])) {
			?>
        <span class="vmshipment_description"><?php echo $viewData['shipment_description'] ?></span>
        <div class="socolissimo_selected"><?php echo $viewData['socolissimo_selected'] ?></div>
		<?php
		}
	} else {
		?>
    <div id="socolissimo_deliveryinfo">
	    <div>
	    <a href='#'  class='<?php echo $viewData['cssId']; ?>><?php echo JText::_ ('VMSHIPMENT_ALATAK_SOCOLISSIMO_CHANGE') ?></a>
</div>
	 <span class="vmshipment_cartname">
	<?php echo $viewData['socolissimoResponseHtml']; ?>
	</span>
    </div>
	<?php
	}
	?>

<div id="plugin_socolissimo">

	<?php 
	echo str_replace('name=', 'rel="socolissimo"  name=', $viewData['socolissimo_form']); ?>


    <null scrolling="yes" src="<?php echo $method->socolissimo_url; ?>" style="display:none;width:650px;height:1040px;border:0;" name="socolissimo_Target2" id="socolissimo_Target2"></null>
</div>
<?php
}
?>






