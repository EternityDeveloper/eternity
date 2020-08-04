// JavaScript Document
var PlanesFinanciamiento = new Class({
	rand : '', //Almacena el random de los ids del formulario form_reserva_(rand)
	dialog_container : null,
	tb_item_list : null,
	page_loading : null,
	_enganche : null,
	_isCharge: false,
	_empresa : {
		id:null,
		interes:null,
		moneda: null,
		complete : false
	},
	tb_last_edit_id:null, //Del listado de la tabla de precio es el item que fue clickeado
	initialize : function(dialog_container,table_name,page_loading){
		this.main_class="PlanesFinanciamiento";
		this.dialog_container=dialog_container;
		this.tb_item_list=table_name;
		this.page_loading=page_loading;
	},
	
	createTable : function(){
		var instance=this;
		
		 createTable(this.tb_item_list,{
						"bFilter": true,
						"bSort": false,
						"bInfo": false,
						"bPaginate": true,
						"bLengthChange": false,
						"bProcessing": true,
						"bServerSide": true,
						"sAjaxSource": "./?mod_financiamiento/listar&x_search=1",
						"sServerMethod": "POST",
						"aoColumns": [ 
								{ "mData": "CODIGO_TP" },
								{ "mData": "PRECIO_TP_F" },
								{ "mData": "IMPUESTO_TP" },
								{ "mData": "POR_IMPUESTO_TP" },
								{ "mData": "CAPITAL_TP_F" },
								{ "mData": "MONEDA_TP" },
								{ "mData": "POR_INTERES" },
								{ "mData": "ENGACHE" },
								{ "mData": "bt_view_plan" },
								{ "mData": "bt_editar" }
							],
						  "oLanguage": {
								"sLengthMenu": "Mostrar _MENU_ registros por pagina",
								"sZeroRecords": "No se ha encontrado - lo siento",
								"sInfo": "Mostrando _START_ a _END_ de _TOTAL_ registros",
								"sInfoEmpty": "Mostrando 0 to 0 of 0 registros",
								"sInfoFiltered": "(filtrado de _MAX_ total registros)",
								"sSearch":"Buscar"
							},
							"fnDrawCallback": function( oSettings ) {
								$(".bt_view_plan").click(function(){
									instance.tb_last_edit_id=$(this).attr("id");
									instance.viewPlanList($(this).attr("id"));
								});	
								
								$(".edit_plan").click(function(){
									instance.editTablaPrecio($(this).attr("id"));
								});	
							}
						});		
	},
	
	editTablaPrecio : function(id){
		var instance=this;
	 
		$('#'+this.dialog_container).showLoading({'addClass': 'loading-indicator-bars'});	

		$.post("./?mod_financiamiento/listar",{
				"edit_tabla_precio":'1',
				"tb_precio": id
			},function(data){
				
				$('#'+instance.dialog_container).hideLoading();	
				var dialog=instance.createDialog(instance.dialog_container,"Editar tabla de precio",data,450);
				instance._dialog=dialog;
				var n = $('#'+dialog);
				n.dialog('option', 'position', [(document.scrollLeft/450), 50]); 

				instance._enganche= new Enganche("enganche_content");
				 
				var sp=$("#enganche_hi").val().split(".");
				for(i=0;i<=sp.length;i++){
					if ($.isNumeric(sp[i])){
						instance._enganche.push(sp[i]);
					}
				}
				
				instance._enganche.draw_data();
				
				$("#plan_empresa").change(function(){
					if ($(this).val()!=""){
						var sp=$(this).val().split("*_*")
						var obj=$.parseJSON(base64decode(sp[1]));
						instance._empresa.id=sp[0];
						instance._empresa.interes=obj;
		
						$(".plan_moneda").show();
						$("#MONEDA_TP").val("");
						$(".plan_detalle").hide();
					 
					}else{
						$(".plan_moneda").hide();
						$(".plan_detalle").hide();
					}
				});
				
				$("#MONEDA_TP").change(function(){
					if ($(this).val()!=""){
						var moneda=$(this).val();
						instance._empresa.moneda=moneda;
						instance.update_detalle_balance_view();
						$(".plan_detalle").show(); 
					 	instance._empresa.complete=true;
					}else{
						instance._empresa.complete=false;
						$(".plan_detalle").hide();
					}
				});
				
				$("#bt_pro_f_cancel").click(function(){
					$("#"+dialog).dialog("destroy");
					$("#"+dialog).remove();
				}); 
				  
				$("#bt_pro_f_save").click(function(){
					instance.doSaveForm();
				}); 
				
				$("#PRECIO_TP").keypress(function(){
					var value=$(this).val();
					if ($.isNumeric(value)){
						var por_inpuesto=instance._empresa.interes.IMPUESTO;
						$("#IMPUESTO_TP").val(value*por_inpuesto/100);
					}else{
						$("#IMPUESTO_TP").val("0");
					}
				});
				
				
				instance._empresa.complete=true;
				instance.validateForm(); 
 
			});	
	},
	
	viewPlanList : function(id){
		var instance=this; 
		$.post("./?mod_financiamiento/listar",{
				"edit_plan":'1',
				"tb_precio":id
			},function(data){ 
				var dialog=instance.createDialog(instance.dialog_container,"Plan de Financiamiento",data,$(window).width()-40);
				instance._dialog=dialog;
				var n = $('#'+dialog);
				n.dialog('option', 'position', [(document.scrollLeft/450), 30]); 
				
				createTable("tb_financiamiento",{
								"bFilter": true,
								"bSort": false,
								"bInfo": false,
								"bPaginate": false,
								"bLengthChange": false, 
								  "oLanguage": {
										"sLengthMenu": "Mostrar _MENU_ registros por pagina",
										"sZeroRecords": "No se ha encontrado - lo siento",
										"sInfo": "Mostrando _START_ a _END_ de _TOTAL_ registros",
										"sInfoEmpty": "Mostrando 0 to 0 of 0 registros",
										"sInfoFiltered": "(filtrado de _MAX_ total registros)",
										"sSearch":"Buscar"
									} 
								});	
								
			
				$(".edit_plan_fin").click(function(){
					instance.removePlanOfList($(this).attr("id"));	
				});
				
				$("#bt_close").click(function(){
					instance.close(dialog);
				});
				
				/*
 				var tbody=$('#tb_financiamiento').children("tbody");
				var tr=$(tbody, $('.rs_add_link').parent().parent()).parent();
				tr.css( 'cursor', 'pointer' );
			 
				tr.hover(function(){ 
					$(this).addClass('hover_tr');  
				},function(){ 
					$(this).removeClass('hover_tr'); 
				});*/
										
		});
	},
	/*
		MUESTRA UN RESUMEN DE LOS PLANES DE FINANCIAMIENTO 
	 	AGRUPADOS POR PLAZO Y % DE ENGANCHE
	*/
	viewGroupPlanList : function(){
		var instance=this;
 		instance.post("./?mod_financiamiento/listar",{
				"show_plan_group":'1' 
			},function(data){ 
				var dialog=instance.createDialog(instance.dialog_container,"Financiamiento",data,500);
				instance._dialog=dialog;
				var n = $('#'+dialog);
				n.dialog('option', 'position', [(document.scrollLeft/450), 30]); 
				
				 createTable("tb_financiamiento",{
								"bFilter": true,
								"bSort": true,
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
									
										$(".select_plan_fin").click(function(){
											var obj= $.parseJSON($.base64.decode($(this).attr("id")));					
											if (obj.moneda!=""){
												instance.close(dialog);	
												instance.fire("onSelectPlanGrup",obj);
											}
										});
										if (!instance._isCharge){ 
											 $('<button type="button" id="fn_customize_f">Agregar</button>').appendTo('#fp_financiamiento div.dataTables_filter'); 	
											 $("#fn_customize_f").click(function(){ 
												instance.close(dialog);
												instance.doCustomViewPlan(); 	
												
											  }); 
											instance._isCharge=true;
										}										
										
									}
								});	


				
				$("#bt_close").click(function(){
					instance.close(dialog);
				}); 
										
		});
	},	
	doCustomViewPlan : function(){
		var instance=this;  
		var data='<div  class="fsPage"><table width="100%" border="0" cellspacing="0" cellpadding="0"><tr><td><strong>Moneda</strong></td>    <td><select name="moneda_tp" id="moneda_tp"  class="textfield textfieldsize"  style="height:30px;width:120px;"><option value="">Seleccionar</option><option value="LOCAL">LOCAL</option><option value="DOLARES">DOLARES</option></select></td>  </tr>  <tr>    <td><strong>Plazo</strong></td>    <td><input type="text" name="plazo" id="plazo" /></td></tr><tr><td><strong>% Enganche</strong></td><td><input type="text" name="enganche" id="enganche" /></td>  </tr><tr><td colspan="2" align="center"><button type="button" id="bt_fn_agregar">Agregar</button></td>  </tr></table></div>';
		
		var dialog=this.createDialog(this.dialog_container,"Personalizar Financiamiento",data,400);
		
		$("#bt_fn_agregar").click(function(){ 
			//alert($("#moneda_tp option:selected").val());
			var err=false;
			var moneda=$("#moneda_tp option:selected").val();
			if (moneda==""){
				err=true;
			}
			var plazo=$("#plazo").val();
			if (!$.isNumeric(plazo)){
				err=true;
			}
			var enganche=$("#enganche").val();
			if (!$.isNumeric(enganche)){
				err=true;
			}
			if (!err){
				err=false;
				if (plazo>48){
					err=true;
					alert('El plazo no puede ser mayor de 48');
				}
				if (enganche>100){
					err=true;
					alert('El % de enganche no debe ser mayor de 100%');
				}
				if (!err){
					instance.close(dialog);
					instance.fire("onSelectPlanGrup",{"moneda":moneda,'plazo':plazo,'enganche':enganche,'plan':'CST-PLAN'});
				}
			}else{
				alert('Verifique que los campos no esten vacios y que sean numeros los digitos ingresados!');
			}
			
 
		});  		
 
	},
	questionView : function(filtro){
		var instance=this; 
		
		if (!filtro.isfilter){
			var data='<br><br><center><button type="button" id="moneda_local" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" role="button" aria-disabled="false"><span class="ui-button-text">LOCAL</span></button>&nbsp;&nbsp;<button id="moneda_dolar" type="button" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" role="button" aria-disabled="false"><span class="ui-button-text">DOLAR</span></button></center>';
			
			var dialog=this.createDialog(this.dialog_container,"TIPO MONEDA",data,400);
			
			$("#moneda_local").click(function(){
				$("#"+dialog).dialog("destroy");
				$("#"+dialog).remove();
				instance.fire("select_moneda",'LOCAL');
			}); 
					
			$("#moneda_dolar").click(function(){
				$("#"+dialog).dialog("destroy");
				$("#"+dialog).remove();
				instance.fire("select_moneda",'DOLAR')
			}); 		
		}else{
			this.fire("select_moneda",filtro.moneda);
		}
	},	
	
	viewPlanListFromProduct : function(id,filtro){
		var instance=this;
		//alert(filtro.moneda)
		///this.addListener("select_moneda",function(moneda){
			//$('#'+this.dialog_container).showLoading({'addClass': 'loading-indicator-bars'});	
		
		instance.post("./?mod_financiamiento/listar",{
				"plan_custom_list_show":'1',
				"producto":id,
				'tipo_moneda':filtro.moneda,
				'type_plan_filter': JSON.stringify(filtro)
			},function(data){
				//$('#'+instance.dialog_container).hideLoading();	
				var dialog=instance.createDialog(instance.dialog_container,
												"Plan de Financiamiento",data,$(window).width()-40);
				instance._dialog=dialog;
				var n = $('#'+dialog);
				n.dialog('option', 'position', [(document.scrollLeft/450), 30]); 
				
			//   $("#accordion").accordion();
			   instance.doSelectPlanByMoneda('select_plan_local',filtro.moneda,dialog);
			  // instance.doSelectPlanByMoneda('select_plan_dolar','DOLARES',dialog); 						
		});
		//});
		//this.questionView(filtro);
		
	},
	
	viewPlanListProduct : function(filtro){
		var instance=this; 
		instance.post("./?mod_financiamiento/listar",{
				"plan_custom_list_show":'1', 
				'tipo_moneda':filtro.moneda,
				'type_plan_filter': JSON.stringify(filtro)
			},function(data){
				//$('#'+instance.dialog_container).hideLoading();	
				var dialog=instance.createDialog(instance.dialog_container,
												"Plan de Financiamiento",data,$(window).width()-40);
				instance._dialog=dialog;
				var n = $('#'+dialog);
				n.dialog('option', 'position', [(document.scrollLeft/450), 30]); 
				
			//   $("#accordion").accordion();
			   instance.doSelectPlanByMoneda('select_plan_local',filtro.moneda,dialog);
			  // instance.doSelectPlanByMoneda('select_plan_dolar','DOLARES',dialog); 						
		});
		//});
		//this.questionView(filtro);
		
	},	
	
	doSelectPlanByMoneda : function(class_name,moneda,dialog){
		var instance=this;
		var tr=$('td', $('.'+class_name).parent().parent()).parent(); 
		tr.css( 'cursor', 'pointer' );
		tr.click(function(){
			var nTds=$(this).children(); 
			var  financiamiento= { 
				"codigo":$(nTds[0]).text(),
				"plazo":$(nTds[1]).text(),
				"por_enganche":$(nTds[2]).text(),
				"monto_enganche":$(nTds[3]).text(),
				"capital_financiar":$(nTds[4]).text(),
				"por_interes":$(nTds[10]).text(), 
				"plan_id":$(nTds).find("a").attr("id"),
				"precio":parseFloat($(nTds[4]).text().replace(",",""))+  parseFloat($(nTds[3]).text().replace(",","")),
				'moneda': moneda
			}
	 
			instance.close(dialog);
			instance._financiamiento=financiamiento;
			instance.fire("plan_select",financiamiento);  
		});
		tr.hover(function(){ 
			$(this).addClass('hover_tr');  
		},function(){ 
			$(this).removeClass('hover_tr'); 
		}); 
	},
	
	removePlanOfList : function(id){
		var instance=this;
		var div=this.createDiv(this.dialog_container);
		$("#"+div).html("<center><strong>Desea desabilitar el Plan de Financiamiento</strong></center><br><center><img src=\"images/error.png\"/></center>");
		$("#"+div).dialog({
				  resizable: false,
				  height:270,
				  modal: true,
				  close: function (ev, ui) {
					$(this).dialog("destroy");
					$(this).remove(); 
				  },
				  buttons: {
					"Si": function() {
						
						$('#'+instance.dialog_container).showLoading({'addClass': 'loading-indicator-bars'});	
						$.get("./?mod_financiamiento/listar",{
								"disable_plan":'1',
								"plan_id":id
							},function(data){
								$('#'+instance.dialog_container).hideLoading();	
								$('#'+div).dialog("destroy");
								$('#'+div).remove();
								
								$('#'+instance._dialog).dialog("destroy");
								$('#'+instance._dialog).remove();
								/*RECARGO EL LISTADO DE PLANES*/
								instance.viewPlanList(instance.tb_last_edit_id);
								 
								
						},"json");
						 
					},
					"No": function() {
						$(this).dialog("destroy");
						$(this).remove();
						 
						 
					}
				  }
			});
	},
	
	putCreateButton : function(buttom){
		var instance=this;
		$("#"+buttom).click(function(){
			instance.doCreateView();
		});	
	},
	
	doCreateView : function(){

		var instance=this;
	 
		$('#'+this.dialog_container).showLoading({'addClass': 'loading-indicator-bars'});	

		$.post("./?mod_financiamiento/listar",{
				"add_tabla_precio":'1' 
			},function(data){
				
				$('#'+instance.dialog_container).hideLoading();	
				var dialog=instance.createDialog(instance.dialog_container,"Agregar Tabla de precio",data,450);
				instance._dialog=dialog;
				var n = $('#'+dialog);
				n.dialog('option', 'position', [(document.scrollLeft/450), 50]); 

				instance._enganche= new Enganche("enganche_content");
				instance._enganche.draw_data();
				
				$("#plan_empresa").change(function(){
					if ($(this).val()!=""){
						var sp=$(this).val().split("*_*")
						var obj=$.parseJSON(base64decode(sp[1]));
						instance._empresa.id=sp[0];
						instance._empresa.interes=obj;
		
						$(".plan_moneda").show();
						$("#MONEDA_TP").val("");
						$(".plan_detalle").hide();
					 
					}else{
						$(".plan_moneda").hide();
						$(".plan_detalle").hide();
					}
				});
				
				$("#MONEDA_TP").change(function(){
					if ($(this).val()!=""){
						var moneda=$(this).val();
						instance._empresa.moneda=moneda;
						instance.update_detalle_balance_view();
						$(".plan_detalle").show(); 
					 	instance._empresa.complete=true;
					}else{
						instance._empresa.complete=false;
						$(".plan_detalle").hide();
					}
				});
				
				$("#bt_pro_f_cancel").click(function(){
					$("#"+dialog).dialog("destroy");
					$("#"+dialog).remove();
				}); 
				  
				$("#bt_pro_f_save").click(function(){
					instance.doSaveForm();
				}); 
				
				$("#PRECIO_TP").keypress(function(){
					var value=$(this).val();
					if ($.isNumeric(value)){
						var por_inpuesto=instance._empresa.interes.IMPUESTO;
						$("#IMPUESTO_TP").val(value*por_inpuesto/100);
					}else{
						$("#IMPUESTO_TP").val("0");
					}
				});
				
				instance.validateForm(); 
 
			});
 
	},
	
	doSaveForm : function(){
		var instance=this;
		if ($("#form_plan_financiamiento").valid()){
			 
			 if (this._empresa.complete){
				if (this._enganche.getTotalItems()>0){
					  
					var data={
						"financiamiento_submit":1,
						"enganche":this._enganche._data,
						"empresa":this._empresa,
						"form_data" : $("#form_plan_financiamiento").serializeArray(),
						"type_form" :$("#type_form").val()
					};
					var win="plan_load";
					$('#'+win).showLoading({'addClass': 'loading-indicator-bars'});	
					
					$.get("./?mod_financiamiento/listar",data,function(data){
						$('#'+win).hideLoading();	
						 
						if (!data.error){
							alert(data.mensaje);
							window.location.reload();
						}else{
							alert(data.mensaje);
						} 
			
					},"json"); 
				}else{
					alert('Error debe de seleccionar el enganche!');	
				}	
			 }else{
				 alert('Debe de completar el formulario!');	
			}

		}
		
	},
	
	update_detalle_balance_view : function(){
		var porciento_interes=0;
		if (this._empresa.moneda=="LOCAL"){
			porciento_interes=this._empresa.interes.INTERES_LOCAL
		}else if (this._empresa.moneda=="LOCAL"){
			porciento_interes=this._empresa.interes.INTERES_DOLARES
		}
		$("#plan_por_impuesto").val(this._empresa.interes.IMPUESTO);
		$("#plan_por_interes").val(porciento_interes);
	},
	
	validateForm : function(){
		$("#form_plan_financiamiento").validate({
			rules: {
				"CODIGO_TP": {
					required: true 
				},
				"PRECIO_TP" : {
					required: true ,
					number: true
				},
				"CAPITAL_TP" : {
					required: true ,
					number: true
				},
				"plan_empresa": {
					required: true 
				},
				"MONEDA_TP": {
					required: true 
				},
				"IMPUESTO_TP": {
					required: true,
					number: true 
				} ,
				"plan_por_impuesto": {
					required: true,
					number: true 
				} ,
				"plan_por_interes": {
					required: true,
					number: true 
				} 
			},
			messages : {
				"CODIGO_TP": {
					required: "Campo obligatorio" 
				},
				"PRECIO_TP" : {
					required: "Campo obligatorio"  ,
					number: "Campo numerico"
				},
				"CAPITAL_TP" : {
					required: "Campo obligatorio" ,
					number: "Campo numerico"
				},
				"plan_empresa": {
					required: "Campo obligatorio" 
				},
				"MONEDA_TP": {
					required: "Campo obligatorio" 
				},
				"IMPUESTO_TP": {
					required: "Campo obligatorio" ,
					number: "Campo numerico"
				},
				"plan_por_impuesto": {
					required: "Campo obligatorio" ,
					number: "Campo numerico"
				},
				"plan_por_interes": {
					required: "Campo obligatorio" ,
					number: "Campo numerico"
				} 	
				
			}
		
		});	
		$.validator.messages.required = "Campo obligatorio.";
	}
	
	
});

