<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>" >
<head>
<jdoc:include type="head" />

<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="css/template_css.css" type="text/css" />
<link rel="stylesheet" href="/templates/<?php echo $this->template ?>/css/dop_css.css?ver=1" type="text/css" />
<link rel="stylesheet" href="/templates/<?php echo $this->template ?>/css/fonts.css?ver=1" type="text/css" />
<link rel="stylesheet" href="/templates/<?php echo $this->template ?>/css/template_css.css?ver=1" type="text/css" />

</head>

<body>
<div id="jakor"></div>

<div class="header_1">
  <div class="header_1_left"><jdoc:include type="modules" name="position-1"  /></div>
  <div class="header_1_right"><jdoc:include type="modules" name="position-2" s /></div>
</div>


<div class="menu_1"><jdoc:include type="modules" name="position-3" /></div>
<div class="n2">
  <div class="navigator"><jdoc:include type="modules" name="position-4"  /></div>
</div>
<div class="com_2">
 <div class="component_left"><jdoc:include type="modules" name="position-20" style="moduletable" /></div>
  <div class="component"><jdoc:include type="component" /></div>
</div>
<div class="moduli"><jdoc:include type="modules" name="position-6"  /></div>
<div class="moduli_1"><jdoc:include type="modules" name="position-7" /></div>
<div class="moduli_3"><jdoc:include type="modules" name="position-8"  /></div>
<div class="moduli_3"><jdoc:include type="modules" name="position-9"  /></div>
<div class="moduli_4"><jdoc:include type="modules" name="position-10"  /></div>
<div class="moduli_5"><jdoc:include type="modules" name="position-11"  /></div>
<div class="moduli_6"><jdoc:include type="modules" name="position-12"  /></div>
<div class="moduli_7"><jdoc:include type="modules" name="position-18"  /></div>
<div class="moduli_8"><jdoc:include type="modules" name="position-19"  /></div>
<div class="footer_1">
  <div class="footer_1_2">
    <div class="footer_1_2_logo"><jdoc:include type="modules" name="position-13"  /></div>
    <div class="footer_1_2_mebu"><jdoc:include type="modules" name="position-14"  /></div>
  </div>
</div>
<div class="footer_2_2">
<div class="footer_2">
  <div class="footer_2_left"><jdoc:include type="modules" name="position-15"  /></div>
  <div class="footer_2_center"><jdoc:include type="modules" name="position-16"  /></div>
  <div class="footer_2_right"><jdoc:include type="modules" name="position-17"  /></div>
</div>
</div>
<a href="#jakor" class="jakor"></a>


  
  <script src="//oss.maxcdn.com/jquery.form/3.50/jquery.form.min.js"></script>
  <script src="/templates/<?php echo $this->template ?>/js/template.js"></script>
  <script src="/templates/<?php echo $this->template ?>/js/formplugin.js"></script>
<script src="/templates/<?php echo $this->template ?>/js/init.js"></script>

<script src="https://unpkg.com/imask"></script>
<script>
var elements = document.getElementsByClassName('tel2');
for (var i = 0; i < elements.length; i++) {
  new IMask(elements[i], {
    mask: '+{7}(000)000-00-00',
  });
}
</script>
<script type="text/javascript">
function change_visibility (block_4_close, block_4_open) {
    document.getElementById(block_4_close).style.display='none';
    document.getElementById(block_4_open).style.display='';
}
</script>

</body>
</html>
