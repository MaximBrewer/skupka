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
 * View to edit
 */
class Improved_ajax_loginViewForm extends JViewLegacy
{
  protected $state;
  protected $item;
  protected $form;

  protected $profile;
  protected $regpage;

  /**
   * Display the view
   */
  public function display($tpl = null)
  {
    $this->state  = $this->get('State');
    $this->item   = $this->get('Item');
    $this->form   = $this->get('Form');
    $this->initLanguage();

    // Check for errors.
    if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors));
    }

    $this->addToolbar();
    $this->initTheme();
    parent::display($GLOBALS['j25']? '25' : $tpl);
  }

  protected function initLanguage()
  {

    // load language files
    $lang = JFactory::getLanguage();
    $language = $this->item->language ? $this->item->language : null;

    $lang->load('lib_joomla', JPATH_SITE, $language);
    $lang->load('com_users', JPATH_SITE, $language);
    $lang->load('mod_login', JPATH_SITE, $language);
    $lang->load('plg_user_profile', JPATH_ADMINISTRATOR, $language);
    $lang->load('mod_improved_ajax_login', JPATH_SITE.'/modules/mod_improved_ajax_login', $language);
    if ($this->item->language) {
      $lang->load('', JPATH_SITE, $language);
      // get base form
      $db = JFactory::getDBO();
      $db->setQuery("SELECT * FROM #__offlajn_forms WHERE id < 100 AND type = '{$this->item->type}'");
      $form = $db->loadObject();
      $this->item->props = $form->props;

      $elems = json_decode($this->item->fields)->page[0]->elem;
      $fields = json_decode($form->fields);
      foreach ($fields->page[0]->elem as $id => $elem) {
        foreach ($elem as $name => &$prop) {
          switch ($name) {
            case 'jform[elem_name]':
            case 'jform[elem_type]':
              $prop->value = $prop->value ? $prop->value : $prop->placeholder;
              break;
            case 'jform[elem_label]':
            case 'jform[elem_subtitle]':
            case 'jform[elem_placeholder]':
            case 'jform[elem_title]':
            case 'jform[elem_error]':
            case 'jform[elem_select]':
            case 'jform[elem_article]':
            case 'jform[elem_article_name]':
              if (isset($elems->{$id}) && $elems->{$id}->{$name}->value != $prop->value)
                $prop->value = $elems->{$id}->{$name}->value;
              if (!$prop->value) $prop->placeholder = isset($prop->defaultValue) ? @JText::sprintf($prop->defaultValue, '') : @$prop->placeholder;
              unset($prop->placeholder);
              unset($prop->defaultValue);
              break;
          }
        }
      }
      $this->item->fields = json_encode($fields);
    }

  }

  protected function initTheme()
  {
    // Load module
    $db = JFactory::getDBO();
    $db->setQuery("SELECT id, module, params FROM `#__modules` WHERE published >= 0 AND module LIKE 'mod_improved_ajax_login' ORDER BY published DESC, ordering ASC");
    $modules = $db->loadObjectList();
    if (!$modules || empty($modules)) die('First please create an Improved AJAX Login & Register module <a href="index.php?option=com_modules&view=select">HERE</a>.');
    $module = $modules[0];
    $modPath = JPATH_SITE.'/modules/mod_improved_ajax_login';
    require_once($modPath.'/params/offlajndashboard/library/flatArray.php');
    $modParams = new JRegistry();
    $modParams->loadString($module->params);
    $modParams->loadArray(offflat_array($modParams->toArray()));
    // get regpage
    list($this->regpage) = explode('|*|', $modParams->get('regpage'));
    // If module not saved then set default param values
    $theme = $modParams->get('theme');
    if (!$theme) {
      $theme = $modParams->set('theme', 'elegant');
      $xml = JFactory::getXML("$modPath/themes/$theme/theme.xml");
      foreach ($xml->params->param as $p) {
        $modParams->set((string)$p['name'], (string)$p['default']);
      }
    }
    // Build module CSS
    require_once($modPath.'/classes/ImageHelper.php');
    require_once($modPath.'/classes/cache.class.php');
    require_once($modPath.'/helpers/font.php');
    require_once($modPath.'/helpers/parser.php');
    $cache = new OfflajnMenuThemeCache('default', $module, $modParams);
    $cache->addCss($modPath.'/themes/clear.css.php');
    $cache->addCss($modPath.'/themes/'.$theme.'/theme.css.php');
    $cache->assetsAdded();
    // Set up enviroment variables for the cache generation
    $themeurl = JURI::root(true)."/modules/mod_improved_ajax_login/themes/$theme/";
    $oh7 = new OfflajnHelper7($cache->cachePath, $cache->cacheUrl);
    $cache->addCssEnvVars('themeurl', $themeurl);
    $cache->addCssEnvVars('helper', $oh7);
    $cacheFiles = $cache->generateCache();

    $this->theme = $theme;
    $this->themeCSS = $cacheFiles[0];
  }

  /**
   * Add the page title and toolbar.
   */
  protected function addToolbar()
  {
    JFactory::getApplication()->input->set('hidemainmenu', true);

    $user = JFactory::getUser();
    $isNew = ($this->item->id == 0);
    if (isset($this->item->checked_out)) {
      $checkedOut = !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
    } else {
      $checkedOut = false;
    }
    $canDo = Improved_ajax_loginHelper::getActions();

    $mode = JText::_(JRequest::getInt('orig', 0) ? 'Translator view' : 'Design view');
    JToolBarHelper::title(JText::_('COM_IMPROVED_AJAX_LOGIN_TITLE_FORM').' - '.$mode, 'form.png');

    // If not checked out, can save the item.
    if (!$checkedOut && ($canDo->get('core.edit')||($canDo->get('core.create'))))
    {
      JToolBarHelper::apply('form.apply', 'JTOOLBAR_APPLY');
      JToolBarHelper::save('form.save', 'JTOOLBAR_SAVE');
      //JToolBarHelper::custom('form.rebuild','loop', '', 'Rebuild', false);
    }
/*
    if (!$checkedOut && ($canDo->get('core.create'))){
      JToolBarHelper::custom('form.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
    }
    // If an existing item, can save to a copy.
    if (!$isNew && $canDo->get('core.create')) {
      JToolBarHelper::custom('form.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
    }
*/
    if (empty($this->item->id)) {
      JToolBarHelper::cancel('form.cancel', 'JTOOLBAR_CANCEL');
    }
    else {
      JToolBarHelper::cancel('form.cancel', 'JTOOLBAR_CLOSE');
    }
  }

}
