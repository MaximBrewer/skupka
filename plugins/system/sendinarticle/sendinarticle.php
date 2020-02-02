<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	5.10.7
 * @author	acyba.com
 * @copyright	(C) 2009-2019 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die('Restricted access');
?><?php

class plgSystemSendinarticle extends JPlugin
{
	function __construct(&$subject, $config){
		parent::__construct($subject, $config);
	}
	
	function onBeforeRender(){
		$app = JFactory::getApplication();
		if(!$app->isAdmin() || empty($app->input)) return;

		$input = $app->input;
		if($input->getCmd('option') === 'com_content' && ($input->getCmd('view', 'article') === 'article' || $input->getCmd('view', 'articles') === 'articles')){
			include_once(rtrim(JPATH_ADMINISTRATOR, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_acymailing'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'helper.php');
			acymailing_addScript(false, ACYMAILING_JS.'acymailing.js?v='.filemtime(ACYMAILING_MEDIA.'js'.DS.'acymailing.js'));
			acymailing_addStyle(false, ACYMAILING_CSS.'acypopup.css?v='.filemtime(ACYMAILING_MEDIA.'css'.DS.'acypopup.css'));
			acymailing_addStyle(false, ACYMAILING_CSS.'acyicon.css?v='.filemtime(ACYMAILING_MEDIA.'css'.DS.'acyicon.css'));
		}
	}

	function onContentAfterSave($context, $article, $isNew){
		if($context != 'com_content.article') return;
		if(empty($article->id)) return;

		$app = JFactory::getApplication();
		if(!$app->isAdmin() || empty($app->input)) return;

		include_once(rtrim(JPATH_ADMINISTRATOR, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_acymailing'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'helper.php');

		$mailArticle = acymailing_loadObject('SELECT body FROM #__acymailing_mail WHERE type = \'article\'');
		if(!empty($mailArticle)) preg_match('@{joomlacontent:current@', $mailArticle->body, $matches);

		if(empty($matches)) return;

		$url = acymailing_baseURI().'index.php?option=com_acymailing&ctrl=email&task=chooseListBeforeSend&tmpl=component&articleId='.$article->id;
		$mailArticle = acymailing_loadObject('SELECT body FROM #__acymailing_mail WHERE type = \'article\'');
		preg_match('@{joomlacontent:current@', $mailArticle->body, $matches);
		if(empty($matches)) return;
		$app->enqueueMessage(acymailing_translation_sprintf('ACY_SEND_ARTICLE', acymailing_popup($url, acymailing_translation('ACY_HERE'), '', 600), 'message'));
	}
}
