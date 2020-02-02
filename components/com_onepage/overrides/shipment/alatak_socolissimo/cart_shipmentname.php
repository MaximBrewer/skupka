<?php 
defined('_JEXEC') or 	die(); 
// Here the plugin values
	$assetsPath = VMSOCOLISSIMOPLUGINWEBROOT . '/alatak_socolissimo/assets/css/socolissimo.css';
		$document = JFactory::getDocument ();
		$document->addStyleSheet ($assetsPath);

if (!empty($this->logos)) echo $this->displayLogos($this->logos) . ' ';
?>
<span class="shipment_name"><?php echo $viewData[ 'shipment_name'] ?></span>
<?php if (!empty($viewData[ 'shipment_desc'])) { ?>
	 <span class="shipment_description"><?php echo $viewData[ 'shipment_desc'] ?></span>
<?php } ?>
 <br /><span class="vmshipment_cartname">
<?php echo $viewData[ 'socolissimoResponseHtml'] ?>
	</span>

