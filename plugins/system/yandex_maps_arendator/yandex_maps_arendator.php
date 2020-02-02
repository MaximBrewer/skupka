<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  yandex_maps_arendator.profile
 *
 * @copyright   Copyright (C) 2016 xdan.ru All rights reserved.
 * @license     GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;
class PlgSystemYandex_Maps_Arendator extends JPlugin {
	protected $autoloadLanguage = true;
	protected $statuses = array(
        'PLG_SYSTEM_YANDEX_MAPS_ARENDATOR_STATUS_FREE',
        'PLG_SYSTEM_YANDEX_MAPS_ARENDATOR_STATUS_BOOKED',
    );

	public function onOpenMap(& $html, $map) {
        Jhtml::_('xdwork.dialog');
        Jhtml::_('xdwork.includecss', JURI::root().'plugins/system/yandex_maps_arendator/assets/style.css');
        Jhtml::_('xdwork.includejs', JURI::root().'plugins/system/yandex_maps_arendator/assets/profile.js');
        $html.='<script>
            map'.$map->id.'.events.on("ballonOpen", function () {
                setTimeout(function () {
                    jQuery(".owl-carousel").owlCarousel({
                        items: 1,
                        navigation: true,
                        autoHeight: true
                    });
                }, 300);
            });
        </script>';
        $html.=JHtml::_('xdwork.includePHP', 'plugins/system/yandex_maps_arendator/tmpl/book.php', true);
    }

	public function getIntro($object) {
		$text = $object->description;
		if (preg_match('#^(.*)<hr[\s]*id=("|\')system-readmore("|\')[\s]*(/)?>#usi',$text, $intro)) {
			return $intro[1];
		} else {
			return CModel::truncate($text, $this->params->get('description_intro_length', 200));
		}
	}

    public function generateDescription(& $description, $object) {
        $input = jFactory::getApplication()->input;
        $ballonmode = !(in_array($input->get('object'), array('object')) && in_array($input->get('option'), array('com_yandex_maps')));
        if ($ballonmode) {
            $description = $this->getIntro($object);
        }
        $description .= jFactory::getApplication()->input->get('task').JHtml::_('xdwork.includePHP', 'plugins/system/yandex_maps_arendator/tmpl/description.php', true, array(
            'ballon' => $ballonmode,
            'object'    =>      $object,
            'statuses'  =>      $this->statuses,
        ));
    }

	public function onGenerateFilterWhere($map, & $filters, & $join, & $select) {
        $input = jFactory::getApplication()->input;
        
        if ($input->get('yma_start_period', false) and !$input->get('yma_end_period', false)) {
            $filters[] = '(
                a.id in (select object_id from #__yandex_maps_datetimes where date_value = "'.(date('Y-m-d', strtotime($input->get('yma_start_period', false)))).'")
            )';
        } else {
            if ($input->get('yma_start_period', false)) {
                $filters[] = '(
                    a.id in (select object_id from #__yandex_maps_datetimes where date_value >= "'.(date('Y-m-d', strtotime($input->get('yma_start_period', false)))).'")
                )';
            }
            if ($input->get('yma_end_period', false)) {
                $filters[] = '(
                    a.id in (select object_id from #__yandex_maps_datetimes where date_value <= "'.(date('Y-m-d', strtotime($input->get('yma_end_period', false)))).'")
                )';
            }
        }
        
        if ($input->get('yma_times', false) and count($input->get('yma_times', array(), 'ARRAY'))) {
            $filters[] = '(
                a.id in (select object_id from #__yandex_maps_datetimes where time_value in ('.implode(',', jFactory::getDBO()->quote($input->get('yma_times', array(), 'ARRAY'))).'))
            )';
        }
        print_r($filter);
    }

