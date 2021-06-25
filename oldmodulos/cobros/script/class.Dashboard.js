/*DASHBOARD COBRO*/
var DashBoard = new Class({
	dialog_container : null, 
	_id_gestion: null,
	_filtro_busqueda : {
					contrato:'',
					estatus:'',
					nombre_apellido:'',
					fecha_contrato:'',
					fecha_pago:'',
					no_cuota:'',
					cuotas_pagas:'',
					fecha_ultimo_pago:'',
					monto_a_cobrar:'',
					forma_de_pago:'',
					area_de_cobro:'',
					empresa:''
				},
	initialize : function(dialog_container){
		this.main_class="DashBoard";
		this.dialog_container=dialog_container; 
		var instance=this;
		this.addListener("changeRerport",function(){ 
			instance.post("./?mod_cobros/delegate&listado_cartera",{
					"filtro_busqueda": instance._filtro_busqueda 
			},function(data){
				$("#cartera_c_asignada").html(data); 
				
				$("#list_cartera").dataTable({
										"bFilter": false,
										"bInfo": false, 
										"bPaginate": false,
										  "oLanguage": {
												"sLengthMenu": "Mostrar _MENU_ registros por pagina",
												"sZeroRecords": "No se ha encontrado - lo siento",
												"sInfo": "Mostrando _START_ a _END_ de _TOTAL_ registros",
												"sInfoEmpty": "Mostrando 0 to 0 of 0 registros",
												"sInfoFiltered": "(filtrado de _MAX_ total registros)",
												"sSearch":"Buscar"
											}
										});	

				$(".list_contract_cartera").click(function(){ 
					window.location.href="./?mod_cobros/delegate&contrato_view&id="+$(this).attr("id");
				});
			});  
		})
	},	  
	 
	doDashboard : function(){ 
		var instance=this;
		/*
		$("#gestiones").dataTable({
			"bFilter": false,
			"bInfo": false,
			"bPaginate": false,
			  "oLanguage": {
					"sLengthMenu": "Mostrar _MENU_ registros por pagina",
					"sZeroRecords": "No se ha encontrado - lo siento",
					"sInfo": "Mostrando _START_ a _END_ de _TOTAL_ registros",
					"sInfoEmpty": "Mostrando 0 to 0 of 0 registros",
					"sInfoFiltered": "(filtrado de _MAX_ total registros)",
					"sSearch":"Buscar"
				}
			});
		$("#actividad").dataTable({
			"bFilter": false,
			"bInfo": false,
			"bPaginate": false,
			 "oLanguage": {
					"sLengthMenu": "Mostrar _MENU_ registros por pagina",
					"sZeroRecords": "No se ha encontrado - lo siento",
					"sInfo": "Mostrando _START_ a _END_ de _TOTAL_ registros",
					"sInfoEmpty": "Mostrando 0 to 0 of 0 registros",
					"sInfoFiltered": "(filtrado de _MAX_ total registros)",
					"sSearch":"Buscar"
				}
			});			
			*/
		 
		$("#p_fecha_desde").datepicker({
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
		$("#p_fecha_hasta").datepicker({
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
		
		$(".fecha").datepicker({
			changeMonth: true,
			changeYear: true,
			yearRange: '1900:2050',
			monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'], 
			monthNamesShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'], 
			dateFormat: 'yy-mm-dd',  
			dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sabado'], 
			dayNamesMin: ['D', 'L', 'M', 'X', 'J', 'V', 'S'], 
			dayNamesShort: ['Dom', 'Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab'], 
				
		});				
		
		$("#dash_filtro_avanzado").click(function(){
			instance.filtroAvanzado();
		});
		$("#_filter_call").click(function(){
			instance.Block();
			instance.post("./?mod_cobros/delegate&getDetalleGestion",{
						fdesde:$("#p_fdesde").val(),
						fhasta:$("#p_fhasta").val()
					},function(data){ 
				 $("#detalle_requerimiento").html(data); 
				 instance.unBlock();
			});	
		});		
		
		$("#_cXOficial").click(function(){
			instance.Block();
			instance.post("./?mod_cobros/delegate&getCobrosXoficial",{
						fdesde:$("#pp_fdesde").val(),
						fhasta:$("#pp_fhasta").val()
					},function(data){ 
				 $("#detalle_pendiente_x_cobrar").html(data); 
				 instance.unBlock();
			});	
		});	
		$("#_cXOficialMoto").click(function(){
			instance.Block();
			instance.post("./?mod_cobros/delegate&getDetalleCobro",{
									fdesde:$("#ppp_fdesde").val(),
									fhasta:$("#ppp_fhasta").val()
								},function(data){ 

			    $("#detalle_cobros_oficial_moto").html(data); 
				instance.unBlock();

				$(".oficial_cod_detalle").click(function(){  
				   $("."+$(this).attr("id")).toggle(); 
				});	
				$(".motorizado_detalle").click(function(){   
				   $("."+$(this).attr("id")).toggle(); 
				});									 

				$(".exportar_excel").click(function(){  
				   window.open("./?mod_cobros/delegate&exportToExcel&fdesde="+$("#ppp_fdesde").val()
				   +"&fhasta="+$("#ppp_fhasta").val()); 
				});								

			});	
 				
			window['doEditView']=function(id){  
				instance.doChangeMotorizadoOficialFromRecibo(id); 
			};				
			
		});		
		
		$(".exportar_excel").click(function(){  
		   window.open("./?mod_cobros/delegate&exportToExcel&fdesde="+$("#ppp_fdesde").val()
		   +"&fhasta="+$("#ppp_fhasta").val()); 
		});								
		
		$(".oficial_cod_detalle").click(function(){  
		   $("."+$(this).attr("id")).toggle(); 
		});						
		$(".motorizado_detalle").click(function(){   
		   $("."+$(this).attr("id")).toggle(); 
		});	 
		 
		window['doEditView']=function(id){  
			instance.doChangeMotorizadoOficialFromRecibo(id); 
		};			
		
					
//		this.generatePlot("chart1");
//		this.generatePlot("chart2"); 
		this.doCargarMyCartera();
		
	},  
	doChangeMotorizadoOficialFromRecibo :function(recibo){ 
		var instance=this; 
		var rand=this.getRand();
		instance.post("./?mod_cobros/delegate&doViewChangeOficialMotoFromRecibo",{ 
				'recibo':recibo ,
				"rand":rand
		},function(data){  
			instance.doDialog("myModal_"+rand,instance.dialog_container,data); 
			$("#aplicar_cambio").click(function(){ 
			
				if (!confirm("Esta seguro de realizar esta operacion?")){
					return false;
				}
				instance.post("./?mod_cobros/delegate&doChangeOficialToRecibo",{ 
						'recibo':recibo,
						'oficial':$("#oficial_n").val(),
						'motorizado':$("#motorizado_n").val(),
						'comentario':$("#comentario").val(),
				},function(data){ 
					alert(data.mensaje);
					if (data.valid){
						$("#myModal").modal('hide');
						//window.location.reload();
					}
				},"json");
 
			});
		});	 
	},
	doCargarMyCartera: function(){  
		var instance=this;
		var oficial=""; 
		var gerente="";
		var motorizado="";
		
		$("#_oficial").select2(); 
		$("#_oficial").on("change",function(e) { 
			oficial=e.val;
		});	
		$("#_motorizado").select2(); 
		$("#_motorizado").on("change",function(e) { 
			motorizado=e.val;
		});			
		
		$("#_gerente").select2(); 
		$("#_gerente").on("change",function(e) { 
			gerente=e.val;
			$("#_filtrar_reporte").click();
		});			
		var por_saldos="";
		$("#por_saldos").select2(); 
		$("#por_saldos").on("change",function(e) { 
			por_saldos=e.val;
		});					
		instance.doFiltrarCaltera({});
		$("#_filtrar_reporte").click(function(){  
			var data={
						"p_fecha_desde":$("#p_fecha_desde").val(),
						"p_fecha_hasta":$("#p_fecha_hasta").val(),
						"por_saldos":por_saldos,
						"por_estatus":$("#f_estatus").val(),
						"por_compromiso":$("#por_compromiso").val(),
						"monto_compromiso":$("#monto_compromiso").val(),
						"por_forma_pago":$("#f_forma_pago").val(),
						'contrato_condicion':$("#contrato_condicion").val(),
						'contrato_cuota':$("#contrato_cuota").val(),
						"tipo_cuota":$("#TIPO_CUOTA").val(),
						"oficial":oficial,
						"gerente":gerente,
						"motorizado":motorizado,
						"pendiente_de_pago":$("#pendiente_de_pago:checked").val()
					}

			instance.doFiltrarCaltera(data);
		});
		
		$("#exp_to_excel").click(function(){
			 
			 window.open("./?mod_cobros/delegate&exportMetaToExcel&p_fecha_desde="+$("#p_fecha_desde").val()
							   +"&p_fecha_hasta="+$("#p_fecha_hasta").val()+"&por_saldos="+por_saldos+"&por_estatus="+$("#f_estatus").val()+"&por_compromiso="+$("#por_compromiso").val()+"&monto_compromiso="+$("#monto_compromiso").val()+"&por_forma_pago="+$("#f_forma_pago").val()+"&oficial="+oficial+"&motorizado="+ motorizado +"&pendiente_de_pago="+$("#pendiente_de_pago:checked").val()); 					
		//	instance.doFiltrarCaltera(data);
		});
		
		$("#exp_to_pdf").click(function(){
			 
			 window.open("./?mod_cobros/delegate&exportMetaToPDF&p_fecha_desde="+$("#p_fecha_desde").val()
							   +"&p_fecha_hasta="+$("#p_fecha_hasta").val()+"&por_saldos="+por_saldos+"&por_estatus="+$("#f_estatus").val()+"&por_compromiso="+$("#por_compromiso").val()+"&monto_compromiso="+$("#monto_compromiso").val()+"&por_forma_pago="+$("#f_forma_pago").val()+"&oficial="+oficial+"&motorizado="+ motorizado +"&pendiente_de_pago="+$("#pendiente_de_pago:checked").val()); 					
		//	instance.doFiltrarCaltera(data);
		});				
		
		window['doViewContrato']=function(id,obj){
			$(obj).attr('class','green_selected');
			window.open("./?mod_cobros/delegate&contrato_view&id="+id);
		}			
	},
	doFiltrarCaltera : function(data){
		var instance=this; 
		
		this.Block();
		$.get("./?mod_cobros/delegate&getCarteraAsignada",data,function(data){
			$("#cartera_asignada").html(data);
			
			$("#list_cartera").dataTable({
				"order": [[ 2, "asc" ]],
				"aLengthMenu": [[50,100,300,-1],[50,100,300,2000,'All']],
				"bFilter": true,
				"bInfo": false,
				"bPaginate": true,
				 "oLanguage": {
						"sLengthMenu": "Mostrar _MENU_ registros por pagina",
						"sZeroRecords": "No se ha encontrado - lo siento",
						"sInfo": "Mostrando _START_ a _END_ de _TOTAL_ registros",
						"sInfoEmpty": "Mostrando 0 to 0 of 0 registros",
						"sInfoFiltered": "(filtrado de _MAX_ total registros)",
						"sSearch":"Buscar"
					},
				  "fnDrawCallback": function( oSettings ) {  
						for (i=0;i<oSettings.aoData.length;i++){ 
								var tiempo=parseInt($($(oSettings.aoData[i].nTr).children()[7]).html().trim()); 
								if (tiempo>=31){
									oSettings.aoData[i].nTr.className=oSettings.aoData[i].nTr.className+" AlertColorDanger"; 
								}
								if (tiempo>=25 && tiempo<=30){ 
									oSettings.aoData[i].nTr.className=oSettings.aoData[i].nTr.className+" AlertColor5"; 
								}								
 
							}  	
				  } 
				});	
				 
			instance.unBlock();		
		});			
	},
	doFacturarRecibos : function(){ 
		var instance=this;
		
		$(".filter_").datepicker({
			changeMonth: true,
			changeYear: true,
			yearRange: '1900:2050',
			monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'], 
			monthNamesShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'], 
			dateFormat: 'yy-mm-dd',  
			dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sabado'], 
			dayNamesMin: ['D', 'L', 'M', 'X', 'J', 'V', 'S'], 
			dayNamesShort: ['Dom', 'Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab'], 
				
		});	
				
		$("#no_codigo_barra").focus();	
		$("#no_codigo_barra").keypress(function(e){
			var code = e.keyCode || e.which;
			if(code == 13) {
				instance.post("./?mod_caja/delegate&doPutCobroMotorizado",{"id":$(this).val()},function(data){ 
					if (data.valid){
					 	var fac= new Facturar('content_dialog');
						fac.doView(data.id_nit,data.contrato,'',''); 	
						fac.addListener("onClose",function(){
							$("#no_codigo_barra").focus();
						});
						$("#no_codigo_barra").val('');	
						$("#no_codigo_barra").focus();	
					}
				},"json");	
			}			
		});	
		
		$(".recibo_remove").click(function(){ 
			var fact= new Facturar(instance.dialog_container);
			fact.doViewQuestionRemove($(this).val()); 					
		});			
	},
	doRequerimiento : function(){ 
		var instance=this;
	 		 
		$("#list_cartera").dataTable({
			"bFilter": false,
			"bInfo": false,
			"bPaginate": false,
			  "oLanguage": {
					"sLengthMenu": "Mostrar _MENU_ registros por pagina",
					"sZeroRecords": "No se ha encontrado - lo siento",
					"sInfo": "Mostrando _START_ a _END_ de _TOTAL_ registros",
					"sInfoEmpty": "Mostrando 0 to 0 of 0 registros",
					"sInfoFiltered": "(filtrado de _MAX_ total registros)",
					"sSearch":"Buscar"
				}
			});			
		
		var oficial="";
		$("#_oficial").select2(); 
		$("#_oficial").on("change",function(e){ 
			oficial=e.val;
		});			
			var data={
						"p_fecha_desde":$("#p_fecha_desde").val(),
						"p_fecha_hasta":$("#p_fecha_hasta").val() 
					}		
			instance.doFiltrarRequerimientos(data);		
		$("#_filtrar_buttom").click(function(){ 
			var data={
						"p_fecha_desde":$("#p_fecha_desde").val(),
						"p_fecha_hasta":$("#p_fecha_hasta").val(),
						"oficial":oficial,
						"pendiente_x_cobrar":$("#pendiente_x_cobrar").prop("checked")
					}		 
			instance.doFiltrarRequerimientos(data);
		});
		
		$(".list_contract_cartera").click(function(){ 
	//		instance.doRequerimientoView($(this).attr("id"));
		});
		
		$(".filter_").datepicker({
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
		
		$("#Imprimir").click(function(){
			instance.post("./?mod_caja/delegate&print_dialog_cobro",{},function(data){ 
				instance.doDialog("DetalleImprimir",instance.dialog_container,data); 
				instance.addListener("onCloseWindow",function(){
					window.location.reload();
				}); 
				$('#detalle_imprimir').load(function(){
					var mediaQueryList = window.matchMedia('print');   
					mediaQueryList.addListener(function(mql) {
						if (mql.matches) {
							alert('PRINT');
						} else {
							alert('sali')
						}
					});	
				 
					$('#detalle_imprimir')[0].contentWindow.print(); 			
					setTimeout(function(){
						//instance.CloseDialog("DetalleImprimir");
					},5000);
				});					
			});	

		});		 	
		 
		
	}, 
	doFiltrarRequerimientos : function(data){
		var instance=this; 
		
		this.Block();
		$.get("./?mod_cobros/delegate&doFiltrarRequerimientos",data,function(data){
			$("#requerimiento_cobros").html(data);
			
			$("#list_cartera").dataTable({
				"order": [[ 2, "asc" ]],
				"aLengthMenu": [[200,300,400,-1],[200,300,400,2000,'All']],
				"bFilter": true,
				"bInfo": false,
				"bPaginate": true,
				 "oLanguage": {
						"sLengthMenu": "Mostrar _MENU_ registros por pagina",
						"sZeroRecords": "No se ha encontrado - lo siento",
						"sInfo": "Mostrando _START_ a _END_ de _TOTAL_ registros",
						"sInfoEmpty": "Mostrando 0 to 0 of 0 registros",
						"sInfoFiltered": "(filtrado de _MAX_ total registros)",
						"sSearch":"Buscar"
					},
				  "fnDrawCallback": function( oSettings ) {    
						if (!instance._isCharge){
							 /*
							 $('<button id="client_add"  class="greenButton">Agregar</button>').appendTo('#listado_cliente div.dataTables_filter'); 	
							 $("#client_add").click(function(){ 
								instance.close(dialog);
								instance.doViewCreatePerson(); 	
								
							  });*/
							 
							
							instance._isCharge=true;
						}				  	
				  } 
				});	
				 
			$(".individual_c").click(function(){
				if ($(this).prop("checked")){
					instance.doToPrint($(this).val(),"add");
					$("#Imprimir").prop("disabled",false); 
				}else{
					instance.doToPrint($(this).val(),"rem");
					$("#Imprimir").prop("disabled",true); 
					$(".individual_c:checked").each(function(index, element) { 
						$("#Imprimir").prop("disabled",false); 
					});			
				}
			});					 
			instance.unBlock();		
		});			
	},	
	doToPrint : function(id,cmd){
		var instance=this; 
		instance.post("./?mod_cobros/delegate&requerimiento",{
			"doPrint":true, 
			'id':id,
			"cmd": cmd
		},function(data){  
			
		},"json")
	},	
	doRequerimientoView: function(id){ 
		var instance=this;
	/*	instance.post("./?mod_caja/delegate&recibo_factura",{
				"view_add_caja":'1',
				'id':id
		},function(data){  */
			var dialog=instance.createDialog(instance.dialog_container,"REQUERIMIENTO",'<iframe src="./?mod_caja/delegate&recibo_factura&id='+id+'" width="900" height="410"></iframe>',950);
			instance._dialog=dialog;
			var n = $('#'+dialog);
			n.dialog('option', 'position', [(document.scrollLeft/550), 0]); 
			
		//});
	},
	
	generatePlot : function(chart){
		
		var s1 = [[2002, 112000], [2003, 122000], [2004, 104000], [2005, 99000], [2006, 121000], 
		[2007, 148000], [2008, 114000], [2009, 133000], [2010, 161000], [2011, 173000]];
		var s2 = [[2002, 10200], [2003, 10800], [2004, 11200], [2005, 11800], [2006, 12400], 
		[2007, 12800], [2008, 13200], [2009, 12600], [2010, 13100]];
		
		plot1 = $.jqplot(chart, [s2, s1], { 
			animate: true,
			animateReplot: true, 
			series:[
				{
					pointLabels: {
						show: true
					},
					renderer: $.jqplot.BarRenderer,
					showHighlight: false,
					yaxis: 'y2axis',
					rendererOptions: {  
						animation: {
							speed: 2500
						},
						barWidth: 15,
						barPadding: -15,
						barMargin: 0,
						highlightMouseOver: false
					}
				}, 
				{
					rendererOptions: { 
						animation: {
							speed: 2000
						}
					}
				}
			],
			axesDefaults: {
				pad: 0
			},
			axes: { 
				xaxis: {
					tickInterval: 1,
					drawMajorGridlines: false,
					drawMinorGridlines: true,
					drawMajorTickMarks: false,
					rendererOptions: {
					tickInset: 0.5,
					minorTicks: 1
				}
				},
				yaxis: {
					tickOptions: {
						formatString: "$%'d"
					},
					rendererOptions: {
						forceTickAt0: true
					}
				},
				y2axis: {
					tickOptions: {
						formatString: "$%'d"
					},
					rendererOptions: { 
						alignTicks: true,
						forceTickAt0: true
					}
				}
			},
			highlighter: {
				show: true, 
				showLabel: true, 
				tooltipAxes: 'y',
				sizeAdjust: 7.5 , tooltipLocation : 'ne'
			}
		});
	},     
	filtroAvanzado : function(){
		var instance=this;
		instance.post("./?mod_cobros/delegate&filtro_cobro",{
				"filtro_busqueda": this._filtro_busqueda 
		},function(data){  
			var dialog=instance.createDialog(instance.dialog_container,"Filtro de busqueda",data,530);
			instance._dialog=dialog;
			var n = $('#'+dialog);
			n.dialog('option', 'position', [(document.scrollLeft/550), 100]); 
			   
			$("#act_cancel").click(function(){
				instance.close(dialog);	
			}); 
			
			$("._calendar_t").datepicker({
				changeMonth: true,
				changeYear: true,
				yearRange: '1900:2050',
				monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'], 
				monthNamesShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'], 
				dateFormat: 'dd-mm-yy',  
				dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sabado'], 
				dayNamesMin: ['D', 'L', 'M', 'X', 'J', 'V', 'S'], 
				dayNamesShort: ['Dom', 'Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab']  	
			});	
			
			$("#filtro_applicar").click(function(){
				   
				instance._filtro_busqueda.contrato=$("#f_contrato").val(); 
				instance._filtro_busqueda.estatus=$("#f_estatus").val(); 
				instance._filtro_busqueda.nombre_apellido=$("#f_nombre_apellido").val(); 
				instance._filtro_busqueda.fecha_contrato=$("#f_fecha_contrato").val(); 
				instance._filtro_busqueda.fecha_pago=$("#f_fecha_pago").val(); 
				instance._filtro_busqueda.no_cuota=$("#f_n_cuota").val(); 
				instance._filtro_busqueda.cuotas_pagas=$("#f_cuotas_pagas").val(); 
				instance._filtro_busqueda.fecha_ultimo_pago=$("#f_ultimo_pago").val(); 
				instance._filtro_busqueda.monto_a_cobrar=$("#f_monto_a_cobrar").val(); 
				instance._filtro_busqueda.forma_de_pago=$("#f_forma_pago").val(); 
				instance._filtro_busqueda.area_de_cobro=$("#f_area_de_cobro").val(); 
				instance._filtro_busqueda.empresa=$("#f_empresa").val(); 	 
				
				instance.close(dialog);	
				instance.fire("changeRerport"); 
			});		
  
		});	
	}, 
	validateFormAccion : function(){
		$("#frm_cobro").validate({
			rules: {
				"idaccion": {
					required: true 
				},
				"accion": {
					required: true 
				} 
			},
			messages : {
				"idaccion": {
					required: "Este campo es obligatorio" 
				},
				"accion": {
					required: "Este campo es obligatorio"  
				} 		
				
			}
		
		});	
		$.validator.messages.required = "Campo obligatorio.";
	}
});