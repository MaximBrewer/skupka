<?php
/**
 * @name		Accordeon Menu CK params
 * @package		com_accordeonck
 * @copyright	Copyright (C) 2014. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author		Cedric Keiflin - http://www.template-creator.com - http://www.joomlack.fr
 */

defined('_JEXEC') or die;
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
$hasmaximenumodule = JFile::exists(JPATH_ROOT . '/modules/mod_accordeonck/mod_accordeonck.php');
?>
<div class="btn-wrapper"><ol class="sortable ckmenuselectsortable"><li data-type="separator" data-title="separator"><a class="btn"><?php echo JText::_('COM_MENUMANAGERCK_ADDSEPARATOR') ?><i class="icon-chevron-right"></i></a></li></ol></div>
<div class="btn-wrapper"><ol class="sortable ckmenuselectsortable"><li data-type="heading" data-title="heading"><a class="btn"><?php echo JText::_('COM_MENUMANAGERCK_ADDHEADING') ?><i class="icon-chevron-right"></i></a></li></ol></div>
<div class="btn-wrapper"><ol class="sortable ckmenuselectsortable"><li data-type="url" data-title="url"><a class="btn"><?php echo JText::_('COM_MENUMANAGERCK_ADDEXTERNALURL') ?><i class="icon-chevron-right"></i></a></li></ol></div>
<div class="btn-wrapper alias"><ol class="sortable"><li data-type="alias" data-title="alias"><a class="btn btn-info modal" href="index.php?option=com_menumanagerck&amp;view=list&amp;tmpl=component&amp;function=jSelectAliasck" rel="{handler: 'iframe', size: {x: 800, y: 450}}"><?php echo JText::_('COM_MENUMANAGERCK_ADDALIAS') ?><i class="icon-new"></i></a></li></ol></div>
<?php if ($hasmaximenumodule) { ?><div class="btn-wrapper module"><ol class="sortable"><li data-type="module" data-title="module"><a class="btn btn-info modal" href="index.php?option=com_menumanagerck&amp;layout=default&amp;view=modules&amp;tmpl=component&amp;function=jSelectModuleck" rel="{handler: 'iframe', size: {x: 800, y: 450}}"><?php echo JText::_('COM_MENUMANAGERCK_ADDMODULE') ?><i class="icon-new"></i></a></li></ol></div><?php } ?>

<div id="collapseTypes" class="accordion">
	<div class="accordion-group">
		<div class="accordion-heading">
			<strong>
			<a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#collapseTypes" href="#collapse0"><?php echo JText::_('COM_CONTENT') ?></a>
			</strong>
		</div>
		<div id="collapse0" class="accordion-body collapse" style="height: 0px;">
			<div class="accordion-inner">
				<div class="btn-wrapper createarticle"><ol class="sortable"><li data-type="component" data-title=""><a class="btn modal" href="index.php?option=com_menumanagerck&amp;task=article.add&amp;tmpl=component&amp;function=jSelectArticleck&attribs=0&return=create" rel="{handler: 'iframe', size: {x: 800, y: 450}}"><?php echo JText::_('COM_MENUMANAGERCK_CREATE_NEW__ARTICLE') ?><i class="icon-new"></i></a></li></ol></div>
				<div class="btn-wrapper article"><ol class="sortable"><li data-type="component" data-title=""><a class="btn btn-info modal" href="index.php?option=com_content&amp;layout=modal&amp;view=articles&amp;tmpl=component&amp;function=jSelectArticleck&attribs=0" rel="{handler: 'iframe', size: {x: 800, y: 450}}"><?php echo JText::_('com_content_article_view_default_title') ?><i class="icon-new"></i></a></li></ol></div>
				<div class="btn-wrapper category"><ol class="sortable"><li data-type="component" data-title=""><a class="btn btn-info modal" href="index.php?option=com_menumanagerck&task=custom.select&extension=content&type=category&layout=modal&tmpl=component&function=jSelectContentCategoryck" rel="{handler: 'iframe', size: {x: 800, y: 450}}"><?php echo JText::_('com_content_category_view_default_title') ?><i class="icon-new"></i></a></li></ol></div>
				<div class="btn-wrapper categoryblog"><ol class="sortable"><li data-type="component" data-title=""><a class="btn btn-info modal" href="index.php?option=com_menumanagerck&task=custom.select&extension=content&type=category&layout=modal&tmpl=component&function=jSelectCategoryBlogck" rel="{handler: 'iframe', size: {x: 800, y: 450}}"><?php echo JText::_('com_content_category_view_blog_title') ?><i class="icon-new"></i></a></li></ol></div>
			</div>
		</div>
	</div>
