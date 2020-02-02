<?php
/**
 * sh404SEF - SEO extension for Joomla!
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier - Weeblr llc - 2016
 * @package     sh404SEF
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     4.7.3.3292
 * @date		2016-03-15
 */

// Security check to ensure this file is being included by a parent file.
if (!defined('_JEXEC')) die('Direct Access to this location is not allowed.');

?>

<div class="sh404sef-updates"
	id="sh404sef-updates"><!-- start updates panel markup -->

<table class="adminlist">
<?php if(!$this->updates->status) : ?>
	<thead>
		<tr>
			<td width="130">
			   <?php
			     echo '<a href="javascript: void(0);" onclick="javascript: shSetupUpdates(\'forced\');" > ['
			     . JText::_('COM_SH404SEF_CHECK_UPDATES').']</a>';
			 ?>
			</td>
			<td >
        <?php echo JText::_('COM_SH404SEF_ERROR_CHECKING_NEW_VERSION'); ?>
      </td>
		</tr>
	</thead>

	<?php else : ?>
	<thead>
		<tr>
			<td width="130">    
			   <?php echo '<a href="javascript: void(0);" onclick="javascript: shSetupUpdates(\'forced\');" > ['
			   . JText::_('COM_SH404SEF_CHECK_UPDATES').']</a>';
			   ?>
			</td>
			<td >
      <?php echo $this->updates->statusMessage; ?>
      </td>
		</tr>
	</thead>
	<?php if ($this->updates->shouldUpdate) : ?>
	<tr>
	   <td >
	     <?php echo ShlMvcLayout_Helper::render('com_sh404sef.updates.update_' . Sh404sefConfigurationEdition::$id. '_j2'); ?>
	   </td>
	   <td>
	   <?php 
	   if (!empty( $this->updates->current)) {
	       echo $this->updates->current . ' [' 
	       . '<a target="_blank" href="' . $this->escape( $this->updates->changelogLink) . '" >'
	       . JText::_('COM_SH404SEF_VIEW_CHANGELOG') 
	       . '</a>]'
	       . '&nbsp['
	       . '<a target="_blank" href="' . $this->escape( $this->updates->downloadLink) . '" >'
         . JText::_('COM_SH404SEF_GET_IT') 
         . '</a>]';
	   }  
	   ?>
     </td>
	</tr>
	<tr>
     <td>
       <?php echo JText::_( 'COM_SH404SEF_NOTES')?>
     </td>
     <td>
     <?php 
         echo $this->escape($this->updates->note);
     ?>
     </td>
    </tr>
	<?php
	   endif;
	endif; 
	?>
</table>

<!-- end updates panel markup --></div>