var Enganche = new Class({
 	container : null, 
	_id: "enganche_list",
	_data : [],
	initialize : function(container){
		this.main_class="Enganche";
		this.container=container; 
		var view='<ul id="'+this._id+'" class="enganche"></ul>'; 
		$("#"+container).html(view);
		this._data=null;
		this._data=[];
	},
	
	draw_data : function(){
		if (this._data.length>0){
			for(i=0;i<=this._data.length;i++){
				if ($.isNumeric(this._data[i])){
					this.insertViewItem(this._data[i]);
				}
			}
			this.putAddItem();
		}else{
		  this.putAddItem();
		}
	},
	
	push : function(value){
		this._data.push(value);
	},
	
	existItem : function(value){
		for(i=0;i<=this._data.length;i++){
			if (value==this._data[i]){
				return true;	
			}
		}
		return false;
	},
	
	putAddItem : function(){ 
		if (this.getTotalItems()<3){
			var id=this.getRand();
			var li_id=this.createItem(id);
			var instance=this;
			$("#bt_"+id).click(function(){
				var value=$("#text_"+id).val();
				if (!$.isNumeric(value)){
					alert('Este campo es numerico');
					$("#text_"+id).val('');
				}else{
					if (!instance.existItem(value)){
						instance.push(value);
						$("#"+li_id).html(value);
						instance.putAddItem();
					}else{
						$("#text_"+id).val('');
						alert('Error este enganche ya se encuentra dentro de la lista!');
					}
				}
			});	
		}
	},
	
	insertViewItem : function(val){
		var li_id="li_"+this.getRand();
		var view='<li id="'+li_id+'">'+val+'</li>'
		$("#"+this._id).append(view);	
	},
	createItem : function(id){
		var li_id="li_"+this.getRand();
		var view='<li id="'+li_id+'"><input type="text" class="textfield textfieldsize" name="text_'+id+'" id="text_'+id+'" style="width:30px" autocomplete="off" /><button type="button" class="orangeButton" id="bt_'+id+'">+</button></li>'
		$("#"+this._id).append(view);
		
		$("#text_"+id).change(function(){
			if (!$.isNumeric($(this).val())){
				alert('Este campo es numerico');
				$(this).val('');
			}
		});
		
		
		return li_id;	
	},
	
	getTotalItems : function(){
		return this._data.length;
	}
 
});