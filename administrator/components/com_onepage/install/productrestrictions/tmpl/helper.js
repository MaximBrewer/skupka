function changeCountryNotice(el) {
  var d = el.options[el.selectedIndex].value; 
  jQuery('.countrylistdata').hide(); 
  jQuery('#country_data_'+d).show(); 
  
}

