<?php
/**
 * @name		Slider CK
 * @package		com_slideshowck
 * @copyright	Copyright (C) 2016. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author		Cedric Keiflin - http://www.template-creator.com - http://www.joomlack.fr
 */
namespace Slideshowck;

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use \Slideshowck\CKUri;

class CKInterface extends \stdClass {

	public $imagespath;
	
	public $colorpicker_class = 'color {required:false,pickerPosition:\'top\',pickerBorder:2,pickerInset:3,hash:true}';

	public function __construct($properties = null) {
		$this->imagespath = SLIDESHOWCK_MEDIA_URI . '/images';
	}

	public function createAll($prefix) {
		?>
		<div class="ckheading"><?php echo CKText::_('CK_TEXT_LABEL'); ?></div>
		<?php
		$this->createText($prefix);
		?>
		<div class="ckheading"><?php echo CKText::_('CK_APPEARANCE_LABEL'); ?></div>
		<?php
		$this->createBackgroundColor($prefix);
		$this->createBackgroundImage($prefix);
		$this->createBorders($prefix);
		$this->createRoundedCorners($prefix);
		$this->createShadow($prefix);
		// $this->createTextShadow($prefix);
		?>
		<div class="ckheading"><?php echo CKText::_('CK_DIMENSIONS_LABEL'); ?></div>
		<?php
		$this->createMargins($prefix);
		$this->createDimensions($prefix);
		/*
		?>
		<!--<div class="ckheading"><?php echo CKText::_('CK_ANIMATIONS_LABEL'); ?></div>-->
		<?php 
		$this->createAnimations($prefix);*/
	}

	public function createBackgroundColor($prefix) {
	?>
	<div class="ckrow">
		<label for="<?php echo $prefix; ?>backgroundcolorstart"><?php echo CKText::_('CK_BGCOLOR_LABEL'); ?></label>
		<img class="ckicon" src="<?php echo $this->imagespath ?>/color.png" />
		<input type="text" id="<?php echo $prefix; ?>backgroundcolorstart" name="<?php echo $prefix; ?>backgroundcolorstart" class="cktip <?php echo $prefix; ?> <?php echo $this->colorpicker_class; ?>" title="<?php echo CKText::_('CK_BGCOLOR_DESC'); ?>"/>
		<img class="ckicon" src="<?php echo $this->imagespath ?>/color.png" />
		<input type="text" id="<?php echo $prefix; ?>backgroundcolorend" name="<?php echo $prefix; ?>backgroundcolorend" class="cktip <?php echo $prefix; ?> <?php echo $this->colorpicker_class; ?>" title="<?php echo CKText::_('CK_BGCOLOR2_DESC'); ?>" onchange="ckCheckGradientImageConflict(this, '<?php echo $prefix; ?>backgroundimageurl')"/>
		<img class="ckicon" src="<?php echo $this->imagespath ?>/layers.png" />
		<input type="text" id="<?php echo $prefix; ?>backgroundopacity" name="<?php echo $prefix; ?>backgroundopacity" class="cktip <?php echo $prefix; ?>" style="width:30px;" title="<?php echo CKText::_('CK_BGOPACITY_DESC'); ?>"/>
	</div>
	<?php
	}
	
