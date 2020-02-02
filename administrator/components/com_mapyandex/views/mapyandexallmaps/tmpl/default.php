<?php 
/*
 * @package Joomla 3.x
 * @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @component Yandex Map Component
 * @copyright Copyright (C) Aleksandr Ermakov www.slyweb.ru
 */
defined('_JEXEC') or die('Restricted access'); 
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('dropdown.init');
JHtml::_('formbehavior.chosen', 'select');
?>
<form action="<?php JRoute::_('index.php?option=com_mapyandex'); ?>" method="post" id="adminForm" name="adminForm">
<div id="j-sidebar-container" class="span2">
<?php echo JHtmlSidebar::render(); ?>
</div>
<div id="j-main-container" class="span10">
<div class="adminform">

	<div id="cpanel">
	
		<div class="clearfix">
		
		<div class="js-stools-container-bar">
			<div class="btn-wrapper input-append">
				<input type="text" placeholder="<?php echo JText::_('JSEARCH_FILTER_LABEL'); ?>" value="<?php echo $this->state->get('filter.search');?>" id="filter_search" name="filter_search">			
				<button title="" class="btn hasTooltip" type="submit" data-original-title="<?php echo JText::_('JSEARCH_FILTER_LABEL'); ?>">
					<i class="icon-search"></i>
				</button>
			</div>
			<div class="btn-wrapper">
				<button type="submit" class="btn"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
				<button type="button" class="btn" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
			</div>
		</div>
		</div>
<div id="editcell">
    <table class="table table-striped">
    <thead>
        <tr>
            <th width="5">
                <?php echo JText::_( 'COM_MAPYANDEX_ID' ); ?>
            </th>

            <th width="20">
			<input type="checkbox" name="checkall-toggle" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" value="" onclick="Joomla.checkAll(this);" />             
			
            </th>
			<th width="200">
                <?php echo JText::_( 'COM_MAPYANDEX_NAMEMAP' ); ?>
            </th>
            <th>
                <?php echo JText::_( 'COM_MAPYANDEX_DESCRIPTIONOFTAG' ); ?>
            </th>
			<th>
                <?php echo JText::_( 'COM_MAPYANDEX_HITS' ); ?>
            </th>
			<th>
                <?php echo JText::_( 'COM_MAPYANDEX_YMCODE' ); ?>
            </th>
			<th>
                <?php echo JText::_( 'COM_MAPYANDEX_TYPE' ); ?>
            </th>
			<th>
                <?php echo JText::_( 'COM_MAPYANDEX_TIME' ); ?>
            </th>
        </tr>            
    </thead>
    <?php
    $k = 0;
	
    for ($i=0, $n=count( $this->map ); $i < $n; $i++)
    {

        $row = $this->map[$i];
        $checked    = JHTML::_( 'grid.id', $i, $row->id );
		if($row->map_type == 'calculator') {
			$link = JRoute::_( 'index.php?option=com_mapyandex&view=mapyandexcalculator&layout=formedit&id='. $row->id );
		} else {
			$link = JRoute::_( 'index.php?option=com_mapyandex&view=map&layout=form&id='. $row->id );
		}
		
        ?>
        <tr class="<?php echo "row$k"; ?>">
		    <td>
                <?php echo $row->id; ?>
            </td>

            <td>
              <?php echo $checked; ?>
            </td>
			<td style="text-align:center;">
               <a href="<?php echo $link; ?>"><?php echo $row->name_map_yandex; ?></a> 
            </td>
            <td>
                <a href="<?php echo $link; ?>"><?php echo $row->misil; ?></a>
            </td>
			<td style="text-align:center;">
                <?php echo $row->hits; ?>
            </td>
			<td style="text-align:center;">
                <?php
				if($row->map_type == 'calculator') {
					echo '{mapyandex_calculator_id='.$row->id.'}';
				} else {
					echo '{mapyandex_id='.$row->id.'}';
				}
				
				?>
            </td>
			<td style="text-align:center;">
                <?php
				if($row->map_type == 'calculator') {
					echo JHTML::_('image', 'administrator/components/com_mapyandex/assets/images/calculator.png','','style="margin-bottom: 3px;"');
				} else {
					echo JHTML::_('image', 'administrator/components/com_mapyandex/assets/images/map.png','','style="margin-bottom: 3px;"');
				}
				
				?>
            </td>

			<td style="text-align:center;">

			<?php
			
				echo JHTML::_('date', $row->checked_out_time);
			
			?>
            </td>
        </tr>
        <?php
        $k = 1 - $k;
    }
    ?>
<tr>
	<td colspan="8"><?php echo $this->pageNav->getListFooter(); ?></td>
</tr>
    </table>

</div>
 </div>

 </div>
 </div>
<input type="hidden" name="option" value="com_mapyandex" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="view" value="mapyandexallmaps" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="controller" value="mapyandexallmaps" />

<?php echo JHtml::_('form.token'); ?>
</form>