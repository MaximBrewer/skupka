<?php
/*
# author Roland Soos
# copyright Copyright (C) Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
*/
defined('_JEXEC') or die('Restricted access'); ?><?php
nextendimport('nextend.environment.request');

class NextendAjax {

    function parseRequest() {
        if (!isset($_REQUEST['mode']))
            return;
        switch ($_REQUEST['mode']) {
            case 'subform':
                $this->subform();
                break;
            case 'auth':
                $this->auth();
                break;
            case 'pluginmethod':
                $this->pluginmethod();
                break;
            default:
                break;
        }
    }

    function subform() {
        $response = array();
        if (!isset($_POST['data'])) {
            echo json_encode(array('error' => 'Post not OK!'));
            exit;
        }
        if (get_magic_quotes_gpc() || nextendIsWordPress()) {
            $_POST['data'] = stripslashes($_POST['data']);
        }
        $data = json_decode($_POST['data'], true);
        $configurationXmlFile = rtrim(NextendFilesystem::getBasePath(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $data['xml'];

        if (NextendFilesystem::fileexists($configurationXmlFile)) {
            nextendimport('nextend.css.css');
            nextendimport('nextend.javascript.javascript');
            $css = NextendCSS::getInstance();
            $js = NextendJavascript::getInstance();
            $js->loadLibrary('dojo');

            nextendimport('nextend.form.form');
            $form = new NextendForm();
            $form->loadArray($data['orig']);
            $form->loadArray(array($data['name'] => $data['value']));
            $form->loadXMLFile($configurationXmlFile);

            ob_start();
            $subform = $form->getSubform($data['tab'], $data['name']);
            $subform->initAjax($data['control_name']);
            echo $subform->renderForm();
            echo "<style>";
            echo $css->generateAjaxCSS($data['loadedCSS']);
            echo "</style>";
            $scripts = $js->generateAjaxJs($data['loadedJSS']);

            $html = ob_get_clean();

            $response = array(
                'html' => $html,
                'scripts' => $scripts
            );
        } else {
            $response = array('error' => 'Configuration file not found');
        }

        echo json_encode($response);
        exit;
    }

    function auth() {
        $folder = NextendRequest::getVar('folder');
        if ($folder) {
            $authfile = NextendFilesystem::pathToAbsolutePath($folder) . 'auth.php';
            if (NextendFilesystem::fileexists($authfile)) {
                require_once $authfile;
                if (function_exists('nextend_api_auth_flow')) {
                    nextend_api_auth_flow();
                }
            }
        }

        exit;
    }

    function pluginmethod() {
        $group = NextendRequest::getCmd('group', null);
        $method = NextendRequest::getCmd('method', null);
        if($group && $method){
            JPluginHelper::importPlugin($group);
            $dispatcher = JDispatcher::getInstance();
            $data = null;
            $results = $dispatcher->trigger($method, array(&$data));
        }
        echo json_encode($data);
        exit;
    }

}

if (isset($_REQUEST['nextendajax'])) {
    if (nextendIsJoomla()) {
        $app = JFactory::getApplication();
        if ($app->isAdmin()){
            $ajax = new NextendAjax();
            $ajax->parseRequest();
        }else{
            echo "This function only available in backend!";
            exit;
        }
    }else{
        $ajax = new NextendAjax();
        $ajax->parseRequest();
    }
}
?>