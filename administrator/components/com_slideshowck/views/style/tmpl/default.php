<?php
/**
 * @name		Slideshow CK
 * @package		com_slideshowck
 * @copyright	Copyright (C) 2019. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author		Cedric Keiflin - https://www.template-creator.com - https://www.joomlack.fr
 */

defined('_JEXEC') or die;

require_once(JPATH_ROOT . '/administrator/components/com_slideshowck/helpers/defines.js.php');

\Slideshowck\CKFramework::load();
\Slideshowck\CKFramework::loadFaIconsInline();
SlideshowckHelper::loadCkbox();

$imagespath = SLIDESHOWCK_MEDIA_URI .'/images/';
//JHtml::_('jquery.framework');
$doc = JFactory::getDocument();
$doc->addStylesheet(SLIDESHOWCK_MEDIA_URI . '/assets/admin.css');
$doc->addStylesheet(JUri::root(true) . '/modules/mod_slideshowck/themes/default/css/camera.css');
$doc->addScript(SLIDESHOWCK_MEDIA_URI . '/assets/jscolor/jscolor.js');
$doc->addScript(SLIDESHOWCK_MEDIA_URI . '/assets/admin.js');

$popupclass = ($this->input->get('layout', '', 'string') === 'modal') ? 'ckpopupwizard' : '';

// Load the JS strings
JText::script('CK_DOWNLOAD');
?>
<style>
#stylescontainerleft, #stylescontainerright {
	float :left;
	width: auto;
	padding: 10px;
	box-sizing: border-box;
}

#stylescontainerleft {
	width: 810px;
}
body.contentpane {
	padding-top: 65px;
}
</style>

<?php // Rules for the styles rendering ?>
<div class="menustylescustom" data-prefix="container" data-rule="[container]"></div>
<div class="menustylescustom" data-prefix="slide" data-rule="[slide]"></div>
<div class="menustylescustom" data-prefix="navigation" data-rule="[navigation]"></div>
<div class="menustylescustom" data-prefix="pagination" data-rule="[pagination]"></div>
<div class="menustylescustom" data-prefix="paginationdotthumbs" data-rule="[paginationdotthumbs]"></div>
<div class="menustylescustom" data-prefix="caption" data-rule="[caption]"></div>
<div class="menustylescustom" data-prefix="title" data-rule="[title]"></div>
<div class="menustylescustom" data-prefix="text" data-rule="[text]"></div>
<div class="menustylescustom" data-prefix="button" data-rule="[button]"></div>
<div class="menustylescustom" data-prefix="buttonhover" data-rule="[buttonhover]"></div>


<div id="ckheader">
	<div class="ckheaderlogo"><a href="https://www.joomlack.fr" target="_blank"><img title="JoomlaCK" src="https://media.joomlack.fr/images/logo_ck_white.png" width="35" height="35"></a></div>
	<div class="ckheadermenu">
		<div class="ckheadertitle">SLIDESHOW CK</div>
		<a href="javascript:void(0);"  class="ckheadermenuitem" onclick="ckImportParams('<?php echo $this->input->get('id',0,'int'); ?>')">
			<span class="fa fas fa-file-import cktip" data-placement="bottom" title="<?php echo JText::_('CK_IMPORT') ?>"></span>
			<span class="ckheadermenuitemtext"><?php echo JText::_('CK_IMPORT') ?></span>
		</a>
		<a href="javascript:void(0);"  class="ckheadermenuitem" onclick="ckExportParams('<?php echo $this->input->get('id',0,'int'); ?>')">
			<span class="fa fas fa-file-export cktip" data-placement="bottom" title="<?php echo JText::_('CK_EXPORT') ?>"></span>
			<span class="ckheadermenuitemtext"><?php echo JText::_('CK_EXPORT') ?></span>
		</a>
		<a href="javascript:void(0);"  class="ckheadermenuitem" onclick="ckClearFields()">
			<span class="fa fas fa-broom cktip" data-placement="bottom" title="<?php echo JText::_('CK_CLEAR_FIELDS') ?>"></span>
			<span class="ckheadermenuitemtext"><?php echo JText::_('CK_CLEAR_FIELDS') ?></span>
		</a>
		<a href="javascript:void(0);" class="ckheadermenuitem" onclick="ckPreviewStylesparams()">
			<span class="fa fas fa-eye cktip" data-placement="bottom" title="<?php echo JText::_('CK_PREVIEW') ?>"></span>
			<span class="ckheadermenuitemtext"><?php echo JText::_('CK_PREVIEW') ?></span>
		</a>
		<a href="javascript:void(0)" onclick="window.parent.CKBox.close()" class="ckheadermenuitem ckcancel">
			<span class="fa fa-times cktip" data-placement="bottom" title="<?php echo JText::_('CK_EXIT') ?>"></span>
			<span class="ckheadermenuitemtext"><?php echo JText::_('CK_EXIT') ?></span>
		</a>
		<a href="javascript:void(0);" id="ckpopupstyleswizard_save" class="ckheadermenuitem cksave" onclick="ckSaveStylesparams(this, '<?php echo $this->input->get('id',0,'int'); ?>', '<?php echo $this->input->get('layout','','string'); ?>')">
			<span class="fa fa-check cktip" data-placement="bottom" title="<?php echo JText::_('CK_SAVE') ?>"></span>
			<span class="ckheadermenuitemtext"><?php echo JText::_('CK_SAVE') ?></span>
		</a>
	</div>
