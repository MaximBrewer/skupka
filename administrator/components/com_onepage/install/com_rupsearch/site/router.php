<?php
function RupsearchBuildRoute(&$query)
{
       $segments = array();
	    $segments[] = 'rsearch';
       if (isset($query['view']))
       {
               
                unset($query['view']);
       }
	   
	   unset($query['opt_search']); 
	   unset($query['opt_search']); 
	   unset($query['module_id']); 
	   if (empty($query['limitstart']))
	   unset($query['limitstart']); 
	   unset($query['Search']); 
	   //unset($query['option']); 

       if (isset($query['keyword']))
       {
                $segments[] = $query['keyword'];
                unset($query['keyword']);
       };
	   unset($query['orderby']); 
       return $segments;
}

function RupsearchParseRoute($segments)
{
       $vars = array();
	   $vars['view'] = 'search';
	   
	   $vars['keyword'] = $segments[1];  
      
       return $vars;
}