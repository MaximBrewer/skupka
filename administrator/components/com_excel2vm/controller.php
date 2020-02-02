<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controller');
// отображение ошибок
ini_set("default_charset","utf-8");

$params = JComponentHelper :: getParams("com_excel2vm");
$debug=$params->get('debug',0);

if($debug){
   	ini_set("display_errors","1");
   	ini_set("display_startup_errors","1");
   	ini_set('error_reporting', E_ALL);
}
else{
   ini_set("display_errors","0");
   ini_set("display_startup_errors","0");
}



class Excel2vmController extends JControllerLegacy {

    function __construct() {
        parent :: __construct();
        $GLOBALS['component_name'] =  JText::_('COMPONENT_NAME');

        $this->task = JRequest :: getVar('task', '', '', 'string');
        $this->view = JRequest :: getVar('view', 'excel2vm', '', 'string');
        if (!JFactory::getUser()->authorise("core.".$this->view, "com_excel2vm")){
            $this->setRedirect("index.php");
        	return JError::raiseWarning(404, JText::_("JERROR_ALERTNOAUTHOR"));
        }
        if ($this->task AND !in_array($this->task, $this->getTasks()))
            $this->commonTask();
    }



    function commonTask() {
        $model = $this->getModel($this->view);
        if(!method_exists($model,$this->task)){
            echo "Restricted"; exit();
        }
        $msg = $model->{$this->task}();
        $this->setRedirect("index.php?option=com_excel2vm&view={$this->view}", $msg);
    }

    function get_stat(){
    	if(!file_exists(JPATH_COMPONENT_ADMINISTRATOR.DS.'log.txt'))exit();
    	$mtime=filemtime(JPATH_COMPONENT_ADMINISTRATOR.DS.'log.txt');
		if(time() - $mtime > 30)exit();
		$log=json_decode(file_get_contents(JPATH_COMPONENT_ADMINISTRATOR.DS.'log.txt'));
		$log->last_response=time() - $mtime;
        if($log->cur_row==-1){
            $log->last_response=1;
        }
		echo json_encode($log);
		jexit();
	}

    function get_yml_stat(){
    	if(!file_exists(JPATH_COMPONENT_ADMINISTRATOR.DS.'yml-log.txt'))exit();
    	$mtime=filemtime(JPATH_COMPONENT_ADMINISTRATOR.DS.'yml-log.txt');
		if(time() - $mtime > 30)exit();
		$log=json_decode(file_get_contents(JPATH_COMPONENT_ADMINISTRATOR.DS.'yml-log.txt'));
		$log->last_response=time() - $mtime;
		echo json_encode($log);
		jexit();
	}

    function get_yml_export_stat(){
    	if(!file_exists(JPATH_COMPONENT_ADMINISTRATOR.DS.'yml-export-log.txt'))exit();
    	$mtime=filemtime(JPATH_COMPONENT_ADMINISTRATOR.DS.'yml-export-log.txt');
		if(time() - $mtime > 5)exit();
		$log=json_decode(file_get_contents(JPATH_COMPONENT_ADMINISTRATOR.DS.'yml-export-log.txt'));
		$log->last_response=time() - $mtime;
		echo json_encode($log);
		jexit();
	}

	function abort(){
    	file_put_contents(JPATH_COMPONENT_ADMINISTRATOR.DS.'abort.txt',1);
		jexit();
	}

    function abort_yml(){
    	file_put_contents(JPATH_COMPONENT_ADMINISTRATOR.DS.'yml-abort.txt',1);
		jexit();
	}

    function download(){
    	$model = $this->getModel($this->view);
        $msg = $model->download();
		jexit();
	}

    function delete(){
    	$model = $this->getModel($this->view);
        $msg = $model->delete();
		jexit();
	}

    function delete_all(){
    	$model = $this->getModel($this->view);
        $msg = $model->delete_all();
		jexit();
	}
    function upload(){
       $model = $this->getModel();
       $msg = $model->upload();
       jexit();
    }

    function update_files(){
       $model = $this->getModel($this->view);
       $msg = $model->update_files();
       jexit();
    }

    function get_export_stat(){
    	if(!file_exists(JPATH_COMPONENT_ADMINISTRATOR.DS.'export'.DS.'export_log.txt'))exit();
    	$mtime=filemtime(JPATH_COMPONENT_ADMINISTRATOR.DS.'export'.DS.'export_log.txt');
		if(time() - $mtime > 20){
		  @$data->notmodified=1;
		  $data->row=0;
		  echo json_encode($data);
          exit();
		}
		echo @file_get_contents(JPATH_COMPONENT_ADMINISTRATOR.DS.'export'.DS.'export_log.txt');
		jexit();
	}
    function display($cachable = false, $urlparams = false) {
      if (JFactory::getUser()->authorise("core.excel2vm", "com_excel2vm")){
		JSubMenuHelper::addEntry(JText::_('IMPORT'), 'index.php?option=com_excel2vm',$this->view=='excel2vm'?true:false);
      }
      if (JFactory::getUser()->authorise("core.export", "com_excel2vm")){
		JSubMenuHelper::addEntry(JText::_('EXPORT'), 'index.php?option=com_excel2vm&view=export',$this->view=='export'?true:false);
      }
      if (JFactory::getUser()->authorise("core.yml", "com_excel2vm")){
		JSubMenuHelper::addEntry(JText::_('YML'), 'index.php?option=com_excel2vm&view=yml',$this->view=='yml'?true:false);
      }
      if (JFactory::getUser()->authorise("core.config", "com_excel2vm")){
		JSubMenuHelper::addEntry(JText::_('CONFIGURATIONS'), 'index.php?option=com_excel2vm&view=config',$this->view=='config'?true:false);
      }
      if (JFactory::getUser()->authorise("core.backup", "com_excel2vm")){
		JSubMenuHelper::addEntry(JText::_('RECOVER'), 'index.php?option=com_excel2vm&view=backup',$this->view=='backup'?true:false);
      }
      if (JFactory::getUser()->authorise("core.support", "com_excel2vm")){
		JSubMenuHelper::addEntry(JText::_('SUPPORT'), 'index.php?option=com_excel2vm&view=support',$this->view=='support'?true:false);
      }
	  JFactory::getDocument()->addStyleSheet(JURI::root()."administrator/components/com_excel2vm/assets/style.css");
	  JFactory::getDocument()->addStyleSheet(JURI::root()."administrator/components/com_excel2vm/assets/jquery-ui-1.8.17.custom.css");
    	@$doc = JFactory::getDocument();
		if(substr(JVERSION,0,1)==3){
            JHtml::_('jquery.framework');
            //JHtml::_('bootstrap.framework');
            $doc->addScript('components/com_excel2vm/js/jquery-ui.min.js');
        }
        else{
            $doc->addScript('components/com_excel2vm/js/jquery-1.7.1.min.js');
    		$doc->addScript('components/com_excel2vm/js/jquery-ui.min.js');
    		/*$doc->addScript('https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js');
    		$doc->addScript('https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js');
            $doc->addStyleSheet("https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/themes/smoothness/jquery-ui.css");*/
            $doc->addScriptDeclaration ( 'jQuery.noConflict();' );
        }
        $doc->addScript('components/com_excel2vm/js/jquery.form.js');
        $doc->addScript('components/com_excel2vm/js/jquery.tablesorter.min.js');
        parent :: display($cachable = false, $urlparams = false);
    }

}
?>