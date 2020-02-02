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

$tag = urlencode(JRequest::getString('tag', '', 'GET'));

$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_URL => 'http://offlajn.com/index2.php?option=com_offlajn_related_news&format=raw&tag='.$tag
));
$resp = curl_exec($curl);
curl_close($curl);

echo $resp;
exit;