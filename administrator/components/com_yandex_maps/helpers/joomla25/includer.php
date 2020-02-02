<?php
if (get_magic_quotes_gpc()) {
    $process = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);
    while (list($key, $val) = each($process)) {
        foreach ($val as $k => $v) {
            unset($process[$key][$k]);
            if (is_array($v)) {
                $process[$key][stripslashes($k)] = $v;
                $process[] = &$process[$key][stripslashes($k)];
            } else {
                $process[$key][stripslashes($k)] = stripslashes($v);
            }
        }
    }
    unset($process);
}
jimport('joomla.version');
$version = new JVersion();
if (version_compare($version->RELEASE, '3.1')<0) {
    /*if (version_compare($version->RELEASE, '2.6')<0) {
        class JEventDispatcher extends JDispatcher {}
    }*/
	require_once 'layouts/layout.php';
	require_once 'layouts/base.php';
	require_once 'layouts/file.php';
	require_once 'layouts/helper.php';
	require_once 'html.php';
	require_once 'behavior.php';
	require_once 'sortablelist.php';
	require_once 'bootstrap.php';
	require_once 'formbehavior.php';
	require_once 'jquery.php';
	require_once 'select.php';
	require_once 'form.php';
	require_once 'field.php';
	require_once 'grid.php';
	require_once 'searchtools.php';
	require_once 'normalise.php';
	require_once 'jform.php';

	require_once 'list.php';
	require_once 'pagination/object.php';
	require_once 'pagination/pagination.php';
	JFactory::getDocument()->addStyleSheet(JUri::root().'/administrator/components/com_yandex_maps/helpers/joomla25/jui/css/template.css');
	//JFactory::getDocument()->addStyleSheet(JUri::base().'components/com_yandex_maps/helpers/joomla25/bootstrap/css/bootstrap.min.css');
	//JFactory::getDocument()->addStyleSheet(JUri::base().'components/com_yandex_maps/helpers/joomla25/bootstrap/css/bootstrap-responsive.min.css');
	JFactory::getDocument()->addScript(JUri::root().'/administrator/components/com_yandex_maps/helpers/joomla25/jui/js/jquery.min.js');
	JFactory::getDocument()->addScript(JUri::root().'/administrator/components/com_yandex_maps/helpers/joomla25/jui/js/bootstrap.min.js');
	JFactory::getDocument()->addScript(JUri::root().'/administrator/components/com_yandex_maps/helpers/joomla25/jui/js/template.js');
}

if (version_compare($version->RELEASE, '3.1')==0) {
	require_once 'searchtools.php';
}