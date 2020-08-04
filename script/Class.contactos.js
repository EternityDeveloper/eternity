// JavaScript Document
var PersonContactos = new Class({
 
	formName: null,
	container : null,
	client_id : null,
	address : null,
	phone : null,
	email : null,
	initialize : function(formName,client_id,containerName){
		 this.formName=formName; 
		 this.container=containerName;
		 this.client_id=client_id;
	},
	
	addNewContact: function(){
		var container=this.container;
		var intance=this;
		var obj={	
	  				comp:this.formName,
					client_id:this.client_id,
					show_add_ref:1 ,
					new_c:1
				}
		var instance=this;
		$.post("./?mod_component/comp_contactos",obj,function(data){
			//$("#"+container).before(data);
			var dialog=intance.createDialog(container,"Ver detalles de contactos",data,800);
			$(".st_contactos").tabs({disabled:[1,4,2,4,3,4]}); 
			
			$("#"+instance.formName).find("#remove").click(function(){
				$("#"+dialog).remove();
			});	
			
			$("#"+instance.formName).validate({
				rules: {
					'contactos_apellido[]': {
						required: true,
						minlength: 5	
					},
					'contactos_nombre[]' : {
						required: true,
						minlength: 5	
					}				
				},
				messages : {
					'contactos_apellido[]' : {
						required: "Este campo es obligatorio",
						minlength: "Debes de digitar un minimo de 5 caracteres" 
					},
					'contactos_nombre[]' : {
						required: "Este campo es obligatorio",
						minlength: "Debes de digitar un minimo de 5 caracteres" 
					} 						 					 	
					
				}
			});
			
			$("#bt_contact_save").click(function(){
		
				if ($("#"+instance.formName).valid()){
					$.post("./?mod_client/client_edit",$("#"+instance.formName).serializeArray(),function(data){
				  
						if (data.typeError=="100"){
							alert(data.mensaje); 
							instance.fire("onCreateContact",data);
							$("#"+dialog).remove();
							instance.viewContact("editcontact",data.contact_id,data.contact_id);
							instance.loadContact();
						}else{
							alert(data.mensaje + " error "+data.typeError);
						} 
			
					},"json");
				}
			});
							
		});
	},
	
	addNewAddress : function(contact_id){
		this.address.addNewAddress();	
	},
	addNewPhone : function(contact_id){
		this.phone.addNewPhone();	
	},
	addNewEmail : function(contact_id){
		this.email.addNewEmail();	
	},	
	viewContact : function(divContainer,contact_id,index){
		var container=this.container;
		var thisobject = this;
		var laststatus=0;
		var name_from_address=this.formName+"_"+Math.floor(Math.random()*1000);
		
		var obj={	
					'index' : index,
					comp:name_from_address,
					client_id:this.client_id,
					disable_option:0,
					hide_remove:1,
					show_estatus:1,
					'contact_id' : contact_id 
				}
 
		$.post("./?mod_component/comp_contactos",obj,function(data){
			
			var dialog=thisobject.createDialog(divContainer,"Ver detalles de contactos",data,800);
			
			$("#cerrar_ventana").click(function(){
				$("#"+dialog).dialog("destroy");
				$("#"+dialog).remove();
			});
			
			var n = $('#'+dialog);
			n.dialog('option', 'position', [(document.scrollLeft/450), 50]); 	
				
			$(".st_contactos").tabs(); 
			
			thisobject.address = new PersonAddress('contact_address_',obj.client_id,"address_"+contact_id);
			thisobject.address.setContactID(contact_id);
			thisobject.address.loadAddress();
			thisobject.address.addListener("AddressChangeEstatus",function(){
				thisobject.address.loadAddress();
			});
			thisobject.address.addListener("doEditAddress",function(info){
				thisobject.address.viewAddress("editaddress",info.contact_id,info.index);	
			});
			thisobject.address.addListener("AddressSave",function(form_data){
				$.post("./?mod_client/client_edit",form_data,function(data){
					if (data.typeError=="100"){
						thisobject.address.loadAddress();
						alert(data.mensaje); 
					}else{
						alert(data.mensaje + " error "+data.typeError);
					} 
				},"json");
			});
			
			thisobject.phone = new PersonPhone('contact_phone_',obj.client_id,"phone_"+contact_id);
			thisobject.phone.setContactID(contact_id);
			thisobject.phone.loadPhone();
			thisobject.phone.addListener("onCreatePhone",function(data){
				thisobject.phone.loadPhone();
			});
	
			
			thisobject.email = new PersonEmail('contact_email_',obj.client_id,"email_"+contact_id);
			thisobject.email.setContactID(contact_id);
			thisobject.email.loadEMail();
		  
			
			var laststatus=0; 
			$(getObject("contacto_estado[]",0,obj.comp)).click(function(e){
				laststatus=$(this).val();
			 });
			 
			 var instance=thisobject;
			 $(getObject("contacto_estado[]",0,obj.comp)).change(function(){
				 var c=confirm("Esta seguro que desea desabilitar este Contacto?");
				 
					if (c){
						
						var  type=$(getObject("contacto_tipo[]",0,obj.comp)).val();
						var contacto_id=$(getObject("contact_id",0,obj.comp)).val();
						 
						var sndX={
								'estatus': $(this).val(),
								'remove_contacto_submit':'1',
								'id': instance.client_id,
								'tipo': type,
								'contacto_id':contacto_id
							};
					
						$.post("./?mod_client/client_edit",sndX,function(data){	
							if (data.typeError=="104"){
								alert(data.mensaje);
								window.location.reload();
							}else{
								alert(data.mensaje + " error "+data.typeError);
							}   
						},"json");
						
						 
					}else{
						$(this).val(laststatus);
					}				 
				 
			 });	
		});
	},
	
	loadContact : function(total_direcciones,total_phone){
		var container=this.container;
		var obj={	
					comp:this.formName+"list",
					client_id:this.client_id,
					disable_option:1,
					hide_remove:1,
					show_estatus:1
				}
		var laststatus=0;
		
		var instance=this;
		
		$("#"+container).html('');
		$.post("./?mod_component/comp_contactos_list",obj,function(data){
			$("#"+container).html(data);
			
			$(getObject("role_list",0,obj.comp)).dataTable({
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
					}
				});			
				$(".contact_list_c","#"+obj.comp).click(function(){
					instance.viewContact(container,$(this).attr("contact"),$(this).attr("id"));
				});
		});
	},
 
	
});