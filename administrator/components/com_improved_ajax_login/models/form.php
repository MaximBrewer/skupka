<?php
/*-------------------------------------------------------------------------
# com_improved_ajax_login - com_improved_ajax_login
# -------------------------------------------------------------------------
# @ author    Balint Polgarfi
# @ copyright Copyright (C) 2013 Offlajn.com  All Rights Reserved.
# @ license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @ website   http://www.offlajn.com
-------------------------------------------------------------------------*/
?><?php
// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

/**
 * Improved_ajax_login model.
 */
class Improved_ajax_loginModelform extends JModelAdmin
{
	/**
	 * @var		string	The prefix to use with controller messages.
	 * @since	1.6
	 */
	protected $text_prefix = 'COM_IMPROVED_AJAX_LOGIN';

	public function validate($form, $data, $group = null)
	{
    $data = parent::validate($form, $data, $group);
    // fix for magic quotes
    if (get_magic_quotes_gpc())
    {
      $props = json_decode($data['props']);
      if ($props == null) foreach ($data as $key=>$value)
      {
        $data[$key] = stripslashes($value);
      }
    }
    return $data;
  }

	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * @since	1.6
	 */
	public function getTable($type = 'Form', $prefix = 'Improved_ajax_loginTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get the record form.
	 *
	 * @param	array	$data		An optional array of data for the form to interogate.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	JForm	A JForm object on success, false on failure
	 * @since	1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Initialise variables.
		$app	= JFactory::getApplication();

		// Get the form.
		$form = $this->loadForm('com_improved_ajax_login.form', 'form', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	1.6
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_improved_ajax_login.edit.form.data', array());
		if (empty($data)) {
			$data = $this->getItem();

		}

		return $data;
	}

  protected function getIdByLang($lang, $orig)
  {
    $type = parent::getItem($orig)->type;
    $db = JFactory::getDBO();
    $db->setQuery("SELECT id FROM #__offlajn_forms WHERE state = 1 AND type LIKE '$type' AND language LIKE '$lang'");
    $res = $db->loadRow();

    return isset($res[0])? $res[0] : null;
  }

	/**
	 * Method to get a single record.
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 * @since	1.6
	 */
	public function getItem($pk = null)
	{
    $id = JRequest::getInt('id');
    $orig = JRequest::getInt('orig');
    $lang = JRequest::getString('lang');

    if (!$id) $pk = $this->getIdByLang($lang, $orig);
		if ($item = parent::getItem($pk ? $pk : $orig)) {
			//Do any procesing on fields here if needed
		}
    if (!$id && !$pk) {
      $item->id = null;
      $item->ordering = null;
      $item->state = 1;
      $item->checked_out = null;
      $item->checked_out_time = null;
      $item->created_by = null;
      $item->language = $lang;
    }

		return $item;
	}

	/**
	 * Prepare and sanitise the table prior to saving.
	 *
	 * @since	1.6
	 */
	protected function prepareTable($table)
	{
		jimport('joomla.filter.output');

		if (empty($table->id)) {

			// Set ordering to the last item if not set
			if (@$table->ordering === '') {
				$db = JFactory::getDbo();
				$db->setQuery('SELECT MAX(ordering) FROM #__offlajn_forms');
				$max = $db->loadResult();
				$table->ordering = $max+1;
			}

		}
	}

  public function getCoreFormFields() {
    $params = http_build_query(array(
      'option' => 'com_users',
      'view' => 'registration',
      'tmpl' => 'component',
      'debuglogin' => 1
    ));
    $ch = curl_init(JURI::root());
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $regForm = curl_exec($ch);
    curl_close($ch);

    $document = new DOMDocument();
    libxml_use_internal_errors(true);
    $document->loadHTML($regForm);
    libxml_clear_errors();

    $inputs = $document->getElementsByTagName("input");
    $labels = $document->getElementsByTagName("label");
    $excludes = array("jform[name]", "jform[username]", "jform[email1]", "jform[email2]", "jform[password1]", "jform[password2]");

		$lbls = array();
   	foreach($labels as $label) {
   		$lbls[$label->getAttribute("for")] = $label->textContent;
   	}

		$elems = array();
		$i = 0;
   	foreach ($inputs as $input) {
   		$elem = new stdClass();
   		$attrName = $input->getAttribute("name");
      if (strpos("$attrName", "jform") === 0 && strpos($attrName, "jform[improved]") !== 0 && !in_array($attrName, $excludes)) {
	    	$elems[$i]["name"] = $attrName;
	    	$attrType = $input->getAttribute("type");
	    	$elems[$i]["type"] = $attrType && $attrType != 'hidden' ? $attrType : 'text';
	    	$elems[$i]["required"] = $input->hasAttribute("required");
	    	$elems[$i]["class"] = $input->getAttribute("class");
	    	$elems[$i]["value"] = $input->getAttribute("value");
	    	$elems[$i]["title"] = $input->getAttribute("title");
	    	$elems[$i]["placeholder"] = $input->getAttribute("placeholder");
	    	$attrId = $input->getAttribute("id");
	    	$elems[$i]["label"] = isset($lbls[$attrId]) ? trim($lbls[$attrId], "\t *\r\n") : '';
	    	$i++;
      }
    }
   	return $elems;
  }

}