<?php
/*------------------------------------------------------------------------
# com_vdata - vData
# ------------------------------------------------------------------------
# author    Team WDMtech
# copyright Copyright (C) 2014 www.wdmtech.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.wdmtech.com
# Technical Support:  Forum - http://www.wdmtech.com/support-forum
-----------------------------------------------------------------------*/
defined( '_JEXEC' ) or die( 'Restricted access' );

class VdataRouter extends JComponentRouterBase
{
	public function build(&$query)
	{
		$db = JFactory::getDbo();
		$segments = array();
		
		if(isset($query['view'])){
			if($query['view']=='display'){
				if(!isset($query['limitstart']) && isset($query['start'])){$query['limitstart'] = $query['start'] ;unset($query['start']);}
				$segments[] = $query['view'];
				unset($query['view']);
				if(isset($query['layout'])){
					switch($query['layout']){
						case 'items':
							$segments[] ='items';
							unset($query['layout']);
							if (isset($query['displayid'])){
								$db = JFactory::getDbo();
								$q = 'select id,uidprofile from #__vd_display where id = '.$db->quote($query['displayid']);
								$db->setQuery( $q );
								$results = $db->loadObject();
								if(!empty($results)){
									$segments[] =$results->uidprofile;
								}
								else{
									$segments[] =$query['displayid'];
								}
								unset($query['displayid']);
								foreach($query as $key=>$value){
									if($key!='option'&&$key!='lang'&&$key!='Itemid'){
										$segments[]=$key;
										$segments[] = $value;
										unset($query[$key]);
									}
								}
							}
					   break;
					   case 'item':
							$segments[] ='item';
							unset($query['layout']);
							if (isset($query['displayid'])){
								$db = JFactory::getDbo();
								$q = 'select id,uidprofile from #__vd_display where id = '.$db->quote($query['displayid']);
								$db->setQuery( $q );
								$results = $db->loadObject();
								if(!empty($results)){
									$segments[] =$results->uidprofile;
								}
								else{
									$segments[] =$query['displayid'];
								}
								unset($query['displayid']);	
							
								if(isset($query['id'])){
									$db = JFactory::getDbo();
									$q = 'select uniquealias,profileid,qry,uniquekey from #__vd_display where uidprofile ='.$db->quote($segments[2]);
									$db->setQuery( $q );
									$results = $db->loadObject();
								
									$uniqueAlias=$results->uniquealias;
									$uniqueKey=$results->uniquekey;
									if(!empty($results->profileid)){
										$query1="select params from #__vd_profiles where id=".$db->quote($results->profileid);
										$db->setQuery( $query1 );
										$result1=$db->loadResult();
										
										$params=json_decode($result1);
										$table=$params->table;
									}
								
									if(!empty($results->qry)){
										$qryArray=explode(" ",strtolower($results->qry));
										$keyIndex=array_search("from",$qryArray);
										$table=$qryArray[$keyIndex+1];
									}

									$query2="select $uniqueAlias from $table where $uniqueKey = '".$query['id']."'";
									$db->setQuery( $query2 );
									$aliasTitle=$db->loadResult();
									$segments[] = $aliasTitle;
									unset($query['id']);
								}
							}
					   break;
					}
				}
			}
			else{
				$segments[] =$query['view'];
				unset($query['view']);
			}
		}
		
		return $segments;
   }
	


	public function parse( &$segments )
	{
		$vars = array();
		$app = JFactory::getApplication();
		$menu = $app->getMenu();
		$item = $menu->getActive();
		// Count segments
		$count = count($segments); 
		for ($i = 0; $i < $count; $i++){
			$segments[$i] = preg_replace('/-/', ':', $segments[$i]);
		}	
		if ($count){
			$segments[0] = str_replace(':', '-', $segments[0]);
			switch($segments[0]){
				case 'display':
					$vars['view'] = 'display';
					$count--;
					array_shift($segments);
					if($count){
						$segments[0] = str_replace(':', '-', $segments[0]);
						switch ($segments[0])
						{	   
							case 'items':
								$vars['layout'] ='items';
								$count--;
								array_shift($segments);
								if($count)
								{
									$segments[0] = str_replace(':', '-', $segments[0]);
									$db = JFactory::getDbo();
									$q = 'select id from #__vd_display where uidprofile ='.$db->quote($segments[0]);
									$db->setQuery( $q );
									$results = $db->loadObject();
									$vars['displayid'] =$results->id;
									$count--;
									array_shift($segments);
									if($count){
										$vars[$segments[0]]=$segments[1];
									}
								}
							break;
							case 'item':
								$vars['layout'] ='item';
								$count--;
								array_shift($segments);
								if($count){
									$segments[0] = str_replace(':', '-', $segments[0]);
									$db = JFactory::getDbo();
									$q = 'select id from #__vd_display where uidprofile ='.$db->quote($segments[0]);
									$db->setQuery( $q );
									$results = $db->loadObject();
									$vars['displayid'] =$results->id;
									$count--;
								    array_shift($segments);
								    if($count){
										$segments[0] = str_replace(':', '-', $segments[0]);
										$vars['id']=$segments[0];
									}
								}
							break;
						}
					}
				break;
				default:
					$vars['view'] = $segments[0];
				
			}
		}
		return $vars;
	}
}
	
function VdataBuildRoute(&$query)
{
	$router = new VdataRouter;
	return $router->build($query);
}

function VdataParseRoute($segments)
{ 
	$router = new VdataRouter;
	return $router->parse($segments);
}

