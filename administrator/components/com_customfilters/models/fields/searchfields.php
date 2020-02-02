<?php
/**
 * @package customfilters
 * @version $Id: fields/searchfields.php  2014-8-12 sakisTerzis $
 * @author Sakis Terzis (sakis@breakDesigns.net)
 * @copyright	Copyright (C) 2012-2018 breakDesigns.net. All rights reserved
 * @license	GNU/GPL v2
 */
defined('JPATH_BASE') or die();

jimport('joomla.html.html');
jimport('joomla.access.access');
jimport('joomla.form.formfield');

/**
 *
 * Class that generates a filter list
 *
 * @author Sakis Terzis
 */
class JFormFieldSearchfields extends JFormField
{

    public $searchfields = array(
        'p.product_sku',
        'p.product_gtin',
        'p.product_mpn',
        'l.product_name',
        'l.product_s_desc',
        'l.product_desc',
        'l.metadesc',
        'l.metakey',
        'catlang.category_name',
        'mflang.mf_name',
        'custom'
    );

    /**
     * (non-PHPdoc)
     * @see JFormField::getInput()
     */
    protected function getInput()
    {
        $language = JFactory::getLanguage();
        $language->load('com_virtuemart', JPATH_BASE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_virtuemart');

        ob_start();
        foreach ($this->searchfields as $key => $searchfield) {
            $isprefixed = strpos($searchfield, '.');
            $checked = '';
            // if that setting has no selection. Possibly visited for 1st time
            if (empty($this->value)) {
                $this->value = array(
                    'l.product_name',
                    'l.product_s_desc',
                    'catlang.category_name',
                    'mflang.mf_name',
                    'custom'
                );
            }

            if ($isprefixed)
                $searchfield_unprefixed = substr($searchfield, $isprefixed + 1);
            else
                $searchfield_unprefixed = $searchfield;
            $label = JText::_('COM_VIRTUEMART_' . strtoupper($searchfield_unprefixed));

            if (in_array($searchfield, $this->value))
                $checked = 'checked';
            ?>
<li class="cf_list_param"><input id="<?php echo $key?>" type="checkbox"
	name="jform[keyword_searchfield][]" value="<?php echo $searchfield?>"
	<?php echo $checked;?> /> <label for="<?php echo $key?>"><span
		class="cf_label"><?php echo $label;?></span></label></li>
<?php
        }
        $output = ob_get_contents();
        ob_end_clean();
        $html = '<fieldset class="checkboxes"><ul class="list-group row">' . $output . '</ul></fieldset>';
        return $html;
    }
}