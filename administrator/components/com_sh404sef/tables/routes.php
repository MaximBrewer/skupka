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
if (!defined('_JEXEC'))
	die('Direct Access to this location is not allowed.');

class Sh404sefTableRoutes extends JTable
{
	/**
	 * Current row id
	 *
	 * @var   integer
	 * @access  public
	 */
	public $id = 0;

	/**
	 * Current sef url hit counter
	 *
	 * @var   integer
	 * @access  public
	 */
	public $cpt = 0;

	/**
	 * NON-sef url rank
	 *
	 * will be 0 for the main url, in case of duplicates
	 *
	 * @var   integer
	 * @access  public
	 */
	public $rank = 0;

	/**
	 * SEF url
	 *
	 * @var   string
	 * @access  public
	 */
	public $oldurl = '';

	/**
	 * Non-sef url associated with the alias
	 *
	 * @var   string
	 * @access  public
	 */
	public $newurl = '';

	/**
	 * Date a custom url is added to DB
	 * or a 404 page is recorded to db
	 *
	 * @var   string
	 * @access  public
	 */
	public $dateadd = '';

	/**
	 * Object constructor
	 *
	 * @access protected
	 * @param object $db JDatabase object
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__sh404sef_urls', 'id', $db);
	}

	public function check()
	{
		//initialize
		$this->oldurl = JString::trim($this->oldurl);
		$this->newurl = JString::trim($this->newurl);

		// check for valid URLs
		if (($this->oldurl == '') || ($this->newurl == ''))
		{
			$this->setError(JText::_('COM_SH404SEF_EMPTYURL'));
			return false;
		}

		if (JString::substr($this->oldurl, 0, 1) == '/')
		{
			$this->setError(JText::_('COM_SH404SEF_NOLEADSLASH'));
			return false;
		}

		if (JString::substr($this->newurl, 0, 9) != 'index.php')
		{
			$this->setError(JText::_('COM_SH404SEF_BADURL'));
			return false;
		}

		// check for a 404 record with the same SEF. Delete it if found
		// and we're trying to save a full record, one with sef and non-sef
		if (!empty($this->newurl))
		{
			ShlDbHelper::runQuotedQuery('delete from ?? where ?? = ? and ?? != ? and ?? = ?',
				array('#__sh404sef_urls', 'oldurl', 'dateadd', 'newurl'), array($this->oldurl, '0000-00-00', ''));
		}

		// check for pre-existing non-sef
		$xid = ShlDbHelper::selectObject($this->_tbl, array('id', 'oldurl'), $this->_db->quoteName('newurl') . ' LIKE ?', array($this->newurl));

		// raise error if we found a record with the same non-sef url
		// but don't if both newurl and old url are same. It means we may have changed alias list
		if ($xid && $xid->id != intval($this->id))
		{
			$this->setError(JText::_('COM_SH404SEF_URLEXIST'));
			return false;
		}

		return true;
	}

}
