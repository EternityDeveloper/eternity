var COperacion = new Class({
	dialog_container : null, 
	fields : {
		tipo_movimiento :1	
	}, 
	initialize : function(dialog_container){
		this.main_class="COperacion";
		this.dialog_container=dialog_container; 
	},	  
  
	doViewQuestion : function(){
		var instance=this;
		var tipo_movimiento=0;
		instance.post("?mod_caja/delegate&operacion&view_movimiento",{
				"view_movimiento":'1' 
		},function(data){  
			var dialog=instance.createDialog(instance.dialog_container,"Seleccione el movimiento",data,430);
			instance._dialog=dialog;
			var n = $('#'+dialog);
			n.dialog('option', 'position', [(document.scrollLeft/550), 100]); 
			 
			$("#bt_caja_process").click(function(){
				 window.location.href="./?mod_caja/delegate&operacion&buscador&setid=true&id="+tipo_movimiento;
			});
			
			$("#bt_caja_cancel").click(function(){
				instance.close(dialog);	
			});
			
			$("#tipo_movimiento").change(function(){
				if ($(this).val()!=""){
					tipo_movimiento=$(this).val();
					$("#bt_caja_process").attr("disabled",false);
				}else{
					$("#bt_caja_process").attr("disabled",true);
				}
			});
			 
			 		
			instance.validateForm();

		});	
	} ,

	/*SE UTLIZA PARA HABILITAR LA BUSQUEDA */
	enableSearch : function(search_textbox_id,search_bt_id,search_radio){		
		var instance=this; 
		$("#"+search_textbox_id).keypress(function(e){
			var code = e.keyCode || e.which;
			if(code == 13) { //Enter keycode
				//instance.doSearch($("#"+search_textbox_id).val(),$("input:radio[name ='"+search_radio+"']:checked").val());
				window.location.href="./?mod_caja/delegate&operacion&search="+$("#"+search_textbox_id).val(); 
			}	
			
		});
		$("#"+search_bt_id).click(function(e){ 
			//instance.doSearch($("#"+search_textbox_id).val(),$("input:radio[name ='"+search_radio+"']:checked").val());
			window.location.href="./?mod_caja/delegate&operacion&search="+$("#"+search_textbox_id).val();
		});
		
		$(".item_select").click(function(){ 
			window.location.href="./?mod_caja/delegate&operacion&determinate=1&id="+$(this).attr("id"); 
		});
		
		$(".item_select_contrato").click(function(){ 
			window.location.href="./?mod_cobros/delegate&contrato_view&id="+$(this).attr("id");
		});		
		$(".item_select_reserva").click(function(){ 
			window.location.href="./?mod_caja/delegate&operacion&determinate=1&reserva="+$(this).attr("id"); 
		});	
		
		$(".search_list").dataTable({
					"bSort": false,
					"bInfo": false, 
					"bLengthChange": false,
					"bFilter": false, 
					"bPaginate": true, 
					 "oLanguage": {
							"sLengthMenu": "Mostrar _MENU_ registros por pagina",
							"sZeroRecords": "No se ha encontrado - lo siento",
							"sInfo": "Mostrando _START_ a _END_ de _TOTAL_ registros",
							"sInfoEmpty": "Mostrando 0 to 0 of 0 registros",
							"sInfoFiltered": "(filtrado de _MAX_ total registros)",
							"sSearch":"Buscar"
						} 		
			});	
			 
	},
	
	doSearch : function(val,type){
		var instance=this;  
		this.post("./?mod_caja/delegate&operacion&buscador",{"search_":1,"document":val,"type":type},function(data){
		 
			$("#detalle_search").show();
			$('#detalle_search').html('');
			$('#detalle_search').html(data);
			createTable("caja_table_list",{
					"bSort": false,
					"bInfo": false, 
					"bLengthChange": false,
					"bFilter": false, 
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
			
			$(".item_select").hover(function(){  
				//$(this).addClass('hover_tr');  
			},function(){ 
				$(this).removeClass('hover_tr'); 
			}).css( 'cursor', 'pointer' ).click(function(){
				$('#detalle_search').html('');
				if (type=="CLIENTE"){
					var cliente= new AbonoPersona(instance.dialog_container);
					cliente.viewDetailAccount($(this).attr("id"),'caja_view');		  
				}
				if (type=="RESERVA"){
					var cliente= new PagoReserva(instance.dialog_container);
					//_pago_reserva.searchPerson();
					cliente.viewDetailAccount($(this).attr("id"),$(this).attr("id_nit"),'caja_view');		  
				}
				if (type=="CONTRATO"){
					var cliente= new PagoContrato(instance.dialog_container);
					//_pago_reserva.searchPerson();
					cliente.viewDetailContract($(this).attr("id"),$(this).attr("id_nit"),'caja_view');		  
				}
			}); 
		});
 
	},
	validateForm : function(){
		$("#frm_general").validate({
			rules: {
				"TIPO_DOC": {
					required: true 
				},
				"descripcion": {
					required: true 
				} 
			},
			messages : {
				"TIPO_DOC": {
					required: "Este campo es obligatorio" 
				},
				"descripcion": {
					required: "Este campo es obligatorio"  
				} 		
				
			}
		
		});	
		$.validator.messages.required = "Campo obligatorio.";
	}
});