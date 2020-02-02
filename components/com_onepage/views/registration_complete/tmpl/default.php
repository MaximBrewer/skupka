<?php
/**
 * @version		$Id: view.html.php 21705 2011-06-28 21:19:50Z RuposTel.com $
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

$msg = JFactory::getSession()->get('application.queue');; 
				$msgq1 = JFactory::getApplication()->get('messageQueue', array()); 
		        $msgq2 = JFactory::getApplication()->get('_messageQueue', array()); 
			
				$res = array_merge($msg, $msgq1, $msgq2);
				if (empty($res)) {
?><div id="vmMainPageOPC">
<h1><?php echo JText::_('COM_VIRTUEMART_REG_COMPLETE'); ?></h1>

<?php echo $this->registration_msg; ?>
 
 
</div>
				<?php 
				} 