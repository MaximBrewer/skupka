<?php
/* license: commercial ! */
defined('_JEXEC') or 	die( 'Direct Access to ' . basename( __FILE__ ) . ' is not allowed.' ) ;

?>
<div class="container">
<ul class="nav <?php echo $this->params->get('defaultclass', 'nav-tabs'); ?>">
<?php  $first = true; 
	foreach ($data as $k=>$tab) { ?>
  <li role="presentation" <?php if (!empty($tab['active'])) echo ' class="active" '; ?>><a href="#tab<?php echo $tab['id']; ?>" data-toggle="tab"><?php echo $tab['tabname']; ?></a></li>
	<?php } ?>
</ul>

<div class="tab-content ">
			<?php foreach ($data as $k=>$tab) { ?>
			  <div class="tab-pane <?php if (!empty($tab['active'])) echo 'active'; ?>" id="tab<?php echo $tab['id']; ?>">
					<?php if (!empty($tab['tabdesc'])) { ?><h3><?php echo $tab['tabdesc']; ?></h3><?php } ?>
					<?php echo $tab['tabcontent'];  ?>
				</div>
			<?php }	?>

</div>

</div>