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

class JFormFieldLiteral extends Field
{
    static private $isRegistered = false;

    public $type = 'Section';

    protected function getInput()
    {
        $class = $this->get('class');

        $this->registerAssets();

        return '<div class="ari-literal' . ($class ? ' ' . $class : '') . '">' . $this->prepareMessage($this->value) . '</div>';
    }

    protected function registerAssets()
    {
        if (self::$isRegistered)
            return ;

        JFactory::getDocument()->addStyleDeclaration(
            '.ari-literal {margin-top: 5px;font-style: italic;}'
        );

        self::$isRegistered = true;
    }
}