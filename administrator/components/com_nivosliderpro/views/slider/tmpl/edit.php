<?php
// No direct access.
defined('_JEXEC') or die;

JHtml::_('behavior.tooltip');
JHtml::_('behavior.switcher');
JHtml::_('behavior.formvalidation');

// Load submenu template, using element id 'submenu' as needed by behavior.switcher
if (UniteFunctions::isJoomla3()) {
    echo $this->loadTemplate('navigation');
} else {
    $this->document->setBuffer($this->loadTemplate('navigation'), 'modules', 'submenu');
}
$sliderID = $this->item->id;

try {
    ?>
    <div id="element-box">
        <?php
        if (UniteFunctions::isJoomla3()) {
            echo ' <div class="m">';
        }
        ?>
        <script type="text/javascript">
            Joomla.submitbutton = function(task)
            {
                if (task == 'slider.cancel' || document.formvalidator.isValid(document.id('slider-form'))) {
                    Joomla.submitform(task, document.getElementById('slider-form'));
                }
                else {
                    alert('<?php echo $this->escape(JText::_('COM_NIVOSLIDERPRO_VALIDATION_FORM_FAILED')); ?>');
                }
            }
        </script>

        <form action="<?php echo JRoute::_('index.php?option=com_nivosliderpro&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="slider-form" class="form-validate">

            <div id="config-document">
                <div id="page-general" class="tab">
                    <div class="noshow">
                        <?php echo $this->loadTemplate('general'); ?>
                    </div>
                </div>
                <div id="page-visual" class="tab hidden">
                    <div class="noshow">
                        <?php echo $this->loadTemplate('visual'); ?>
                    </div>
                </div>

                <?php
                HelperNivoPro::includeView("sliders/tmpl/footer.php");
                ?>

            </div>


            <?php /*
              if(!empty($sliderID)):?>
              <div class='slider_edit_slides'>
              <?php
              $urlEdit = HelperNivoPro::getViewUrl_Items($this->item->id);
              echo JHtml::link($urlEdit, JText::_('COM_NIVOSLIDERPRO_EDIT_SLIDES'));
              ?>
              </div>
              <?php endif; */
            ?>


            <?php echo JHtml::_('sliders.end'); ?>		
            <input type="hidden" name="task" value="" />
            <?php echo JHtml::_('form.token'); ?>

        </form>

        <script type="text/javascript">
    <?php
    if (UniteFunctions::isJoomla3()) {
        echo 'jQuery.curCSS = jQuery.css;';
    }
    ?>
            jQuery("document").ready(function() {
                UniteNivoPro.initSliderView();
            });

        </script>
        <?php
        if (UniteFunctions::isJoomla3()) {
            echo '</div>';
        }
        ?>

    </div>
    </div>
    <div class="clr"></div>
    <div id="div_debug"></div>
    <?php
} catch (Exception $e) {
    //show system error
    $message = $e->getMessage();
    ?>
    <script type="text/javascript">
        jQuery(document).ready(function() {
            UniteNivoPro.showSliderViewError("<?php echo $message ?>");
        });
    </script>
    <?php
}
?>