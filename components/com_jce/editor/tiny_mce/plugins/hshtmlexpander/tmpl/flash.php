<?php
/**
* @version $Id: link.php 2008-02-20 Ryan Demmer $
* @package JCE
* @copyright Copyright (C) 2006-2007 Ryan Demmer. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* JCE is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
*/
defined( '_JEXEC' ) or die( 'Restricted access' );
?>
			<fieldset>
				<legend><?php echo WFText::_('WF_HSHTMLEXPAND_LEGEND_GENERAL');?></legend>
				<table border="0" cellpadding="0" cellspacing="4">
					<tr>
						<td><label id="objectwidthlabel" for="objectwidth" class="hastip" title="<?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_OBJECTWIDTH_DESC');?>"><?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_OBJECTWIDTH');?></label></td>
						<td><input type="text" id="objectwidth" value="" /></td>
					</tr>
					<tr>
						<td><label id="objectheightlabel" for="objectheight" class="hastip" title="<?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_OBJECTHEIGHT_DESC');?>"><?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_OBJECTHEIGHT');?></label></td>
						<td><input type="text" id="objectheight" value="" /></td>
					</tr>
					<tr>
						<td class="column1"><label id="swfversionlabel" for="swfversion" class="hastip" title="<?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_SWFVERSION_DESC');?>"><?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_SWFVERSION');?></label></td>
						<td><input id="swfversion" type="text" value="" /></td>
					</tr>
					<tr>
						<td class="nowrap"><label id="swfexpressinstallurllabel" for="swfexpressinstallurl" class="hastip" title="<?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_SWFEXPRESSINSTALLURL_DESC');?>"><?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_SWFEXPRESSINSTALLURL');?></label></td>
						<td><input id="swfexpressinstallurl" type="text" value="" size="150" class="required browser"/></td>
      				</tr>
					<tr>
						<td class="column1"><label id="swfflashvarslabel" for="swfflashvars" class="hastip" title="<?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_SWFFLASHVARS_DESC');?>"><?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_SWFFLASHVARS');?></label></td>
						<td><input id="swfflashvars" type="text" value="" /></td>
					</tr>
					<tr>
						<td class="column1"><label id="swfparamslabel" for="swfparams" class="hastip" title="<?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_SWFPARAMS_DESC');?>"><?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_SWFPARAMS');?></label></td>
						<td><input id="swfparams" type="text" value="" /></td>
					</tr>
					<tr>
						<td class="column1"><label id="swfattributeslabel" for="swfattributes" class="hastip" title="<?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_SWFATTRIBUTES_DESC');?>"><?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_SWFATTRIBUTES');?></label></td>
						<td><input id="swfattributes" type="text" value="" /></td>
					</tr>
				</table>
			</fieldset>
