<?php
/**
 * Favorites Model for Favorites Component
 * 
 * @package    Favorites & Wishlist
 * @subpackage com_wishlist
 * @license  GNU/GPL v2
 * @Copyright (C) 2013 2KWeb Solutions. All rights reserved.
 * This program is distributed under the terms of the GNU General Public License
 *
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport( 'joomla.application.component.modellist' );
/**
 * Favorites Model
 *
 * @package    Joomla.Components
 * @subpackage 	Favorites
 */
class FavoritesModelFavoritessh extends JModelList{

	/**
	 * Favoritessh data array for tmp store
	 *
	 * @var array
	 */
	private $_data;
	
	/**
	 * Method to share favorites
	 *
	 * @access	private
	 */
	private function shareFav($sh_mode){
			$acc_type = JRequest::getString('acc_type',  "public");
			if ($acc_type == "public") $var_date = "share_date=NOW()"; else $var_date = "share_date='1900-01-01'";
			$iswishlist = JRequest::getInt('is_wishlist',  0);
			$share_title = JRequest::getString('share_title',  "");
			$share_desc = JRequest::getString('share_desc',  "");
			$share_pass = JRequest::getString('share_pass',  "");
			$pass_clear = JRequest::getBool('pass_clear',  false);
			if ($share_pass != "" && !$pass_clear) 
			{
				$share_pass = md5($share_pass);
				$query_pass = ", share_pass='" .$share_pass. "'";
			}
			else if ($pass_clear) $query_pass = ", share_pass=''";
			
			$user =& JFactory::getUser();
			$db =& JFactory::getDBO();
			switch ($sh_mode) {
				case "share":
					$db->setQuery("SELECT COUNT(*) FROM #__virtuemart_favorites_sh WHERE user_id=" . $user->id );
					$sh_counter = $db->loadResult();
					if ($sh_counter == 0) $db->setQuery("INSERT INTO #__virtuemart_favorites_sh SET user_id=" .$user->id. ", " .$var_date. ", share_title='" .$share_title. "', share_desc='" .$share_desc. "', isWishList=" .$iswishlist . $query_pass);
					else $db->setQuery("UPDATE #__virtuemart_favorites_sh SET " .$var_date. ", share_title='" . $share_title . "', share_desc='" 
					.$share_desc. "', isWishList=" .$iswishlist . $query_pass. " WHERE user_id=" .$user->id);
					$db->query();
        			break;
    			case "unshare":
        			$db->setQuery("DELETE FROM #__virtuemart_favorites_sh WHERE user_id=" . $user->id);
					$db->query();
					$db->setQuery("UPDATE #__virtuemart_favorites SET product_qty=-1 WHERE user_id=" . $user->id);
					$db->query();
        			break;	
			}
	}
	
	/**
	 * Method to send email(s)
	 *
	 * @access	private
	 */
	private function sendmail($to, $subject, $body){
				$user =& JFactory::getUser();
				if ($user->email != "") {
					$mailer =& JFactory::getMailer();
					$body.= ' '.JText::_('VM_EMAIL_INVITE').' '.JURI::base().'?option=com_wishlist&view=sharelist&user_id='.$user->id;
					$sender = array( 
						$user->email,
						$user->name);
					$mailer->setSender($sender);
					$mail_array = explode(',', $to);
					$mailer->addRecipient($mail_array);
					$mailer->setSubject($subject);
					$mailer->setBody($body);
					$send = $mailer->Send();
					if ($send == "1") JFactory::getApplication()->enqueueMessage(JText::_('VM_EMAIL_SENT'));
				}
			}
	/**
	 * Gets the data
	 * @return mixed The data to be displayed to the user
	 */
	public function getData(){
		$mode = JRequest::getCmd('mode');	
		switch ($mode) {
			case "share":
				$this->shareFav("share");
				break;
			case "unshare":
				$this->shareFav("unshare");
				break;
			case "sendmail":
				$email_to = JRequest::getString('email_to',  "");
				$email_subj = JRequest::getString('email_subj',  "");
				$email_body = JRequest::getString('email_body',  "");
				//Demo System no email will be sent
				//JError::raiseNotice(100, 'This is a demo system, no Email will be sent from here');
				$this->sendmail($email_to,$email_subj,$email_body);
				break;
		}
		// Lets load the data if it doesn't already exist
		if (empty( $this->_data )){
			$user =& JFactory::getUser();
			$db =& JFactory::getDBO();
			$query = "SELECT * FROM `#__virtuemart_favorites_sh` where `user_id` = " . $user->id;
			$db->setQuery( $query );
			$this->_data = $db->loadObject();
		}
		return $this->_data;
	}
}
