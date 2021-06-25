<?php 
if (!isset($protect)){
	exit;
}

$f=1;
if ($f==0){
$topmenu = new topMenu();
	
?><div id="backgroundPopup"></div>
<table width="98%" border="0" id="header_menu">
  <tr>
    <td>
	<?php $topmenu->print_menu("jsddm");?>
	</td>
  </tr>
</table>
<?php } ?>