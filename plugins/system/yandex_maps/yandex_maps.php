<?php
defined('_JEXEC') or die;
jimport('joomla.version');

class plgSystemYandex_maps extends JPlugin {
	private $includejs = false;
    public function __construct(& $subject, $config){
		parent::__construct($subject, $config);
        JFactory::getLanguage()->load('plg_system_yandex_maps', JPATH_ADMINISTRATOR, null, true);
	}
    public function generateFilter(&$filters, $map) {
        if ($this->params->get('show_filter_categories', 1)) {
            $filters[] = JHtml::_('xdwork.includePHP', 'plugins/system/yandex_maps/filter.php', true, ['map'=>$map, 'params'=>$this->params]);
        }
    }
	private function isAjax(){
		return !$this->params->get('work_for_ajax', 0) and isset($_SERVER['HTTP_X_REQUESTED_WITH']) and $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest';
	}
	private function includejs($params) {
		if (!$this->includejs) {
			jhtml::_('xdwork.includemodeinline', true);
			$this->includejs = true;
			
			if ($params->get('include_jquery', 1)) {
				echo '<script src="'.JURI::root().'media/com_yandex_maps/js/jquery.min.js'.'"></script>';
			} else {
				echo '<script>(jQuery!==undefined) && (XDjQuery = jQuery)</script>';
			}
			echo '<link rel="stylesheet" href="'.JURI::root().'media/com_yandex_maps/css/frontend.css'.'" type="text/css" />';
			echo '<link rel="stylesheet" href="'.JURI::root().'media/com_yandex_maps/css/custom.css'.'" type="text/css" />';
			echo '<script src="'.JURI::root().'media/com_yandex_maps/js/frontend.js'.'"></script>';
			echo '<script src="'.JURI::root().'media/com_yandex_maps/js/custom.js'.'"></script>';
		}
	}
	public function onAfterRender() {
        $application = JFactory::getApplication();
		if($application->isAdmin() or $this->isAjax()) return false;
		
		if (JFactory::getDocument()->getType() !== 'html' && JFactory::getDocument()->getType() !== 'feed') {
			return;
		}

        $version = new JVersion();

        if (version_compare($version->RELEASE, '3.1') < 0) {
            $body = JResponse::getBody();
        } else {            
            $body = implode('', $application->getBody(true));
        }
		
		$result = array();
		$params = JComponentHelper::getParams('com_yandex_maps');

		
		// скрипты карт уже подключены?
		$this->includejs = preg_match('#media/com_yandex_maps/js/frontend\.js#', $body);
		$multybite = preg_match('#.#u', $body) ? 'u' : '';

		$replace_buffer = array();
		// Для компонента K2 {map id} может попасть в метатег <meta name="description" content="{map 1}" /> и соотвественно убить разметку
		// поэтому перед обработкой, маскируем все метатеги
		
		$body = preg_replace_callback('#<meta[^>]+>#Uis'.$multybite, function($matches) use(&$replace_buffer){
			$hash = '<!--'.md5($matches[0]).'-->';
			$replace_buffer[$hash] = $matches[0];
			return $hash;
		}, $body);
        
		// обрабатываем карту
		if (preg_match_all('#\{map[\s\t\n\r]*([0-9]+)(\s[^}]+)?\}#si'.$multybite, $body, $listofmatches)) {
			JHtml::addIncludePath(JPATH_ROOT.'/components/com_yandex_maps/helpers');
			include_once JPATH_ADMINISTRATOR."/components/com_yandex_maps/helpers/CModel.php";
			JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_yandex_maps/models');
			foreach ($listofmatches[1] as $i=>$mapid) {
				if (!isset($result[$listofmatches[0][$i]])) {
					$map = @JModelLegacy::getInstance('Maps', 'Yandex_MapsModel')->model($mapid);
					if ($listofmatches[2][$i]) {
						parse_str(html_entity_decode($listofmatches[2][$i]), $pars);
						if (isset($pars['address'])) {
							$map->setAddress($pars['address']);
							unset($pars['address']);
						}
						foreach($pars as $key=>$value) {
							$map->$key = $value;
						}
					}
					if ($map->id) {
						ob_start();
						$this->includejs($params);
						call_user_func(function($map, $params) {
							include JPATH_ROOT.'/modules/mod_yandex_maps/tmpl/'.($map->settings->get('template', 'default') == 0 ? 'default' : $map->settings->get('template', 'default')).'.php';
						}, $map, $params);
						$data = ob_get_clean();
					} else {
						$data = 'Map not exist or not enabled';
					}
					$result[$listofmatches[0][$i]] = $data;
				}
			}
			$body = str_replace(array_keys($result),array_values($result), $body);
		}

		$result = array();
		if (preg_match_all('#\{mapmodule[\s\t\n\r]*([0-9]+)\}#sui'.$multybite, $body, $listofmatches)) {
			jimport('joomla.application.module.helper');
			$module = JModuleHelper::getModule('mod_yandex_maps' , 'Map');
			foreach ($listofmatches[1] as $i=>$module_id) {
				$data = '';
				$item = jFactory::getDBO()->setQuery('SELECT * FROM #__modules where module="mod_yandex_maps" and id='.(int)$module_id)->loadObject();
				if ($item->id) {
					ob_start();
					$this->includejs($params);
					$module->params = $item->params;
					echo JModuleHelper::renderModule($module);
					$data = ob_get_clean();
				}
				$result[$listofmatches[0][$i]] = $data;
				
			}
			$body = str_replace(array_keys($result),array_values($result), $body);
		}

		// возвращаем метатеги на место
		$body = strtr($body, $replace_buffer);
		
		// для K2 и т.п. подчищаем все метатеги от наших спец макросов
		$body = preg_replace('#\{(map|module)[^\}]+\}#is'.$multybite, '', $body);


        if (version_compare($version->RELEASE, '3.1') < 0) {
            JResponse::setBody($body);
        } else {            
            $application->setBody($body);
        }
	}
    function onAjaxUploadImage() {
        jHtml::addIncludePath(JPATH_ROOT.'/components/com_yandex_maps/helpers');
        return jHtml::_('xdwork.upload','photo');
    }
}