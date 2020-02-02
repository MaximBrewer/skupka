<?php
JHTML::_('behavior.tooltip');
JHTML::_('behavior.modal');

$user = JFactory::getUser();
$userId = $user->get('id');
$listOrder = $this->state->get('list.ordering');
$listDirn = $this->state->get('list.direction');
$canOrder = true; //$user->authorise('core.edit.state', 'com_contact.category');
$saveOrder = $listOrder == 'a.ordering';
$checkAllFunction = "checkAll(this)";
if (UniteFunctions::isJoomla3())
    $checkAllFunction = "Joomla.checkAll(this)";
?>

<form action="<?php echo JRoute::_('index.php?option=com_nivosliderpro&view=items'); ?>" method="post" name="adminForm" id="adminForm">

    <input type="hidden" name="id" value="<?php echo $this->sliderID ?>">
    <input type="hidden" name="sliderid" value="<?php echo $this->sliderID ?>">

    <fieldset id="filter-bar">

        <div class="filter-select fltlft">

            <?php if (!empty($this->linkSliderSettings)): ?>
                <a href="<?php echo $this->linkSliderSettings ?>" id="link_slider_settings" class="button-primary"><?php echo JText::_('COM_NIVOSLIDERPRO_EDIT_SLIDER_SETTINGS') ?></a>
            <?php endif ?>

        </div>

        <div class="filter-select fltrt">

            <select name="filter_published" class="inputbox" onchange="this.form.submit()">
                <option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED'); ?></option>
                <?php echo JHtml::_('select.options', array(JHtml::_('select.option', '1', 'JPUBLISHED'), JHtml::_('select.option', '0', 'JUNPUBLISHED')), 'value', 'text', $this->state->get('filter.published'), true); ?>
            </select>

            <?php
            /*
              $options = array();
              $options[] = JHTML::_( 'select.option', '1', 'Item 1' );
              $options[] = JHTML::_( 'select.option', '2', 'Item 2' );
              $options[] = JHTML::_( 'select.option', '3', 'Item 3' );
              echo JHTML::_( 'select.genericlist', $options, 'drop-down' );
             */
            ?>

        </div>
    </fieldset>
    <div class="clr"> </div>

    <table class="adminlist">
        <thead>
            <tr>
                <th width="1%">
                    <input type="checkbox" name="checkall-toggle" value="" onclick="<?php echo $checkAllFunction?>" />
                </th>
                <th>
                    <?php echo JText::_('JGLOBAL_TITLE') ?>
                </th>
                <th width="240"> 
                    <?php echo JText::_('COM_NIVOSLIDERPRO_IMAGE'); ?>
                </th>				
                <th width="10%">
                    <?php echo JText::_('COM_NIVOSLIDERPRO_SLIDER'); ?>
                </th>								
                <th width="5%">
                    <?php echo JText::_('JPUBLISHED') ?>
                </th>
                <th width="10%">
                    <?php echo JText::_('JGRID_HEADING_ORDERING') ?>
                </th>
                <th width="1%">
                    <?php echo JText::_('JGRID_HEADING_ID') ?>
                </th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <td colspan="10">
                    <?php echo $this->pagination->getListFooter(); ?>
                </td>
            </tr>
        </tfoot>
        <tbody>
            <?php
            $n = count($this->items);

            foreach ($this->items as $i => $item) :
                $ordering = ($listOrder == 'a.ordering');
                $canCreate = true; //$user->authorise('core.create',		'com_contact.category.'.$item->catid);
                $canEdit = true; //$user->authorise('core.edit',			'com_contact.category.'.$item->catid);
                $canCheckin = true; //$user->authorise('core.manage',		'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
                $canEditOwn = true; //$user->authorise('core.edit.own',		'com_contact.category.'.$item->catid) && $item->created_by == $userId;
                $canChange = true; //$user->authorise('core.edit.state',	'com_contact.category.'.$item->catid) && $canCheckin;
                //get params
                $params = new JRegistry();
                $params->loadString($item->params, "json");

                $urlRoot = JURI::root();

                //get image url's:
                $image = $params->get("image");
                $thumbUrl = UniteFunctionJoomla::getImageOutputUrl($image, 200, 100, true);
                $imageUrl = $urlRoot . $image;

                $img_file = pathinfo($imageUrl, PATHINFO_BASENAME);

                $itemTitle = $item->title . " ($img_file)";
                ?>
                <tr class="row<?php echo $i % 2; ?>">
                    <td class="center">
    <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                    </td>
                    <td>
    <?php if ($item->checked_out) : ?>
                            <?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'items.', $canCheckin); ?>
                        <?php endif; ?>
                        <?php if ($canEdit || $canEditOwn) : ?>
                            <a href="<?php echo JRoute::_('index.php?option=com_nivosliderpro&task=item.edit&id=' . (int) $item->id); ?>">
                            <?php echo $this->escape($itemTitle); ?></a>
                            <?php else : ?>
                                <?php echo $this->escape($itemTitle); ?>
                        <?php endif; ?>
                        <p class="smallsub">
                        <?php echo JText::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias)); ?></p>
                    </td>
                    <td align="center">
    <?php if (!empty($imageUrl)) : ?>
                            <a class="modal" href="<?php echo $imageUrl ?>">
                                <img src="<?php echo $thumbUrl ?>" alt="slide image" />
                            </a>
    <?php endif; ?>
                    </td>
                    <td class="center">
    <?php echo $item->slider_name; ?>
                    </td>								
                    <td class="center">
    <?php echo JHtml::_('jgrid.published', $item->published, $i, 'items.', true, 'cb'); ?>
                    </td>
                    <td class="order">
    <?php if ($canChange) : ?>
                            <?php if ($saveOrder) : ?>
                                <?php if ($listDirn == 'asc') : ?>
                                    <span><?php echo $this->pagination->orderUpIcon($i, ($item->catid == @$this->items[$i - 1]->catid), 'items.orderup', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
                                    <span><?php echo $this->pagination->orderDownIcon($i, $n, ($item->catid == @$this->items[$i + 1]->catid), 'items.orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
            <?php elseif ($listDirn == 'desc') : ?>
                                    <span><?php echo $this->pagination->orderUpIcon($i, ($item->catid == @$this->items[$i - 1]->catid), 'items.orderdown', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
                                    <span><?php echo $this->pagination->orderDownIcon($i, $n, ($item->catid == @$this->items[$i + 1]->catid), 'items.orderup', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
            <?php endif; ?>
                            <?php endif; ?>
                            <?php $disabled = $saveOrder ? '' : 'disabled="disabled"'; ?>
                            <input type="text" name="order[]" size="5" value="<?php echo $item->ordering; ?>" <?php echo $disabled ?> class="text-area-order" />
                        <?php else : ?>
                            <?php echo $item->ordering; ?>
                        <?php endif; ?>
                    </td>
                    <td align="center">
    <?php echo $item->id; ?>
                    </td>
                </tr>
<?php endforeach; ?>
        </tbody>
    </table>

    <div>
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="boxchecked" value="0" />
        <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
        <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
<?php echo JHtml::_('form.token'); ?>
    </div>

</form>