	public function createBackgroundImage($prefix) {
	?>
	<div class="ckrow">
		<label for="<?php echo $prefix; ?>backgroundimageurl"><?php echo CKText::_('CK_BACKGROUNDIMAGE_LABEL'); ?></label>
		<img class="ckicon" src="<?php echo $this->imagespath ?>/image.png" />
		<div class="ckbutton-group">
			<input type="text" id="<?php echo $prefix; ?>backgroundimageurl" name="<?php echo $prefix; ?>backgroundimageurl" class="cktip <?php echo $prefix; ?>" title="<?php echo CKText::_('CK_BACKGROUNDIMAGE_DESC'); ?>" onchange="ckCheckGradientImageConflict(this, '<?php echo $prefix; ?>backgroundcolorend')" style="max-width: none; width: 150px;"/>
			<a class="modal ckbutton" href="<?php echo CKUri::base(true) ?>/index.php?option=com_slideshowck&view=browse&tmpl=component&field=<?php echo $prefix; ?>backgroundimageurl" rel="{handler: 'iframe'}" ><?php echo CKText::_('CK_SELECT'); ?></a>
			<a class="ckbutton" href="javascript:void(0)" onclick="$ck(this).parent().find('input').val('');"><?php echo CKText::_('CK_CLEAR'); ?></a>
		</div>
	</div>
	<div class="ckrow">
		<label></label>
		<span><img class="ckicon" src="<?php echo $this->imagespath ?>/offsetx.png" /></span><span style="width:30px;"><input type="text" id="<?php echo $prefix; ?>backgroundimageleft" name="<?php echo $prefix; ?>backgroundimageleft" class="<?php echo $prefix; ?> cktip" style="width:30px;" title="<?php echo CKText::_('CK_BACKGROUNDPOSITIONX_DESC'); ?>" /></span>
		<span><img class="ckicon" src="<?php echo $this->imagespath ?>/offsety.png" /></span><span style="width:30px;"><input type="text" id="<?php echo $prefix; ?>backgroundimagetop" name="<?php echo $prefix; ?>backgroundimagetop" class="<?php echo $prefix; ?> cktip" style="width:30px;" title="<?php echo CKText::_('CK_BACKGROUNDPOSITIONY_DESC'); ?>" /></span>
		<div class="ckbutton-group">
			<input class="" type="radio" value="repeat" id="<?php echo $prefix; ?>backgroundimagerepeat" name="<?php echo $prefix; ?>backgroundimagerepeat" class="<?php echo $prefix; ?>" />
			<label class="ckbutton first" for="<?php echo $prefix; ?>backgroundimagerepeat"><img class="ckicon" src="<?php echo $this->imagespath ?>/bg_repeat.png" />
			</label><input class="<?php echo $prefix; ?>" type="radio" value="repeat-x" id="<?php echo $prefix; ?>backgroundimagerepeat-x" name="<?php echo $prefix; ?>backgroundimagerepeat" />
			<label class="ckbutton"  for="<?php echo $prefix; ?>backgroundimagerepeat-x"><img class="ckicon" src="<?php echo $this->imagespath ?>/bg_repeat-x.png" />
			</label><input class="<?php echo $prefix; ?>" type="radio" value="repeat-y" id="<?php echo $prefix; ?>backgroundimagerepeat-y" name="<?php echo $prefix; ?>backgroundimagerepeat" />
			<label class="ckbutton last"  for="<?php echo $prefix; ?>backgroundimagerepeat-y"><img class="ckicon" src="<?php echo $this->imagespath ?>/bg_repeat-y.png" />
			</label><input class="<?php echo $prefix; ?>" type="radio" value="no-repeat" id="<?php echo $prefix; ?>backgroundimagerepeatno-repeat" name="<?php echo $prefix; ?>backgroundimagerepeat" />
			<label class="ckbutton last"  for="<?php echo $prefix; ?>backgroundimagerepeatno-repeat"><img class="ckicon" src="<?php echo $this->imagespath ?>/bg_no-repeat.png" /></label>
		</div>
	</div>
	<?php
	}
	public function createRoundedCorners($prefix) {
	?>
	<div class="ckrow">
		<label for="<?php echo $prefix; ?>borderradiustopleft"><?php echo CKText::_('CK_ROUNDEDCORNERS_LABEL'); ?></label>
		<span><img class="ckicon" src="<?php echo $this->imagespath ?>/border_radius_tl.png" /></span><span style="width:30px;"><input type="text" id="<?php echo $prefix; ?>borderradiustopleft" name="<?php echo $prefix; ?>borderradiustopleft" class="<?php echo $prefix; ?> cktip" style="width:30px;" title="<?php echo CKText::_('CK_ROUNDEDCORNERSTL_DESC'); ?>" /></span>
		<span><img class="ckicon" src="<?php echo $this->imagespath ?>/border_radius_tr.png" /></span><span style="width:30px;"><input type="text" id="<?php echo $prefix; ?>borderradiustopright" name="<?php echo $prefix; ?>borderradiustopright" class="<?php echo $prefix; ?> cktip" style="width:30px;" title="<?php echo CKText::_('CK_ROUNDEDCORNERSTR_DESC'); ?>" /></span>
		<span><img class="ckicon" src="<?php echo $this->imagespath ?>/border_radius_br.png" /></span><span style="width:30px;"><input type="text" id="<?php echo $prefix; ?>borderradiusbottomright" name="<?php echo $prefix; ?>borderradiusbottomright" class="<?php echo $prefix; ?> cktip" style="width:30px;" title="<?php echo CKText::_('CK_ROUNDEDCORNERSBR_DESC'); ?>" /></span>
		<span><img class="ckicon" src="<?php echo $this->imagespath ?>/border_radius_bl.png" /></span><span style="width:30px;"><input type="text" id="<?php echo $prefix; ?>borderradiusbottomleft" name="<?php echo $prefix; ?>borderradiusbottomleft" class="<?php echo $prefix; ?> cktip" style="width:30px;" title="<?php echo CKText::_('CK_ROUNDEDCORNERSBL_DESC'); ?>" /></span>
	</div>
	<?php
	}
	public function createShadow($prefix) {
	?>
	<div class="ckrow">
		<label for="<?php echo $prefix; ?>shadowcolor"><?php echo CKText::_('CK_SHADOW_LABEL'); ?></label>
		<img class="ckicon" src="<?php echo $this->imagespath ?>/color.png" />
		<span><input type="text" id="<?php echo $prefix; ?>shadowcolor" name="<?php echo $prefix; ?>shadowcolor" class="<?php echo $prefix; ?> <?php echo $this->colorpicker_class; ?>" /></span>
		<span><img class="ckicon" src="<?php echo $this->imagespath ?>/shadow_blur.png" /></span><span style="width:30px;"><input type="text" id="<?php echo $prefix; ?>shadowblur" name="<?php echo $prefix; ?>shadowblur" class="<?php echo $prefix; ?> cktip" style="width:30px;" title="<?php echo CKText::_('CK_SHADOWBLUR_DESC'); ?>" /></span>
		<span><img class="ckicon" src="<?php echo $this->imagespath ?>/shadow_spread.png" /></span><span style="width:30px;"><input type="text" id="<?php echo $prefix; ?>shadowspread" name="<?php echo $prefix; ?>shadowspread" class="<?php echo $prefix; ?> cktip" style="width:30px;" title="<?php echo CKText::_('CK_SHADOWSPREAD_DESC'); ?>" /></span>
	</div>
	<div class="ckrow">
		<label></label>
		<span><img class="ckicon" src="<?php echo $this->imagespath ?>/offsetx.png" /></span><span style="width:30px;"><input type="text" id="<?php echo $prefix; ?>shadowoffseth" name="<?php echo $prefix; ?>shadowoffseth" class="<?php echo $prefix; ?> cktip" style="width:30px;" title="<?php echo CKText::_('CK_OFFSETX_DESC'); ?>" /></span>
		<span><img class="ckicon" src="<?php echo $this->imagespath ?>/offsety.png" /></span><span style="width:30px;"><input type="text" id="<?php echo $prefix; ?>shadowoffsetv" name="<?php echo $prefix; ?>shadowoffsetv" class="<?php echo $prefix; ?> cktip" style="width:30px;" title="<?php echo CKText::_('CK_OFFSETY_DESC'); ?>" /></span>
		<div class="ckbutton-group">
			<input class="<?php echo $prefix; ?>" type="radio" value="0" id="<?php echo $prefix; ?>shadowinsetno" name="<?php echo $prefix; ?>shadowinset" />
			<label class="ckbutton last"  for="<?php echo $prefix; ?>shadowinsetno" style="width:auto;"><?php echo CKText::_('CK_OUT'); ?>
			</label><input class="<?php echo $prefix; ?>" type="radio" value="1" id="<?php echo $prefix; ?>shadowinsetyes" name="<?php echo $prefix; ?>shadowinset" />
			<label class="ckbutton last"  for="<?php echo $prefix; ?>shadowinsetyes" style="width:auto;"><?php echo CKText::_('CK_IN'); ?></label>
		</div>
	</div>
	<?php
	}
	public function createTextShadow($prefix) {
	?>
	<div class="ckrow">
		<label for="<?php echo $prefix; ?>textshadowcolor"><?php echo CKText::_('CK_TEXTSHADOW_LABEL'); ?></label>
		<img class="ckicon" src="<?php echo $this->imagespath ?>/color.png" />
		<span><input type="text" id="<?php echo $prefix; ?>textshadowcolor" name="<?php echo $prefix; ?>textshadowcolor" class="<?php echo $prefix; ?> <?php echo $this->colorpicker_class; ?>" /></span>
		<span><img class="ckicon" src="<?php echo $this->imagespath ?>/shadow_blur.png" /></span><span style="width:30px;"><input type="text" id="<?php echo $prefix; ?>textshadowblur" name="<?php echo $prefix; ?>textshadowblur" class="<?php echo $prefix; ?> cktip" style="width:30px;" title="<?php echo CKText::_('CK_SHADOWBLUR_DESC'); ?>" /></span>
		<span><img class="ckicon" src="<?php echo $this->imagespath ?>/offsetx.png" /></span><span style="width:30px;"><input type="text" id="<?php echo $prefix; ?>textshadowoffsetx" name="<?php echo $prefix; ?>textshadowoffsetx" class="<?php echo $prefix; ?> cktip" style="width:30px;" title="<?php echo CKText::_('CK_OFFSETX_DESC'); ?>" /></span>
		<span><img class="ckicon" src="<?php echo $this->imagespath ?>/offsety.png" /></span><span style="width:30px;"><input type="text" id="<?php echo $prefix; ?>textshadowoffsety" name="<?php echo $prefix; ?>textshadowoffsety" class="<?php echo $prefix; ?> cktip" style="width:30px;" title="<?php echo CKText::_('CK_OFFSETY_DESC'); ?>" /></span>
	</div>
	<?php
	}

