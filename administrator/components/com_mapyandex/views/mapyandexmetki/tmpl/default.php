<?php 
/*
 * @package Joomla 3.x
 * @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @component Yandex Map Component
 * @copyright Copyright (C) Aleksandr Ermakov www.slyweb.ru
 */
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.multiselect');
JHtml::_('dropdown.init');
JHtml::_('formbehavior.chosen', 'select');
 ?>
<form action="<?php echo JRoute::_('index.php?option=com_mapyandex&view=mapyandexmetki'); ?>" method="post" name="adminForm" id="adminForm">
<div id="j-sidebar-container" class="span2">
<?php echo JHtmlSidebar::render(); ?>
</div>
<div id="j-main-container" class="span10">
<div class="adminform">
<div class="pga-cpanel">
	<div id="cpanel">
	
	<fieldset id="filter-bar">
		<div class="filter-search btn-group pull-left">
			<label class="element-invisible" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_MAPYANDEX_SEARCH_IN_TITLE'); ?>" />
			</div>
		<div class="btn-group pull-left hidden-phone">
		<button class="btn tip hasTooltip" type="submit" title="<?php JText::_($txtFs);?>"><i class="icon-search"></i></button>
		<button class="btn tip hasTooltip" type="button" onclick="document.id('filter_search').value='';this.form.submit();"
		 title="<?php JText::_($txtFc);?>"><i class="icon-remove"></i></button>
		</div>
		<div class="btn-group pull-right hidden-phone">
			<?php echo $this->pageNav->getLimitBox(); ?>
		</div>
	</fieldset>
	
	
<div id="editcell">
    <table class="table table-striped">
    <thead>
        <tr>
            <th width="5">
                <?php echo JText::_( 'COM_MAPYANDEX_ID' ); ?>
            </th>

            <th width="20">
              <input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->foobar ); ?>);" />
            </th>
			<th width="200">
                <?php echo JText::_( 'COM_MAPYANDEX_NAMEMARKER' ); ?>
            </th>
            <th>
                <?php echo JText::_( 'COM_MAPYANDEX_TEXTMAR' ); ?>
            </th>
						<th>
                <?php echo JText::_( 'COM_MAPYANDEX_WHERE' ); ?>
            </th>

			<th>
				<?php echo JText::_( 'COM_MAPYANDEX_ICONMARKER' ); ?>

            </th>
				<th>
                <?php echo JText::_( 'COM_MAPYANDEX_WHO' ); ?>
            </th>
            </th>
				<th>
                <?php echo JText::_( 'COM_MAPYANDEX_IMG' ); ?>
            </th>
			<th>
                <?php echo JText::_( 'COM_MAPYANDEX_TIME' ); ?>
            </th>

        </tr>            
    </thead>
    <?php

    $k = 0;
    for ($i=0, $n=count( $this->foobar ); $i < $n; $i++)
    {
        $row =& $this->foobar[$i];
        $checked    = JHTML::_( 'grid.id', $i, $row->id );
        $link = JRoute::_( 'index.php?option=com_mapyandex&view=mapyandexmetki&layout=formedit&id='. $row->id );
 
        ?>
        <tr class="<?php echo "row$k"; ?>">
		    <td>
                <?php echo $row->id; ?>
            </td>
            <td>
              <?php echo $checked; ?>
            </td>
			<td>
               <a href="<?php echo $link; ?>"><?php echo $row->name_marker; ?></a> 
            </td>
            <td>
                <a href="<?php echo $link; ?>"><?php echo (!empty($row->misil)) ? $row->misil : JText::_( 'COM_MAPYANDEX_NO_DESCRIPTION' ); ?></a>
            </td>

			<td style="text-align:center;">
				<a href="<?php echo 'index.php?option=com_mapyandex&view=map&layout=form&id='.$row->id_map;?>" target="_blank"><?php echo $row->id_map;?></a>
            </td>
						<td style="text-align:center;">
				<style type="text/css">
							/* Tooltips */
				.nofon {
				   border-radius:6px;
				   background: #fff;
				   border: 1px solid #D4D5AA;
				   padding: 10px;
				   max-width: 200px;
				}

				
				</style>
			<?php if($row->deficon !=='') {?>
                <?php 			
				
				echo JHTML::tooltip(JHTML::_('image', 'administrator/components/com_mapyandex/assets/images/deficon/'.$row->deficon.'.png','','style="margin-bottom: 3px;" class="nofon"'), $title = '', JURI::root(true).'administrator/components/com_mapyandex/assets/images/icon-16-search.png', JHTML::_('image', 'administrator/components/com_mapyandex/assets/images/icon-16-search.png','','style="margin-bottom: 3px;"'), $href = '',  $alt = 'Такой будет ваша метка!',$class = 'hasTip'); 
				
				} 
				else { 
				echo JHTML::_('image', 'administrator/components/com_mapyandex/assets/images/deficon/whiteSmallPoint.png','','style="width:19px; height:20px; margin-bottom: 3px;"'); }?>
            </td>
			<td style="text-align:center;">
			<?php 	($row->whoadd == 0) ? $row->username = 'Это Вы!' : $row->username = $row->username; 

			if((int)$row->whoadd !==0) {
			if($row->username) { 
				$row->username = '('.$row->username.')';
			} else {
				$row->username = '';
			}
			?>
				<a href="<?php echo JURI::root(true).'/administrator/index.php?option=com_users&view=user&layout=edit&id='.$row->whoadd; ?>"><?php echo 'id - '.$row->whoadd.' '.$row->username; ?></a>
            <?php
			} else { 
				echo $row->username; ?></a>
			<?php
				}  
			?>
			</td>
			
			<td style="text-align:center;">

			<?php if($row->deficon !=='') {?>
                <?php 			
				$userimg = json_decode($row->userimg);
				$userpath = $this->params->get('userpathtoimg');
				
				if(empty($userpath)) {

					$userpath = '/images/mapyandex/yandexmarkerimg/';
				}
				
				if(strpos($userpath,'/') == 0) {
					$userpath = substr($userpath,1);
				
				}
				if($userimg) {
					if(is_file($_SERVER['DOCUMENT_ROOT'].JURI::root(true).'/'.$userpath.$userimg->smallfile)) {
					
						echo JHTML::_('image',$userpath.$userimg->smallfile,'','style="margin-bottom: 3px;"');
					} else {
						echo JHTML::_('image', 'administrator/components/com_mapyandex/assets/images/noimg.jpg','','style="margin-bottom: 3px;"');
					}
				} else {
						echo JHTML::_('image', 'administrator/components/com_mapyandex/assets/images/noimg.jpg','','style="margin-bottom: 3px;"');
					}
				} 
				else { 
					echo JHTML::_('image', 'administrator/components/com_mapyandex/assets/images/deficon/whiteSmallPoint.png','','style="width:19px; height:20px; margin-bottom: 3px;"'); 
				}?>
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
	<td colspan="9"><?php echo $this->pageNav->getListFooter(); ?></td>
</tr>
    </table>

</div>
 </div>
 </div>
 </div>
 </div>
<input type="hidden" name="controller" value="mapyandexmetki" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="view" value="mapyandexmetki" />
<input type="hidden" name="boxchecked" value="0" />
<?php echo JHtml::_('form.token'); ?>
</form>