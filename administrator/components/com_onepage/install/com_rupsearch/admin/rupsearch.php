<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_content
 * @copyright	Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
 
defined('_JEXEC') or die;

$css = '
div.subhead-collapse {
display: none; 
}
header { display: none; }
img.logo { display: none; }
'; 
JFactory::getDocument()->addStyleDeclaration($css); 
$db = JFactory::getDBO(); 

$qt = "CREATE TABLE IF NOT EXISTS `#__com_rupsearch_stats` (
  `keyword` varchar(1000) CHARACTER SET utf8 NOT NULL,
  `md5` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `count` bigint(20) NOT NULL,
  `accessstamp` int(11) NOT NULL DEFAULT '0',
  UNIQUE KEY `md5` (`md5`),
  KEY `count` (`count`),
  KEY `accessstamp` (`accessstamp`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1; "; 
$prefix = $db->getPrefix();
		
		$table = '#__com_rupsearch_stats'; 
   $table = str_replace('#__', '', $table); 
   $table = str_replace($prefix, '', $table); 
 $q = "SHOW TABLES LIKE '".$db->getPrefix().$table."'";
	 $db->setQuery($q);
	  $r = $db->loadResult();
	   if (empty($r)) 
	   {
	     $db->setQuery($qt); 
		 $db->query(); 
	   }

$q = 'select * from #__com_rupsearch_stats order by `count` desc limit 0,500'; 
$db->setQuery($q); 
$res = $db->loadAssocList(); 
?><h1>Top 500 keywords</h1>
<?php
if (!empty($res))
{
	?>
	<table class="adminTable table">
	<?php
	foreach ($res as $row)
	{
		?><tr><td><?php echo $row['keyword']; ?></td><td><?php echo $row['count']; ?></td></tr>
		<?php
	}
	?>
	</table>
	<?php
}