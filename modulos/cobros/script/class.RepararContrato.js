var RepararContrato = new Class({
	dialog_container : null,
	config: null,
	enc_id_nit :0,
	_view : null,
	initialize : function(dialog_container){
		this.main_class="CierreCaja";
		this.dialog_container=dialog_container;   
	},	
	setView : function(){
		var instance=this;
		$("#bt_cierre_caja").click(function(){
			var id_caja=$("#sele_id_caja option:selected").val();
			var tipo_cierre=$("#tipo_cierre option:selected").val();
			instance.post("./?mod_caja/delegate&operacion",{"processCierreCaja":1,"id_caja":id_caja,"tipo_cierre":tipo_cierre},function(data){ 
				alert(data.mensaje);
				if (data.valid){
					window.location.reload();
				}
			
			},"json");
			
		});
		
		$("#agregar_fact").click(function(){
		 	
		});
	},	
	
	doCierre : function(){
		var instance=this;
		$("#bt_cierre_caja").click(function(){
 			if (confirm('Esta seguro que desea procesar el cierre!')){  
				instance.post("./?mod_caja/delegate&procesarCierre",{},function(data){
					if (!data.valid){
						window.location.href="./?mod_caja/delegate&cierres";	
					}else{
						alert(data.mensaje)	
					} 
				},"json");	 
			}
		}); 
	},
	doListadoRecibo : function(){
		var instance=this;
		$(".textfield").datepicker({
				changeMonth: true,
				changeYear: true,
				yearRange: '1900:2050',
				monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'], 
				monthNamesShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'], 
				dateFormat: 'dd-mm-yy',  
				dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sabado'], 
				dayNamesMin: ['D', 'L', 'M', 'X', 'J', 'V', 'S'], 
				dayNamesShort: ['Dom', 'Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab'], 
					
			});		
 			createTable("listado_recibo_tb",{
				"bSort": false,
				"bFilter": true,
				"bInfo": false,
				"bPaginate": true,
				"bLengthChange": false,
				"oLanguage": {
						"sLengthMenu": "Mostrar _MENU_ registros por pagina",
						"sZeroRecords": "No se ha encontrado - lo siento",
						"sInfo": "Mostrando _START_ a _END_ de _TOTAL_ registros",
						"sInfoEmpty": "Mostrando 0 to 0 of 0 registros",
						"sInfoFiltered": "(filtrado de _MAX_ total registros)",
						"sSearch":"Buscar"
					},
				"fnDrawCallback": function( oSettings ) {
						//$(".listado_recibo").click(function(){
						//	instance.doAsignarRecibo($(this).attr("id"));
					//	});	
						 
						
					} 					
				});		 
	 

	},
	doFix : function(id){
		var instance=this; 
		instance.post("./?mod_cobros/delegate&doRepararContrato",{id:id},function(data){ 
 			instance.doDialog("DetalleImprimir",instance.dialog_container,data); 
			instance.addListener("onCloseWindow",function(){ 
			}); 
			
			
			$("#mode_edit").click(function(){
				instance.post("./?modeEdit=true",{},
				function(data){  
					alert("Modo edicion activado!");  
				});
			});

			$("#eliminar_movimiento").click(function(){
				$("#eliminar_movimiento").prop('disabled',true);
				instance.post("./?mod_cobros/delegate&doRemoveMovimientos",
					{id:id},
				function(data){ 
					instance.CloseDialog("DetalleImprimir");
					alert("Proceso realizado!");
					setTimeout(function(){
						instance.doFix(id);	
					},1000);
				},"json");
			});
			
			instance.addListener("cambio_realizado",function(){
				instance.CloseDialog("DetalleImprimir"); 
				setTimeout(function(){
					instance.doFix(id);	
				},1000);	
			});
			
			
			$("#ajustar_contrato").click(function(){ 
				instance.doAjustarContrato(id);
			});
			
			
		});
	}, 
	doAjustarContrato: function(id){
		var instance=this; 
		instance.post("./?mod_contratos/listar&doViewAjustarContrato",{id:id},function(data){ 
 			instance.doDialog("myModal",instance.dialog_container,data); 
			instance.addListener("onCloseWindow",function(){ 
			}); 
			  
			$("#_cuotas").change(function(){ 
				instance.post("./?mod_contratos/listar&doCalcularAjusteContrato",
					{
						id:id,
						cuotas:$("#_cuotas").val(),
						por_interes:$("#_por_interes").val()
						},
				function(data){   
					 $("#_monto_interes").html(instance.number_format(data.interes));
					 $("#_compromiso").html(instance.number_format(data.monto_cuota));
					 
				},"json");
			});
			
			$("#aplicar_Cambio_contrato").click(function(){ 
				instance.post("./?mod_contratos/listar&doAplicarAjusteContrato",
					{
						id:id,
						cuotas:$("#_cuotas").val(),
						por_interes:$("#_por_interes").val()
						},
				function(data){  
					instance.CloseDialog("myModal");  
					alert('Cambio realizado!');
					instance.fire('cambio_realizado');
					 
				},"json");
			});			
			
			
			
		});
	},
	doSendEmailRecibo : function(id){
		var instance=this; 
		instance.post("./?mod_caja/delegate&doDialogSendMailRecibo",{id:id},function(data){ 
 			instance.doDialog("detalleDialogMail",instance.dialog_container,data); 
			instance.addListener("onCloseWindow",function(){
			//	window.location.reload();
			});
			$("#agregar_recibo_mail").click(function(){
				instance.doViewEditPerson($(this).attr("id_nit"));
			});
			
			var tiny=new TINY.editor.edit('editor',{
									id:'input',
									width:890,
									height:175,
									cssclass:'te',
									controlclass:'tecontrol',
									rowclass:'teheader',
									dividerclass:'tedivider',
									controls:['bold','italic','underline','strikethrough','|','subscript','superscript','|','orderedlist','unorderedlist','|','outdent','indent','|','leftalign','centeralign','rightalign','blockjustify','|','unformat','|','undo','redo','n','font','size','style','|','image','hr','link','unlink','|','cut','copy','paste','print'],
									footer:true,
									fonts:['Verdana','Arial','Georgia','Trebuchet MS'],
									xhtml:true,
									cssfile:'style.css',
									bodyid:'editor',
									footerclass:'tefooter',
									toggle:{text:'source',activetext:'wysiwyg',cssclass:'toggle'},
									resize:{cssclass:'resize'}
								});
			 
		});
	},
	doCierreView : function(pending_cierre,fecha){
		var instance=this; 
		$("#procesar_cierre").click(function(){
 			instance.doViewQuestion(pending_cierre,fecha); 
		});  
		
		$(".textfield").datepicker({
				changeMonth: true,
				changeYear: true,
				yearRange: '1900:2050',
				monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'], 
				monthNamesShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'], 
				dateFormat: 'dd-mm-yy',  
				dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sabado'], 
				dayNamesMin: ['D', 'L', 'M', 'X', 'J', 'V', 'S'], 
				dayNamesShort: ['Dom', 'Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab'], 
					
			});				
			  
		$("#oficial_cierre").select2();
		$("#oficial_cierre").on("change",function(e) { 
			$("#oficial_hd").val(e.val);
		});

		$(".fpago_evnt").click(function(){
		//	alert($(this).attr("id"));
			instance.doChangeRecibo($(this).attr("id"));
		});
		
		$("._btcerrar").click(function(){
			//alert($(this).val());
			instance.doCerrar($(this).val());
		});
		
	
			
	},
	doCerrar : function(id){
		var instance=this; 
		instance.post("./?mod_caja/delegate&doViewDepositarBanco",{id:id},function(data){ 
 			instance.doDialog("myModal",instance.dialog_container,data); 
			instance.addListener("onCloseWindow",function(){
			//	window.location.reload();
			});
			this._rand=this.getRand();
			$("#doAgregarCierre").click(function(){ 			 
				instance.post("./?mod_caja/delegate&doAddToCierre",{
						"cierre":id,
						"monto_envio":$("#monto_envio").val(),
						"descripcion_envio":$("#descripcion_envio").val(),
						"_banco_destino":$("#_banco_destino").val(),
						"rand":instance._rand,
					},function(data){
						alert(data.mensaje)	
					
					},"json");		
			});
			 
			$(".btsavedetalle_fpago").click(function(){ 
			 	var id=$(this).attr("id") 
				instance.post("./?mod_caja/delegate&doSaveTipoCambioFormaPago",{
						"recibo":$(this).attr("value"),
						"tasa":$("#"+id+"_tasa").val(),
						"_forma_pago":$("#"+id+"_forma_pago").val()
					},function(data){
						alert(data.mensaje)	
					
					},"json");		
			});
			
						
		});			
	},
	doAsignarRecibo : function(id){
		var instance=this; 
		instance.post("./?mod_caja/delegate&doAsignarRecibo",{id:id},function(data){ 
 			instance.doDialog("myModal",instance.dialog_container,data); 
			instance.addListener("onCloseWindow",function(){
			});
 
			$("#doSaveAsignarRecibo").click(function(){  
				instance.post("./?mod_caja/delegate&doSaveAsignacionRecibo",{
						"recibo":id,
						"reporte_venta":$("#REPORTE_VENTA").val(),
						"motorizado":$('#motorizado').val(),
						"oficial":$('#motorizado').val()
					},function(data){
						alert(data.mensaje)	
					},"json");		
			});
	
			
						
		});			
	},
	doChangeRecibo : function(id){
		var instance=this; 
		instance.post("./?mod_caja/delegate&doEditRecibo",{id:id},function(data){ 
 			instance.doDialog("myModal",instance.dialog_container,data); 
			instance.addListener("onCloseWindow",function(){
				window.location.reload();
			});
			
			$("#doSaveEditRecibo").click(function(){ 
			 
				instance.post("./?mod_caja/delegate&doSaveChangeTasaRecibo",{
						"recibo":id,
						"tasa":$("#tasa_cambio").val()
					},function(data){
						alert(data.mensaje)	
					
					},"json");		
			});
			 
			$(".btsavedetalle_fpago").click(function(){ 
			 	var id=$(this).attr("id") 
				instance.post("./?mod_caja/delegate&doSaveTipoCambioFormaPago",{
						"recibo":$(this).attr("value"),
						"tasa":$("#"+id+"_tasa").val(),
						"_forma_pago":$("#"+id+"_forma_pago").val()
					},function(data){
						alert(data.mensaje)	
					
					},"json");		
			});
			
						
		});			
	},
	doViewQuestion : function(pending_cierre,fecha){
		var instance=this; 
		instance.post("./?mod_caja/delegate&cierres_question",{},function(data){ 
 			instance.doDialog("myModal",instance.dialog_container,data); 
			instance.addListener("onCloseWindow",function(){
				//alert('fsd');	
			});
			var dateToday =null;
			if (pending_cierre==0){
				dateToday=fecha;
			}else{	
				dateToday = new Date();
			}
			$("#p_fecha_desde").datepicker({  
				minDate: dateToday,	
				maxDate: dateToday,
				yearRange: '1900:2050',
				monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'], 
				monthNamesShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'], 
				dateFormat: 'yy-mm-dd',  
				dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sabado'], 
				dayNamesMin: ['D', 'L', 'M', 'X', 'J', 'V', 'S'], 
				dayNamesShort: ['Dom', 'Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab'],  
			});
			
			var tipo_cierre=null;
			var periodo=null;
			var id_caja=null;
			$("#tipo_cierre").change(function(){
				tipo_cierre=$(this).val(); 
			});	
			$("#p_fecha_desde").change(function(){
				periodo=$(this).val();  
			});
			$("#ID_CAJA").change(function(){
				id_caja=$(this).val();  
			});			
							
			$("#procesar_acta").click(function(){ 
			
				if (tipo_cierre==null){
					alert('Debe de seleccionar un tipo de cierre');
					return false;
				}
				if (id_caja==null){
					alert('Debe de seleccionar una caja');
					return false;
				}					
				if (periodo==null){
					alert('Debe de seleccionar un periodo');
					return false;
				}	  
				instance.post("./?mod_caja/delegate&savePeriodoCierre",{
						"tipo":tipo_cierre,
						"periodo":periodo,
						"id_caja":id_caja
					},function(data){
						if (!data.valid){
							window.location.href="./?mod_caja/delegate&cierres&view_detalle_cierre";	
						}else{
							alert(data.mensaje)	
						}
						
					},"json");		
			});
			
						
		});			
	},
	doAnularReciboCaja : function(id){
		var instance=this; 
		instance.post("./?mod_caja/delegate&viewReciboRemove",{"id":id},function(data){ 
 			instance.doDialog("myModal",instance.dialog_container,data); 
			instance.addListener("onCloseWindow",function(){
			});
			$("#procesar_anulacion_recibo").click(function(){ 	  
				instance.post("./?mod_caja/delegate&doAnularReciboCaja",{
						"id":id,
						"descripcion":$("#anulacion_descripcion").val()
					},function(data){
						if (!data.valid){
							alert(data.mensaje);
							window.location.reload()
						}else{
							alert(data.mensaje)	
						}
						
					},"json");		
			});
			
						
		});			
	}
	
	
	
	
});