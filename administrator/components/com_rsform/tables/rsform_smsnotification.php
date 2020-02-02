<?php
/**
 * @package    RSForm! Pro
 * @copyright  (c) 2007 - 2016 RSJoomla!
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

class TableRSForm_SmsNotification extends JTable
{
	/**
	 * Primary Key
	 *
	 * @var int
	 */
	public $form_id = null;

	public $admin_sms  = 1;
	public $user_sms   = 1;
	public $admin_from = '';
	public $admin_to   = '';
	public $user_from  = '';
	public $user_to    = '';
	public $admin_text = '';
	public $user_text  = '';

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function __construct(& $db)
	{
		parent::__construct('#__rsform_smsnotification', 'form_id', $db);
	}
}