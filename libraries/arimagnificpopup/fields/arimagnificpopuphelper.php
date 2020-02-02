<?php
/*
 * ARI Framework
 *
 * @package		ARI Framework
 * @version		1.0.0
 * @author		ARI Soft
 * @copyright	Copyright (c) 2009 www.ari-soft.com. All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
 *
 */
defined('_JEXEC') or die;

require_once dirname(__FILE__) . '/../loader.php';

use \Arisoft\Joomla\Fields\Field as Field;

class JFormFieldArimagnificpopuphelper extends Field
{
    static private $isRegistered = false;

	public $type = 'Arimagnificpopuphelper';

    protected $hidden = true;

	protected function getLabel()
	{
		return '';
	}

	protected function getInput()
	{
        $this->registerAssets();

		return '';
	}

    private function registerAssets()
    {
        if (self::$isRegistered)
            return ;

        $hidePro = (bool)$this->form->getValue('hidePro', 'params');
        $jsOptions = array(
            'hidePro' => $hidePro
        );

        $doc = JFactory::getDocument();

        JHtml::_('jquery.framework', true);
        $doc->addScript(JURI::root(true) . '/media/arimagnificpopup/fields/arimagnificpopuphelper/helper.js');
        $doc->addScriptDeclaration(
            sprintf(
                ';jQuery(function($) { var helper = new ARIMagnificPopupFieldsHelper(%1$s); helper.init(); });',
                json_encode($jsOptions)
            )
        );

        self::$isRegistered = true;
    }
}