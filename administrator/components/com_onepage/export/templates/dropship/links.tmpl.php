<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
JHtml::_('jquery.framework');
JHTML::stylesheet('https://cdnjs.cloudflare.com/ajax/libs/uikit/3.0.0-beta.8/css/uikit.min.css'); 
JHTML::stylesheet('https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css'); 
JHTML::script('https://cdnjs.cloudflare.com/ajax/libs/uikit/3.0.0-beta.8/js/uikit.min.js'); 


?>
<table class="uk-table">
    
	<caption>Generated XLS</caption>
    <thead>
        <tr>
			<th>Supplier</th>
			<th>Link</th>
            
        </tr>
    </thead>
    
    <tbody>
	 <?php foreach ($fout as $ind=>$file) { 
	 
	  $item = $ehelper->getExportItem($tidd['tid'], $fhash[$ind]);
	  $link = $ehelper->getPdfLink($item);
	 ?>
	 <tr><td><?php echo $mfs[$ind]['mf_name']; ?></td>
	 <td><a href="<?php echo $link; ?>"><?php echo $file; ?></a></td></tr>
	 <?php } ?>
	</tbody>
</table>