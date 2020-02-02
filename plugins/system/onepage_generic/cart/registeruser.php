<?php
/**
** Parts of this code is written by joomlaprofessionals.com Copyright (c) 2012, 2015 All Right Reserved.
** Many part of this code is from VirtueMart Team Copyright (c) 2004 - 2015. All rights reserved.
** Some parts might even be Joomla and is Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved. 
** http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
** This source is free software. This version may have been modified pursuant
** to the GNU General Public License, and as distributed it includes or
** is derivative of works licensed under the GNU General Public License or
** other free or open source software licenses.
**
** THIS CODE AND INFORMATION ARE PROVIDED "AS IS" WITHOUT WARRANTY OF ANY 
** KIND, EITHER EXPRESSED OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
** IMPLIED WARRANTIES OF MERCHANTABILITY AND/OR FITNESS FOR A
** PARTICULAR PURPOSE. 

** <author>Joomlaproffs / Virtuemart team</author>
** <email>info@joomlaprofessionals.com</email>
** <date>2015</date>
*/

defined('_JEXEC') or die('Restricted access');

$app = JFactory::getApplication();
$postData = $app->input->post->getArray();

if(!isset($postData['address_type'])){
    $postData['address_type'] = 'BT';
}


$user_modal = VmModel::getModel('user');
$virtuemart_response = $user_modal->store($postData);

if(!isset($virtuemart_response["success"]) || $virtuemart_response["success"]==false || $virtuemart_response == false) 
{
	$messages=array();
	foreach(JFactory::getApplication()->getMessageQueue() as $message) 
	{
		$messages[]=$message["message"];
	} 
	$returnarray =  array('error'=>1,'message'=>implode(" ",$messages));
	echo json_encode($returnarray);
}
else
	echo json_encode($virtuemart_response);
exit;