	public function createDimensions($prefix) {
	?>
	<div class="ckrow">
		<label for="<?php echo $prefix; ?>width"><?php echo CKText::_('CK_WIDTH_LABEL'); ?></label>
		<span><img class="ckicon" src="<?php echo $this->imagespath ?>/width.png" /></span><span style="width:30px;"><input type="text" id="<?php echo $prefix; ?>width" name="<?php echo $prefix; ?>width" class="<?php echo $prefix; ?> cktip" style="width:30px;" title="<?php echo CKText::_('CK_WIDTH_DESC'); ?>" /></span>
		<span><img class="ckicon" src="<?php echo $this->imagespath ?>/height.png" /></span><span style="width:30px;"><input type="text" id="<?php echo $prefix; ?>height" name="<?php echo $prefix; ?>height" class="<?php echo $prefix; ?> cktip" style="width:30px;" title="<?php echo CKText::_('CK_HEIGHT_DESC'); ?>" /></span>
	</div>
	<?php
	}

	public function createMargins($prefix) {
	?>
	<div class="ckrow">
		<label for="<?php echo $prefix; ?>margintop"><?php echo CKText::_('CK_MARGIN_LABEL'); ?></label>
		<span><img class="ckicon" src="<?php echo $this->imagespath ?>/margin_top.png" /></span><span style="width:30px;"><input type="text" id="<?php echo $prefix; ?>margintop" name="<?php echo $prefix; ?>margintop" class="<?php echo $prefix; ?> cktip" style="width:30px;" title="<?php echo CKText::_('CK_MARGINTOP_DESC'); ?>" /></span>
		<span><img class="ckicon" src="<?php echo $this->imagespath ?>/margin_right.png" /></span><span style="width:30px;"><input type="text" id="<?php echo $prefix; ?>marginright" name="<?php echo $prefix; ?>marginright" class="<?php echo $prefix; ?> cktip" style="width:30px;" title="<?php echo CKText::_('CK_MARGINRIGHT_DESC'); ?>" /></span>
		<span><img class="ckicon" src="<?php echo $this->imagespath ?>/margin_bottom.png" /></span><span style="width:30px;"><input type="text" id="<?php echo $prefix; ?>marginbottom" name="<?php echo $prefix; ?>marginbottom" class="<?php echo $prefix; ?> cktip" style="width:30px;" title="<?php echo CKText::_('CK_MARGINBOTTOM_DESC'); ?>" /></span>
		<span><img class="ckicon" src="<?php echo $this->imagespath ?>/margin_left.png" /></span><span style="width:30px;"><input type="text" id="<?php echo $prefix; ?>marginleft" name="<?php echo $prefix; ?>marginleft" class="<?php echo $prefix; ?> cktip" style="width:30px;" title="<?php echo CKText::_('CK_MARGINLEFT_DESC'); ?>" /></span>
	</div>
	<div class="ckrow">
		<label for="<?php echo $prefix; ?>paddingtop"><?php echo CKText::_('CK_PADDING_LABEL'); ?></label>
		<span><img class="ckicon" src="<?php echo $this->imagespath ?>/padding_top.png" /></span><span style="width:30px;"><input type="text" id="<?php echo $prefix; ?>paddingtop" name="<?php echo $prefix; ?>paddingtop" class="<?php echo $prefix; ?> cktip" style="width:30px;" title="<?php echo CKText::_('CK_PADDINGTOP_DESC'); ?>" /></span>
		<span><img class="ckicon" src="<?php echo $this->imagespath ?>/padding_right.png" /></span><span style="width:30px;"><input type="text" id="<?php echo $prefix; ?>paddingright" name="<?php echo $prefix; ?>paddingright" class="<?php echo $prefix; ?> cktip" style="width:30px;" title="<?php echo CKText::_('CK_PADDINGRIGHT_DESC'); ?>" /></span>
		<span><img class="ckicon" src="<?php echo $this->imagespath ?>/padding_bottom.png" /></span><span style="width:30px;"><input type="text" id="<?php echo $prefix; ?>paddingbottom" name="<?php echo $prefix; ?>paddingbottom" class="<?php echo $prefix; ?> cktip" style="width:30px;" title="<?php echo CKText::_('CK_PADDINGBOTTOM_DESC'); ?>" /></span>
		<span><img class="ckicon" src="<?php echo $this->imagespath ?>/padding_left.png" /></span><span style="width:30px;"><input type="text" id="<?php echo $prefix; ?>paddingleft" name="<?php echo $prefix; ?>paddingleft" class="<?php echo $prefix; ?> cktip" style="width:30px;" title="<?php echo CKText::_('CK_PADDINGLEFT_DESC'); ?>" /></span>
	</div>
	<?php
	}

