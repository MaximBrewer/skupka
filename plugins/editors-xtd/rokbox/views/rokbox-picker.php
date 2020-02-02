<?php
/**
 * Constant that is checked in included files to prevent direct access.
 */
define('_JEXEC', 1);

// Set base directory. Following should usually work even with symbolic linked plugin.
// PS. Current directory is JROOT/plugins/editors-xtd/rokbox/views so we need to go 4 levels up.
define('JPATH_BASE', dirname(dirname(dirname(dirname(dirname(
    isset($_SERVER['SCRIPT_FILENAME']) ? $_SERVER['SCRIPT_FILENAME'] : __FILE__
))))));

// Define Joomla constants.
require_once JPATH_BASE . '/includes/defines.php';

// Bootstrap Joomla frontend.
require_once JPATH_BASE . '/includes/framework.php';

// Instantiate the application.
$app = JFactory::getApplication('site');

// Initialise the application.
$app->initialise();

// JUri::root() points to this script, we need to remove path to the plugin..
$base = rtrim(dirname(dirname(dirname(dirname(JUri::root(true))))), '/');

// Alright, now we have everything loaded, lets get the request variables...
if (version_compare(JVERSION, '3.0', '>='))
{
    $input = $app->input;

    // $base = $input->getString('bp');
    $asset = $input->getString('asset');
    $author = $input->getInt('author');
    $editor = $input->getString('textarea');
}
else
{
    // $base = JRequest::getString('bp');
    $asset = JRequest::getString('asset');
    $author = JRequest::getInt('author');
    $editor = JRequest::getString('textarea');
}

function rb_renderPicker($text) {
    global $asset, $author, $base;

    $mediamanager = $base . '/administrator/index.php?option=com_media&view=images&tmpl=component&asset='.$asset.'&author='.$author.'&e_name=';
    $rokgallery = $base . '/administrator/index.php?option=com_rokgallery&view=gallerypicker&tmpl=component&textarea=';

    $mediamanager_rel = 'rel="{handler: \'iframe\', size: {x: 800, y: 500}}"';
    $rokgallery_rel = 'rel="{handler: \'iframe\', size: {x: 695, y: 400}}"';

    $picker = array();
    if (0 && is_file(JPATH_ROOT . '/plugins/editors-xtd/rokgallery/rokgallery.xml')) {
        $picker[] = '<div class="picker">';
        $picker[] = '	<select data-mediatype>';
        $picker[] = '		<option '.$rokgallery_rel.' value="'.htmlspecialchars($rokgallery.$text, ENT_QUOTES, 'UTF-8').'" selected>RokGallery</option>';
        $picker[] = '		<option '.$mediamanager_rel.' value="'.htmlspecialchars($mediamanager.$text, ENT_QUOTES, 'UTF-8').'">MediaManager</option>';
        $picker[] = '	</select>';
        $picker[] = '	<a data-picker class="modal-button '.$text.'" href="#"><span>Pick</span></a>';
        $picker[] = '</div>';
    } else {
        $picker[] = '<div class="picker '.$text.'">';
        $picker[] = '	<a data-picker class="modal-button '.$text.'" '.$mediamanager_rel.' href="'.htmlspecialchars($mediamanager.$text, ENT_QUOTES, 'UTF-8').'" tabindex="-1"><span>Pick</span></a>';
        $picker[] = '</div>';
    }

    return implode("\n", $picker);
}

?>
<!doctype html>
<html>
<head>
	<title>RokBox Snippets Generator</title>
	<link rel="stylesheet" href="../assets/css/rokbox.css" />
	<link rel="stylesheet" href="<?php echo $base . '/media/system/css/modal.css'; ?>" />
	<script src="<?php echo $base . '/media/system/js/mootools-core.js' ?>" type="text/javascript" charset="utf-8"></script>
	<script src="<?php echo $base . '/media/system/js/mootools-more.js' ?>" type="text/javascript" charset="utf-8"></script>
	<script src="<?php echo $base . '/media/system/js/modal.js'; ?>" type="text/javascript" charset="utf-8"></script>
	<script src="../assets/js/rokbox.js" type="text/javascript" charset="utf-8"></script>
</head>
<body>
	<div class="container">
		<input type="hidden" name="editor_id" value="<?php echo htmlspecialchars($editor, ENT_QUOTES, 'UTF-8'); ?>" />
		<div class="row">
			<span class="label">Link<span class="required-input">*</span></span>
			<input id="link" name="link" data-required type="text" placeholder="ie, images/powered_by.png" />
			<?php echo rb_renderPicker('link'); ?>
		</div>
		<div class="row">
			<span class="label">DOM Element</span>
			<input id="element" name="element" type="text" placeholder="ie, body form#form-login // div.some-class-name" />
			<div class="notice">Specify a CSS rule that matches the element in the page you want to render in the popup.</div>
		</div>
		<div class="row">
			<span class="label">Album</span>
			<input name="album" type="text" placeholder="RokBox, Gallery, Personal, etc..." />
		</div>
		<div class="row">
			<span class="label">Caption</span>
			<input name="caption" type="text" placeholder="" />
		</div>
		<div class="row">
			<span class="label">Content</span>
			<label for="text" class="radio">
				<input id="text" data-switcher name="content" type="radio" value="text" checked />
				Text
			</label>
			<label for="thumbnail" class="radio">
				<input id="thumbnail" data-switcher name="content" type="radio" value="thumbnail" />
				Thumbnail
			</label>
			<div class="sub-row">
				<input class="text_text" id="text_text" name="text" type="text" placeholder="ie, My RokBox" />
				<div class="notice text_text">Leave the field blank to wrap your current selection in the Editor</div>

				<input class="thumbnail_text" id="thumbnail_text" name="thumbnail" type="text" placeholder="ie, images/powered_by.png" /><?php echo rb_renderPicker('thumbnail_text'); ?>
				<div class="notice thumbnail_text">Leave the field blank to auto-generate thumbnails if the Link is a local image</div>
			</div>
		</div>

		<div class="footer">
			<ul>
				<li><a href="#" id="button-insert-new">Insert and New</a></li>
				<li><a href="#" id="button-insert-close">Insert and Close</a></li>
				<li><a href="#" id="button-cancel">Cancel</a></li>
			</ul>
		</div>
	</div>
	<script>if (!window.jModalClose) { window.jModalClose = function(){ SqueezeBox.closeBtn.fireEvent('click'); }; }</script>
</body>
</html>
