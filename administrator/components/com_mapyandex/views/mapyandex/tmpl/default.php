<?php defined('_JEXEC') or die('Restricted access');

$document	=  JFactory::getDocument();
$document->addScript('/administrator/components/com_mapyandex/assets/js/ajax-key.js');

?>

<form action="index.php" method="post" id="adminForm" name="adminForm">
<div id="j-sidebar-container" class="span2">
<?php echo JHtmlSidebar::render(); ?>
</div>
<div id="j-main-container" class="span10">

<div class="adminform">
<div class="pga-cpanel-left">
	<div id="cpanel">
		<?php
		
		$link = 'index.php?option=com_mapyandex';
		echo $this->MapYandexRenderAdmin->quickIconButton( $link, 'icon-48-mapyandex.png', JText::_('COM_MAPYANDEX_HOME_PAGE') );
		
		$link = 'index.php?option=com_mapyandex&view=mapyandexallmaps';
		echo $this->MapYandexRenderAdmin->quickIconButton( $link, 'icon-48-mapyandexall.png', JText::_('COM_MAPYANDEX_ALLMAPS') );
		
		$link = 'index.php?option=com_mapyandex&view=mapyandexallmaps&layout=form';
		echo $this->MapYandexRenderAdmin->quickIconButton( $link, 'sample-48.png', JText::_( 'COM_MAPYANDEX_YANDEXNEWMAP' ) );
		$link = 'index.php?option=com_mapyandex&view=mapyandexmetki';
		echo $this->MapYandexRenderAdmin->quickIconButton( $link, 'icon-48-mapyandexe.png', JText::_( 'COM_MAPYANDEX_NEWYMAR' ) );
		
		$link = 'index.php?option=com_mapyandex&view=mapyandexcalculator';
		echo $this->MapYandexRenderAdmin->quickIconButton( $link, 'mapcalculator-48.png', JText::_( 'COM_MAPYANDEX_CALCULATOR' ) );

		$link = 'index.php?option=com_mapyandex&view=mapyandexregion';
		echo $this->MapYandexRenderAdmin->quickIconButton( $link, 'regions-48.png', JText::_( 'COM_MAPYANDEX_REGIONS' ) );

		$link = 'index.php?option=com_mapyandex&view=mapyandexroute';
		echo $this->MapYandexRenderAdmin->quickIconButton( $link, 'routes-48.png', JText::_( 'COM_MAPYANDEX_ROUTES' ) );
		
		$link = 'index.php?option=com_mapyandex&view=mapyandexdoc';
		echo $this->MapYandexRenderAdmin->quickIconButton( $link, 'icon-48-mapyandexdoc.png', JText::_( 'COM_MAPYANDEX_DOC' ) );
		?>
				
		<div style="clear:both">&nbsp;</div>
		<p>&nbsp;</p>
		
		<form>
			<label for="name"><?php echo JText::_( 'COM_MAPYANDEX_KEY' );?></label>
			<input type="text" id="yandex-key" name="key" value="<?php echo $this->key;?>"></input>
		<?php
		echo '<div id="pg-update-save"><a href="http://www.slyweb.ru/yandexmap/version/index.php?version='.  $this->MapYandexRenderInfo->getYndexMapVersion() .'" target="_blank">'.  JText::_('COM_MAPYANDEX_KEY_SAVE') .'</a></div>';
		?>
		<div id="pg-update-save-message"></div>
		
		<?php
		if(!$this->key) {
			JError::raiseError( 4711, 'You need yandex api key. <br/> Link https://developer.tech.yandex.ru/services/. See input below.' );
			
		}
			$MapYandex = JPluginHelper::getPlugin('content', 'mapyandex');
			if(!$MapYandex) {
				echo '<hr><p>'.JText::_('COM_MAPYANDEX_PLUGIN_NOT_ENABLED').'</p>';
			}
		?>
		</form>
		
		
		
	</div>
</div>
		
<div class="pga-cpanel-right">
	<div class="well">
		<div style="float:right;margin:10px;">
			<?php echo JHTML::_('image', 'administrator/components/com_mapyandex/assets/images/logo-slyweb.png', 'slyweb.ru' );?>
		</div>
			
		<?php

		echo '<h3>'.  JText::_('COM_MAPYANDEX_VERSION').'</h3>'
		.'<p>'.  $this->MapYandexRenderInfo->getYndexMapVersion() .'</p>';

		echo '<h3>'.  JText::_('COM_MAPYANDEX_YM_VERSION').'</h3>'
		.'<p>2.0.10</p>';

		echo '<h3>'.  JText::_('COM_MAPYANDEX_COPYRIGHT').'</h3>'
		.'<p>© 2012 - '.  date("Y"). ' Aleksandr Ermakov</p>'
		.'<p><a href="http://www.slyweb.ru/" target="_blank">www.slyweb.ru</a></p>';

		echo '<h3>'.  JText::_('COM_MAPYANDEX_LICENCE').'</h3>'
		.'<p><a href="http://www.gnu.org/licenses/gpl-2.0.html" target="_blank">GPLv2</a></p>';
		
	
		?>

		
		<p>Yandex™, Yandex Maps™, are registered trademarks of Yandex Inc.</p>
		
		<?php
		echo '<div style="border-top:1px solid #c2c2c2"></div>'
.'<div id="pg-update"><a href="http://www.slyweb.ru/yandexmap/version/index.php?version='.  $this->MapYandexRenderInfo->getYndexMapVersion() .'" target="_blank">'.  JText::_('COM_MAPYANDEX_CHECK_FOR_UPDATE') .'</a></div>';
		?>
		
	</div>
</div>

</div>
<input type="hidden" name="task" value="" />
<input type="hidden" name="option" value="com_mapyandex" />
<input type="hidden" name="view" value="mapyandexcp" />
<?php echo JHtml::_('form.token'); ?>

</div>
</form>