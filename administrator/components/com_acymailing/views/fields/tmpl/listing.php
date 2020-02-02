<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	5.10.7
 * @author	acyba.com
 * @copyright	(C) 2009-2019 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die('Restricted access');
?><div id="acy_content">
	<div id="iframedoc"></div>
	<style>
		.front{
			display: none;
		}

		th.back{
			background-color: #7F99C3;
		}
	</style>
	<script>
		function displayTable(classToHide, classToDisplay, elementClicked){
			var elementNonActive = document.getElementsByClassName('customfields_pane');
			var elementToHide = document.getElementsByClassName(classToHide);
			var elementToDisplay = document.getElementsByClassName(classToDisplay);
			var elementColor = document.querySelectorAll('th.' + classToDisplay);

			for(var i = 0; i < elementNonActive.length; i++){
				elementNonActive[i].className = elementNonActive[i].className.replace(' active', '');
			}
			elementClicked.className += ' active';

			for(var i = 0; i < elementToHide.length; i++){
				elementToHide[i].style.display = 'none';
				elementToHide[i].style.backgroundColor = 'transparent';
			}

			for(var i = 0; i < elementToDisplay.length; i++){
				elementToDisplay[i].style.display = 'table-cell';
			}

			for(var i = 0; i < elementColor.length; i++){
				elementColor[i].style.backgroundColor = '#7F99C3';
			}

			document.getElementsByClassName('container_pane')[0].colSpan = elementColor.length;
		}
	</script>
	<form action="<?php echo acymailing_completeLink('fields'); ?>" method="post" name="adminForm" id="adminForm">
		<table class="acymailing_table" cellpadding="1" id="fieldListing">
			<thead>
			<tr class="hidewp" style="background-color: #F9F9F9">
				<th colspan="7"></th>
				<th class="container_pane" colspan="3" style="padding: 0;">
					<div style="height: 40px; ;font-size: 0; padding: 0;">
						<div class="customfields_pane frontendfields" onclick="displayTable('back', 'front', this)"><?php echo acymailing_translation('FRONTEND'); ?></div>
						<div class="customfields_pane backendfields active" onclick="displayTable('front', 'back', this)"><?php echo acymailing_translation('BACKEND'); ?></div>
					</div>
				</th>
				<th colspan="3"></th>
			</tr>
			<tr>
				<th class="title titlenum">
					<?php echo acymailing_translation('ACY_NUM'); ?>
				</th>
				<th class="title titleorder">
					<?php echo '<i class="icon-menu-2"></i>'; ?>
				</th>
				<th class="title titlebox">
					<input type="checkbox" name="toggle" value="" onclick="acymailing.checkAll(this);"/>
				</th>
				<th class="title">
					<?php echo acymailing_translation('FIELD_COLUMN'); ?>
				</th>
				<th class="title">
					<?php echo acymailing_translation('FIELD_LABEL'); ?>
				</th>
				<th class="title">
					<?php echo acymailing_translation('FIELD_TYPE'); ?>
				</th>
				<th class="title titletoggle">
					<?php echo acymailing_translation('REQUIRED'); ?>
				</th>
				<th class="front title titletoggle">
					<?php echo acymailing_translation('DISPLAY_FORM'); ?>
				</th>
				<th class="front title titletoggle">
					<?php echo acymailing_translation('DISPLAY_ACYPROFILE'); ?>
				</th>
				<th class="front title titletoggle">
					<?php echo acymailing_translation('DISPLAY_ACYLISTING'); ?>
				</th>
				<th class="front title titletoggle">
					<?php echo acymailing_translation('DISPLAY_FRONTJOOMLEREGISTRATION'); ?>
				</th>
				<th class="front title titletoggle">
					<?php echo acymailing_translation('DISPLAY_JOOMLAPROFILE'); ?>
				</th>
				<th class="back title titletoggle">
					<?php echo acymailing_translation('DISPLAY_ACYPROFILE'); ?>
				</th>
				<th class="back title titletoggle">
					<?php echo acymailing_translation('DISPLAY_ACYLISTING'); ?>
				</th>
				<th class="back title titletoggle hidewp">
					<?php echo acymailing_translation('DISPLAY_JOOMLAPROFILE'); ?>
				</th>
				<th class="title titletoggle">
					<?php echo acymailing_translation('ACY_PUBLISHED'); ?>
				</th>
				<th class="title titletoggle">
					<?php echo acymailing_translation('CORE'); ?>
				</th>
				<th class="title titleid">
					<?php echo acymailing_translation('ACY_ID'); ?>
				</th>
			</tr>
			</thead>
			<tbody id="acymailing_sortable_listing">
			<?php
			$k = 0;
			$ordering = '';

			for($i = 0, $a = count($this->rows); $i < $a; $i++){
				$row =& $this->rows[$i];
				$ordering .= ',"order['.$i.']='.$row->ordering.'"';

				$publishedid = 'published_'.$row->fieldid;
				$requiredid = 'required_'.$row->fieldid;
				$backendid = 'backend_'.$row->fieldid;
				$frontcompid = 'frontcomp_'.$row->fieldid;
				$frontformid = 'frontform_'.$row->fieldid;
				$listingid = 'listing_'.$row->fieldid;
				$frontlistingid = 'frontlisting_'.$row->fieldid;
				$frontjoomlaregistrationid = 'frontjoomlaregistration_'.$row->fieldid;
				$frontjoomlaprofileid = 'frontjoomlaprofile_'.$row->fieldid;
				$joomlaprofileid = 'joomlaprofile_'.$row->fieldid;
				?>
				<tr class="<?php echo "row$k"; ?>" acyorderid="<?php echo $row->fieldid; ?>">
					<td align="center" style="text-align:center">
						<?php echo $i + 1; ?>
					</td>
					<td class="acyicon-draghandle"><img alt="" src="<?php echo ACYMAILING_IMAGES; ?>icons/drag.png" /></td>
					<td align="center" style="text-align:center">
						<?php echo acymailing_gridID($i, $row->fieldid); ?>
					</td>
					<td>
						<a href="<?php echo acymailing_completeLink('fields&task=edit&fieldid='.$row->fieldid); ?>">
							<?php echo $row->namekey; ?>
						</a>
					</td>
					<td>
						<?php echo $this->fieldsClass->trans($row->fieldname); ?>
					</td>
					<td>
						<?php
						if(empty($this->fieldtype->allValues[$row->type])){
							echo '<span style="color:red">Type not found: '.$row->type.'</span>';
						}else{
							echo $this->fieldtype->allValues[$row->type];
						} ?>
					</td>
					<td align="center" style="text-align:center">
						<span id="<?php echo $requiredid ?>" class="loading"><?php echo $this->toggleClass->toggle($requiredid, (int)$row->required, 'fields') ?></span>
					</td>
					<td class="front" align="center" style="text-align:center">
						<span id="<?php echo $frontcompid ?>" class="loading"><?php echo $this->toggleClass->toggle($frontcompid, (int)$row->frontcomp, 'fields') ?></span>
					</td>
					<td class="front" align="center" style="text-align:center">
						<span id="<?php echo $frontformid ?>" class="loading"><?php echo $this->toggleClass->toggle($frontformid, (int)$row->frontform, 'fields') ?></span>
					</td>
					<td class="front" align="center" style="text-align:center">
						<span id="<?php echo $frontlistingid ?>" class="loading"><?php echo $this->toggleClass->toggle($frontlistingid, (int)$row->frontlisting, 'fields') ?></span>
					</td>
					<td class="front" align="center" style="text-align:center">
						<span id="<?php echo $frontjoomlaregistrationid ?>" class="loading"><?php echo $this->toggleClass->toggle($frontjoomlaregistrationid, (int)$row->frontjoomlaregistration, 'fields') ?></span>
					</td>
					<td class="front" align="center" style="text-align:center">
						<span id="<?php echo $frontjoomlaprofileid ?>" class="loading"><?php echo $this->toggleClass->toggle($frontjoomlaprofileid, (int)$row->frontjoomlaprofile, 'fields') ?></span>
					</td>
					<td class="back" align="center" style="text-align:center">
						<span id="<?php echo $backendid ?>" class="loading"><?php echo $this->toggleClass->toggle($backendid, (int)$row->backend, 'fields') ?></span>
					</td>
					<td class="back" align="center" style="text-align:center">
						<span id="<?php echo $listingid ?>" class="loading"><?php echo $this->toggleClass->toggle($listingid, (int)$row->listing, 'fields') ?></span>
					</td>
					<td class="back hidewp" align="center" style="text-align:center">
						<span id="<?php echo $joomlaprofileid ?>" class="loading"><?php echo $this->toggleClass->toggle($joomlaprofileid, (int)$row->joomlaprofile, 'fields') ?></span>
					</td>
					<td align="center" style="text-align:center">
						<span id="<?php echo $publishedid ?>" class="loading"><?php echo $this->toggleClass->toggle($publishedid, (int)$row->published, 'fields') ?></span>
					</td>
					<td align="center" style="text-align:center">
						<?php echo $this->toggleClass->display('activate', $row->core); ?>
					</td>
					<td width="1%" align="center">
						<?php echo $row->fieldid; ?>
					</td>
				</tr>
				<?php
				$k = 1 - $k;
			}
			?>
			</tbody>
		</table>

		<input type="hidden" name="boxchecked" value="0"/>
		<?php acymailing_formOptions(); ?>
	</form>
</div>

<?php acymailing_sortablelist('fields', ltrim($ordering, ',')); ?>
