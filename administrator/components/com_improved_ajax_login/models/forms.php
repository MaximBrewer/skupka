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
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * Methods supporting a list of Improved_ajax_login records.
 */
class Improved_ajax_loginModelforms extends JModelList
{

  /**
   * Constructor.
   *
   * @param    array    An optional associative array of configuration settings.
   * @see        JController
   * @since    1.6
   */
  public function __construct($config = array())
  {
    if (empty($config['filter_fields'])) {
      $config['filter_fields'] = array(
        'id', 'a.id',
        'ordering', 'a.ordering',
        'state', 'a.state',
        'created_by', 'a.created_by',
        'title', 'a.title',
        'type', 'a.type',
        'theme', 'a.theme',
        'props', 'a.props',
        'fields', 'a.fields',
        'language', 'a.language',
      );
    }

    parent::__construct($config);
  }


  /**
   * Method to auto-populate the model state.
   *
   * Note. Calling getState in this method will result in recursion.
   */
  protected function populateState($ordering = null, $direction = null)
  {
    // Initialise variables.
    $app = JFactory::getApplication('administrator');

    // Load the filter state.
    $search = $app->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
    $this->setState('filter.search', $search);

    $published = $app->getUserStateFromRequest($this->context.'.filter.state', 'filter_published', '', 'string');
    $this->setState('filter.state', $published);


    //Filtering type
    $this->setState('filter.type', $app->getUserStateFromRequest($this->context.'.filter.type', 'filter_type', '', 'string'));

    //Filtering theme
    $this->setState('filter.theme', $app->getUserStateFromRequest($this->context.'.filter.theme', 'filter_theme', '', 'string'));


    // Load the parameters.
    $params = JComponentHelper::getParams('com_improved_ajax_login');
    $this->setState('params', $params);

    // List state information.
    parent::populateState('a.title', 'asc');
  }

  /**
   * Method to get a store id based on model configuration state.
   *
   * This is necessary because the model is used by the component and
   * different modules that might need different sets of data or different
   * ordering requirements.
   *
   * @param string    $id A prefix for the store id.
   * @return  string    A store id.
   * @since 1.6
   */
  protected function getStoreId($id = '')
  {
    // Compile the store id.
    $id.= ':' . $this->getState('filter.search');
    $id.= ':' . $this->getState('filter.state');

    return parent::getStoreId($id);
  }

  /**
   * Build an SQL query to load the list data.
   *
   * @return  JDatabaseQuery
   * @since 1.6
   */
  protected function getListQuery()
  {
    $regpage = 'joomla';

    // Get registraion module
    $db = $this->getDbo();
    $db->setQuery("SELECT id, module, params FROM `#__modules` WHERE module LIKE 'mod_improved_ajax_login' ORDER BY published DESC LIMIT 1");
    $mod = $db->loadObject();
    if (!empty($mod->params)) {
      $params = json_decode($mod->params);
      if (isset($params->moduleparametersTab->regpage)) {
        list($regpage) = explode('|*|', $params->moduleparametersTab->regpage);
      }
    }
    $registration = 'registration';
    if ($regpage == 'hikashop') $registration.= '/hikashop';
    if ($regpage == 'jomsocial') $registration.= '/jomsocial';
    if ($regpage == 'virtuemart') $registration.= '/virtuemart';
    if ($regpage == 'k2') $registration.= '/k2';

    $query  = $db->getQuery(true);
    // Select the required fields from the table.
    $query->select($this->getState('list.select', 'a.*'));
    $query->from('`#__offlajn_forms` AS a');

    // Join over the users for the checked out user.
    $query->select('uc.name AS editor');
    $query->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');

    // Join over the user field 'created_by'
    $query->select('created_by.name AS created_by');
    $query->join('LEFT', '#__users AS created_by ON created_by.id = a.created_by');

    // Filter by registration type
    $query->where("a.type LIKE '$registration'");
    // Filter by published state
    $published = $this->getState('filter.state');
    if (is_numeric($published)) {
        $query->where('a.state = '.(int) $published);
    } else if ($published === '') {
        $query->where('(a.state IN (0, 1))');
    }


    // Filter by search in title
    $search = $this->getState('filter.search');
    if (!empty($search)) {
      if (stripos($search, 'id:') === 0) {
        $query->where('a.id = '.(int) substr($search, 3));
      } else {
        $search = $db->Quote('%'.$db->escape($search, true).'%');
        $query->where('( a.title LIKE '.$search.' )');
      }
    }



    //Filtering type
    $filter_type = $this->state->get("filter.type");
    if ($filter_type) {
      $query->where("a.type = '".$db->escape($filter_type)."'");
    }

    //Filtering theme
    $filter_theme = $this->state->get("filter.theme");
    if ($filter_theme) {
      $query->where("a.theme = '".$db->escape($filter_theme)."'");
    }


    // Add the list ordering clause.
    $orderCol = $this->state->get('list.ordering');
    $orderDirn  = $this->state->get('list.direction');
    if ($orderCol && $orderDirn) {
        $query->order($db->escape($orderCol.' '.$orderDirn));
    }

    return $query;
  }
}
