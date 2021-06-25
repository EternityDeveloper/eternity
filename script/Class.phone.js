// JavaScript Document
var PersonPhone = new Class({
	index 	: null,
	formName: null,
	container : null,
	client_id : null,
	contact_id: 0,
	initialize : function(formName,client_id,containerName){
		 this.formName=formName; 
		 this.container=containerName;
		 this.client_id=client_id;
	},
	setContactID : function(contact){
		this.contact_id=contact;
	},
	addNewPhone : function(){
		var obj={	
					comp:this.formName,
					client_id:this.client_id,
					add_edit_buttom:1,
					contact_id : this.contact_id,
					addnew : 1
				}
				
		var instance=this;
		$.post("./?mod_component/comp_telefonos",obj,function(data){
			$("#"+instance.container).before(data);
			//var inst=instance;
			
			$("#"+instance.formName).find("#remove").click(function(){
				$("#"+instance.formName).remove();
			})
			
			$("#"+instance.formName).validate({
				rules: {
					'telefono_area[]': {
						required: true,
						number: true,
						minlength: 3	
					},
					'telefonos[]': {
						required: true,
						number: true,
						minlength: 7	
					} 
				},
				messages : {
					'telefono_area[]' : {
						required: "Este campo es obligatorio",
						minlength: "Debes de ingresar minimo de 3 digitos",
						number: "Este campo es numerico"
					},
					'telefonos[]' : {
						required: "Este campo es obligatorio",
						minlength: "Debes de ingresar minimo de 7 digitos",
						number: "Este campo es numerico"
					} 	
					
				}
			
			});	
			
			$("#"+instance.formName).find("#save_phone").click(function(){
				if ($("#"+instance.formName).valid()){
					$.post("./?mod_client/client_edit",$("#"+instance.formName).serializeArray(),function(data){
						if (data.typeError=="100"){
						//	window.location.hash="tabs-3"
							alert(data.mensaje);
							$("#"+instance.formName).remove();
							instance.fire("onCreatePhone",data);	
						//	window.location.reload();
						}else{
							alert(data.mensaje + " error "+data.typeError);
						} 
			 
					},"json");
				}
			})		
			
		});
	},
	viewPhone : function(divContainer,contact_id,index){
		var container=this.container;
		var instance = this;
		var laststatus=0;
		var name_from_address=this.formName+"_"+Math.floor(Math.random()*1000);
		var rand=this.getRand();
		var obj={	
					'index' : index,
					comp:name_from_address,
					client_id:this.client_id,
					disable_option:1,
					show_estatus:1,
					hide_remove:1,
					'contact_id' : contact_id ,
					'rand':rand
				}
 		//$('#'+container).showLoading({'addClass': 'loading-indicator-bars'});	
		
		instance.post("./?mod_component/comp_telefonos",obj,function(data){
			//$('#'+container).hideLoading();	
			var dialog=instance.createDialog(divContainer,"Ver detalles telefono",data,700);
			
			var back_tipo_telefono=null;
			$("#cerrar_ventana3").click(function(){
			   $("#"+dialog).dialog("close");
			   $("#"+dialog).remove();
			});
			
			back_tipo_telefono=$(getObject("telefonos_tipo[]",0,obj.comp)).val();
			
			$(getObject("telefono_estado[]",0,obj.comp)).click(function(e){
				laststatus=$(this).val();
			 });
			/* 
			$(getObject("telefono_estado[]",0,obj.comp)).change(function(){
				 var c=confirm("Esta seguro que desea desabilitar este telefono?");
				 
					if (c){
						
						var address_type=$(getObject("telefonos_tipo[]",0,obj.comp)).val();
						var phone_id=$(getObject("phone_id",0,obj.comp)).val();
						
						var sndX={
								'estatus': $(this).val(),
								'phone_submit':'1',
								'id': obj.client_id,
								'tipo': address_type,
								'phone_id': phone_id
							};
					
						$.get("./?mod_client/client_edit",sndX,function(data){	
							if (data.typeError=="104"){
								alert(data.mensaje);
								//window.location.reload();
								//instance.viewPhone(divContainer,contact_id,index); 	
								 $("#"+dialog).dialog("close");
			   					 $("#"+dialog).remove();
								 instance.loadPhone();
							}else{
								alert(data.mensaje + " error "+data.typeError);
							} 
						},"json");
						
						 
					}else{
						$(this).val(laststatus);
					}				 
				 
			 });
			*/
			
			$("#save_phone_bt_"+rand).click(function(){
				 
				var address_type=$(getObject("telefonos_tipo[]",0,obj.comp)).val();
				var phone_id=$(getObject("phone_id",0,obj.comp)).val();
				
				var sndX={
						'estatus': $(getObject("telefono_estado[]",0,obj.comp)).val(),
						'phone_submit':'1',
						'id': obj.client_id,
						'tipo': address_type,
						'old_tipo':back_tipo_telefono,
						'phone_id': phone_id,
						'telefono_area':$(getObject("telefono_area[]",0,obj.comp)).val(),
						'telefonos':$(getObject("telefonos[]",0,obj.comp)).val(),
						'telefono_extension':$(getObject("telefono_extension[]",0,obj.comp)).val() 
					};
				
				$.get("./?mod_client/client_edit",sndX,function(data){	
					if (data.typeError=="104"){
						alert(data.mensaje);
						//window.location.reload();
						//instance.viewPhone(divContainer,contact_id,index); 	
						 $("#"+dialog).dialog("close");
						 $("#"+dialog).remove();
						 instance.loadPhone();
					}else{
						alert(data.mensaje + " error "+data.typeError);
					} 
				},"json");
			});
		});
		
		
	},
	
	loadPhone : function(){
		var container=this.container;
		$("#"+container).html('');
		 
		var instance = this;
		var obj={	
					comp:this.formName+"list",
					client_id:this.client_id,
					disable_option:1,
					show_estatus:1,
					hide_remove:1,
					'contact_id' : this.contact_id
				}
		var laststatus=0;
		$.post("./?mod_component/comp_telefonos_list",obj,function(data){
			$("#"+container).append(data);
			
			$(getObject("role_list",0,obj.comp)).dataTable({
				"bFilter": true,
				"bLengthChange": false,
				"bInfo": false,
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
			
		 	
			$(".phoneView").click(function(){
				//$("#"+container).append('<div id="editphone"></div>');
 				instance.viewPhone(container,$(this).attr("contact"),$(this).attr("id")); 	
				
			});
			
		});
	}
	
});