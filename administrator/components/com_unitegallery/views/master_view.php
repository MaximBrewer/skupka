<?php
/**
 * @package Unite Gallery
 * @author UniteCMS.net / Valiano
 * @copyright (C) 2012 Unite CMS, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('_JEXEC') or die('Restricted access');

	global $uniteGalleryVersion;
		
	
	HelperUG::putGlobalHtmlOutput();
	
?>
						
<div id="div_debug"></div>

<div id="debug_line" style="display:none"></div>
<div id="debug_side" style="display:none"></div>

<div class='unite_error_message' id="error_message" style="display:none;"></div>

<div class='unite_success_message' id="success_message" style="display:none;"></div>

<div id="viewWrapper" class="unite-view-wrapper unite-admin">

<?php
	self::requireView($view);
		
?>

</div>

<div class="unite-clear"></div>
<div class="unite-plugin-version-line unite-admin">
	<?php UniteProviderFunctionsUG::putFooterTextLine() ?>
	Plugin verson <?php echo $uniteGalleryVersion?>
</div>


<div id="divColorPicker" style="display:none;"></div>

<?php
	HelperUG::putGlobalClientSideTextHtml();	
?>
	
		