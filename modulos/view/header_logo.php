<?php
/* esto es por si alguien accede a este link directamente*/
if (!isset($protect)){
	exit;
}

//print_r($protect->getIfAccessPageById(2));
 
//print_r($protect->getIDPermiso(2));
//echo $protect->getRoleId();

 
?><div class="content_menu">
<ul id="menu">
    <li id="icon"><a href="#"> </a></li>
<?php if (($protect->getIfAccessPageById(2))){ ?>
    <li><a href="./?mod_client/client_list" class="drop">Datos Personales</a><!-- Begin Home Item --><!-- End 2 columns container -->
	  <div class="dropdown_1column">
        
                <div class="col_1">
                
                    <ul class="simple">
                    <?php if ($protect->getIfAccessPageById(11)){ ?>
                        <li><a href="./?mod_client/client_add">Agregar persona</a></li>
                    <?php } ?>    
                        <li><a href="./?mod_client/client_list">Listar persona</a></li>
                    </ul>   
                     
                </div>
                
		</div>
    </li><!-- End Home Item -->
<?php } ?>    
<?php if (($protect->getIfAccessPageById(89))){ ?>
<li><a href="#" class="drop">Caja</a><!-- Begin Home Item --><!-- End 2 columns container -->
  <div class="dropdown_1column">
    
        <div class="col_1">
            <ul>  
            <?php if (($protect->getIfAccessPageById(89))){ ?>
            	 <li><a href="./?mod_caja/delegate&operacion">Operacion de caja</a></li> 
            <?php } ?>                   
            <?php if (($protect->getIfAccessPageById(158))){ ?>            
                 <li><a href="./?mod_caja/delegate&cierres">Cierres</a></li>           
            <?php } ?> 
            <?php if (($protect->getIfAccessPageById(159))){ ?>            
                 <li><a href="./?mod_caja/delegate&recibos_list">Listar recibos</a></li>           
                                             
            <?php } ?>  
            <?php if (($protect->getIfAccessPageById(194))){ ?>            
                 <li><a href="./?mod_caja/delegate&cheque_devueltos_list">Listar cheques devueltos</a></li                         
            ><?php } ?>                                                            
               <?php if (($protect->getIfAccessPageById(177))){ ?>   
                <li><a href="./?mod_caja/delegate&listado_recibo_motorizado">Cobrar por motorizado</a></li>
                <li><a href="./?mod_caja/delegate&facturar_lote">Anular recibos en lote</a></li>
               <?php  } ?>   
            </ul>   
             
        </div>
        
    </div>
</li><!-- End Home Item -->
<?php } ?>  
<?php if (($protect->getIfAccessPageById(197))){ ?>
<li><a href="#" class="drop">Capillas</a><!-- Begin Home Item --><!-- End 2 columns container -->
  <div class="dropdown_1column">
    
        <div class="col_1">
            <ul>  
            <?php if (($protect->getIfAccessPageById(89))){ ?>
            	 <li><a href="./?mod_capillas/delegate&obituario">Obituarios</a></li> 
            <?php } ?>         
            </ul>   
             
        </div>
        
    </div>
</li><!-- End Home Item -->
<?php } ?>    
<?php if (($protect->getIfAccessPageById(183))){ ?>
<li><a href="./?mod_cobros/delegate&dashboard" class="drop">Cobro</a><!-- Begin Home Item --><!-- End 2 columns container -->
  <div class="dropdown_1column">
    
        <div class="col_1">
            <ul>  
            <?php if (($protect->getIfAccessPageById(183))){ ?>
            	 <li><a href="./?mod_cobros/delegate&dashboard">Dashboard</a></li> 
                 <li><a href="./?mod_cobros/delegate&requerimiento">Req. de cobro</a></li>    
				 <!-- <li><a href="./?mod_cobros/delegate&zonas">Zonificación</a></li> -->                                 
 			<?php } ?>                   
               <li><a href="./?mod_cobros/delegate&cierre_acta">Listado Acta</a></li>            
             <!--  
                 <li><a href="./?mod_cobros/delegate&metas">Meta Cobro</a></li>  
                 <li><a href="./?mod_cobros/delegate&ubicacion">Ubicación Cartera</a></li>  
                 <li><a href="./?mod_cobros/delegate&listarRuta">Listar Ruta</a></li>  
                   -->             
                            
            </ul>   
             
        </div>
        
    </div>
</li><!-- End Home Item -->
<?php } ?>    
<?php if ($protect->getIfAccessPageById(87) || $protect->getIfAccessPageById(88)){ ?>
<li><a href="#" class="drop">Contratos</a><!-- Begin Home Item --><!-- End 2 columns container -->
	  <div class="dropdown_1column">
        
            <div class="col_1">
                <ul>  
                  <?php if ($protect->getIfAccessPageById(87)){ ?>
                    <li><a href="./?mod_contratos/listar">Listado de Ofertas</a></li>
                   <?php } ?>   
                     <?php if ($protect->getIfAccessPageById(88)){ ?> 
                    <li><a href="./?mod_contratos/list_contratos">Listado contrato</a></li> 
                      <?php } ?>                    
                                   
                </ul>   
                 
            </div>
            
		</div>
    </li><!-- End Home Item -->
 <?php } ?>  
