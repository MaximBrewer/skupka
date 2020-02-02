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
	class JViewOrder_export extends OPCView
	{
		function display($tpl = null)
		{	
		 
			if (!class_exists('VmConfig'))
			require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
		 
		     VmConfig::loadConfig(true); 
				if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php'))
			include(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php');

			 require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'config.php'); 
		 
			$model = new JModelConfig(); 
			
			$modelexport = $this->getModel();
			$this->jforms = $modelexport->getJforms(); 
			//$limit = JRequest::getVar('limit', $mainframe->getCfg('list_limit'));
			//limitstart = JRequest::getVar('limitstart', 0);
			
			
			/*
			if (!file_exists(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'ps_onepage.php'))
			{
			 $model->install(true);
			}
			*/
			
			
			
			$model->loadVmConfig('config'); 
			require_once(JPATH_COMPONENT.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'export_helper.php'); 
			$this->ehelper = new OnepageTemplateHelper;
			
			
			
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
			$php = $model->retPhp();
			$sids = $model->getShippingRates();
			if (empty($sids)) $sids = array(); 
			
			$coref = array(); 
			$ulist = $model->getUserFieldsLists($coref); 
			$this->assignRef('clist', $coref); 
			$this->assignRef('ulist', $ulist); 
			
			
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
			
			
			$ehelper = $this->ehelper; 
		    $export_templates = $ehelper->getExportTemplates('ALL');
			$this->assignRef('export_templates', $export_templates); 
			
			
			
			
			$errors = $model->getErrMsgs();
			$statuses = $model->getOrderStatuses();
			$codes = $model->getJLanguages();
			
			//$exthtml = $model->getExtensions();
			$groups = $model->listShopperGroups(); 
			//$vatgroups = $model->listShopperGroupsSelect();
		    //$lfields = $model->listUserfields();
			//function getArticleSelector($name, $value, $required=false)
			require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
			$this->lang_thank_youpage = OPCconfig::getValue('ty_page', 'ty_page', 0, array(0=>'')); 
			
			$default = 'https://pdf.rupostel.com/convert.php'; 
			$this->api_url = OPCconfig::getValue('xml_export', 'api_url', 0, $default, false); 
			
			$this->api_username = OPCconfig::getValue('xml_export', 'api_username', 0, '', false); 
			$this->api_password = OPCconfig::getValue('xml_export', 'api_password', 0, '', false); 
			$this->xml_debug = OPCconfig::getValue('xml_export', 'xml_debug', 0, false, false); 
			
			$defualt = JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'export'.DIRECTORY_SEPARATOR;
			$this->export_dir = OPCconfig::getValue('xml_export', 'export_dir', 0, $defualt, false); 
			
			
			$this->model =& $model; 
			
			$this->assignRef('groups', $groups);
			//$this->assignRef('vatgroups', $vatgroups);
			//$this->assignRef('lfields', $lfields); 
			//$this->assignRef('exthtml', $exthtml);
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
			
			$model2 = $this->getModel(); 
			$model2->checkTable(); 
			// $currencies = $model->getAllCurrency($limitstart, $limit);
			
			// $total = $model->countRows();
			
			jimport('joomla.html.pagination');
			//$pageNav = new JPagination($total, $limitstart, $limit);
						
			//$this->assignRef('currencies', $currencies);
			//$this->assignRef('pageNav', $pageNav);
			
			parent::display($tpl); 
		}
	}
