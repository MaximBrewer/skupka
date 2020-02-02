<?php
/*
*
* @copyright Copyright (C) 2007 - 2012 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* One Page checkout is free software released under GNU/GPL and uses code from VirtueMart
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
*/

	defined( '_JEXEC' ) or die( 'Restricted access' );
	jimport('joomla.application.component.view');
	class JViewConfig extends OPCView
	{
		function display($tpl = null)
		{	
			if (!file_exists(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php')) {
				JFactory::getApplication()->enqueueMessage('Install Virtuemart before OPC !'); 
				return ''; 
			}
			else {
			if (!class_exists('VmConfig'))
			require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
		 
		     VmConfig::loadConfig(true); 
				if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php'))
			include(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php');

			//$limit = JRequest::getVar('limit', $mainframe->getCfg('list_limit'));
			//limitstart = JRequest::getVar('limitstart', 0);
			$model = $this->getModel();
			 
			 
			 require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
			/*
			if (!file_exists(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'ps_onepage.php'))
			{
			 $model->install(true);
			}
			*/
			
			require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'tracking.php'); 
			$modelT = new JModelTracking(); 
			
			$this->isEnabled = $modelT->isEnabled(); 
			$this->modelT = $modelT; 
			$this->statuses = $model->getOrderStatuses();
			$this->configT = $modelT->getStatusConfig($this->statuses); 
			$this->assignRef('modelT', $modelT); 
			$this->registration = $model->getOPCRegistration(); 
			
			$model->loadVmConfig(); 
			$this->acyfields = $model->getAcyFields(); 
			$this->ftypes = $ftypes = $model->getFTypes(); 
			$this->assignRef('ftypes', $ftypes); 
			
			$this->opcexts = $model->getOPCExtensions(); 
			$countries = $model->getShippingCountries();
			$pms = $model->getPaymentMethods();
			$sty = $model->getClassNames();
			$default_country = $model->getDefaultC();
			
			$dis = $model->getDisabledOPC(); 
			$this->assignRef('disable_onepage', $dis); 
			
			$model->checkLangFiles(); 
			
			$trackingfiles = $model->getPhpTrackingThemes(); 
			$this->assignRef('trackingfiles', $trackingfiles); 
			
			$model->getExtLangVars(); 
			$langs = $model->getLanguages(); 
			$css = $model->retCss();
			
			$calcs = $model->getCalcs(); 
			$this->calcs = $calcs; 
			$this->assignRef('calcs', $calcs); 
			
			
			$php = $model->retPhp();
			$sids = $model->getShippingRates();
			if (empty($sids)) $sids = array(); 
			
			$coref = array(); 
			$ulist = $model->getUserFieldsLists($coref); 
			$this->assignRef('clist', $coref); 
			$this->assignRef('ulist', $ulist); 
			
			$positions = $model->getThemePositions(); 
			
			$this->assignRef('positions', $positions); 
			
			$langse = array(); 
			$exts = array(); 
			$lext = $model->listExts($exts, $langse); 
			 
			$adminxts = array(); 
			$langse2 = array(); 
			$lext2 = $model->listExtsaAdmin($adminxts, $langse2); 
			
			$this->assignRef('exts', $exts); 
			$this->assignRef('adminxts', $adminxts); 
			$this->assignRef('extlangs', $langse); 
			
			$langerr = $model->getlangerr(); 
			$this->assignRef('langerr', $langerr); 
			
			//$lang_vars = $model->getLangVars();
			$templates = $model->getTemplates();
			$errors = $model->getErrMsgs();
			$statuses = $model->getOrderStatuses();
			$codes = $model->getJLanguages();
			
			//$exthtml = $model->getExtensions();
			$groups = $model->listShopperGroups(); 
			//$vatgroups = $model->listShopperGroupsSelect();
		    //$lfields = $model->listUserfields();
			//function getArticleSelector($name, $value, $required=false)
			require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
			require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'videohelp.php'); 
			$this->lang_thank_youpage = OPCconfig::getValue('ty_page', 'ty_page', 0, array(0=>'')); 
			
			
			// opc per lang config: 
			$opclang = JRequest::getVar('opclang', ''); 
			
			if (!isset($tos_config)) $tos_config = ''; 
			$tos_config = OPCconfig::getValue('opc_config', 'tos_config', 0, $tos_config, $opclang); 

			if (!isset($op_articleid)) $op_articleid = ''; 
			$op_articleid = OPCconfig::getValue('opc_config', 'op_articleid', 0, $op_articleid, $opclang); 

			
			if (empty($adc_op_articleid)) $adc_op_articleid = 0; 
			$adc_op_articleid = OPCconfig::getValue('opc_config', 'adc_op_articleid', 0, $adc_op_articleid, $opclang); 
			
			
			
			//$tos_itemid, newitemid, op_customitemidty
			// opc per lang config end...
			
			$articleselector = $model->getArticleSelector('tos_config', $tos_config); 
			$articleselector2 = $model->getArticleSelector('op_articleid', $op_articleid); 
			
			$articleselector3 = $model->getArticleSelector('adc_op_articleid', $adc_op_articleid); 
			$this->model =& $model; 
			$this->assignRef('articleselector', $articleselector); 
			$this->assignRef('articleselector2', $articleselector2); 
			$this->assignRef('articleselector3', $articleselector3); 
			$this->assignRef('groups', $groups);
			$this->assignRef('vatgroups', $vatgroups);
			$this->assignRef('lfields', $lfields); 
			$this->assignRef('exthtml', $exthtml);
			$this->assignRef('codes', $codes);
			$this->assignRef('statuses', $statuses);
			$this->assignRef('errors', $errors);
			$this->assignRef('templates', $templates);
			//$this->assignRef('lang_vars', $lang_vars); 
			if (empty($pms)) $pms = array(); 
			$this->assignRef('pms', $pms);
			$this->assignRef('sty', $sty);
			$this->assignRef('countries', $countries);
			$this->assignRef('default_country', $default_country);
			$this->assignRef('langs', $langs); // ok
			$this->assignRef('css', $css);
			$this->assignRef('php', $php);
			$this->assignRef('sids', $sids);
			
			// $currencies = $model->getAllCurrency($limitstart, $limit);
			$this->currencies = $model->getCurrencies(); 
			
			// $total = $model->countRows();
			
			jimport('joomla.html.pagination');
			//$pageNav = new JPagination($total, $limitstart, $limit);
						
			//$this->assignRef('currencies', $currencies);
			//$this->assignRef('pageNav', $pageNav);
			
			parent::display($tpl); 
			}
		}
	}
