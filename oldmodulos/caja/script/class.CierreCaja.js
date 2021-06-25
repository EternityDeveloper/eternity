var CierreCaja = new Class({
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
	doPrintRecibo : function(id){
		var instance=this; 
		instance.post("./?mod_caja/delegate&print_dialog",{id:id},function(data){ 
 			instance.doDialog("DetalleImprimir",instance.dialog_container,data); 
			instance.addListener("onCloseWindow",function(){
			//	window.location.reload();
			});
			
			$('#detalle_imprimir').load(function(){
				$('#detalle_imprimir')[0].contentWindow.print();
				setTimeout(function(){
					instance.CloseDialog("DetalleImprimir");
				},9000);
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
	doViewEditPerson : function(id_nit){ 
		var instance=this;
		this._person_component= new ModuloPersonas('Datos personales',this.dialog_container,this.dialog_container);
		var person= new Persona(this._person_component);
		person.addListener("onEditPerson",function(data){
			alert(data.mensaje);
		});
		/*PONGO EL MODULO DE PERSONA EN MODO EDITAR*/
		person.setView("edit");
		/**********************************/
		person.addListener("onViewCreate",function(){
			$("#tipo_clte").hide();			  
			$("#sys_clasificacion_persona").hide();
		});
				
		this._person_component.addModule(person);
		
		var direccion= new Direccion(this._person_component);
		this._person_component.addModule(direccion);
		
		direccion.addListener("doLoadViewComplete",function(obj){
		//	instance.insertIntoView(obj)
		});
		
		/**/
		var empresa= new personEmpresa(this._person_component);
		this._person_component.addModule(empresa);
		/***************************************************/
		 
		/**/
		var telefono= new Telefono(this._person_component);
		this._person_component.addModule(telefono);
		/***************************************************/
		 
		var email= new Email(this._person_component);
		this._person_component.addModule(email);	
	 
		
		/*Y vuelvo a cargar la vista*/
		this._person_component.loadMainView();
		/**************************************/
  
		/*Le digo que cliente es el que sera editado*/
		this._person_component.setPersonID(id_nit);
		
		this._person_component.selected();

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