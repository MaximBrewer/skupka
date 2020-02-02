<?php defined('_JEXEC') or die('Restricted access'); ?>

<?php
JHTML::_('behavior.tooltip');
JHTML::_('behavior.modal');

$user = JFactory::getUser();
$userId = $user->get('id');

$listOrder = $this->state->get('list.ordering');
$listDirn = $this->state->get('list.direction');

$canOrder = true; //$user->authorise('core.edit.state', 'com_contact.category');
$saveOrder = $listOrder == 'a.ordering';

$table = new UniteAdminTable($this->state);
$table->addFilter(UniteAdminTable::FILTER_TYPE_PUBLISHED);

$checkAllFunction = "checkAll(this)";
if (UniteFunctions::isJoomla3())
    $checkAllFunction = "Joomla.checkAll(this)";
?>

<form action="<?php echo JRoute::_('index.php?option=com_nivosliderpro&view=sliders'); ?>" method="post" name="adminForm" id="adminForm">

<?php
$table->putFilterBar();
?>

    <div class="clr"> </div>

    <table class="adminlist">
        <thead>
            <tr>
                <th width="1%">
                    <input type="checkbox" name="checkall-toggle" value="" onclick="<?php echo $checkAllFunction?>" />
                </th>
                <th>
<?php echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
                </th>
                <th width="8%">
                </th>								
                <th width="5%">
<?php echo JHtml::_('grid.sort', 'JPUBLISHED', 'a.published', $listDirn, $listOrder); ?>
                </th>
                <th width="10%">
                    <?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ORDERING', 'a.ordering', $listDirn, $listOrder); ?>
<?php if ($canOrder && $saveOrder) : ?>
                        <?php echo JHtml::_('grid.order', $this->items, 'filesave.png', 'items.saveorder'); ?>
                    <?php endif; ?>
                </th>
                <th width="1%">
                    <?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
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
foreach ($this->items as $i => $slider) :
    $ordering = ($listOrder == 'a.ordering');
    $canCreate = true;
    $canEdit = true;
    $canCheckin = true;
    $canEditOwn = true;
    $canChange = true;

    $sliderID = $slider->id;

    $urlSliderSettings = HelperNivoPro::getViewUrl_Slider($sliderID);
    $urlEditSlides = HelperNivoPro::getViewUrl_Items($sliderID);
    $title = $this->escape($slider->title);
    ?>
                <tr class="row<?php echo $i % 2; ?>">
                    <td class="center">
                <?php echo JHtml::_('grid.id', $i, $sliderID); ?>
                    </td>
                    <td>
                        <a href="<?php echo $urlSliderSettings ?>"><?php echo $title ?></a>
                        <p class="smallsub">
    <?php echo JText::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($slider->alias)); ?>
                        </p>
                    </td>
                    <td class="center">
                            <?php echo JHtml::link($urlEditSlides, JText::_('COM_NIVOSLIDERPRO_EDIT_SLIDES')) ?>
                    </td>
                    <td class="center">
                        <?php echo JHtml::_('jgrid.published', $slider->published, $i, 'sliders.', true, 'cb'); ?>
                    </td>
                    <td class="order">
                        <?php if ($canChange) : ?>
                            <?php if ($saveOrder) : ?>
            <?php if ($listDirn == 'asc') : ?>
                                    <span><?php echo $this->pagination->orderUpIcon($i, ($slider->ordering == @$this->items[$i - 1]->ordering), 'items.orderup', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
                                    <span><?php echo $this->pagination->orderDownIcon($i, $n, ($slider->ordering == @$this->items[$i + 1]->ordering), 'items.orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
                                <?php elseif ($listDirn == 'desc') : ?>
                                    <span><?php echo $this->pagination->orderUpIcon($i, ($slider->ordering == @$this->items[$i - 1]->ordering), 'items.orderdown', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
                                    <span><?php echo $this->pagination->orderDownIcon($i, $n, ($slider->ordering == @$this->items[$i + 1]->ordering), 'items.orderup', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
                                <?php endif; ?>
                            <?php endif; ?>
        <?php $disabled = $saveOrder ? '' : 'disabled="disabled"'; ?>
                            <input type="text" name="order[]" size="5" value="<?php echo $slider->ordering; ?>" <?php echo $disabled ?> class="text-area-order" />
                        <?php else : ?>
                            <?php echo $slider->ordering; ?>
                        <?php endif; ?>
                    </td>
                    <td align="center">
                        <?php echo $slider->id; ?>
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

<script type="text/javascript">

                        UniteAdmin.hideSystemMessageDelay();

</script>

<?php
HelperNivoPro::includeView("sliders/tmpl/footer.php");
?>
