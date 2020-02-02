
function tabClick(tabid)
{
  
  // ul 
  tab = document.getElementById(tabid);
  ul2 = tab.parentNode;
  ul = ul2.parentNode;
  var rels = [];
  for (var i = 0; i<ul.childNodes.length; i++)
  {
   ul.childNodes[i].className = ""; 
   for (var j = 0; j<ul.childNodes[i].childNodes.length; j++)
    {
     if (ul.childNodes[i].childNodes[j].className == "selected")
     {
      ul.childNodes[i].childNodes[j].className = "";
	  
     }
	 if (typeof ul.childNodes[i].childNodes[j].rel != 'undefined')
	  {
		  rels.push(ul.childNodes[i].childNodes[j].rel); 
	  }
	 
    }
   
  }
  
  // li
  tab.parentNode.className = "selected";
  tab.className = "selected"
  for (var i=0; i<rels.length; i++)
  {
	  
	  {
	  var d = document.getElementById(rels[i]); 
	  if (d != null)
	  {
		  if (rels[i] != tab.rel)
		  d.style.display = 'none'; 
		  else
		  d.style.display = 'block'; 
	  }
	  }
  }
  /*
  var tabcon = document.getElementById(tab.rel);
  var parentn = document.getElementById('tabscontent');
  for (i=0; i<parentn.childNodes.length; i++)
  {
    if (typeof(parentn.childNodes[i].style) != 'undefined')
    if (parentn.childNodes[i].id != tab.rel)
    parentn.childNodes[i].style.display = 'none';
    else parentn.childNodes[i].style.display = 'block';
  }
  */
  return false;
}

/* old code: 

function tabClick(tabid)
{
  
  // ul 
  tab = document.getElementById(tabid);
  ul2 = tab.parentNode;
  ul = ul2.parentNode;
  for (var i = 0; i<ul.childNodes.length; i++)
  {
   ul.childNodes[i].className = ""; 
   for (var j = 0; j<ul.childNodes[i].childNodes.length; j++)
    {
     if (ul.childNodes[i].childNodes[j].className == "selected")
     {
      ul.childNodes[i].childNodes[j].className = "";
     }
    }
   
  }
  // li
  tab.parentNode.className = "selected";
  tab.className = "selected"
  
  var tabcon = document.getElementById(tab.rel);
  var parentn = document.getElementById('tabscontent');
  for (i=0; i<parentn.childNodes.length; i++)
  {
    if (typeof(parentn.childNodes[i].style) != 'undefined')
    if (parentn.childNodes[i].id != tab.rel)
    parentn.childNodes[i].style.display = 'none';
    else parentn.childNodes[i].style.display = 'block';
  }
  return false;
}

*/



function setEqualHeight(columns)  
 {  
 var tallestcolumn = 0;  
 columns.each(  
 function()  
 {  
 if (typeof jQuery == 'undefined') return;
 currentHeight = jQuery(this).height();  
 if(currentHeight > tallestcolumn)  
 {  
 tallestcolumn  = currentHeight;  
 }  
 }  
 );  
 columns.height(tallestcolumn);  
 } 
 			function setEqualHeight2(columns)  
 {  
 var tallestcolumn = 0;  
 columns.each(  
 function()  
 {  
 currentHeight = jQuery(this).height();  
 if(currentHeight > tallestcolumn)  
 {  
 tallestcolumn  = currentHeight;  
 }  
 }  
 );  
 columns.height(tallestcolumn);  
 }
 
 if (typeof jQuery != 'undefined')
 {
jQuery(document).ready(function() {  
  jQuery('div#opc_basket').on('refresh', setH); 
  setH(); 

});  
 }
 
 function setH() {
	setEqualHeight(jQuery(".op_basket_row.op_basket_header  > div"));  
  setEqualHeight2(jQuery(".op_basket_row.op_basket_rows  > div")); 
 }
 
 
