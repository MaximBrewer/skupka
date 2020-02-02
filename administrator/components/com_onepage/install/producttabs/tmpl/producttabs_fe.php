<?php
/* license: commercial ! */
defined('_JEXEC') or 	die( 'Direct Access to ' . basename( __FILE__ ) . ' is not allowed.' ) ;
?>
<div class="vmMainPageOPCTabs">
     
	<ul id="vmtabs" class="shadetabs<?php echo $this->params->get('defaultclass', ''); ?>">
	<?php 
	$first = true; 
	foreach ($data as $k=>$tab) { ?>
	<li class="litab <?php 
	if (!empty($tab['active'])) echo ' selected '; ?>" ><a class="litab <?php if (!empty($tab['active'])) echo ' selected '; ?>" href="#" rel="tab<?php echo $tab['id']; ?>" id="atab<?php echo $tab['id']; ?>" onclick="javascript: return tabClickDefault(this);"><?php echo $tab['tabname']; ?></a></li>
    
   
	<?php } ?> 
	</ul>
	<div class="vmTabContentInner" id="tabscontent">
	
	
	  	<?php foreach ($data as $k=>$tab) { ?>
		<div id="tab<?php echo $tab['id']; ?>" class="tabcontent3" <?php if (empty($tab['active'])) echo ' style="display: none;" '; ?> >
	
	<div class="vmTabContent" >
	<div class="vmTabSub">
	<div class="vmTabSubInner">
	<strong><?php echo $tab['tabdesc']; ?></strong>
	</div>	
	</div>	
	 </div>
	 <div>
	<?php echo $tab['tabcontent']; ?>
    </div>
     </div>
	<?php } ?> 
	</div>
	
	
	
</div>