</div>

<div id="ckpopupstyleswizard" class="<?php echo $popupclass; ?>">
	<input type="hidden" id="id" name="id" value="<?php echo $this->item->id; ?>" />
	<input type="hidden" id="layoutcss" name="layoutcss" value="<?php echo $this->item->layoutcss; ?>" />
	<input type="hidden" id="params" name="params" value="<?php echo htmlspecialchars($this->item->params); ?>" />
	<input type="hidden" id="returnFunc" name="returnFunc" value="<?php echo htmlspecialchars($this->input->get('returnFunc', '', 'cmd')); ?>" />
	<div id="stylescontainer" style="min-width: 1300px;" class="animateck">
	<div id="stylescontainerleft" class="ckinterface">
		<label for="name" style="display: inline-block;"><?php echo JText::_('CK_NAME'); ?></label>
		<input type="text" id="name" name="name" value="<?php echo $this->item->name; ?>" />
		<div id="styleswizard_options" class="styleswizard">
			<div class="ckinterfacetablink current" data-tab="tab_container" data-group="main"><?php echo JText::_('CK_SLIDESHOW'); ?></div>
			<div class="ckinterfacetablink" data-tab="tab_caption" data-group="main"><?php echo JText::_('CK_CAPTION'); ?></div>
			<div class="ckinterfacetablink" data-tab="tab_title" data-group="main"><?php echo JText::_('CK_TITLE'); ?></div>
			<div class="ckinterfacetablink" data-tab="tab_description" data-group="main"><?php echo JText::_('CK_DESCRIPTION'); ?></div>
			<div class="ckinterfacetablink" data-tab="tab_button" data-group="main"><?php echo JText::_('CK_BUTTON'); ?></div>
			<div class="ckinterfacetablink" data-tab="tab_custom" data-group="main"><?php echo JText::_('CK_CUSTOM_CSS'); ?></div>
			<div class="ckinterfacetablink" data-tab="tab_presets" data-group="main"><?php echo JText::_('CK_PRESETS'); ?></div>
			<div class="ckclr"></div>
			<div class="ckinterfacetab current hascol" id="tab_container" data-group="main">
				<div class="ckcol_left">
					<div class="ckinterfacetablink current" data-tab="tab_mainslider" data-group="container"><?php echo JText::_('CK_CONTAINER'); ?></div>
					<div class="ckinterfacetablink" data-tab="tab_mainnavigation" data-group="container"><?php echo JText::_('CK_NAVIGATION'); ?></div>
					<div class="ckinterfacetablink" data-tab="tab_mainpagination" data-group="container"><?php echo JText::_('CK_PAGINATION'); ?></div>
				</div>
				<div class="ckcol_right">
					<div class="ckinterfacetab current" id="tab_mainslider" data-group="container">
						<?php
						echo $this->interface->createMargins('container');
						echo $this->interface->createBorders('container');
						echo $this->interface->createShadow('container');
						?>
					</div>
					<div class="ckinterfacetab" id="tab_mainnavigation" data-group="container">
						<?php echo SlideshowckHelper::getProMessage() ?>
					</div>
					<div class="ckinterfacetab" id="tab_mainpagination" data-group="container">
						<?php echo SlideshowckHelper::getProMessage() ?>
					</div>
				</div>
				<div style="clear:both;"></div>
			</div>
			<div class="ckinterfacetab" id="tab_caption" data-group="main">
				<div class="ckrow">
					<label for="layoutposition"><?php echo JText::_('CK_LAYOUT'); ?></label>
					<img class="ckicon" src="<?php echo $this->interface->imagespath ?>/layout.png" />
					<?php 
					$html = '<span class="ckinfo" style="display: inline-block;"><i class="fas fa-info"></i><a href="https://www.joomlack.fr/en/joomla-extensions/slideshow-ck" target="_blank">' . JText::_('SLIDESHOWCK_ONLY_PRO') . '</a></span>';
					echo $html
					?>
				</div>
				<?php echo $this->interface->createAll('caption'); ?>
			</div>
			<div class="ckinterfacetab" id="tab_title" data-group="main">
				<?php echo SlideshowckHelper::getProMessage() ?>
			</div>
			<div class="ckinterfacetab" id="tab_description" data-group="main">
				<?php echo SlideshowckHelper::getProMessage() ?>
			</div>
			<div class="ckinterfacetab hascol" id="tab_button" data-group="main">
				<div class="ckcol_left">
					<div class="ckinterfacetablink current" data-tab="tab_buttonnormal" data-group="button"><?php echo JText::_('CK_BUTTON'); ?></div>
					<div class="ckinterfacetablink" data-tab="tab_buttonhover" data-group="button"><?php echo JText::_('CK_BUTTON_HOVER'); ?></div>
				</div>
				<div class="ckcol_right">
					<div class="ckinterfacetab current" id="tab_buttonnormal" data-group="button">
						<?php echo SlideshowckHelper::getProMessage() ?>
					</div>
					<div class="ckinterfacetab" id="tab_buttonhover" data-group="button">
						<?php echo SlideshowckHelper::getProMessage() ?>
					</div>
					<div style="clear:both;"></div>
				</div>
			</div>
			
			<div class="ckinterfacetab" id="tab_custom" data-group="main">
				<div id="customcssbuttons">
					<div class="customcssbutton ckbutton" data-prefix="container" data-rule="[container] { }"><?php echo JText::_('CK_CONTAINER'); ?></div>
					<div class="customcssbutton ckbutton" data-prefix="caption" data-rule="[caption] { }"><?php echo JText::_('CK_CAPTION'); ?></div>
					<div class="customcssbutton ckbutton" data-prefix="title" data-rule="[title] { }"><?php echo JText::_('CK_TITLE'); ?></div>
					<div class="customcssbutton ckbutton" data-prefix="text" data-rule="[text] { }"><?php echo JText::_('CK_TEXT'); ?></div>
					<div class="customcssbutton ckbutton" data-prefix="button" data-rule="[button] { }"><?php echo JText::_('CK_BUTTON'); ?></div>
					<div class="customcssbutton ckbutton" data-prefix="buttonhover" data-rule="[buttonhover] { }"><?php echo JText::_('CK_BUTTON_HOVER'); ?></div>
					<div class="customcssbutton ckbutton" data-prefix="paginationdotthumbs" data-rule="[paginationdotthumbs] { }"><?php echo JText::_('CK_PAGINATION_WITH_DOTS'); ?></div>
				</div>
				<textarea id="customcss" name="customcss" style="width:450px;height:300px;"></textarea>
			</div>
			<div class="ckinterfacetab" id="tab_presets" data-group="main">
				<?php echo SlideshowckHelper::getProMessage() ?>
			</div>
		</div>
	</div>
	<div id="stylescontainerright">
		<div id="previewarea">
			<div class="ckstyle"></div>
			<div class="slideshowck camera_wrap camera_amber_skin" id="slideshowckdemo1" style="display: block; height: 250px; width:403px;margin-bottom: 61px;">
				<div class="camera_fakehover">
					<div class="camera_target">
						<div class="cameraCont">
							<div class="cameraSlide cameraSlide_0 cameracurrent" style="visibility: visible; z-index: 999;">
								<img src="<?php echo JUri::root(true) ?>/media/com_slideshowck/images/slides/road.jpg" class="imgLoaded" style="visibility: visible; height: auto; margin-left: 0px; margin-right: 0px; margin-top: 0px; position: absolute; width: 403px;" data-alignment="" data-portrait="" alt="On the road again" width="1280" height="800">
								<div class="camerarelative" style="width: 403px; height: 250px;"></div>
							</div>
						</div>
					</div>
					<div class="camera_overlayer"></div>
					<div class="camera_target_content">
						<div class="cameraContents">
							<div class="cameraContent cameracurrent" style="display: block;"></div>
							<div class="cameraContent" style="display: block;">
								<div class="camera_caption moveFromLeft" style="visibility: visible;">
									<div>
										<div class="camera_caption_title">On the road again</div>
										<div class="camera_caption_desc">Lorem ipsum dolor sit amet</div>
										<a class="camera-button" href="#">Read more ...</a>
									</div>
								</div>
							</div>
							<div class="cameraContent"></div>
						</div>
					</div>
					<div class="camera_pie">
						<canvas id="pie_camera_wrap_128" width="38" height="38" style="position: absolute; z-index: 1002; right: 0px; top: 0px; display: none; opacity: 0.8;"></canvas>
					</div>
					<div class="camera_commands" style="opacity: 1;">
						<div class="camera_play" tabindex="0" aria-label="Start the slideshow" style="display: block;"></div>
						<div class="camera_stop" tabindex="0" aria-label="Pause the slideshow" style="display: none;"></div>
					</div>
					<div class="camera_prev" tabindex="0" style="opacity: 1;">
						<span></span>
					</div>
					<div class="camera_next" tabindex="0" style="opacity: 1;">
						<span></span>
					</div>
				</div>
				<div class="camera_thumbs_cont" style="visibility: visible;"></div>
				<div class="camera_pag">
					<ul class="camera_pag_ul">
						<li class="pag_nav_0 cameracurrent" style="position:relative; z-index:1002" tabindex="0" aria-label="Show slide 1"><span><span>0</span></span><img src="<?php echo JUri::root(true) ?>/media/com_slideshowck/images/slides/road.jpg" class="camera_thumb" style="position: absolute; width:100px;height:62px;opacity: 1;display: block; top: -65px;left:-46px;"><div class="thumb_arrow" style="opacity: 1;display: block;margin-top: -3px;"></div></li>
						<li class="pag_nav_1" style="position:relative; z-index:1002" tabindex="0" aria-label="Show slide 2"><span><span>1</span></span><img src="<?php echo JUri::root(true) ?>/media/com_slideshowck/images/slides/th/road_th.jpg" class="camera_thumb" style="position: absolute; opacity: 0;"><div class="thumb_arrow" style="opacity: 0;"></div></li>
						<li class="pag_nav_2" style="position:relative; z-index:1002" tabindex="0" aria-label="Show slide 3"><span><span>2</span></span><img src="<?php echo JUri::root(true) ?>/media/com_slideshowck/images/slides2/th/sea_th.jpg" class="camera_thumb" style="position: absolute; opacity: 0;"><div class="thumb_arrow" style="opacity: 0;"></div></li>
					</ul>
				</div>
				<div class="camera_loader" style="display: none;"></div>
			</div>
		</div>
	</div>
	<div style="clear:both;"></div>
</div>
<?php require_once ('default_importexport.php'); ?>
</div>
<script type="text/javascript">
	SLIDESHOWCK.CKCSSREPLACEMENT = new Object();
	<?php foreach (SlideshowckHelper::getCssReplacement() as $tag => $rep) { ?>
	SLIDESHOWCK.CKCSSREPLACEMENT['<?php echo $tag ?>'] = '<?php echo $rep ?>';
	<?php } ?>

	jQuery(document).ready(function($){
		CKBox.initialize({});
		CKBox.assign($('a.modal'), {
			parse: 'rel'
		});
		CKApi.Tooltip('.cktip');

		// manage the tabs
		ckInitTabs();
		// launch the preview when the user do a change
		$('#styleswizard_options input,#styleswizard_options select,#styleswizard_options textarea').change(function() {
			ckPreviewStylesparams();
		});

		ckApplyStylesparams();
		ckSetFloatingOnPreview();
		ckPlayAnimationPreview();
		
		$ck('.customcssbutton').click(function() {
			$ck('#customcss').val($ck('#customcss').val() + $ck(this).attr('data-rule'));
		});
	});
</script>