	public function createBorders($prefix) {
	?>
	<div class="ckrow">
		<label for="<?php echo $prefix; ?>bordertopcolor"><?php echo CKText::_('CK_BORDERCOLOR_LABEL'); ?></label>
		<img class="ckicon" src="<?php echo $this->imagespath ?>/color.png" />
		<span><input type="text" id="<?php echo $prefix; ?>bordertopcolor" name="<?php echo $prefix; ?>bordertopcolor" class="<?php echo $prefix; ?> <?php echo $this->colorpicker_class; ?>" title="<?php echo CKText::_('CK_BORDERCOLOR_DESC'); ?>"/></span>
		<span style="width:30px;"><input type="text" id="<?php echo $prefix; ?>bordertopsize" name="<?php echo $prefix; ?>bordertopsize" class="<?php echo $prefix; ?> cktip" style="width:30px;border-top-color:#237CA4;" title="<?php echo CKText::_('CK_BORDERTOPWIDTH_DESC'); ?>" /></span>
		<span>
			<select id="<?php echo $prefix; ?>bordertopstyle" name="<?php echo $prefix; ?>bordertopstyle" class="<?php echo $prefix; ?> cktip" style="width: 70px; border-radius: 0px;">
				<option value="solid">solid</option>
				<option value="dotted">dotted</option>
				<option value="dashed">dashed</option>
			</select>
		</span>
	</div>
	<div class="ckrow">
		<label></label>
		<img class="ckicon" src="<?php echo $this->imagespath ?>/color.png" />
		<span><input type="text" id="<?php echo $prefix; ?>borderrightcolor" name="<?php echo $prefix; ?>borderrightcolor" class="<?php echo $prefix; ?> <?php echo $this->colorpicker_class; ?>" title="<?php echo CKText::_('CK_BORDERCOLOR_DESC'); ?>"/></span>
		<span style="width:30px;"><input type="text" id="<?php echo $prefix; ?>borderrightsize" name="<?php echo $prefix; ?>borderrightsize" class="<?php echo $prefix; ?> cktip" style="width:30px;border-right-color:#237CA4;" title="<?php echo CKText::_('CK_BORDERRIGHTWIDTH_DESC'); ?>" /></span>
		<span>
			<select id="<?php echo $prefix; ?>borderrightstyle" name="<?php echo $prefix; ?>borderrightstyle" class="<?php echo $prefix; ?> cktip" style="width: 70px; border-radius: 0px;">
				<option value="solid">solid</option>
				<option value="dotted">dotted</option>
				<option value="dashed">dashed</option>
			</select>
		</span>
	</div>
	<div class="ckrow">
		<label></label>
		<img class="ckicon" src="<?php echo $this->imagespath ?>/color.png" />
		<span><input type="text" id="<?php echo $prefix; ?>borderbottomcolor" name="<?php echo $prefix; ?>borderbottomcolor" class="<?php echo $prefix; ?> <?php echo $this->colorpicker_class; ?>" title="<?php echo CKText::_('CK_BORDERCOLOR_DESC'); ?>"/></span>
		<span style="width:30px;"><input type="text" id="<?php echo $prefix; ?>borderbottomsize" name="<?php echo $prefix; ?>borderbottomsize" class="<?php echo $prefix; ?> cktip" style="width:30px;border-bottom-color:#237CA4;" title="<?php echo CKText::_('CK_BORDERBOTTOMWIDTH_DESC'); ?>" /></span>
		<span>
			<select id="<?php echo $prefix; ?>borderbottomstyle" name="<?php echo $prefix; ?>borderbottomstyle" class="<?php echo $prefix; ?> cktip" style="width: 70px; border-radius: 0px;">
				<option value="solid">solid</option>
				<option value="dotted">dotted</option>
				<option value="dashed">dashed</option>
			</select>
		</span>
	</div>
	<div class="ckrow">
		<label></label>
		<img class="ckicon" src="<?php echo $this->imagespath ?>/color.png" />
		<span><input type="text" id="<?php echo $prefix; ?>borderleftcolor" name="<?php echo $prefix; ?>borderleftcolor" class="<?php echo $prefix; ?> <?php echo $this->colorpicker_class; ?>" title="<?php echo CKText::_('CK_BORDERCOLOR_DESC'); ?>"/></span>
		<span style="width:30px;"><input type="text" id="<?php echo $prefix; ?>borderleftsize" name="<?php echo $prefix; ?>borderleftsize" class="<?php echo $prefix; ?> cktip" style="width:30px;border-left-color:#237CA4;" title="<?php echo CKText::_('CK_BORDERLEFTWIDTH_DESC'); ?>" /></span>
		<span>
			<select id="<?php echo $prefix; ?>borderleftstyle" name="<?php echo $prefix; ?>borderleftstyle" class="<?php echo $prefix; ?> cktip" style="width: 70px; border-radius: 0px;">
				<option value="solid">solid</option>
				<option value="dotted">dotted</option>
				<option value="dashed">dashed</option>
			</select>
		</span>
	</div>
	<?php
	}

