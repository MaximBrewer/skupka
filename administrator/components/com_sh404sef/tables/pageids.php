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

// Security check to ensure this file is being included by a parent file.
if (!defined('_JEXEC')) die('Direct Access to this location is not allowed.');

jimport( 'joomla.database.table');


class Sh404sefTablePageids extends JTable {

  /**
   * Current row id
   *
   * @var   integer
   * @access  public
   */
  public $id = 0;

  /**
   * Non-sef url associated with the alias
   *
   * @var   string
   * @access  public
   */
  public $newurl = '';

  /**
   * pageId to the non-sef url associated with the alias
   *
   * @var   string
   * @access  public
   */
  public $pageid = '';


  /**
   * Type of alias : deprecated : aliases and pageids are stored in separate DB tables
   *
   * Can be
   *   Sh404sefHelperGeneral::COM_SH404SEF_URLTYPE_ALIAS (=0) for a regular alias
   *   Sh404sefHelperGeneral::COM_SH404SEF_URLTYPE_PAGEID (=1) for an auto created pageid
   *
   * @var   integer
   * @access  public
   */
  public $type = Sh404sefHelperGeneral::COM_SH404SEF_URLTYPE_PAGEID;

  /**
   * Object constructor
   *
   * @access public
   * @param object $db JDatabase object
   */
  public function __construct( &$db ) {

    parent::__construct( '#__sh404sef_pageids', 'id', $db);
  }

  function check() {

    // if existing,
    if (!empty( $this->id) ) {
      return true;
    }

    if (empty( $this->pageid) || empty( $this->newurl)) {
      return false;
    }

    // if new record, check there is no record with same pageid
    // or non-sef
    $count = ShlDbHelper::count($this->_tbl, '*', $this->_db->quoteName( 'pageid') . ' = ? or ' . $this->_db->quoteName( 'newurl') . ' = ?', array($this->pageid, $this->newurl));

    return empty( $count);
  }
}
