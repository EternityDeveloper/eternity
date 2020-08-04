<?php
/* esto es por si alguien accede a este link directamente*/
if (!isset($protect)){
	exit;
}	

if (!validateField($_REQUEST,"id_acta")){	  
	exit;
}

$_acta=json_decode(System::getInstance()->Decrypt($_REQUEST['id_acta'])); 
SystemHtml::getInstance()->includeClass("cobros","Actas");    
$actas=new Actas($protect->getDBLink());
$listado_c=array();
$data=STCSession::GI()->getSubmit("_info_acta");
$listado_c=$data['listado_c'];

$inf=System::getInstance()->Encrypt(json_encode(array("id_acta"=>$_acta)));

if (!isset($_acta->idacta)){
	exit;
}

$desistidos=0;
$monto_atraso=0;
foreach($listado_c as $key =>$row){
	$desistidos++;
	$monto_atraso=$monto_atraso+$row->monto_atraso;
}
 
?><div class="wizard" id="satellite-wizard" data-title="CIERRE DE ACTA"> 
    <!-- Step 1 Name & FQDN -->
    <div class="wizard-card" data-cardname="name">
        <h3><?php echo $_acta->idacta;?></h3>

        <div class="wizard-input-section">
            <div class="form-group">
                <div class="col-sm-6">
                  <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                      <td width="200" align="center"><strong>CONTRATOS</strong></td>
                    </tr>
                    <tr>
                      <td align="center"><?php echo $desistidos;?></td>
                    </tr>
                    <tr>
                      <td align="center">&nbsp;</td>
                    </tr>
                    <tr>
                      <td align="center"><strong>MONTO TOTAL</strong></td>
                    </tr>
                    <tr>
                      <td align="center"><?php echo number_format($monto_atraso,2);?></td>
                    </tr>
                    <tr>
                      <td>&nbsp;</td>
                    </tr>
                  </table>
                </div>
            </div>
        </div>
    </div>

    <div class="wizard-card" data-cardname="group">
        <h3>DESCARGAR ACTA</h3>

        <div class="wizard-input-section">
            <p>
              <button type="button" class="btn btn-danger" id="descarga_acta" value="<?php echo $inf;?>" >Descargar ACTA</button></p>
        </div>
    </div>

    <div class="wizard-card wizard-card-overlay" data-cardname="services">
        <h3>FIRMA DE ACTA</h3>
        <div class="wizard-input-section">
            <p>Se recomienda que descarge el acta y todos lo involucrados la firmen para poder continuar con el siguiente paso.
            </p>
        </div>
    </div>

    <div class="wizard-card wizard-card-overlay" data-cardname="location">
        <h3> DOCUMENTO</h3>

        <div class="wizard-input-section">
            <p>Cargue el documento del acta firmado.
            </p>
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td valign="top">Descripcion</td>
              </tr>
              <tr>
                <td valign="top"><textarea name="doc_descripcion" id="doc_descripcion" style="height:60px;" cols="25" rows="5" class="form-control"></textarea></td>
              </tr>
              <tr>
                <td valign="top">&nbsp;</td>
              </tr>
            </table>

             <form id="upload" method="post" action="./?mod_cobros/delegate&cierre_acta_listado&upload_doc&info_acta=<?php echo $inf;?>" enctype="multipart/form-data">
               <table width="100%" border="0" align="left" cellpadding="0" cellspacing="0">
      <tr>
        <td width="200">
			<div id="drop">
				Arrojar aqui 
				<a>Navegar</a>
				<input type="file" name="upl" multiple data-validate="validateUpload" />
			</div>


		 </td>
        <td valign="top">
			<ul>
			</ul></td>
      </tr>
    </table>
  </form> 

      </div>
    </div>
    <div class="wizard-card">
      <h3>FINALIZAR</h3> 
      <div class="wizard-input-section">
        <p>Paso final, dar click en procesar para procesar cierre de acta.</p>
        </div>
      <div class="wizard-error">
          <div class="alert alert-error">
                <strong>There was a problem</strong> with your submission.
                Please correct the errors and re-submit.
        </div>
      </div>

        <div class="wizard-failure">
            <div class="alert alert-error">
                <strong>There was a problem</strong> submitting the form.
                Please try again in a minute.
            </div>
        </div>

        <div class="wizard-success">
            <div class="alert alert-success">
                <span class="create-server-name"></span>Cierre efectuado.</strong>
            </div> 
          <a class="btn btn-success im-done wizard-close" id="close_w" aria-hidden="true">Cerrar</a>
        </div>
    </div>
</div>