	public function createText($prefix) { 
	?>
	<div class="ckrow">
		<label for="<?php echo $prefix; ?>textgfont"><?php echo CKText::_('CK_FONTSTYLE_LABEL'); ?></label>
		<img class="ckicon" src="<?php echo $this->imagespath ?>/font_add.png" />
		<input type="text" id="<?php echo $prefix; ?>textgfont" name="<?php echo $prefix; ?>textgfont" class="<?php echo $prefix; ?> cktip gfonturl" onchange="ckCleanGfontName(this);" title="<?php echo CKText::_('CK_GFONT_DESC'); ?>" style="max-width:none;width:250px;" />
		<input type="hidden" id="<?php echo $prefix; ?>textisgfont" name="<?php echo $prefix; ?>textisgfont" class="isgfont <?php echo $prefix; ?>" />
	</div>
	<div class="ckrow">
		<label></label>
		<img class="ckicon" src="<?php echo $this->imagespath ?>/style.png" />
		<input type="text" id="<?php echo $prefix; ?>fontsize" name="<?php echo $prefix; ?>fontsize" class="<?php echo $prefix; ?> cktip" style="width:30px;" title="<?php echo CKText::_('CK_FONTSIZE_DESC'); ?>" />
		<img class="ckicon" src="<?php echo $this->imagespath ?>/color.png" />
		<span><?php echo CKText::_('CK_NORMAL'); ?></span>
		<input type="text" id="<?php echo $prefix; ?>color" name="<?php echo $prefix; ?>color" class="<?php echo $prefix; ?> cktip <?php echo $this->colorpicker_class; ?>" title="<?php echo CKText::_('CK_FONTCOLOR_DESC'); ?>" />
	</div>
	<div class="ckrow">
		<label for="">&nbsp;</label><img class="ckicon" src="<?php echo $this->imagespath ?>/font.png" />
		<div class="ckbutton-group">
			<input class="<?php echo $prefix; ?>" type="radio" value="left" id="<?php echo $prefix; ?>textalignleft" name="<?php echo $prefix; ?>textalign" />
			<label class="ckbutton first" for="<?php echo $prefix; ?>textalignleft"><img class="ckicon" src="<?php echo $this->imagespath ?>/text_align_left.png" />
			</label><input class="<?php echo $prefix; ?>" type="radio" value="center" id="<?php echo $prefix; ?>textaligncenter" name="<?php echo $prefix; ?>textalign" />
			<label class="ckbutton"  for="<?php echo $prefix; ?>textaligncenter"><img class="ckicon" src="<?php echo $this->imagespath ?>/text_align_center.png" />
			</label><input class="<?php echo $prefix; ?>" type="radio" value="right" id="<?php echo $prefix; ?>textalignright" name="<?php echo $prefix; ?>textalign" />
			<label class="ckbutton last"  for="<?php echo $prefix; ?>textalignright"><img class="ckicon" src="<?php echo $this->imagespath ?>/text_align_right.png" /></label>
		</div>
		<div class="ckbutton-group">
			<input class="<?php echo $prefix; ?>" type="radio" value="lowercase" id="<?php echo $prefix; ?>texttransformlowercase" name="<?php echo $prefix; ?>texttransform" />
			<label class="ckbutton first cktip" title="<?php echo CKText::_('CK_LOWERCASE'); ?>" for="<?php echo $prefix; ?>texttransformlowercase"><img class="ckicon" src="<?php echo $this->imagespath ?>/text_lowercase.png" />
			</label><input class="<?php echo $prefix; ?>" type="radio" value="uppercase" id="<?php echo $prefix; ?>texttransformuppercase" name="<?php echo $prefix; ?>texttransform" />
			<label class="ckbutton cktip" title="<?php echo CKText::_('CK_UPPERCASE'); ?>" for="<?php echo $prefix; ?>texttransformuppercase"><img class="ckicon" src="<?php echo $this->imagespath ?>/text_uppercase.png" />
			</label><input class="<?php echo $prefix; ?>" type="radio" value="capitalize" id="<?php echo $prefix; ?>texttransformcapitalize" name="<?php echo $prefix; ?>texttransform" />
			<label class="ckbutton cktip" title="<?php echo CKText::_('CK_CAPITALIZE'); ?>" for="<?php echo $prefix; ?>texttransformcapitalize"><img class="ckicon" src="<?php echo $this->imagespath ?>/text_capitalize.png" />
			</label><input class="<?php echo $prefix; ?>" type="radio" value="default" id="<?php echo $prefix; ?>texttransformdefault" name="<?php echo $prefix; ?>texttransform" />
			<label class="ckbutton cktip" title="<?php echo CKText::_('CK_DEFAULT'); ?>" for="<?php echo $prefix; ?>texttransformdefault"><img class="ckicon" src="<?php echo $this->imagespath ?>/text_default.png" />
			</label>
		</div>
	</div>
	<div class="ckrow">
		<label for="<?php echo $prefix; ?>fontweightbold"></label>
		<img class="ckicon" src="<?php echo $this->imagespath ?>/text_bold.png" />
		<div class="ckbutton-group">
			<input class="<?php echo $prefix; ?>" type="radio" value="bold" id="<?php echo $prefix; ?>fontweightbold" name="<?php echo $prefix; ?>fontweight" />
			<label class="ckbutton first cktip" title="" for="<?php echo $prefix; ?>fontweightbold" style="width:auto;"><?php echo CKText::_('CK_BOLD'); ?>
			</label><input class="<?php echo $prefix; ?>" type="radio" value="normal" id="<?php echo $prefix; ?>fontweightnormal" name="<?php echo $prefix; ?>fontweight" />
			<label class="ckbutton cktip" title="" for="<?php echo $prefix; ?>fontweightnormal" style="width:auto;"><?php echo CKText::_('CK_NORMAL'); ?>
			</label>
		</div>
		<img class="ckicon" src="<?php echo $this->imagespath ?>/text_underline.png" />
		<div class="ckbutton-group">
			<input class="<?php echo $prefix; ?>" type="radio" value="underline" id="<?php echo $prefix; ?>fontunderlineunderline" name="<?php echo $prefix; ?>fontunderline" />
			<label class="ckbutton first cktip" title="" for="<?php echo $prefix; ?>fontunderlineunderline" style="width:auto;"><?php echo ucfirst(CKText::_('CK_UNDERLINE')); ?>
			</label><input class="<?php echo $prefix; ?>" type="radio" value="none" id="<?php echo $prefix; ?>fontunderlinenone" name="<?php echo $prefix; ?>fontunderline" />
			<label class="ckbutton cktip" title="" for="<?php echo $prefix; ?>fontunderlinenone" style="width:auto;"><?php echo CKText::_('CK_NORMAL'); ?>
			</label>
		</div>
	</div>
	<?php
	}

