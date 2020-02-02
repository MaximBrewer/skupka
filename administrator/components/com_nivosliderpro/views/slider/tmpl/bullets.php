<?php
	
	$bulletsSets = HelperNivoPro::getBulletsList();
	
?>

<div class="bullets_grid">
	<ul class="bullets_list">
	<?php
		foreach($bulletsSets as $name=>$set){
			$urlPreview = $set["url_preview"];
			
			?>
				<li>
					<a href="javascript:void(0)" class="link_bullet" id="<?php echo $name?>" title="<?php echo $name?>">
						<span>
							<img src="<?php echo $urlPreview?>" class="bullet_default"></img>
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

	jQuery("a.link_bullet").click(function(){
		var link = jQuery(this);
		var setName = this.id;
		
		window.parent.onBulletsSelect(setName);
	});
	
</script>