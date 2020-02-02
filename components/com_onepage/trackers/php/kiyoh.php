<?php
/*
*
* @copyright Copyright (C) 2007 - 2013 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* One Page checkout is free software released under GNU/GPL and uses code from VirtueMart
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
* stAn note: Always use default headers for your php files, so they cannot be executed outside joomla security 
*
*/

defined( '_JEXEC' ) or die( 'Restricted access' );

$domain = $this->params->kiyoh_domain; 
if (empty($domain)) $domain = 'nl'; 


	$lang = $this->order['details']['BT']->order_language; 
	if (!empty($lang) && (stripos($lang, '-')!==false))
	 {
	   $a = explode('-', $lang); 
	   $lang = strtolower($a[0]); 
	 }
	 $langid = 1; 
	 switch ($lang) {
		 case 'nl': 
		 $langid = 1; 
		 break; 
		 case 'en': 
		 $langid = 4; 
		 break; 
		 case 'de': 
		 $langid = 3; 
		 break; 
		 case 'fr': 
		 $langid = 2; 
		 break; 
		 case 'se':  
		 $langid = 17;
		 break;
		 default: 
		 $langid = 1; 
		 
	 }
	 
	require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'loader.php');  
    $OPCloader = new OPCloader; 
	$url = 'https://www.kiyoh.'.$domain.'/set.php?user='.$this->params->kiyoh_mailaddress.'&connector='.$this->params->kiyoh_connector_id.'&action='.$this->params->kiyoh_action.'&targetMail='.$this->order['details']['BT']->email.'&delay='.$this->params->kiyoh_delay.'&language='.$langid; 
	$ret = $OPCloader->fetchUrl($url); 
	if (JFactory::getApplication()->isAdmin()) {
		if (function_exists('VmInfo')) {
			VmInfo('Kiyoh CURL executed via URL '.$url.' and returned: '.$ret); 
		}
		JFactory::getApplication()->enqueueMessage('Kiyoh CURL returned: '.$ret, 'notice'); 
	}

/*
stAn: original code commented as this didn't really work
?>

<!-- kiyoh start -->
<iframe src="https://www.kiyoh.<?php echo $domain; ?>/set.php?user=<?php echo $this->params->kiyoh_mailaddress; ?>&connector=<?php echo $this->params->kiyoh_connector_id; ?>&action=<?php echo $this->params->kiyoh_action; ?>&targetMail=<?php echo $this->order['details']['BT']->email; ?>&delay=<?php echo $this->params->kiyoh_delay;?>&language=<?php echo $langid; ?>" scrolling="no" frameborder="0" width="1" height="1" style="max-width: 1px; max-height: 1px;"></iframe>

<?php 
*/
?>

<script>
	  if ((typeof console != 'undefined')  && (typeof console.log != 'undefined')  &&  (console.log != null))
	  {
	     console.log('OPC Tracking: Kiyoh tracking initialized to URL <?php echo htmlentities($url); ?>'); 
	  }
</script>
