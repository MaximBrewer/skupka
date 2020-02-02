<?php
/*
 * ARI Framework
 *
 * @package		ARI Framework
 * @version		1.0.0
 * @author		ARI Soft
 * @copyright	Copyright (c) 2009 www.ari-soft.com. All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
 * 
 */
namespace Arisoft\Joomla\Articles;

defined('_JEXEC') or die;

use JFactory, JApplicationHelper, ContentHelperRoute;

class Helper
{
	public static function getArticles($idList)
	{		
		$articleSeq = array();
		$articleIdList = array();
		$articleAliasList = array();
		$articleNameList = array();

		foreach ($idList as $id)
		{
			$seqItem = array('value' => '', 'type' => '');
			
			if (preg_match('/^\d+$/', $id))
			{
				$articleIdList[] = $seqItem['value'] = intval($id, 10);
				
				$seqItem['type'] = 'id';
			}
			else if ($id == JApplicationHelper::stringURLSafe($id))
			{
				$articleAliasList[] = $id;
				
				$seqItem['value'] = $id;
				$seqItem['type'] = 'alias';
			}
			else if (!empty($id))
			{
				$articleNameList[] = $id;
				
				$seqItem['value'] = $id;
				$seqItem['type'] = 'name';
			}
			
			if ($seqItem['value'])
				$articleSeq[] = $seqItem;
		}

		if (count($articleNameList) == 0 && count($articleAliasList) == 0 && count($articleNameList) == 0)
			return array();
	
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		
		$query->select(
			array(
				'C.id',
				'C.alias',
				'C.title',
				'C.catid',
				'CASE WHEN CHAR_LENGTH(C.alias) THEN CONCAT_WS(":", C.id, C.alias) ELSE C.id END AS slug'
			)
		);
		$query->from('#__content C');

		if (count($articleIdList) > 0)
		{
			$query->where('C.id IN (' . join(',', $articleIdList) . ')', 'OR');
		}
		
		if (count($articleAliasList) > 0)
		{
			$query->where('C.alias IN (' . join(',', array_map(array($db, 'Quote'), $articleAliasList)) . ')', 'OR');
		}
		
		if (count($articleNameList) > 0)
		{
			$query->where('C.title IN (' . join(',', array_map(array($db, 'Quote'), $articleNameList)) . ')', 'OR');
		}
		
		$db->setQuery($query);
		$articles = $db->loadAssocList();

		if ($db->getErrorNum())
		{
			return array();
		}
		
		$articlesTree = array(
			'alias' => array(),
			
			'id' => array(),
			
			'name' => array()
		);
		foreach ($articles as &$article)
		{
			$article['link'] = ContentHelperRoute::getArticleRoute($article['slug'], $article['catid']);
			
			$articlesTree['alias'][$article['alias']] =& $article;
			$articlesTree['id'][$article['id']] =& $article;
			$articlesTree['name'][$article['title']] =& $article;
		}
		
		$sortedArticles = array();
		
		foreach ($articleSeq as $seqItem)
		{
			$itemType = $seqItem['type'];
			$itemValue = $seqItem['value'];
			
			if (isset($articlesTree[$itemType][$itemValue]))
				$sortedArticles[] =& $articlesTree[$itemType][$itemValue];
		}
		
		return $sortedArticles;
	}
}