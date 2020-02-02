<?php

?>

	<div id="visual_wrapper" class="visual_wrapper">
		<table class="table_visual">
			<tr>
				<td valign="top" class="cell_left">
					<ul id="tabs_visual">
						<li>
							<a data-tab="#tab-generalvis" href="javascript:void(0)" id="generalvis" class="selected">General</a>
						</li>
						<li>
							<a data-tab="#tab-border" href="javascript:void(0)" id="border">Border</a>
						</li>
						<li>
							<a data-tab="#tab-shadow" href="javascript:void(0)" id="shadow">Shadow</a>
						</li>
						<li>
							<a data-tab="#tab-arrows" href="javascript:void(0)" id="arrows">Arrows</a>
						</li>
						<li>
							<a data-tab="#tab-bullets" href="javascript:void(0)" id="bullets">Bullets</a>
						</li>
						<li>
							<a data-tab="#tab-captions" href="javascript:void(0)" id="captions">Captions</a>
						</li>
						<li>
							<a data-tab="#tab-captiontext" href="javascript:void(0)" id="captiontext">Caption Text</a>
						</li>																		
					</ul>
					
					<?php if($this->isNew == false):?>				
						<a href="<?php echo $this->linkEditSlides?>" id="button_edit_slides_visual" class="button-primary">Edit Slides</a>
					<?php endif;?>
					
				</td>
				<td valign="top" class="cell_right" >
					<div id="tab-generalvis" class="visual_panel">
						<?php echo $this->putVisualFields("general") ?>
					</div>
					<div id="tab-border" class="visual_panel hidden">
						<?php echo $this->putVisualFields("border") ?>
					</div>
					<div id="tab-shadow" class="visual_panel hidden">
						<?php echo $this->putVisualFields("shadow") ?>
					</div>					
					<div id="tab-arrows" class="visual_panel hidden">
						<?php echo $this->putVisualFields("arrows") ?>
					</div>
					<div id="tab-bullets" class="visual_panel hidden">
						<?php
							echo $this->putVisualFields("bullets");
						?>
					</div>
					<div id="tab-captions" class="visual_panel hidden">
						<?php
							echo $this->putVisualFields("captions");
						?>
					</div>					
					<div id="tab-captiontext" class="visual_panel hidden">
						<?php
							echo $this->putVisualFields("captiontext");
						?>
					</div>					
										 
					<div class="slider_visual_wrapper">
						<div id="slider_container" style="<?php echo $this->style?>">
							<div id="caption_back" class="caption_wrapper" style="<?php echo $this->styleCaptions?>">
								<p id="caption_text" class="caption_text" style="<?php echo $this->styleCaptionsText?>">
								This is <b>html caption</b>, you can play with the text style.
								</p>
							</div>
							<div id="bullets_wrapper" style="<?php echo $this->styleBullets?>" class="<?php echo $this->classBullets?>">
							<?php
								echo $this->bulletsHtml; 
							?>
							</div>
							
							<div id="arrows_container">
								<img id="arrow_left" src="<?php echo $this->urlArrowLeft?>" style="<?php echo $this->styleArrowLeft?>" class="dir_arrow" />
								<img id="arrow_right" src="<?php echo $this->urlArrowRight?>" style="<?php echo $this->styleArrowRight?>" class="dir_arrow" />
							</div>
							
						</div>
					</div>
					
				</td>
		</table>				
	</div>
	
