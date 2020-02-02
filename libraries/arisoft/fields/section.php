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

class JFormFieldSection extends Field
{
	public $type = 'Section';

	protected function getLabel()
	{
		return '';
	}

	protected function getInput()
	{
		$title = $this->get('label');
		$description = $this->get('description');
		$class = $this->get('class');
		$open = (bool)$this->get('open', 0);
		$close = (bool)$this->get('close', 0);

		$html = array('</div>');

		if ($open || !$close)
		{
			$html[] = '</div>';
			$html[] = '<div class="well well-small ari-section ' . $class . '">';

			if ($title)
			{
				$html[] = '<h4>' . $this->prepareMessage($title) . '</h4>';
			}

			if ($description)
			{
				$html[] = '<div>' . $this->prepareMessage($description) . '</div>';
			}

			$html[] = '<div><div>';
		}

		if (!$open && !$close)
		{
			$html[] = '</div>';
		}

		return join('', $html);
	}
}