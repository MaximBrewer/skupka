<?php
/**
 * sh404SEF - SEO extension for Joomla!
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier - Weeblr llc - 2016
 * @package     sh404SEF
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @date        2016-03-15
 */

defined('JPATH_BASE') or die;

/**
 * Authenticate a user against Google Analytics API
 *
 * @package        sh404SEF
 */
class JFormFieldWbgaauth extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var        string
	 */
	protected $type = 'wbgaauth';

	/**
	 * Method to get certain otherwise inaccessible properties from the form field object.
	 *
	 * @param   string $name The property name for which to the the value.
	 *
	 * @return  mixed  The property value or null.
	 *
	 */
	public function __get($name)
	{

		switch ($name)
		{
			case 'element':
				return $this->$name;
				break;
		}

		$value = parent::__get($name);
		return $value;
	}

	/**
	 * Method to get the field input markup.
	 *
	 * @return    string    The field input markup.
	 */
	protected function getInput()
	{
		$html = array();

		$displayData = array();
		$displayData['authRequired'] = $this->form->getValue('wbgaauth_auth_required', null, true);
		$key = $this->form->getValue('wbgaauth_client_id_key', null, '');
		$displayData['clientId'] = Sh404sefHelperAnalytics_auth::getGaAuthClientId($key);

		$html[] = ShlMvcLayout_Helper::render('com_sh404sef.analytics.ga_auth', $displayData);

		return implode('', $html);
	}
}
