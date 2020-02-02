<?php
/*------------------------------------------------------------------------
# com_offlajn_installer - Offlajn Installer
# ------------------------------------------------------------------------
# author    Balint Polgarfi
# copyright Copyright (C) 2012 Offlajn.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.offlajn.com
-------------------------------------------------------------------------*/
?>
<?php
defined('_JEXEC') or die('Restricted access');
jimport('joomla.installer.helper');
jimport('joomla.filesystem.folder');

if(!function_exists('deleteExtFolder')){

  function deleteExtFolder() {
    $pkg_path = JPATH_ADMINISTRATOR.'/components/com_offlajn_installer/extensions';
    if (file_exists($pkg_path)) JFolder::delete($pkg_path);
    if (!version_compare(JVERSION,'1.6.0','lt')) {
      $db = JFactory::getDBO();
      $db->setQuery("DELETE FROM #__menu WHERE title='com_offlajn_installer'");
      $db->query();
    }
  }

  function com_install(){
    register_shutdown_function("deleteExtFolder");
    $installer = new Installer();
    $installer->install();
    return true;
  }

  function com_uninstall(){
    $installer = new Installer();
    $installer->uninstall();
    return true;
  }

  class Installer extends JObject {

    var $name = 'Offlajn Installer';
    var $com = 'com_offlajn_installer';

    function install() {
      $db = JFactory::getDBO();
      $pkg_path = JPATH_ADMINISTRATOR.'/components/'.$this->com.'/extensions/';

      $suc = glob($pkg_path . '*/installmsg/successful.html');
      $suc = isset($suc[0]) ? file_get_contents($suc[0]) : '';
      $unsuc = glob($pkg_path . '*/installmsg/unsuccessful.html');
      $unsuc = isset($unsuc[0]) ? file_get_contents($unsuc[0]) : '';

      $extensions = array_merge(JFolder::folders($pkg_path,'^(?!com_)\w+$'),JFolder::folders($pkg_path,'^com_\w+$'));
      $v3 = version_compare(JVERSION,'3.0.0','ge');
      if ($v3) {
        foreach($extensions as $pkg) {
          $f = $pkg_path.'/'.$pkg;
          $xmlfiles = JFolder::files($f, '.xml$', 1, true);
          foreach($xmlfiles AS $xmlf){
            $file = file_get_contents($xmlf);
            file_put_contents($xmlf, preg_replace("/<\/install/","</extension",preg_replace("/<install/","<extension",$file)));
          }
        }
      }
      $error = array();
      ob_start();
      foreach($extensions as $pkg) {
        if ($pkg == 'plg_sticky_toolbar' && !$v3) continue;
        $installer = new JInstaller();
        $installer->setOverwrite(true);
        try {
          $success = $installer->install($pkg_path.'/'.$pkg);
        } catch (Exception $ex) {}
        if ($success) {
          $ext = explode('_', $pkg, 2);
          if ($ext[0] == 'plg') {
            $db->setQuery("UPDATE `#__extensions` SET enabled = 1 WHERE element = '{$ext[1]}' AND type = 'plugin' LIMIT 1");
            $db->query();
          }
          $msgcolor = "#E0FFE0";
          $msgtext  = "$pkg successfully installed.";
        } else {
          $msgcolor = "#FFD0D0";
          $msgtext  = "ERROR: Could not install the $pkg. Please contact us on our support page: http://offlajn.com/support.html";
          $error[] = "Could not install $pkg";
        } ?>
        <table bgcolor="<?php echo $msgcolor; ?>" width ="100%">
          <tr style="height:30px">
            <td><font size="2"><b><?php echo $msgtext; ?></b></font></td>
          </tr>
        </table><?php
        if ($success && file_exists("$pkg_path/$pkg/install.php")) {
          require_once "$pkg_path/$pkg/install.php";
          $com = new $pkg();
          $com->install();
        }
      }
      $table = ob_get_contents();
      ob_end_clean();
      if (count($error)) {
        if ($unsuc) {
          echo $unsuc;
          foreach ($error as $e) { echo $e.'<br/>'; }
          echo '</div>';
        } else echo $table;
      } else echo $suc ? $suc : $table;

      if (version_compare(JVERSION,'1.6.0','lt')) {
        $db->setQuery("UPDATE #__plugins SET published=1 WHERE name LIKE '%offlajn%' OR name LIKE 'Nextend Joomla 3.0 compatibility' OR name LIKE 'Nextend Dojo Loader'");
      } else {
        $db->setQuery("UPDATE #__extensions SET enabled=1 WHERE (name LIKE '%offlajn%' OR name LIKE 'Nextend Joomla 3.0 compatibility' OR name LIKE 'Nextend Dojo Loader') AND type='plugin'");
      }
      $db->query();
    }

    function uninstall() {
    }

  }

  class com_offlajn_installerInstallerScript
  {
    function install($parent) {
      com_install();
    }

    function uninstall($parent) {
      com_uninstall();
    }

    function update($parent) {
      com_install();
    }
  }
}