<?php if ($protect->getIfAccessPageById(36)){ ?>
    <li>
    <a href="?mod_estructurac/list_view2" class="drop">Estructura comercial</a>
	  <div class="dropdown_1column">
        
            <div class="col_1">
                <ul>  
                     <li> <a href="?mod_estructurac/list_view2" class="drop">Listar</a></li>  
                  <?php  //if ($protect->getIfAccessPageById(87)){ ?>
                    <li><a href="./?mod_estructurac/list_view2&listar_metas=1">Metas de ventas</a></li>
                   <?php //} ?>  
                                  
                </ul>   
                 
            </div>
            
		</div>

    </li>
<?php } ?>
<?php if (($protect->getIfAccessPageById(52)) ||  
			($protect->getIfAccessPageById(77))||
			($protect->getIfAccessPageById(80))||
			($protect->getIfAccessPageById(81))   ){ ?>
   <li><a href="#" class="drop">Producto</a><!-- Begin 4 columns Item -->
    
      <div class="dropdown_4columns"><!-- Begin 4 columns container -->
 		<?php if (($protect->getIfAccessPageById(52)) ){?>
        <div class="col_1">
            
          <h3>Inventario</h3>
                <ul>
                    <li><a href="?mod_inventario/inventario_list">Listar</a></li>
                </ul>   
                 
        </div>
         <?php } ?>    
         <?php if (($protect->getIfAccessPageById(77))){?>
        <div class="col_1">
            
                <h3>Reservas</h3>
                <ul>
                    <li><a href="?mod_inventario/reserva/listado_reserva">Listar</a><a href="?mod_inventario/mante_jardines_fases"></a></li> 
                </ul>   
                 
          </div>
          <?php } ?>    
           <?php if (($protect->getIfAccessPageById(81)) || ($protect->getIfAccessPageById(80))){?>
 			<div class="col_1">
            
                <h3>Planes de Financiamiento</h3>
                <ul>
                    <li><a href="?mod_financiamiento/listar">Listar</a></li> 
                    <?php if ($protect->getIfAccessPageById(81)){?>
                    <li><a href="?mod_financiamiento/descuentos/listar">Descuentos</a></li> 
                    <?php } ?>
                    <li><a href="?mod_financiamiento/listar&pl_inters_comision">Plazos interes & comision</a></li> 
                </ul>   
             
          </div>         
         <?php } ?>    
      </div><!-- End 4 columns container -->
    
    </li><!-- End 4 columns Item -->   
<?php } ?>
 <?php if (($protect->getIfAccessPageById(78)) || ($protect->getIfAccessPageById(97))  || ($protect->getIfAccessPageById(83))  || ($protect->getIfAccessPageById(84))){?>
    <li><a href="#" class="drop">Ventas</a><!-- Begin 4 columns Item -->
      <div class="dropdown_1column">
        <div class="col_1">
          <ul class="simple">
            <?php if ($protect->getIfAccessPageById(78)){?>
            <li><a href="./?mod_prospectos/listar">Listar</a></li> 
             <?php } ?>  
            <?php if ($protect->getIfAccessPageById(150)){?>
            <li><a href="./?mod_prospectos/listar&reporte_asesor">Gestion x Asesor / Pilar</a></li> 
            <li><a href="./?mod_prospectos/listar&agenda">Mi agenda</a></li>   
             <?php } ?>  
            <?php if ($protect->getIfAccessPageById(149)){?>
             <li><a href="./?mod_prospectos/listar&report_gerente">Gestion x Gerentes / Pilar</a></li> 
             <li><a href="./?mod_prospectos/listar&agenda">Mi agenda</a></li> 
             <?php } ?>   
            <?php if ($protect->getIfAccessPageById(196)){?>
             <li><a href="./?mod_prospectos/listar&report_gerente_esp">Gestion x Gerentes / Pilar</a></li>  
             <?php } ?>     
            <?php 
				/* Listado de todos los prospectos 	*/
			if ($protect->getIfAccessPageById(97)){?>
            <li><a href="./?mod_prospectos/listado_all_prospecto">Listar todos</a></li> 
             <?php } ?>               
             <?php if ($protect->getIfAccessPageById(83)){?>
             <li><a href="?mod_prospectos/listar_fracasos">Listar Vencidos</a></li>
              <?php } ?>  
             <?php if ($protect->getIfAccessPageById(84)){?>
             <li><a href="?mod_prospectos/listar_reasignados">Listar Reasignados</a></li>
              <?php } ?>   
             <?php if ($protect->getIfAccessPageById(99)){?>
             <li><a href="?mod_prospectos/cartera_asesor">Mi Cartera</a></li>
              <?php } ?>                   
             <?php if ($protect->getIfAccessPageById(100)){?>
             <li><a href="?mod_prospectos/listado_asesor">Listado asesores</a></li>
              <?php } ?>  
             <?php if ($protect->getIfAccessPageById(104)){?>
             <li><a href="?mod_prospectos/consultar_clientes">Consultar contratos</a></li>
              <?php } ?>                
          </ul>
        </div>
      </div>
      </li>
 <?php } ?>     
 <?php if (($protect->getIfAccessPageById(65)) || ($protect->getIfAccessPageById(81)) ){ ?>     
    <li><a href="#" class="drop">Servicios</a><!-- Begin 4 columns Item -->
    
        <div class="dropdown_4columns"><!-- Begin 4 columns container -->
		<?php if ($protect->getIfAccessPageById(81)){?>
          <div class="col_1">
            
            <h3>Servicios</h3>
                <ul>
                    <li><a href="?mod_servicios/servicios_list">Listar</a></li>
                   
                </ul>   
                 
          </div>
        <?php } ?>
       <?php if ($protect->getIfAccessPageById(81)){?> 
          <div class="col_1">
            
                <h3>Componentes</h3>
                <ul>
                    <li><a href="?mod_servicios/componentes">Listar</a><a href="#"></a></li>
                </ul>   
                 
            </div>
      </div><!-- End 4 columns container -->
     <?php } ?>
    
    </li><!-- End 4 columns Item -->
<?php } ?>
 <?php if ( 
			($protect->getIfAccessPageById(59))|| 
			($protect->getIfAccessPageById(55))|| 
			($protect->getIfAccessPageById(95)) ||
			($protect->getIfAccessPageById(90))  ){ ?>  
     
    <li><a href="#" class="drop">Mantenimiento</a><!-- Begin 4 columns Item -->
    
         <div class="dropdown_4columns"><!-- Begin 4 columns container -->
          <?php if (($protect->getIfAccessPageById(55)) || 
			($protect->getIfAccessPageById(59))){?> 
          <div class="col_1">
            <h3>Jardines</h3>
            <ul>
             <?php if ($protect->getIfAccessPageById(81)){?> 
              <li><a href="?mod_inventario/mante_jardines_fases">Jardines y Fase</a></li>
              <?php } ?> 
               <li><a href="?mod_inventario/jardines">Activos de Jardines</a></li> 
            </ul>
          </div>
          <?php } ?>
          <?php if (($protect->getIfAccessPageById(95))){?> 
          <div class="col_1">
            <h3>Reservas</h3>
            <ul> 
              <li><a href="?mod_inventario/reserva/reservar&amp;tipo_reserva=1">Tipo reservas</a></li> 
            </ul>
          </div>
          <?php } ?>
          <?php if ($protect->getIfAccessPageById(90)){  ?>
          <div class="col_1">
            <h3>Prospectos</h3>
            <ul>
              <li><a href="?mod_prospectos/listar&amp;tipo_pilar">Tipo de Pilares</a></li>
              
            </ul>
          </div>
           <?php if ($protect->getIfAccessPageById(102)){  ?>
           <div class="col_1">
            <h3>Cierres</h3>
            <ul>
              <li><a href="?mod_estructurac/delegate&cierre">Fechas de cierres</a></li>
              
            </ul>
          </div>  
          <?php } ?>
          
           <?php //if ($protect->getIfAccessPageById(155)){  ?>
           <div class="col_1">
            <h3>Papeleria</h3>
            <ul>
              <li><a href="?mod_papeleria/delegate&listar">Listar</a></li>  
              <li><a href="?mod_papeleria/delegate&listar_asignar">Asignar Asesor</a></li>
              <li><a href="?mod_papeleria/delegate&listarDocumentos">Documentos</a></li>                 
            </ul>
          </div>  
          <?php //} ?> 
                      

		  <div class="col_1">
            <h3>Caja</h3>
            <ul>
              <li><a href="?mod_caja/delegate&caja">Caja</a></li> 
              <li><a href="?mod_configuracion/delegate&tipo_documento">Tipo de Documentos</a></li>  
              <li><a href="?mod_configuracion/delegate&tipo_movimiento">Tipo de Movimientos</a></li>   
            </ul>
          </div>
		  <div class="col_1">
            <h3>Cat&aacute;logos Planillas</h3>
            <ul>
              <?php if ($protect->getIfAccessPageById(138)){?> 
                 <li><a href="./?mod_planillas/pl_centralfile_cat&choice=<?=System::getInstance()->Encrypt(1)?>">Tabla  Comisiones Asesor</a></li>
              <?php } ?> 
              <?php if ($protect->getIfAccessPageById(138)){?> 
                 <li><a href="./?mod_planillas/pl_centralfile_cat&choice=<?=System::getInstance()->Encrypt(2)?>">Tabla  Bono x Auxilio</a></li>
              <?php } ?> 
              <?php if ($protect->getIfAccessPageById(138)){?>  
                 <li><a href="./?mod_planillas/pl_centralfile_cat&choice=<?=System::getInstance()->Encrypt(3)?>">Tabla  Bono x Plan</a></li>
              <?php } ?>
              
               <?php if ($protect->getIfAccessPageById(138)){?>  
                 <li><a href="./?mod_planillas/pl_centralfile_cat&choice=<?=System::getInstance()->Encrypt(4)?>">Ingresos-Descuentos</a></li>
              <?php } ?>
                
              <?php if ($protect->getIfAccessPageById(138)){?> 
                 <li><a href="./?mod_planillas/pl_centralfile_cat&choice=<?=System::getInstance()->Encrypt(5)?>">Tabla Comisiones Gerente</a></li>
              <?php } ?> 
              
              <?php if ($protect->getIfAccessPageById(138)){?> 
                 <li><a href="./?mod_planillas/pl_centralfile_cat&choice=<?=System::getInstance()->Encrypt(6)?>">Tabla Diferidos</a></li>
              <?php } ?>  
             </ul>
          </div>
		  <div class="col_1">
            <h3>Cat&aacute;logos Cobros</h3>
            <ul>
             
              <!-- Cobros -->
               <?php if ($protect->getIfAccessPageById(138)){?> 
                 <li><a href="./?mod_planillas/pl_centralfile_cat&choice=<?=System::getInstance()->Encrypt(7)?>">Comisiones Cobros</a></li>
              <?php } ?> 
              <?php if ($protect->getIfAccessPageById(138)){?> 
                 <li><a href="./?mod_planillas/pl_centralfile_cat&choice=<?=System::getInstance()->Encrypt(8)?>">Ingresos-Descuentos</a></li>
              <?php } ?>
              <?php if ($protect->getIfAccessPageById(138)){?> 
                 <li><a href="./?mod_planillas/pl_centralfile_cat&choice=<?=System::getInstance()->Encrypt(9)?>">Tipos de Incentivo</a></li>
              <?php } ?>  
              
            </ul>
          </div>                           
     	<?php } ?>
         </div>
    
    </li><!-- End 4 columns Item -->
<?php } ?>    
 <?php if (($protect->getIfAccessPageById(15)) || 
			($protect->getIfAccessPageById(21))|| 
			($protect->getIfAccessPageById(18))   ){ ?> 
    <li><a href="#" class="drop">Configuracion</a><!-- Begin 4 columns Item -->
    
      <div class="dropdown_1column"><!-- Begin 4 columns container -->
      <ul>
        <?php if ($protect->getIfAccessPageById(15)){?>        
 			<li><a href="?mod_security/pantalla_list">Pantallas del sistema</a><a href="#"></a></li>
   		 <?php } ?> 
       <?php if ($protect->getIfAccessPageById(21)){?>         
          <li><a href="?mod_security/menu_list">Menu del sistema</a><a href="#"></a></li>
    	 <?php } ?> 
  		<?php if ($protect->getIfAccessPageById(18)){?>         
           <li><a href="?mod_security/role_list">Roles y Permisos</a></li> 
         <?php } ?>  
   
      </ul>   
 	</div>
    </li><!-- End 4 columns Item -->
  <?php } ?>    
     <?php if ($protect->getIfAccessPageById(82)){?>          
    <li><a href="#" class="drop">Reportes</a><!-- Begin 4 columns Item -->
      <div class="dropdown_1column">
        <div class="col_1">
          <ul class="simple">
     <?php if ($protect->getIfAccessPageById(156)){?>  
            <li><a href="./?mod_reportes/report&type=2">Reporte de Ventas</a></li> 
      <?php }?>   
     <?php if ($protect->getIfAccessPageById(157)){?>  
            <li><a href="./?mod_cobros/delegate&zonas&distribuccion=1">Distribución de clientes</a></li> 
      <?php }?>         
      
      <?php if ($protect->getIfAccessPageById(157)){?>          
            <li><a href="./?mod_reportes/report&type=6">Reporte produccion</a></li> 
            <li><a href="./?mod_reportes/report&type=10">Detalle de Ventas</a></li> 
            <li><a href="./?mod_reportes/report&type=11">Detalle Ventas x producto</a></li>             
      <?php }?>   
      <?php if ($protect->getIfAccessPageById(170)){?>          
            <li><a href="./?mod_reportes/report&type=8">Mi produccion</a></li> 
      <?php }?>         
      <?php if ($protect->getIfAccessPageById(172)){?>          
            <li><a href="./?mod_reportes/report&type=9">Reporte venta</a></li> 
      <?php }?>  
      <?php if ($protect->getIfAccessPageById(198)){?>          
            <li><a href="./?mod_reportes/report&type=12">Reporte venta</a></li> 
      <?php }?>         
      <?php if ($protect->getIfAccessPageById(172)){?>          
            <li><a href="./?mod_reportes/report&type=6">Reporte Auditoria</a></li> 
      <?php }?>   
      <?php if ($protect->getIfAccessPageById(0)){?>          
            <li><a href="./?mod_reportes/report&type=3">Reporte asesor</a></li> 
      <?php }?>   
     <?php if ($protect->getIfAccessPageById(0)){?>  
            <li><a href="./?mod_reportes/report&type=4">Reporte analisis</a></li> 
      <?php }?>  
      

          </ul>
        </div>
      </div>
      </li>
    <?php } ?>  
     <?php if ($protect->getIfAccessPageById(155)){?>          
    <li><a href="#" class="drop">Archivo</a><!-- Begin 4 columns Item -->
      <div class="dropdown_1column">
        <div class="col_1">
          <ul class="simple">
     <?php if ($protect->getIfAccessPageById(189)){?>  
            <li><a href="./?mod_archivo/delegate&listado">Listar</a></li> 
            <li><a href="./?mod_archivo/delegate&listado_persona">Asignar correlativo</a></li> 
      <?php }?>      
           <?php if ($protect->getIfAccessPageById(155)){  ?>
           <div class="col_1">
            <h3>Papeleria</h3>
            <ul>
              <?php if ($protect->getIfAccessPageById(187)){?>              
              <li><a href="?mod_papeleria/delegate&listar">Listar</a></li>  
		      <?php }?>                    
              <?php if ($protect->getIfAccessPageById(186)){?>  
              <li><a href="?mod_papeleria/delegate&listar_asignar">Asignar recibos</a></li>
              <?php }?>            
              <?php if ($protect->getIfAccessPageById(188)){?>                
              <li><a href="?mod_papeleria/delegate&listarDocumentos">Documentos</a></li>                 
		      <?php }?>      
            </ul>
          </div>  
          <?php } ?>       
          </ul>
        </div>
      </div>
      </li>
    <?php } ?>       
  
