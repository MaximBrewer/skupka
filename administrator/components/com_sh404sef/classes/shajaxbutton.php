<?php
/**
 * sh404SEF - SEO extension for Joomla!
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier - Weeblr llc - 2016
 * @package     sh404SEF
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     4.7.3.3292
 * @date		2016-03-15
 */

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

/**
 * Renders a popup window button
 *
 * @package 	Joomla.Framework
 * @subpackage		HTML
 * @since		1.5
 */
class JButtonShajaxbutton extends JButton
{
  /**
   * Button type
   *
   * @access	protected
   * @var		string
   */
  var $_name = 'Shajax';

  
  function render( &$definition )
  {
    /*
     * Initialize some variables
     */
    $html = null;
    $id   = call_user_func_array(array(&$this, 'fetchId'), array($definition));
    $action = call_user_func_array(array(&$this, 'fetchButton'), $definition);

    // Build id attribute
    if ($id) {
      $id = "id=\"$id\"";
    }

    // Build the HTML Button
    $html .= "<li class=\"button\" $id>\n";
    $html .= $action;
    $html .= "</li>\n";

    return $html;
  }
  
  public function fetchButton( $type='Popup', $name = '', $text = '', $url = '', $userOptions = array() )
  {
    // not needed, we normally have our own already called
    //JHTML::_('behavior.modal');

    // merge with default options
    $defaultOptions = array();
    $options = array_merge( $defaultOptions, $userOptions);

    $text	= JText::_($text);
    $class	= $this->fetchIconClass($name);
    $doTask	= $this->_getCommand($name, $url, $userOptions);

    $buttonClass = empty($options['class']) ? '' : 'class="' . $options['class'] . '"';
    $id = empty($options['id']) ? '' : 'id="' . $options['id'] . '"';

   // $html	= "<a $buttonClass $id href=\"$doTask\" >\n";
    $html	= "<a $buttonClass $id href=\"javascript:void(0)\" onclick=\"$doTask\" >\n";
    $html .= "<span class=\"{$class}\" title=\"$text\">\n";
    $html .= "</span>\n";
    $html	.= "$text\n";
    $html	.= "</a>\n";

    return $html;
  }

  /**
   * Get the button id
   *
   * Redefined from JButton class
   *
   * @access		public
   * @param		string	$name	Button name
   * @return		string	Button CSS Id
   * @since		1.5
   */
  public function fetchId($definition)
  {
    return $this->_parent->getName().'-'."shajax-{$definition[1]}";
  }

  /**
   * Get the JavaScript command for the button
   *
   * @access	private
   * @param	object	$definition	Button definition
   * @return	string	JavaScript command string
   * @since	1.5
   */
  public function _getCommand($name, $url, $userOptions)
  {
    if (substr($url, 0, 4) !== 'http') {
      $url = JURI::base().$url;
    }

    $commands = array();

    // calculate the various parts of the ajax call params, encoded as JSon
    if(!empty($userOptions['update'])) {
      $commands['update'] = $userOptions['update'];
    }
        
    // make that a json object
    $params = ShlSystem_Convert::arrayToJSObject( $commands);
    
    // calculate the full command
    //$command  = 'javascript: onclick=shAjaxHandler(\'' . $name . '\',' . $params .',' .$userOptions['closewindow'].')';
    $command  = 'shAjaxHandler(\'' . $name . '\',' . $params .',' .$userOptions['closewindow'].')';
    
    return $command;
  }
}
