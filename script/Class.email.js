// JavaScript Document
var PersonEmail = new Class({
	formName: null,
	container : null,
	client_id : null,
	contact_id : null,
	initialize : function(formName,client_id,containerName){
		 this.formName=formName; 
		 this.container=containerName;
		 this.client_id=client_id;
	},
	addNewEmail : function(){
		var container=this.container;
		var obj={	
					comp:this.formName,
					client_id:this.client_id,
					show_add_buttom:1,
					show_add_email:1,
					'contact_id':this.contact_id,
					addnew :1
				}
		var instance=this;
		$.post("./?mod_component/comp_emails",obj,function(data){
			$("#"+container).before(data);
			
			$("#"+instance.formName).find("#remove").click(function(){
				$("#"+instance.formName).remove();
			});		
			
			$("#"+instance.formName).validate({
				rules: {
					'email_direccion[]': {
						required: true,
						email: true,
						minlength: 3	
					}
				},
				messages : {
					'email_direccion[]' : {
						required: "Este campo es obligatorio",
						minlength: "Debes de digitar un minimo de 3 caracteres",
						email: "Debe de digitar un email valido"
					} 	
					
				}
			});
			
			
			$("#emails_save_"+obj.comp).click(function(){
				if ($("#"+instance.formName).valid()){
					$.post("./?mod_client/client_edit",$("#"+instance.formName).serializeArray(),function(data){
						if (data.typeError=="100"){
							alert(data.mensaje);
							instance.fire("onCreateEmail",data);	
							$("#"+instance.formName).remove();
							
							instance.loadEMail(); 
						}else{
							alert(data.mensaje + " error "+data.typeError);
						} 
				
					},"json");
				}
			});
							
		});
	},
	setContactID : function(contact){
		this.contact_id=contact;
	},
	viewEmail : function(divContainer,contact_id,index){
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
					hide_remove:1,
					show_estatus:1,
					'contact_id' : contact_id,
					'rand':rand
				}
	   
		$.post("./?mod_component/comp_emails",obj,function(data){
 
			var dialog=instance.createDialog(divContainer,"Ver detalles email",data,700);
			 
			$("#cerrar_ventana4").click(function(){
			   $("#"+dialog).dialog("close");
			}); 
		 
			$(getObject("email_estado[]",0,obj.comp)).click(function(e){
				laststatus=$(this).val();
			 });
			 /*
			 $(getObject("email_estado[]",0,obj.comp)).change(function(){
				 var c=confirm("Esta seguro que desea desabilitar este E-mail?");
				 
					if (c){
						
						var type=$(getObject("email_tipo[]",0,obj.comp)).val();
						var email_id=$(getObject("email_id",0,obj.comp)).val();
			 
						
						var sndX={
								'estatus': $(this).val(),
								'email_submit':'1',
								'id': obj.client_id,
								'tipo': type,
								'email_id' : email_id
							};
					
						$.post("./?mod_client/client_edit",sndX,function(data){	
							if (data.typeError=="104"){
								alert(data.mensaje);
								$("#"+dialog).dialog("close");
								$("#"+dialog).remove();
								instance.loadEMail(); 
							}else{
								alert(data.mensaje + " error "+data.typeError);
							}   
						},"json");
						
						 
					}else{
						$(this).val(laststatus);
					}				 
				 
			 });*/
			 
			 back_tipo=$(getObject("email_tipo[]",0,obj.comp)).val();
			 
			 $("#save_email_bt_"+rand).click(function(){
				 
					var type=$(getObject("email_tipo[]",0,obj.comp)).val();
					var email_id=$(getObject("email_id",0,obj.comp)).val();
		 
					
					var sndX={
							'estatus':$(getObject("email_estado[]",0,obj.comp)).val(),
							'email_submit':'1',
							'old_tipo':back_tipo,
							'id': obj.client_id,
							'tipo': type,
							'email_id' : email_id,
							'email_direccion':$(getObject("email_direccion[]",0,obj.comp)).val(),
							'email_descripcion':$(getObject("email_descripcion[]",0,obj.comp)).val()							
						};
				
					instance.post("./?mod_client/client_edit",sndX,function(data){	
						if (data.typeError=="104"){
							alert(data.mensaje);
							$("#"+dialog).dialog("close");
							$("#"+dialog).remove();
							instance.loadEMail(); 
						}else{
							alert(data.mensaje + " error "+data.typeError);
						}   
					},"json");
			 });
			 
		});
		
	},
	
	loadEMail : function(){
		var container=this.container;
		$("#"+container).html('');
		var instance=this;
		var obj={	
					comp:this.formName+"list",
					client_id:this.client_id,
					'contact_id' : this.contact_id,
					disable_option:1,
					hide_remove:1,
					show_estatus:1
				}
 
		var laststatus=0;
		$.post("./?mod_component/comp_emails_list",obj,function(data){
 
 			$("#"+instance.container).html(data);
			
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
 			 
			$(".emailView","#"+obj.comp).click(function(){
				instance.viewEmail(container,$(this).attr("contact"),$(this).attr("id"));
			});
			
		});
	}
	
});