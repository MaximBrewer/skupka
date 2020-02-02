<?php

/**
* @package plugin ByeByeGenerator
* @copyright (C) 2010-2011 RicheyWeb - www.richeyweb.com
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* ByeByeGenerator Copyright (c) 2010 Michael Richey.
* ByeByeGenerator is licensed under the http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
*
* ByeByeGenerator version 1.3 for Joomla 1.6.x devloped by RicheyWeb
*
*/
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

/**
 * ByeByeGenerator system plugin
 */
class plgSystemByeByeGenerator extends JPlugin
{
	/**
	 * Constructor
	 *
	 * @access	protected
	 * @param	object	$subject The object to observe
	 * @param 	array   $config  An array that holds the plugin configuration
	 * @since	1.0
	 */
         public $_done;
	function plgSystemByeByeGenerator( &$subject, $config )
	{
            $this->_done = array(
                'html'=>array(
                    'generator'=>false,
                    'robots'=>false
                    ),
                'feed'=>array(
                    'generator'=>false
                    )
                );
		parent::__construct( $subject, $config );
	}
	
	/* The generator tag can be altered at any time before the page is rendered */
	function onAfterDispatch()
	{
            
            /* only run in frontend */
            $app = JFactory::getApplication();
            if($app->isAdmin()) return true;
            
            /* do we bother, are we customizing the generator? */
            $document = JFactory::getDocument();
            if ((int)$this->params->get('generator') == 0) {
                /* test that the document is HTML output which will contain a generator*/
                if(in_array($document->getType(),array('html','feed'))) {
                    $document->setGenerator($this->params->get('custom'));
                    if($document->getType() == 'html') $this->_done['generator'] = true;
                }
            } elseif ((int)$this->params->get('generator') == 1) {
                jimport('joomla.version');
                $version = new JVersion();
                $version = explode('.',$version->getShortVersion());
                if($version[0] >= 2) { // removing it the 2.5+ way
                    $document->setGenerator('');
                    if($document->getType() == 'html') $this->_done['generator'] = true;
                }
            }
            return true;
	}

	/* The generator tag isn't added until the document is rendered */
    function onAfterRender() {
        if (JFactory::getApplication()->isAdmin()) return true;
        /* are we manipulating a valid doctype? */
        $document = JFactory::getDocument();
        if (!in_array($document->getType(),array('html','feed'))) return true;
        /* do we bother, are we removing the generator? */
        if ((int) $this->params->get('generator') != 1 AND (int) $this->params->get('robots') != 1) return true;
        $replace = array();
        if ((int) $this->params->get('generator') == 1) $replace[] = 'generator';
        if ((int) $this->params->get('robots') == 1) $replace[] = 'robots';
        if (count($replace)) JResponse::setBody($this->_byebye(JResponse::getBody(), $replace, $document->getType()));
        return true;
    }

    private function _byebye($buffer, $replace, $doctype) {
        $replacements = array();
        foreach ($replace as $match) {
            if($match == 'generator') {
                switch($doctype) {
                    case 'feed':
                        preg_match('/<generator.*?<\/generator>\n/',$buffer,$position, PREG_OFFSET_CAPTURE);
                        if (count($position)) {
                            $replacements[$position[0][0]] = '';
                        }
                        preg_match('/<!-- generator=".*?" -->\n/',$buffer,$position, PREG_OFFSET_CAPTURE);
                        if (count($position)) $replacements[$position[0][0]] = '';
                    break;
                    default:
                        if(!$this->_done[$doctype][$match]) {
                            preg_match('/<meta name="' . $match . '".*?\/>\n/', $buffer, $position, PREG_OFFSET_CAPTURE);
                            if (count($position)) $replacements[$position[0][0]] = '';
                            $this->_done[$doctype][$match]=true;
                        }
                    break;
                }
            } else {
                preg_match('/<meta name="' . $match . '".*?\/>\n/', $buffer, $position, PREG_OFFSET_CAPTURE);
                if (count($position)) $replacements[$position[0][0]] = '';
            }
        }
        $buffer = str_replace(array_keys($replacements), $replacements, $buffer);
        return $buffer;
    }
}
