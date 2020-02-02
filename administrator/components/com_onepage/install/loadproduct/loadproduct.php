<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  Content.loadproduct 
 *
 * @copyright   Copyright (C) 2005 - 2015 RuposTel.com, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;


class PlgContentLoadproduct extends JPlugin
{
	protected static $modules = array();

	protected static $mods = array();

	
	public function onContentPrepare($context, &$article, &$params, $page = 0)
	{
		
		$option = JRequest::getVar('option', ''); 
		if ($option == 'com_virtuemart')
		if ($context === 'com_virtuemart.productdetails')
		{
			$product_id = $article->virtuemart_product_id; 
			$parent_id = $article->product_parent_id; 
			$id = $this->_getArticleId($product_id); 
			if (empty($id))
			$id = $this->_getArticleId($parent_id); 
			
			if (!empty($id))
			{
				
				require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_content'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'route.php'); 
				$t = ContentHelperRoute::getArticleRoute($id); 
							
				$url = JRoute::_($t); 
				
				$app = JFactory::getApplication(); 
				$app->redirect($url); 
				$app->close(); 
			}
			
		
			
		}
		
		// Don't run this plugin when the content is being indexed
		if ($context == 'com_finder.indexer')
		{
			return true;
		}
		
		if ((!empty($article->params)) && (is_object($article->params)))
		{
			
			
			$product_id = (int)$article->params->get('virtuemart_product_id'); 
		}
		else
		{
			$article->text = str_replace('{loadproduct}', '', $article->text); 
			return true; 
		}
		
		
		// Simple performance check to determine whether bot should process further
		if (strpos($article->text, '{loadproduct}') === false)
		{
			
			if (!empty($product_id))
			{
				
			$attribs = array(); 
			$attribs['virtuemart_product_id'] = $product_id; 
			$article->text .= $this->_renderModuleByName('mod_virtuemart_product_multi', $attribs); 
			
			}
			$article->text = str_replace('{loadproduct}', '', $article->text); 
			return true;
		}
		
		if (empty($product_id)) {
			$article->text = str_replace('{loadproduct}', '', $article->text); 
			return; 
		}

		
				
				$attribs = array(); 
				$attribs['virtuemart_product_id'] = $product_id; 
				$output = $this->_renderModuleByName('mod_virtuemart_product_multi', $attribs); 

				$article->text = str_replace('{loadproduct}', $output, $article->text); 
				
				
			
		
	}
   
   public function onContentPrepareForm($form, $data)
   {
	   
	   
      if (!($form instanceof JForm))
      {
         
         return false;
      }
 $name = $form->getName(); 
 if ($name !== 'com_content.article') return; 
 
 
      // Add the extra fields to the form.
      JForm::addFormPath(dirname(__FILE__) . '/loadproduct');
      $form->loadFile('loadproduct', false);
      return true;
   }
   
   private function _tableExists($table)
  {
   
   
   $db = JFactory::getDBO();
   $prefix = $db->getPrefix();
   $table = str_replace('#__', '', $table); 
   $table = str_replace($prefix, '', $table); 
   $table = $db->getPrefix().$table; 
   
   
   
   
   
   $q = 'select * from '.$table.' where 1 limit 0,1';
   
   $q = "SHOW TABLES LIKE '".$table."'";
	   $db->setQuery($q);
	   $r = $db->loadResult();
	   
	   
	   
	   if (!empty($r)) 
	    {
	
		return true;
		}
	
   return false;
  }
   private function _createTable()
   {
	   $q = 'CREATE TABLE IF NOT EXISTS `#__plg_content_loadproduct` (
  `article_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  UNIQUE KEY `article_id` (`article_id`),
  KEY `product_id` (`article_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;'; 
   
   $db = JFactory::getDBO(); 
   $db->setQuery($q); 
   $db->query(); 
   }
   public function onContentAfterSave($context, $article, $isNew)
   {
	   
	   if (!$this->_tableExists('plg_content_loadproduct'))
	   {
		   $this->_createTable(); 
	   }
	   
      $articleId = (int)$article->id;
	  $params = new JRegistry($article->attribs); 
	  $virtuemart_product_id = (int)$params->get('virtuemart_product_id'); 
	  
      if ((!empty($articleId)) && (!empty($virtuemart_product_id)))
      {
		  $db = JFactory::getDBO(); 
		 $q = 'insert into #__plg_content_loadproduct (`article_id`, `product_id`) values ('.(int)$articleId.', '.(int)$virtuemart_product_id.') on duplicate key update product_id = '.(int)$product_id;

		$db->setQuery($q); 		 
		$db->query(); 
		 
        
      }
 
      return true;
   }
   
   private function _getArticleId($product_id)
   {
	   
	   $db = JFactory::getDBO(); 
	   $q = 'select `article_id` from #__plg_content_loadproduct where product_id = '.(int)$product_id.' limit 0,1'; 
	   $db->setQuery($q); 
	   $res = $db->loadResult();
       if (empty($res)) return 0; 	   
	   $ret = (int)$res; 
	   return $ret; 
   }
	
    private function _renderModuleByName($name, $params=null)
	{
	    jimport( 'joomla.application.module.helper' );

	    $document   = JFactory::getDocument();
		$renderer   = $document->loadRenderer('module');
		if (empty($params))
		$params   = array();
		$module = JModuleHelper::getModule($name); 
		return JModuleHelper::renderModule($module, $params);
		//return $renderer->render($module, $params);

	}
	

	protected function _load($position, $style = 'none')
	{
		self::$modules[$position] = '';
		$document	= JFactory::getDocument();
		$renderer	= $document->loadRenderer('module');
		$modules	= JModuleHelper::getModules($position);
		$params		= array('style' => $style);
		ob_start();

		foreach ($modules as $module)
		{
			echo $renderer->render($module, $params);
		}

		self::$modules[$position] = ob_get_clean();

		return self::$modules[$position];
	}

	
	protected function _loadmod($module, $title, $style = 'none')
	{
		self::$mods[$module] = '';
		$document	= JFactory::getDocument();
		$renderer	= $document->loadRenderer('module');
		$mod		= JModuleHelper::getModule($module, $title);

		// If the module without the mod_ isn't found, try it with mod_.
		// This allows people to enter it either way in the content
		if (!isset($mod))
		{
			$name = 'mod_' . $module;
			$mod  = JModuleHelper::getModule($name, $title);
		}

		$params = array('style' => $style);
		ob_start();

		echo $renderer->render($mod, $params);

		self::$mods[$module] = ob_get_clean();

		return self::$mods[$module];
	}
}
