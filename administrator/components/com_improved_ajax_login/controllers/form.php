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

jimport('joomla.application.component.controllerform');

/**
 * Form controller class.
 */
class Improved_ajax_loginControllerForm extends JControllerForm
{

  function __construct()
  {
    $this->view_list = 'forms';
    parent::__construct();
  }

  public function save($key = null, $urlVar = null)
  {
    $saved = parent::save($key, $urlVar);
    if ($saved)
    {
      $db = JFactory::getDBO();
      $data = JRequest::getVar('jform', array(), 'POST', 'array', JREQUEST_ALLOWRAW);

      // fix for magic quotes
      if (get_magic_quotes_gpc())
      {
        $props = json_decode($data['props']);
        if ($props == null) foreach ($data as $key=>$value)
        {
          $data[$key] = stripslashes($value);
        }
      }
      $fields = json_decode($data['fields']);
      if (isset($fields->page))
      {
        function getAttr($obj, $name)
        {
          $name = 'jform[elem_'.$name.']';
          return isset($obj->{$name})? $obj->{$name} : null;
        }
        $captcha = 0;
        foreach ($fields->page as $page)
        {
          foreach ($page->elem as $elem)
          {
            $type = getAttr($elem, 'type');
            if ($type->value == 'captcha') $captcha = 1;
          }
        }
        // init recaptcha
        $db->setQuery("UPDATE #__extensions SET custom_data = ".($captcha? "'IALR'" : "''")." WHERE name = 'plg_captcha_recaptcha'");
        $db->query();
      }
    }
    return $saved;
  }

  public function getFields() {
    $userParams = JComponentHelper::getParams('com_users');
    if(!$userParams->get('allowUserRegistration')) {
      $res = array(
        'error' => 'error',
        'msg' => "Registration isn't allowed"
      );
      die(json_encode($res));
    }

    $model = $this->getModel();
    echo json_encode($model->getCoreFormFields());
    exit;
  }

  public function delete() {
    $id = JRequest::getInt('id');
    if ($id < 100) return false;

    $db = JFactory::getDBO();
    //$db->setQuery("DELETE FROM #__offlajn_forms WHERE id = $id");
    $db->setQuery("UPDATE #__offlajn_forms SET state = -1 WHERE id = $id");
    $res = $db->query();
    $this->setRedirect('index.php?option=com_improved_ajax_login&view=forms');
    return $res;
  }

}