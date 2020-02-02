<?php
	
	$arrowsSets = HelperNivoPro::getArrowsList();
	
?>

<div class="arrows_grid">
	<ul class="arrows_list">
	<?php
		foreach($arrowsSets as $name=>$set){
			$urlLeft = $set["url_left"];
			$urlRight = $set["url_right"];
			?>
				<li>
					<a href="javascript:void(0)" class="link_arrow" id="<?php echo $name?>" title="<?php echo $name?>">
						<span>
							<img src="<?php echo $urlLeft?>" class="arrow_left"></img>
							<img src="<?php echo $urlRight?>" class="arrow_right"></img>
						</span>
					</a>
				</li>
			<?php
		}
	?>
	</ul>
	<div class="clear"></div>
</div>

<script type="text/javascript">

	jQuery("a.link_arrow").click(function(){
		var link = jQuery(this);
		var data = {};
		data.url_left = link.find(".arrow_left").attr("src"); 
		data.url_right = link.find(".arrow_right").attr("src");
		data.arrowName = this.id;
		
		window.parent.onArrowsSelect(data);
	});
	
</script>