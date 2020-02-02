<?php
/*
 * ARI Magnific Popup
 *
 * @package		ARI Magnific Popup
 * @version		1.0.0
 * @author		ARI Soft
 * @copyright	Copyright (c) 2010 www.ari-soft.com. All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
 * 
 */

namespace Arimagnificpopup\Plugin\Contenttype\Engine;

defined('_JEXEC') or die;

use Arimagnificpopup\Plugin\Contenttype\Base As EngineBase;
use Joomla\Utilities\ArrayHelper as ArrayHelper;
use Arisoft\Utilities\Utilities as Utilities;

class Inline extends EngineBase
{
    protected function prepareParams($attrs, $params, $content)
    {
        $title = ArrayHelper::getValue($attrs, 'title', '');

        if ($title)
            $params->set('inline_title', $title);

        $link = Utilities::extractContent($content, 'link');
        $content = Utilities::extractContent($content, 'content');

        $params->set('inline_link', $link);
        $params->set('inline_content', $content);

        return $params;
    }
}