<?php               
   if ($protect->getIfAccessPageById(114)){?>    
       <li><a href="#" class="drop">Planillas</a><!-- Begin 4 columns Item -->
      
       <div class="dropdown_1column">
        <h3>Asesores</h3>
        <div class="col_1">
           <ul class="simple">
             <?php if ($protect->getIfAccessPageById(137)){ ?>  
                     <!-- <li><a href="./?mod_planillas/pl_main_file&generar">Generar Datos</a></li> -->
                      <!--<li><a href="./?mod_planillas/pl_genera_calculos">Generar Datos</a></li> -->                 
                      <li><a href="./?mod_planillas/pl_centralfile_ase&choice=<?=System::getInstance()->Encrypt(1)?>">Generar Comisiones</a></li>
             <?php }?>
              <?php if ($protect->getIfAccessPageById(137)){ ?>     
                      <li><a href="./?mod_planillas/pl_centralfile_ase&choice=<?=System::getInstance()->Encrypt(2)?>">Bono x Plan</a></li>
                       
             <?php }?>   
             <?php if ($protect->getIfAccessPageById(137)){ ?>     
                      <li><a href="./?mod_planillas/pl_centralfile_ase&choice=<?=System::getInstance()->Encrypt(3)?>">Bono x Auxilio</a></li>
                       
             <?php }?>  
             
             <?php if ($protect->getIfAccessPageById(137)){ ?>     
                      <li><a href="./?mod_planillas/pl_centralfile_ase&choice=<?=System::getInstance()->Encrypt(6)?>">Generar Diferidos</a></li>
                       
             <?php }?>    

             <?php if ($protect->getIfAccessPageById(137)){ ?>     
                      <li><a href="./?mod_planillas/pl_centralfile_ase&choice=<?=System::getInstance()->Encrypt(4)?>">Ingresos/Descuentos (Asesores)</a></li>
                       
             <?php }?>   
                
             <?php if ($protect->getIfAccessPageById(137)){ ?>     
                      <li><a href="./?mod_planillas/pl_centralfile_ase&choice=<?=System::getInstance()->Encrypt(5)?>">Impresi&oacute;n de Planilla</a></li>
                      
             <?php }?>   
             <?php if ($protect->getIfAccessPageById(200)){ ?>     
                      <li><a href="./?mod_planillas/delegate&listado_cxc">Cuentas x Cobrar</a></li>
              <?php }?>                
          </ul>
        </div>
        <h3>Gerentes</h3>
          <div class="col_1">
           <ul class="simple">
             <?php if ($protect->getIfAccessPageById(131)){ ?>           
                      <li><a href="./?mod_planillas/pl_centralfile_ger&choice=<?=System::getInstance()->Encrypt(1)?>">Generar Comisiones</a></li>
             <?php }?>
             
             <?php if ($protect->getIfAccessPageById(131)){ ?>           
                      <li><a href="./?mod_planillas/pl_centralfile_ger&choice=<?=System::getInstance()->Encrypt(2)?>">Ingresos y Descuentos (Gerentes)</a></li>
             <?php }?>             
             
             <?php if ($protect->getIfAccessPageById(131)){ ?>     
                      <li><a href="./?mod_planillas/pl_centralfile_ger&choice=<?=System::getInstance()->Encrypt(3)?>">Impresion Planilla Gerente</a></li>
                      
             <?php }?>
             <?php if ($protect->getIfAccessPageById(200)){ ?>     
                      <li><a href="./?mod_planillas/delegate&listado_cxc_gerente">Cuentas x Cobrar</a></li>
              <?php }?>              
             
          </ul>
     
        </div>
      
 
      </div>
      </li>
    <?php } ?>          

        
	<li class="menu_right"><a href="#" class="drop"><img src="images/settings.fw.png"></a>
    
		<div class="dropdown_1column align_right">
        
                <div class="col_1">
                
                    <ul class="simple">
                        <li><a href="./?logoff">Salir</a></li>
                    </ul>   
                     
                </div>
                
		</div>
        
	</li>

    


</ul>
</div>