	public function generateFilter(& $filters, $map) {
        if ($this->params->get('show_filter_datetimes', 1)) {
            $filters[] = JHtml::_('xdwork.includePHP', 'plugins/system/yandex_maps_arendator/tmpl/filter.php', true, array('map'=>$map, 'params' => $this->params));
        }
    }
	/**
	 * Constructor
	 *
	 * @param   object  &$subject  The object to observe
	 * @param   array   $config    An array that holds the plugin configuration
	 *
	 * @since   1.5
	 */
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		JFormHelper::addFieldPath(__DIR__ . '/fields');
	}

	/**
	 * Runs on content preparation
	 *
	 * @param   string  $context  The context for the data
	 * @param   object  $data     An object containing the data for the form.
	 *
	 * @return  boolean
	 *
	 * @since   1.6
	 */
	public function onContentPrepareData($context, $data)
	{
		// Check we are manipulating a valid form.
		if (!in_array($context, array('com_users.profile', 'com_users.user', 'com_users.registration', 'com_admin.profile')))
		{
			return true;
		}

		if (is_object($data))
		{
			$userId = isset($data->id) ? $data->id : 0;

			if (!isset($data->profile) and $userId > 0)
			{
				// Load the profile data from the database.
				$db = JFactory::getDbo();
				$db->setQuery(
					'SELECT profile_key, profile_value FROM #__user_profiles' .
						' WHERE user_id = ' . (int) $userId . " AND profile_key LIKE 'profile.%'" .
						' ORDER BY ordering'
				);

				try
				{
					$results = $db->loadRowList();
				}
				catch (RuntimeException $e)
				{
					$this->_subject->setError($e->getMessage());

					return false;
				}

				// Merge the profile data.
				$data->profile = array();

				foreach ($results as $v) {
					$k = str_replace('profile.', '', $v[0]);
					$data->profile[$k] = json_decode($v[1], true);

					if ($data->profile[$k] === null){
						$data->profile[$k] = $v[1];
					}
				}
			}
		}

        if (!JHtml::isRegistered('users.owner')) {
            JHtml::register('users.owner', array(__CLASS__, 'owner'));
        }
        
        if (!JHtml::isRegistered('users.mapobjects')) {
            JHtml::register('users.mapobjects', array(__CLASS__, 'mapobjects'));
        }

		return true;
	}

    public static function owner($value) {
        return $value ? JText::_('PLG_SYSTEM_YANDEX_MAPS_ARENDATOR_OWNER') :  JText::_('PLG_SYSTEM_YANDEX_MAPS_ARENDATOR_USER');
    }
    
    public static function mapobjects($value) {
        JFormHelper::addFieldPath(dirname(__FILE__) . '/fields');
        $form = new JForm('name');
        JHtml::addIncludePath(JPATH_ROOT.'/administrator/components/com_yandex_maps/helpers/html');
        $mapobjects = JFormHelper::loadFieldType('mapobjects', true);
        $mapobjects->setForm($form);
        $mapobjects->setup(simplexml_load_string('<field />'), null);
        return $mapobjects->getInput();
    }
    
    public function onBeforeRender() {
        JHtml::addIncludePath(JPATH_ROOT.'/components/com_yandex_maps/helpers');
        Jhtml::_('xdwork.includejs', '
            window.connectorPathURL = "'.str_replace(JPATH_ROOT, '', dirname(__FILE__)).'/";
            window.currentLanguage = "'.jFactory::getlanguage()->getTag().'";
        ', true);
        jhtml::_('xdwork.jstext', JPATH_ADMINISTRATOR . '/language/%lang%/%lang%.plg_system_yandex_maps_arendator.ini', array(
            'PLG_SYSTEM_YANDEX_MAPS_ARENDATOR_BOOK_EMAIL_BODY',
            'PLG_SYSTEM_YANDEX_MAPS_ARENDATOR_USE_MODERATION',
            'PLG_SYSTEM_YANDEX_MAPS_ARENDATOR_HOW_DO_MODERATION_LABEL',
            'PLG_SYSTEM_YANDEX_MAPS_ARENDATOR_HOW_DO_MODERATION_DESCRIPTION',
            'PLG_SYSTEM_YANDEX_MAPS_ARENDATOR_POSTMODERATION',
            'PLG_SYSTEM_YANDEX_MAPS_ARENDATOR_DONT_USE_MODERATION',
            'PLG_SYSTEM_YANDEX_MAPS_ARENDATOR_DONT_USE_MODERATION',
            'PLG_SYSTEM_YANDEX_MAPS_ARENDATOR_USER_ADDED_OBJECT_MESSAGE',
            'PLG_SYSTEM_YANDEX_MAPS_ARENDATOR_USER_ADDED_OBJECT',
        ));
    }
    
	/**
	 * Adds additional fields to the user editing form
	 *
	 * @param   JForm  $form  The form to be altered.
	 * @param   mixed  $data  The associated data for the form.
	 *
	 * @return  boolean
	 *
	 * @since   1.6
	 */


	public function onContentPrepareForm($form, $data)
	{
		if (!($form instanceof JForm))
		{
			$this->_subject->setError('JERROR_NOT_A_FORM');

			return false;
		}

		// Check we are manipulating a valid form.
		$name = $form->getName();

		if (!in_array($name, array('com_admin.profile', 'com_users.user', 'com_users.profile', 'com_users.registration')))
		{
			return true;
		}

		// Add the registration fields to the form.
		JForm::addFormPath(dirname(__FILE__) . '/profiles');
		$form->loadFile('profile', false);

        if ($name == 'com_users.registration' || $data->profile['owner']) {
            $form->removeField('objects', 'profile');
        }
        
		return true;
	}
}
