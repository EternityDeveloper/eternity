<?php
/* esto es por si alguien accede a este link directamente*/
if (!isset($protect)){
	exit;
}	
 
?><div class="wizard" id="gestion-wizard" data-title="Solicitud de Gestión"> 
    <!-- Step 1 Name & FQDN -->
    <div class="wizard-card" data-cardname="name">
        <h3>Tipo gestión</h3> 
        <div class="wizard-input-section">
            <div class="form-group">
                <div class="col-sm-6">
                  <table width="100%" border="0" cellspacing="0" cellpadding="0" style="width:450px;">
                  <input type="hidden" data-validate="validate_gestion">
       		<?php 
			
			$SQL="SELECT * FROM `tipos_gestiones`";
			$rs=mysql_query($SQL);
			while($row=mysql_fetch_assoc($rs)){
				$id=System::getInstance()->Decrypt(json_encode($row));
			?>
                    <tr>
                      <td width="5" ><input type="radio" class="tipo_gestion_cc" name="tipo_gestion" id="tipo_gestion" value="<?php echo $id;?>" ></td>
                      <td width="200" ><?php echo $row['gestion'];?></td>
                    </tr>
             <?php }?>       
                    <tr>
                      <td colspan="2">&nbsp;</td>
                    </tr>
                  </table>
                </div>
            </div>
        </div>
    </div>

    <div class="wizard-card" data-cardname="group">
        <h3>Detalle</h3> 
        <div class="wizard-input-section">
          <table width="100" border="0" cellspacing="0" cellpadding="0" style="width:450px;">
             <tr>
               <td align="center" style="background-color:#CCCCCC"><strong>DATOS MONETARIOS DEL  CONTRATO</strong></td>
             </tr>
             <tr>
               <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
                 <tr>
                   <td><strong>Monto saldado    </strong></td>
                   <td>&nbsp;</td>
                 </tr>
                 <tr>
                   <td><strong>Monto a Abonar  </strong></td>
                   <td>&nbsp;</td>
                 </tr>
                 <tr>
                   <td><strong>Nuevo Saldo        </strong></td>
                   <td>&nbsp;</td>
                 </tr>
                 <tr>
                   <td><strong>Cambiar   Plazo</strong></td>
                   <td>&nbsp;</td>
                 </tr>
                 <tr>
                   <td><strong>Nuevo Plazo    </strong></td>
                   <td>&nbsp;</td>
                 </tr>
                 <tr>
                   <td><strong>Nueva Cuota $ </strong></td>
                   <td>&nbsp;</td>
                 </tr>
               </table></td>
             </tr>
           </table>
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