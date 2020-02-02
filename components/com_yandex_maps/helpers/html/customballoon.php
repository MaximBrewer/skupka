<?php defined('_JEXEC') or die; ob_start();?>
<div id="xdm_wg_sidebar_balloon" style="display:none" class="xdm_wg_sidebar xdm_wg_position<?php echo $map->settings->get('show_description_in_custom_balloon', 0)?>">
	<div class="xdm_wg_sidebar_view">
		<div class="xdm_wg_sidebar_content">
			<div class="xdm_wg_header">
				<div class="xdm_wg_title"></div>
			</div>
			<div class="xdm_wg_wrapper">
				<div class="xdm_wg_wrapper_scroller">
					<div class="xdm_wg_item_view">
						<a href="#">
							<div class="xdm_wg_object_name"></div>
						</a>
						<div class="xdm_wg_description"></div>
					</div>
				</div>
			</div>
			<div onclick="jQuery(this).closest('.xdm_wg_sidebar').hide()" title="Закрыть" class="xdm_wg_close"></div>
		</div>
	</div>
</div>
<?php 
return ob_get_clean();