<?php // com_k2
if (JFile::exists(JPATH_ROOT . '/components/com_k2/k2.php')) 
	{ ?>
	<div class="accordion-group">
		<div class="accordion-heading">
			<strong>
			<a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#collapseTypes" href="#collapse1"><?php echo JText::_('K2') ?></a>
			</strong>
		</div> 
		<div id="collapse1" class="accordion-body collapse" style="height: 0px;">
			<div class="accordion-inner">
				<div class="btn-wrapper k2"><ol class="sortable"><li data-type="component" data-title=""><a class="btn btn-info modal" href="index.php?option=com_k2&view=items&task=element&tmpl=component&object=k2" rel="{handler: 'iframe', size: {x: 800, y: 450}}"><?php echo JText::_('COM_MENUMANAGERCK_ITEM') ?><i class="icon-new"></i></a></li></ol></div> 
				<div class="btn-wrapper id"><ol class="sortable"><li data-type="component" data-title=""><a class="btn btn-info modal" href="index.php?option=com_k2&view=categories&task=element&tmpl=component&object=k2" rel="{handler: 'iframe', size: {x: 800, y: 450}}"><?php echo JText::_('JCATEGORY') ?><i class="icon-new"></i></a></li></ol></div> 
			</div>
		</div>
	</div>
	<?php 
	} 
?>
<?php // com_jshopping
if (JFile::exists(JPATH_ROOT . '/components/com_jshopping/jshopping.php')) 
	{ 
		$jshoppingitem_attribs = base64_encode(json_encode(Array("controller" => "product", "category_id" => null, "product_id" => null)));
		$jshoppingcategory_attribs = base64_encode(json_encode(Array("controller" => "products", "category_id" => null)));
		?>
	<div class="accordion-group">
		<div class="accordion-heading">
			<strong>
			<a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#collapseTypes" href="#collapse2"><?php echo JText::_('JoomShopping') ?></a>
			</strong>
		</div> 
		<div id="collapse2" class="accordion-body collapse" style="height: 0px;">
			<div class="accordion-inner">
				<div class="btn-wrapper jshoppingitem"><ol class="sortable"><li data-type="component"><a class="btn btn-info modal" href="index.php?option=com_menumanagerck&task=custom.select&extension=jshopping&type=item&layout=modal&tmpl=component&function=jSelectItemck&object=jshoppingitem&attribs=<?php echo $jshoppingitem_attribs ?>" rel="{handler: 'iframe', size: {x: 800, y: 450}}"><?php echo JText::_('COM_MENUMANAGERCK_PRODUCT') ?><i class="icon-new"></i></a></li></ol></div> 
				<div class="btn-wrapper jshoppingcategory"><ol class="sortable"><li data-type="component"><a class="btn btn-info modal" href="index.php?option=com_menumanagerck&task=custom.select&extension=jshopping&type=category&layout=modal&tmpl=component&function=jSelectItemck&object=jshoppingcategory&attribs=<?php echo $jshoppingcategory_attribs ?>&<?php echo JSession::getFormToken() ?>=1" rel="{handler: 'iframe', size: {x: 800, y: 450}}"><?php echo JText::_('JCATEGORY') ?><i class="icon-new"></i></a></li></ol></div> 
			</div>
		</div>
	</div>
		<?php 
	}
?>
<?php // com_hikashop
if (JFile::exists(JPATH_ROOT . '/components/com_hikashop/hikashop.php')) 
	{ 
		$hikashopitem_attribs = base64_encode(json_encode(Array("view" => "product", "layout" => "show", "product_id" => null)));
		$hikashopcategory_attribs = base64_encode(json_encode(Array("view" => "product", "layout" => "listing", "category_id" => null)));
		?>
	<div class="accordion-group">
		<div class="accordion-heading">
			<strong>
			<a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#collapseTypes" href="#collapse3"><?php echo JText::_('Hikashop') ?></a>
			</strong>
		</div> 
		<div id="collapse3" class="accordion-body collapse" style="height: 0px;">
			<div class="accordion-inner">
				<div class="btn-wrapper hikashopitem"><ol class="sortable"><li data-type="component"><a class="btn btn-info modal" href="index.php?option=com_menumanagerck&task=custom.select&extension=hikashop&type=item&layout=modal&tmpl=component&function=jSelectItemck&object=hikashopitem&attribs=<?php echo $hikashopitem_attribs ?>" rel="{handler: 'iframe', size: {x: 800, y: 450}}"><?php echo JText::_('COM_MENUMANAGERCK_PRODUCT') ?><i class="icon-new"></i></a></li></ol></div> 
				<div class="btn-wrapper hikashopcategory"><ol class="sortable"><li data-type="component"><a class="btn btn-info modal" href="index.php?option=com_menumanagerck&task=custom.select&extension=hikashop&type=category&layout=modal&tmpl=component&function=jSelectItemck&object=hikashopcategory&attribs=<?php echo $hikashopcategory_attribs ?>&<?php echo JSession::getFormToken() ?>=1" rel="{handler: 'iframe', size: {x: 800, y: 450}}"><?php echo JText::_('JCATEGORY') ?><i class="icon-new"></i></a></li></ol></div> 
			</div>
		</div>
	</div>
		<?php 
	}
?>
</div>