	public function createAnimations($prefix) {
	?>
		<div class="ckrow">
			<label for="<?php echo $prefix; ?>animdur"><?php echo CKText::_('CK_DURATION'); ?></label>
			<img class="ckicon" src="<?php echo $this->imagespath ?>/hourglass.png" />
			<input class="<?php echo $prefix; ?>" type="text" name="<?php echo $prefix; ?>animdur" id="<?php echo $prefix; ?>animdur" value="1" /> [s]
		</div>
		<div class="ckrow">
			<label for="<?php echo $prefix; ?>animdelay"><?php echo CKText::_('CK_DELAY'); ?></label>
			<img class="ckicon" src="<?php echo $this->imagespath ?>/hourglass.png" />
			<input class="<?php echo $prefix; ?>" type="text" name="<?php echo $prefix; ?>animdelay" id="<?php echo $prefix; ?>animdelay" value="0" /> [s]
		</div>
		<div class="ckrow">
			<label for="<?php echo $prefix; ?>animfade"><?php echo CKText::_('CK_FADE'); ?></label>
			<img class="ckicon" src="<?php echo $this->imagespath ?>/shading.png" />
			<select class="<?php echo $prefix; ?>" type="list" name="<?php echo $prefix; ?>animfade" id="<?php echo $prefix; ?>animfade" value="" style="width: 100px;" >
				<option value="0"><?php echo CKText::_('JNO'); ?></option>
				<option value="1"><?php echo CKText::_('JYES'); ?></option>
			</select>
		</div>
		<div class="ckrow">
			<label for="<?php echo $prefix; ?>animmove"><?php echo CKText::_('CK_MOVE'); ?></label>
			<img class="ckicon" src="<?php echo $this->imagespath ?>/shape_square_go.png" />
			<select class="<?php echo $prefix; ?>" type="list" name="<?php echo $prefix; ?>animmove" id="<?php echo $prefix; ?>animmove" value="" style="width: 100px;" >
				<option value="0"><?php echo CKText::_('JNO'); ?></option>
				<option value="1"><?php echo CKText::_('JYES'); ?></option>
			</select>
			<select class="<?php echo $prefix; ?> cktip" title="<?php echo CKText::_('CK_DIRECTION'); ?>" type="list" name="<?php echo $prefix; ?>animmovedir" id="<?php echo $prefix; ?>animmovedir" value="" style="width: 100px;" >
				<option value="ltrck"><?php echo CKText::_('CK_LEFT_TO_RIGHT'); ?></option>
				<option value="rtlck"><?php echo CKText::_('CK_RIGHT_TO_LEFT'); ?></option>
				<option value="ttbck"><?php echo CKText::_('CK_TOP_TO_BOTTOM'); ?></option>
				<option value="bttck"><?php echo CKText::_('CK_BOTTOM_TO_TOP'); ?></option>
			</select>
			<input class="<?php echo $prefix; ?> cktip" title="<?php echo CKText::_('CK_DISTANCE'); ?>" type="text" name="<?php echo $prefix; ?>animmovedist" id="<?php echo $prefix; ?>animmovedist" value="40" /> [px]
		</div>
		
		<div class="ckrow">
			<label for="<?php echo $prefix; ?>animrot"><?php echo CKText::_('CK_ROTATE'); ?></label>
			<img class="ckicon" src="<?php echo $this->imagespath ?>/shape_rotate_clockwise.png" />
			<select class="<?php echo $prefix; ?>" type="list" name="<?php echo $prefix; ?>animrot" id="<?php echo $prefix; ?>animrot" value="" style="width: 100px;" >
				<option value="0"><?php echo CKText::_('JNO'); ?></option>
				<option value="1"><?php echo CKText::_('JYES'); ?></option>
			</select>
			<select class="<?php echo $prefix; ?>" type="list" name="<?php echo $prefix; ?>animrotrad" id="<?php echo $prefix; ?>animrotrad" value="" style="width: 100px;" >
				<option value="45">45°</option>
				<option value="90">90°</option>
				<option value="180">180°</option>
				<option value="270">270°</option>
				<option value="360">360°</option>
			</select>
		</div>
		<div class="ckrow">
			<label for="<?php echo $prefix; ?>animscale"><?php echo CKText::_('CK_SCALE'); ?></label>
			<img class="ckicon" src="<?php echo $this->imagespath ?>/shape_handles.png" />
			<select class="<?php echo $prefix; ?>" type="list" name="<?php echo $prefix; ?>animscale" id="<?php echo $prefix; ?>animscale" value="" style="width:100px;" >
				<option value="0"><?php echo CKText::_('JNO'); ?></option>
				<option value="1"><?php echo CKText::_('JYES'); ?></option>
			</select>
		</div>
		<div class="ckrow">
			<a class="ckbutton" href="javascript:void(0)" onclick="ckPlayAnimationPreview('<?php echo $prefix; ?>')"><i class="icon icon-play"></i><?php echo CKText::_('CK_PLAY_ANIMATION'); ?></a>
		</div>
	<?php
	}

