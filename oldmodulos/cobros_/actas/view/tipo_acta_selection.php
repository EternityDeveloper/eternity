<?php 
if (!isset($protect)){
	exit;	
}
 
?>
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog"  style="width:400px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel"> ACTA</h4>
      </div>
      <div class="modal-body">
     
			<table width="359px" border="0" cellspacing="0" cellpadding="0" >
                <tr>
                  <td width="50%" height="100" align="center"><a href="#" id="anulados"><strong>ANULADOS</strong></a></td>
                  <td width="50%" align="center"><a href="#" id="desistidos"><strong>DESISTIDOS</strong></a></td>
                </tr>
            </table>
      </div>
 
    </div>
  </div>
</div>