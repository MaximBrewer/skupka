function actttfsp() {
var f=document.getElementById("ttfspForm");
	for (var i = 0; i<f.elements.length; i++) {
		 if (null!=f.elements[i].getAttribute("maxlength")) {
			if (f.elements[i].value.length > f.elements[i].getAttribute("maxlength")){
				alert(nolen+f.elements[i].getAttribute("maxlength"));
				f.elements[i].style.border="2px solid #F00";
				f.elements[i].focus();
				return false;
			}
		}
		 if (null!=f.elements[i].getAttribute("required")) {
			if (isEmpty(f.elements[i].value)){
		eltitle='';
		 if (null!=f.elements[i].getAttribute("error")){
			eltitle = f.elements[i].getAttribute("error");
		} else {
			if (null!=document.getElementById('t'+f.elements[i].id)){
				eltitle = document.getElementById('t'+f.elements[i].id).value;
			}
		}
				f.elements[i].style.border="2px solid #F00";
				alert(eltitle+' '+noempty);
				f.elements[i].focus();
				return false;
			}
		}
		 if (null!=f.elements[i].getAttribute("mask")) {
			if (validmask(f.elements[i].value,  f.elements[i].getAttribute("mask"))){
				f.elements[i].style.border="2px solid #F00";
				alert(nomask+f.elements[i].getAttribute("mask"));
				f.elements[i].focus();
				return false;
			}
		}

	}
	return true;
}
function isEmpty(str) {
   		for (var i = 0; i < str.length; i++)
      		if (" " != str.charAt(i))
        		return false;
     	return true;
}
function validmask(nval, mask){
		len=nval.length;
		if(len == 0) return false;
		for(i=0; i<len; i++){
			if (mask.indexOf(nval.charAt(i))<0){
		 return true;
		}
		}
		return false;
}
function isValidEml(eml, strict){
		if(!eml) return true;
 		if ( !strict ) eml = eml.replace(/^\s+|\s+$/g, "");
 		return (/^([a-z0-9_\-]+\.)*[a-z0-9_\-]+@([a-z0-9][a-z0-9\-]*[a-z0-9]\.)+[a-z]{2,4}$/i).test(eml);
}
function limitchar( elemid, charlimit ){
elementid='ttfsp_'+elemid;
cntchar='cntch'+elemid;
ttfspform='ttfspForm';
	if (document.getElementById(ttfspform).elements[elementid]) {
		if (document.getElementById(ttfspform).elements[elementid].value.length > charlimit) {
		alert(nolimit);
			document.getElementById(ttfspform).elements[elementid].value = document.getElementById(ttfspform).elements[elementid].value.substring(0,charlimit);
		} else {
			if (document.getElementById(ttfspform).elements[cntchar]) {
				document.getElementById(ttfspform).elements[cntchar].value = charlimit - document.getElementById(ttfspform).elements[elementid].value.length;
			}
		}
	}
}
function editrec(publ, vl, id, rmail){
	if (publ==2){
		var cnf = vl==1 ? yesrecept : norecept;
		if (!confirm(cnf)) return;
	}
	document.ttfspedForm.publ.value = publ;
	document.ttfspedForm.vl.value = vl;
	document.ttfspedForm.id.value = id;
	document.ttfspedForm.rmail.value = rmail;	
	document.ttfspedForm.submit();	
}
function del_recept(id){
			if (!confirm(delrecept)) return;
			document.getElementById("idDelRec").value = id;
			document.getElementById("delRec").submit();
}
function del_order (idrec, number_order){
			if (!confirm(delorder)) return;
			document.getElementById("IdOrdRec").value = idrec;
			document.getElementById("NumOrdRec").value = number_order;
			document.getElementById("delNumOrdRec").submit();
}