	public function createEffects($prefix) {
	?>
		<div class="ckrow">
			<label for="<?php echo $prefix; ?>fxdur"><?php echo CKText::_('CK_DURATION'); ?></label>
			<img class="ckicon" src="<?php echo $this->imagespath ?>/hourglass.png" />
			<input class="<?php echo $prefix; ?>" type="text" name="<?php echo $prefix; ?>fxdur" id="<?php echo $prefix; ?>fxdur" value="1" /> [s]
		</div>
		<div class="ckrow">
			<label for="<?php echo $prefix; ?>fxdelay"><?php echo CKText::_('CK_DELAY'); ?></label>
			<img class="ckicon" src="<?php echo $this->imagespath ?>/hourglass.png" />
			<input class="<?php echo $prefix; ?>" type="text" name="<?php echo $prefix; ?>fxdelay" id="<?php echo $prefix; ?>fxdelay" value="0" /> [s]
		</div>
		<div class="ckheading"><?php echo CKText::_('CK_OPACITY'); ?></div>
		<div class="ckrow">
			<label for="<?php echo $prefix; ?>fxopacity"><?php echo CKText::_('CK_NORMAL_STATE'); ?></label>
			<img class="ckicon" src="<?php echo $this->imagespath ?>/shading.png" />
			<input class="<?php echo $prefix; ?>" type="text" name="<?php echo $prefix; ?>fxopacity" id="<?php echo $prefix; ?>fxopacity" value="" >
		</div>
		<div class="ckrow">
			<label for="<?php echo $prefix; ?>fxopacity"><?php echo CKText::_('CK_HOVER_STATE'); ?></label>
			<img class="ckicon" src="<?php echo $this->imagespath ?>/shading.png" />
			<input class="<?php echo $prefix; ?>" type="text" name="<?php echo $prefix; ?>hoverfxopacity" id="<?php echo $prefix; ?>hoverfxopacity" value="" >
		</div>
		<div class="ckrow">
			<label for="<?php echo $prefix; ?>fxopacity"><?php echo CKText::_('CK_ACTIVE_STATE'); ?></label>
			<img class="ckicon" src="<?php echo $this->imagespath ?>/shading.png" />
			<input class="<?php echo $prefix; ?>" type="text" name="<?php echo $prefix; ?>activefxopacity" id="<?php echo $prefix; ?>activefxopacity" value="" >
		</div>
		<div class="ckheading"><?php echo CKText::_('CK_MOVE'); ?></div>
		<div class="ckrow">
			<label for="<?php echo $prefix; ?>fxmove"><?php echo CKText::_('CK_NORMAL_STATE'); ?></label>
			<img class="ckicon" src="<?php echo $this->imagespath ?>/shape_square_go.png" />
			<select class="<?php echo $prefix; ?> cktip" title="<?php echo CKText::_('CK_DIRECTION'); ?>" type="list" name="<?php echo $prefix; ?>fxmovedir" id="<?php echo $prefix; ?>fxmovedir" value="" style="width: 100px;" >
				<option value="ltrck"><?php echo CKText::_('CK_LEFT_TO_RIGHT'); ?></option>
				<option value="rtlck"><?php echo CKText::_('CK_RIGHT_TO_LEFT'); ?></option>
				<option value="ttbck"><?php echo CKText::_('CK_TOP_TO_BOTTOM'); ?></option>
				<option value="bttck"><?php echo CKText::_('CK_BOTTOM_TO_TOP'); ?></option>
			</select>
			<input class="<?php echo $prefix; ?> cktip" title="<?php echo CKText::_('CK_DISTANCE'); ?>" type="text" name="<?php echo $prefix; ?>fxmovedist" id="<?php echo $prefix; ?>fxmovedist" value="" /> [px]
		</div>
		<div class="ckrow">
			<label for="<?php echo $prefix; ?>hoverfxmove"><?php echo CKText::_('CK_HOVER_STATE'); ?></label>
			<img class="ckicon" src="<?php echo $this->imagespath ?>/shape_square_go.png" />
			<select class="<?php echo $prefix; ?>hover cktip" title="<?php echo CKText::_('CK_DIRECTION'); ?>" type="list" name="<?php echo $prefix; ?>hoverfxmovedir" id="<?php echo $prefix; ?>hoverfxmovedir" value="" style="width: 100px;" >
				<option value="ltrck"><?php echo CKText::_('CK_LEFT_TO_RIGHT'); ?></option>
				<option value="rtlck"><?php echo CKText::_('CK_RIGHT_TO_LEFT'); ?></option>
				<option value="ttbck"><?php echo CKText::_('CK_TOP_TO_BOTTOM'); ?></option>
				<option value="bttck"><?php echo CKText::_('CK_BOTTOM_TO_TOP'); ?></option>
			</select>
			<input class="<?php echo $prefix; ?>hover cktip" title="<?php echo CKText::_('CK_DISTANCE'); ?>" type="text" name="<?php echo $prefix; ?>hoverfxmovedist" id="<?php echo $prefix; ?>hoverfxmovedist" value="" /> [px]
		</div>
		<div class="ckrow">
			<label for="<?php echo $prefix; ?>activefxmove"><?php echo CKText::_('CK_ACTIVE_STATE'); ?></label>
			<img class="ckicon" src="<?php echo $this->imagespath ?>/shape_square_go.png" />
			<select class="<?php echo $prefix; ?>active cktip" title="<?php echo CKText::_('CK_DIRECTION'); ?>" type="list" name="<?php echo $prefix; ?>activefxmovedir" id="<?php echo $prefix; ?>activefxmovedir" value="" style="width: 100px;" >
				<option value="ltrck"><?php echo CKText::_('CK_LEFT_TO_RIGHT'); ?></option>
				<option value="rtlck"><?php echo CKText::_('CK_RIGHT_TO_LEFT'); ?></option>
				<option value="ttbck"><?php echo CKText::_('CK_TOP_TO_BOTTOM'); ?></option>
				<option value="bttck"><?php echo CKText::_('CK_BOTTOM_TO_TOP'); ?></option>
			</select>
			<input class="<?php echo $prefix; ?>active cktip" title="<?php echo CKText::_('CK_DISTANCE'); ?>" type="text" name="<?php echo $prefix; ?>activefxmovedist" id="<?php echo $prefix; ?>activefxmovedist" value="" /> [px]
		</div>
		<div class="ckheading"><?php echo CKText::_('CK_ROTATE'); ?></div>
		<div class="ckrow">
			<label for="<?php echo $prefix; ?>fxrot"><?php echo CKText::_('CK_NORMAL_STATE'); ?></label>
			<img class="ckicon" src="<?php echo $this->imagespath ?>/shape_rotate_clockwise.png" />
			<select class="<?php echo $prefix; ?>" type="list" name="<?php echo $prefix; ?>fxrot" id="<?php echo $prefix; ?>fxrot" value="" style="width: 100px;" >
				<option value="0"><?php echo CKText::_('JNO'); ?></option>
				<option value="1"><?php echo CKText::_('JYES'); ?></option>
			</select>
			<input class="<?php echo $prefix; ?>" type="text" name="<?php echo $prefix; ?>fxrotrad" id="<?php echo $prefix; ?>fxrotrad" value="" /> °
		</div>
		<div class="ckrow">
			<label for="<?php echo $prefix; ?>hoverfxrot"><?php echo CKText::_('CK_HOVER_STATE'); ?></label>
			<img class="ckicon" src="<?php echo $this->imagespath ?>/shape_rotate_clockwise.png" />
			<select class="<?php echo $prefix; ?>hover" type="list" name="<?php echo $prefix; ?>hoverfxrot" id="<?php echo $prefix; ?>hoverfxrot" value="" style="width: 100px;" >
				<option value="0"><?php echo CKText::_('JNO'); ?></option>
				<option value="1"><?php echo CKText::_('JYES'); ?></option>
			</select>
			<input class="<?php echo $prefix; ?>hover" type="text" name="<?php echo $prefix; ?>hoverfxrotrad" id="<?php echo $prefix; ?>hoverfxrotrad" value="" /> °
		</div>
		<div class="ckrow">
			<label for="<?php echo $prefix; ?>activefxrot"><?php echo CKText::_('CK_ACTIVE_STATE'); ?></label>
			<img class="ckicon" src="<?php echo $this->imagespath ?>/shape_rotate_clockwise.png" />
			<select class="<?php echo $prefix; ?>active" type="list" name="<?php echo $prefix; ?>activefxrot" id="<?php echo $prefix; ?>activefxrot" value="" style="width: 100px;" >
				<option value="0"><?php echo CKText::_('JNO'); ?></option>
				<option value="1"><?php echo CKText::_('JYES'); ?></option>
			</select>
			<input class="<?php echo $prefix; ?>active" type="text" name="<?php echo $prefix; ?>activefxrotrad" id="<?php echo $prefix; ?>activefxrotrad" value="" /> °
		</div>
		<div class="ckheading"><?php echo CKText::_('CK_SCALE'); ?></div>
		<div class="ckrow">
			<label for="<?php echo $prefix; ?>fxscale"><?php echo CKText::_('CK_NORMAL_STATE'); ?></label>
			<img class="ckicon" src="<?php echo $this->imagespath ?>/shape_handles.png" />
			<input class="<?php echo $prefix; ?>" type="text" name="<?php echo $prefix; ?>fxscale" id="<?php echo $prefix; ?>fxscale" value="" />
		</div>
		<div class="ckrow">
			<label for="<?php echo $prefix; ?>hoverfxscale"><?php echo CKText::_('CK_HOVER_STATE'); ?></label>
			<img class="ckicon" src="<?php echo $this->imagespath ?>/shape_handles.png" />
			<input class="<?php echo $prefix; ?>hover" type="text" name="<?php echo $prefix; ?>hoverfxscale" id="<?php echo $prefix; ?>hoverfxscale" value="" />
		</div>
		<div class="ckrow">
			<label for="<?php echo $prefix; ?>activefxscale"><?php echo CKText::_('CK_ACTIVE_STATE'); ?></label>
			<img class="ckicon" src="<?php echo $this->imagespath ?>/shape_handles.png" />
			<input class="<?php echo $prefix; ?>active" type="text" name="<?php echo $prefix; ?>activefxscale" id="<?php echo $prefix; ?>activefxscale" value="" />
		</div>
		<div class="ckheading"><?php echo CKText::_('CK_BLUR'); ?></div>
		<div class="ckrow">
			<label for="<?php echo $prefix; ?>fxblur"><?php echo CKText::_('CK_NORMAL_STATE'); ?></label>
			<img class="ckicon" src="<?php echo $this->imagespath ?>/wrap-behind.png" />
			<input class="<?php echo $prefix; ?>" type="text" name="<?php echo $prefix; ?>fxblur" id="<?php echo $prefix; ?>fxblur" value="" />
		</div>
		<div class="ckrow">
			<label for="<?php echo $prefix; ?>hoverfxblur"><?php echo CKText::_('CK_HOVER_STATE'); ?></label>
			<img class="ckicon" src="<?php echo $this->imagespath ?>/wrap-behind.png" />
			<input class="<?php echo $prefix; ?>hover" type="text" name="<?php echo $prefix; ?>hoverfxblur" id="<?php echo $prefix; ?>hoverfxblur" value="" />
		</div>
		<div class="ckrow">
			<label for="<?php echo $prefix; ?>activefxblur"><?php echo CKText::_('CK_ACTIVE_STATE'); ?></label>
			<img class="ckicon" src="<?php echo $this->imagespath ?>/wrap-behind.png" />
			<input class="<?php echo $prefix; ?>active" type="text" name="<?php echo $prefix; ?>activefxblur" id="<?php echo $prefix; ?>activefxblur" value="" />
		</div>
		<div class="ckheading"><?php echo CKText::_('CK_BRIGHTNESS'); ?></div>
		<div class="ckrow">
			<label for="<?php echo $prefix; ?>fxbrightness"><?php echo CKText::_('CK_NORMAL_STATE'); ?></label>
			<img class="ckicon" src="<?php echo $this->imagespath ?>/lightbulb.png" />
			<input class="<?php echo $prefix; ?>" type="text" name="<?php echo $prefix; ?>fxbrightness" id="<?php echo $prefix; ?>fxbrightness" value="" />
		</div>
		<div class="ckrow">
			<label for="<?php echo $prefix; ?>hoverfxbrightness"><?php echo CKText::_('CK_HOVER_STATE'); ?></label>
			<img class="ckicon" src="<?php echo $this->imagespath ?>/lightbulb.png" />
			<input class="<?php echo $prefix; ?>hover" type="text" name="<?php echo $prefix; ?>hoverfxbrightness" id="<?php echo $prefix; ?>hoverfxbrightness" value="" />
		</div>
		<div class="ckrow">
			<label for="<?php echo $prefix; ?>activefxbrightness"><?php echo CKText::_('CK_ACTIVE_STATE'); ?></label>
			<img class="ckicon" src="<?php echo $this->imagespath ?>/lightbulb.png" />
			<input class="<?php echo $prefix; ?>active" type="text" name="<?php echo $prefix; ?>activefxbrightness" id="<?php echo $prefix; ?>activefxbrightness" value="" />
		</div>
		<div class="ckheading"><?php echo CKText::_('CK_GRAYSCALE'); ?></div>
		<div class="ckrow">
			<label for="<?php echo $prefix; ?>fxgrayscale"><?php echo CKText::_('CK_NORMAL_STATE'); ?></label>
			<img class="ckicon" src="<?php echo $this->imagespath ?>/lightbulb_off.png" />
			<input class="<?php echo $prefix; ?>" type="text" name="<?php echo $prefix; ?>fxgrayscale" id="<?php echo $prefix; ?>fxgrayscale" value="" />
		</div>
		<div class="ckrow">
			<label for="<?php echo $prefix; ?>hoverfxgrayscale"><?php echo CKText::_('CK_HOVER_STATE'); ?></label>
			<img class="ckicon" src="<?php echo $this->imagespath ?>/lightbulb_off.png" />
			<input class="<?php echo $prefix; ?>hover" type="text" name="<?php echo $prefix; ?>hoverfxgrayscale" id="<?php echo $prefix; ?>hoverfxgrayscale" value="" />
		</div>
		<div class="ckrow">
			<label for="<?php echo $prefix; ?>activefxgrayscale"><?php echo CKText::_('CK_ACTIVE_STATE'); ?></label>
			<img class="ckicon" src="<?php echo $this->imagespath ?>/lightbulb_off.png" />
			<input class="<?php echo $prefix; ?>active" type="text" name="<?php echo $prefix; ?>activefxgrayscale" id="<?php echo $prefix; ?>activefxgrayscale" value="" />
		</div>
	<?php
	}
}
