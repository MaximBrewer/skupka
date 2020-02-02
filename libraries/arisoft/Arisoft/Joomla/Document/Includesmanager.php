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
namespace Arisoft\Joomla\Document;

defined('_JEXEC') or die;

use JFactory;

class Includesmanager
{
	private $initState = null;
	
	function __construct($saveInitState = true)
	{
		if ($saveInitState) 
			$this->saveInitState();
	}
	
	public function saveInitState()
	{
		$this->initState = $this->getCurrentState();
	}

	public function getInitState()
	{
		return $this->initState;
	}
	
	public function getCurrentState()
	{
		$currentState = array();
		$document = JFactory::getDocument();
		if ($document->getType() != 'html') 
			return $currentState; 

		$currentState = $document->getHeadData();

		return $currentState;
	}
	
	public function deleteState()
	{
		$this->initState = null;
	}
	
	public function getDifferences($deleteState = true)
	{
		$differences = array();
		$initState = $this->getInitState();

		$currentState = $this->getCurrentState();
		if ($currentState)
		{
			if (!empty($currentState['styleSheets']))
			{
				foreach ($currentState['styleSheets'] as $style => $styleInfo)
				{
					if (!array_key_exists($style, $initState['styleSheets']))
						$differences[] = sprintf('<link rel="stylesheet" href="%s" type="%s" />', $style, $this->getMimeType($styleInfo, 'text/css'));
				}
			}

			if (!empty($currentState['style']))
			{
				foreach ($currentState['style'] as $type => $style) 
				{
					if (!empty($initState['style'][$type]))
					{
						$difStyle = '';
						if (strpos($style, $initState['style'][$type]) === 0)
							$difStyle = trim(substr($style, strlen($initState['style'][$type])));

						if (!empty($difStyle))
							$differences[] = sprintf('<style type="%s">%s</style>', $type, $difStyle);
					}
					else
					{
						$differences[] = sprintf('<style type="%s">%s</style>', $type, $style);
					}
				}
			}
			
			if (!empty($currentState['scripts']))
			{
				foreach ($currentState['scripts'] as $script => $type)
				{
					if (!array_key_exists($script, $initState['scripts']))
					{
						if (is_array($type))
							$type = $this->getMimeType($type, 'text/javascript');
                        
						$differences[] = sprintf('<script type="%s" src="%s"></script>', $type, $script);
					}
				}
			}
			
			if (!empty($currentState['script']))
			{
				foreach ($currentState['script'] as $type => $script) 
				{
					if (!empty($initState['script'][$type]))
					{
						$difScript = '';
						if (strpos($script, $initState['script'][$type]) === 0)
							$difScript = trim(substr($script, strlen($initState['script'][$type])));
							
						if (!empty($difScript))
							$differences[] = sprintf('<script type="%s">%s</script>', $type, $difScript);
					}
					else
					{
						$differences[] = sprintf('<script type="%s">%s</script>', $type, $script);
					}
				}
			}
			
			if (!empty($currentState['custom']))
			{
				foreach ($currentState['custom'] as $customTag)
				{
					if (!in_array($customTag, $initState['custom']))
						$differences[] = $customTag;
				}
			}			
		}

		if ($deleteState) 
			$this->deleteState();

		return $differences;
	}

    private function getMimeType($itemMeta, $defaultType = '')
    {
        if (!empty($itemMeta['mime']))
            return $itemMeta['mime'];

        if (!empty($itemMeta['type']))
            return $itemMeta['type'];

        return $defaultType;
    }
}