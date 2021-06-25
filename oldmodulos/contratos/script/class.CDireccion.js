var CDireccion = new Class({
	dialog_container : null,
	_id_nit : null,
	_rand : 0,
	_producto : null,
	_filtro : null,
	_address_obj : null,
	_direccion_id : null,
	initialize : function(dialog_container,financiamiento){
		this.main_class="CDireccion";
		this.dialog_container=dialog_container;  
	},  
	/*Carga la vista de detalle de agregar Direccion*/
	loadView: function(serie_contrato,no_contrato){ 
		var instance=this;
		instance.post("./?mod_contratos/listar",{
				"list_client_direccion":'1',
				"serie_contrato":serie_contrato,
				"no_contrato":no_contrato,
				"direccion_id":instance._direccion_id
		},function(data){
			instance.doDialog("modal_address_contrato",instance.dialog_container,data); 
			instance.addListener("onCloseWindow",function(){
				instance.fire("doCancelAddress");	
			}) 			    
			
			var info=$("#bt_add_direccion").attr("data");
 
			$("#add_close").click(function(){
				instance.CloseDialog('modal_address_contrato');
				instance.calcularMontoGeneral()
			});
			
			$("#bt_create_direccion").click(function(){
				instance.CloseDialog('modal_address_contrato');
				instance.loadViewAdd(serie_contrato,no_contrato);
			});  
			 
			$(".direccion_select").click(function(){  	 
				var obj=$.parseJSON($.base64.decode($(this).attr("id")));  
				instance.post("./?mod_contratos/listar&process_address",obj,function(data){ 
					if (!data.error){
						$("#direccion_c").html(data.address);
						$("#bt_add_direccion").attr("id_address",data.address_id);
						$("#bt_add_direccion").html("Cambiar");
						instance._address_obj=data.address_id;
					}
					
					alert(data.mensaje);
					
					instance.CloseDialog("modal_address_contrato");
					
				},"json");   				
			});
		 
			if (info!=""){
				var obj=$.parseJSON($.base64.decode(info)); 
				instance._address_obj=obj;  
				$("#provincia_id").val(instance._address_obj.provincia);
				$("#provincia_id").change();				 
			}
		});
	},
	/*Carga la vista de detalle de agregar Direccion*/
	loadViewAdd: function(serie_contrato,no_contrato){ 
		var instance=this;
		instance.post("./?mod_contratos/listar",{
				"view_direccion":'1',
				"serie_contrato":serie_contrato,
				"no_contrato":no_contrato
		},function(data){
			   //modal_create_address_contrato
/*			var dialog=instance.createDialog(instance.dialog_container,"Direccion de cobro",data,800);
			instance._dialog=dialog;
			var n = $('#'+dialog);
			n.dialog('option', 'position', [(document.scrollLeft/550), 20]);*/
			instance.doDialog("modal_create_address_contrato",instance.dialog_container,data); 
			instance.addListener("onCloseWindow",function(){
				instance.fire("doCancelAddress");
			}) 				  
			
			var info=$("#bt_add_direccion").attr("data");
 
			$("#add_close").click(function(){
				instance.close(dialog);
				instance.calcularMontoGeneral()
			});
			
			$('#faster_search').select2({
			  multiple: false,
			  minimumInputLength: 4,
			  query: function (query){ 
				  $.post("./?mod_contratos/listar&search_direccion=1",{"motorizados":'1',"sSearch":query.term},
				  		function(data){  
					 		query.callback(data);
				   },"json");   
			  }
			});
			
			$("#faster_search").on("change", 
				function(e) { 
					$("#_address").show();
					instance._address_obj=$.parseJSON($.base64.decode(e.val));  
					$("#provincia_id").val(instance._address_obj.provincia);
					$("#provincia_id").change();
					
			});
			
			$("#provincia_id").change(function(){ 
				instance.post("./?mod_contratos/listar",
					{"request_address":1,"action":"loadciudad","provicina":$(this).val()},
					function(data){  
						 $("#ciudad_charge").html(data); 
						 instance.captureCiudad(); 
						 $("#cuidad_id").val(instance._address_obj.ciudad);
						 $("#cuidad_id").change();
			    },"text");   
			});
 			 
			$("#add_addres_buttom").click(function(){
				var post=$("#addess_c_view").serializeArray();
				post.push({"name":"serie_contrato","value":serie_contrato});
				post.push({"name":"no_contrato","value":no_contrato});	 	 
				instance.post("./?mod_contratos/listar&process_address",post,function(data){ 
					if (!data.error){
						$("#direccion_c").html(data.address)
						$("#bt_add_direccion").html("Cambiar");
					} 
					alert(data.mensaje);
					instance.fire("onAddAdress");
					instance.CloseDialog("modal_create_address_contrato");
					
				},"json");   				
			});
		 
			if (info!=""){
				var obj=$.parseJSON($.base64.decode(info)); 
				instance._address_obj=obj;  
				$("#provincia_id").val(instance._address_obj.provincia);
				$("#provincia_id").change();				 
			}
		});
	},	
	captureCiudad : function(){
		var instance=this;
		$("#cuidad_id").change(function(){  
			instance.post("./?mod_contratos/listar",
				{
				 "request_address":1,
				 "action":"loadsector",
				 "idciudad":$(this).val()
				},
				function(data){  
					 $("#sector_charge").html(data); 
					 $("#sector_id").val(instance._address_obj.sector); 
			},"text");   
		});
	} 
	
});