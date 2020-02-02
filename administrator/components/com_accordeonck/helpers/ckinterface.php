<?php
/**
 * @name		Accordeon Menu CK params
 * @package		com_accordeonck
 * @copyright	Copyright (C) 2016. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author		Cedric Keiflin - http://www.template-creator.com - http://www.joomlack.fr
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

class CK_Interface extends JObject {

	private $imagespath;
	
	private $colorpicker_class = 'color {required:false,pickerPosition:\'top\',pickerBorder:2,pickerInset:3,hash:true}';

	public function __construct($properties = null) {
		$this->imagespath = JUri::root(true) . '/administrator/components/com_accordeonck/images/';
	}

	public function createBorders($prefix) {
	?>
	<div class="ckrow">
		<label for="<?php echo $prefix; ?>bordertopcolor"><?php echo JText::_('CK_BORDERCOLOR_LABEL'); ?></label>
		
		<img class="iconck" src="<?php echo $this->imagespath ?>/color.png" />
		<span><input type="text" id="<?php echo $prefix; ?>bordertopcolor" name="<?php echo $prefix; ?>bordertopcolor" class="<?php echo $prefix; ?> <?php echo $this->colorpicker_class; ?>" title="<?php echo JText::_('CK_BORDERCOLOR_DESC'); ?>"/></span>
		<span style="width:30px;"><input type="text" id="<?php echo $prefix; ?>bordertopwidth" name="<?php echo $prefix; ?>bordertopwidth" class="<?php echo $prefix; ?> hasTip" style="width:30px;border-top-color:#237CA4;" title="<?php echo JText::_('CK_BORDERTOPWIDTH_DESC'); ?>" /></span>
		<span>
			<select id="<?php echo $prefix; ?>bordertopstyle" name="<?php echo $prefix; ?>bordertopstyle" class="<?php echo $prefix; ?> hasTip" style="width: 70px; border-radius: 0px;">
				<option value="solid">solid</option>
				<option value="dotted">dotted</option>
				<option value="dashed">dashed</option>
			</select>
		</span>
		<br /><label></label>
		<img class="iconck" src="<?php echo $this->imagespath ?>/color.png" />
		<span><input type="text" id="<?php echo $prefix; ?>borderrightcolor" name="<?php echo $prefix; ?>borderrightcolor" class="<?php echo $prefix; ?> <?php echo $this->colorpicker_class; ?>" title="<?php echo JText::_('CK_BORDERCOLOR_DESC'); ?>"/></span>
		<span style="width:30px;"><input type="text" id="<?php echo $prefix; ?>borderrightwidth" name="<?php echo $prefix; ?>borderrightwidth" class="<?php echo $prefix; ?> hasTip" style="width:30px;border-right-color:#237CA4;" title="<?php echo JText::_('CK_BORDERRIGHTWIDTH_DESC'); ?>" /></span>
		<span>
			<select id="<?php echo $prefix; ?>borderrightstyle" name="<?php echo $prefix; ?>borderrightstyle" class="<?php echo $prefix; ?> hasTip" style="width: 70px; border-radius: 0px;">
				<option value="solid">solid</option>
				<option value="dotted">dotted</option>
				<option value="dashed">dashed</option>
			</select>
		</span>
		<br /><label></label>
		<img class="iconck" src="<?php echo $this->imagespath ?>/color.png" />
		<span><input type="text" id="<?php echo $prefix; ?>borderbottomcolor" name="<?php echo $prefix; ?>borderbottomcolor" class="<?php echo $prefix; ?> <?php echo $this->colorpicker_class; ?>" title="<?php echo JText::_('CK_BORDERCOLOR_DESC'); ?>"/></span>
		<span style="width:30px;"><input type="text" id="<?php echo $prefix; ?>borderbottomwidth" name="<?php echo $prefix; ?>borderbottomwidth" class="<?php echo $prefix; ?> hasTip" style="width:30px;border-bottom-color:#237CA4;" title="<?php echo JText::_('CK_BORDERBOTTOMWIDTH_DESC'); ?>" /></span>
		<span>
			<select id="<?php echo $prefix; ?>borderbottomstyle" name="<?php echo $prefix; ?>borderbottomstyle" class="<?php echo $prefix; ?> hasTip" style="width: 70px; border-radius: 0px;">
				<option value="solid">solid</option>
				<option value="dotted">dotted</option>
				<option value="dashed">dashed</option>
			</select>
		</span>
		<br /><label></label>
		<img class="iconck" src="<?php echo $this->imagespath ?>/color.png" />
		<span><input type="text" id="<?php echo $prefix; ?>borderleftcolor" name="<?php echo $prefix; ?>borderleftcolor" class="<?php echo $prefix; ?> <?php echo $this->colorpicker_class; ?>" title="<?php echo JText::_('CK_BORDERCOLOR_DESC'); ?>"/></span>
		<span style="width:30px;"><input type="text" id="<?php echo $prefix; ?>borderleftwidth" name="<?php echo $prefix; ?>borderleftwidth" class="<?php echo $prefix; ?> hasTip" style="width:30px;border-left-color:#237CA4;" title="<?php echo JText::_('CK_BORDERLEFTWIDTH_DESC'); ?>" /></span>
		<span>
			<select id="<?php echo $prefix; ?>borderleftstyle" name="<?php echo $prefix; ?>borderleftstyle" class="<?php echo $prefix; ?> hasTip" style="width: 70px; border-radius: 0px;">
				<option value="solid">solid</option>
				<option value="dotted">dotted</option>
				<option value="dashed">dashed</option>
			</select>
		</span>
	</div>
	<?php
	}
}
