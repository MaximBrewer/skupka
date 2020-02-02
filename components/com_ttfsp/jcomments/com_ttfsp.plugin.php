<?php
/**
 * JComments plugin for TT FS+ support
 *
 * @version 2.0
 * @package JComments
 * @author Sergey M. Litvinov (smart@joomlatune.ru)
 * @copyright (C) 2006-2012 by Sergey M. Litvinov (http://www.joomlatune.ru)
 * @license GNU/GPL: http://www.gnu.org/copyleft/gpl.html
 **/

class jc_com_ttfsp extends JCommentsPlugin
{
	function getObjectTitle($id)
	{
		$db = JCommentsFactory::getDBO();
		$db->setQuery('SELECT name, id FROM #__ttfsp_spec WHERE id=' . $id);
		return $db->loadResult();
	}
 
	function getObjectLink($id)
	{
		$_Itemid = self::getItemid( 'com_ttfsp' );
		if (JCOMMENTS_JVERSION == '1.0') {
			$link = sefRelToAbs( 'index.php?option=com_ttfsp&amp;view=detail&amp;idspec=' . $id . '&amp;Itemid=' . $_Itemid );
		} else {
			$link = JRoute::_('index.php?option=com_ttfsp&amp;view=detail&amp;idspec=' . $id . '&amp;Itemid=' . $_Itemid );
		}
		return $link;
	}

	function getObjectOwner($id)
	{
		$db = JCommentsFactory::getDBO();
		$db->setQuery('SELECT id FROM #__ttfsp_spec WHERE id = ' . $id);
		$userid = $db->loadResult();
		return $userid;
	}
}
?>