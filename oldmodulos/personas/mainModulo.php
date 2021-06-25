<?php
if (!isset($protect)){
	exit;
}	 

?>
<div class="modal fade" id="doPersonModule" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog"  style="width:900px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="main_module_title">&nbsp;</h4>
      </div>
      
      <div class="modal-body"> 
        <div id="main_module"> 
          <div id="tabs_main">
            <ul id="ul_modulos"  class="tabs">
            </ul>
          </div>
        </div>
 
      </div> 
    </div>
  </div>
</div>