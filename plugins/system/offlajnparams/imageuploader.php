<?php
/*-------------------------------------------------------------------------
# plg_offlajnparams - Offlajn Params
# -------------------------------------------------------------------------
# @ author    Balint Polgarfi
# @ copyright Copyright (C) 2016 Offlajn.com  All Rights Reserved.
# @ license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @ website   http://www.offlajn.com
-------------------------------------------------------------------------*/
?><?php
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

$files = JRequest::get('FILES');

if (isset($_REQUEST['identifier']) && isset(${'_SESSION'}['offlajnupload']) && isset(${'_SESSION'}['offlajnupload'][$_REQUEST['identifier']])) {
  if (defined('DEMO')) {
    echo json_encode(array('err'=>'Upload NOT allowed on the DEMO site!') );
    exit;
  }
  $folder = ${'_SESSION'}['offlajnupload'][$_REQUEST['identifier']];
  if(isset($files['img'])){
    preg_match('/([^\s]+(\.(?i)(jpe?g|png|gif|bmp|svg))$)/', $files['img']['name'], $out);
    if(count($out) == 0){
      echo json_encode(array('err'=>$files['img']['name'].' is NOT an image') );
      exit;
    }
    move_uploaded_file($files['img']['tmp_name'], $folder.'/'.$files['img']['name']);
    echo json_encode( array('name' => $files['img']['name']) );
    exit;
  }
  preg_match('/([^\s]+(\.(?i)(jpe?g|png|gif|bmp|svg))$)/', $_REQUEST['name'], $out);
  if(count($out) == 0){
    echo json_encode(array('err'=>$_REQUEST['name'].' is NOT an image') );
    exit;
  }
	// Open temp file
	$out = fopen($folder.'/'.$_REQUEST['name'], "wb");
	if ($out) {
		// Read binary input stream and append it to temp file
		$in = fopen("php://input", "rb");

		if ($in) {
			while ($buff = fread($in, 4096))
				fwrite($out, $buff);
		}
		fclose($in);
		fclose($out);
	}
  echo json_encode(array());
  exit;
}
?>