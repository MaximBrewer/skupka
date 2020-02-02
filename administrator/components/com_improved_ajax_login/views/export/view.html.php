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
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * View class for a list of Improved_ajax_login.
 */
class Improved_ajax_loginViewExport extends JViewLegacy
{

	public function display($tpl = null)
	{
    $db = JFactory::getDBO();
    $db->setQuery("SELECT id, name, username, email, block, registerDate, lastvisitDate FROM #__users ORDER BY id ASC");
    $users = $db->loadObjectList('id');

    $db->setQuery('SELECT fields FROM #__offlajn_forms WHERE id = 1');
    $form = $db->loadObject();
    $fields = json_decode($form->fields)->page[0]->elem;

    $fieldname = array();
    foreach ($fields as $field) {
      if (isset($field->{'jform[elem_name]'})) {
        $name = $field->{'jform[elem_name]'}->value;
        if (!$name) {
          $name = strtolower($field->{'jform[elem_label]'}->value);
          $name = preg_replace('/\s+/', '_', $name);
          $name = preg_replace('/\W/', '', $name);
        }
        $fieldname[$name] = '';
      }
    }
    unset($fieldname['name']);
    unset($fieldname['username']);
    unset($fieldname['password1']);
    unset($fieldname['password2']);
    unset($fieldname['email1']);
    unset($fieldname['email2']);
    unset($fieldname['tos']);

        $db->setQuery("SELECT p.* FROM #__user_profiles AS p
                                    INNER JOIN #__users AS u ON u.id = p.user_id ORDER BY user_id ASC, ordering ASC");
    $profiles = $db->loadObjectList();

    header("Content-type: text/csv; charset=utf-8");
    header("Content-Disposition: attachment;filename=users.csv");

    echo "id;name;username;email;block;registerDate;lastvisitDate";
    foreach ($fieldname as $key=>$value) {
    	echo ";{$key}";
    }
    echo "\n";

    $i = 0;
    foreach ($users as $id=>$user) {
    	echo "{$user->id};{$user->name};{$user->username};{$user->email};{$user->block};{$user->registerDate};{$user->lastvisitDate}";
      if (isset($profiles[$i]->user_id) && $profiles[$i]->user_id == $id) {
        $fn = array_merge(array(), $fieldname);
        while (@$profiles[$i]->user_id == $id) {
          $key = explode('.', $profiles[$i]->profile_key, 2);
          if (isset($key[1])) {
            $key = $key[1];
            if (isset($fn[$key])) {
              $fn[$key] = $profiles[$i]->profile_value;
            }
          }
          $i++;
        }
        foreach ($fn as $value) {
        	echo ';"'.json_decode($value).'"';
        }
      }
      echo "\n";
    }

    JExit(0);
	}

}
