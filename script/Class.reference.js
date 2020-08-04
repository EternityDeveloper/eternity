// JavaScript Document
var PersonReference = new Class({
	formName: null,
	container : null,
	client_id : null,
	initialize : function(formName,client_id,containerName){
		 this.formName=formName; 
		 this.container=containerName;
		 this.client_id=client_id;
	},
	addNewRef : function(){
		var container=this.container;
		var obj={	
					index:this.index,
					comp:this.formName,
					client_id:this.client_id,
					show_add_ref:1,
					addnew : 1
				}
		var instance=this;
		$.post("./?mod_component/comp_referencia",obj,function(data){
			$("#"+container).before(data);
			
			$("#"+instance.formName).find("#remove").click(function(){
				$("#"+instance.formName).remove();
			});	
			
			$("#"+instance.formName).validate({
				rules: {
					'referencia_nombre[]': {
						required: true,
						minlength: 5	
					},
					'referencia_telefono_one[]': {
						required: true,
						number: true,
						minlength: 8	
					} 				
				},
				messages : {
					'referencia_nombre[]' : {
						required: "Este campo es obligatorio",
						minlength: "Debes de digitar un minimo de 5 caracteres" 
					},
					'referencia_telefono_one[]' : {
						required: "Este campo es obligatorio",
						minlength: "Debes de ingresar minimo de 8 digitos" ,
						number:"Este campo es numerico"
					} 				 	
					
				}
			});	
			
			/*AL PRECIONAR EL BOTON DE GUARDAR SAVE*/
			$("#"+instance.formName).find("#add_reference").click(function(){
				if ($("#"+instance.formName).valid()){
					$.get("./?mod_client/client_edit",$("#"+instance.formName).serializeArray(),function(data){
						if (data.typeError=="100"){
							alert(data.mensaje);
							instance.fire("onCreate",data);	
							$("#"+instance.formName).remove();
							instance.loadRef(); 
						}else{
							alert(data.mensaje + " error "+data.typeError);
						} 
			
					},"json");
				}	
			});					
	
		});
	},
	viewRef : function(divContainer,contact_id,index){
		var container=this.container;
		var instance = this;
		var laststatus=0;
		var name_from_address=this.formName+"_"+Math.floor(Math.random()*1000);
		var rand=this.getRand();
		var obj={	
					'index' : index,
					comp:name_from_address,
					client_id:this.client_id,
					disable_option:0,
					hide_remove:1,
					show_estatus:1,
					'contact_id' : contact_id,
					'rand':rand
				}
	
		instance.post("./?mod_component/comp_referencia",obj,function(data){
 
			var dialog=instance.createDialog(divContainer,"Ver detalles de referencias",data,700);
			 
			$("#cerrar_ventana5").click(function(){
		    	$("#"+dialog).dialog("close");
			});
				
			$(getObject("ref_estado[]",0,obj.comp)).click(function(e){
				laststatus=$(this).val();
			 });
			 
			 /*
			 $(getObject("ref_estado[]",0,obj.comp)).change(function(){
				 var c=confirm("Esta seguro que desea desabilitar esta referencia?");
				 
					if (c){
						
						var type=$(getObject("referencia_tipo[]",0,obj.comp)).val();
						var refer_id=$(getObject("refer_id",0,obj.comp)).val();
						
						var sndX={
								'estatus': $(this).val(),
								'ref_submit':'1',
								'id': obj.client_id,
								'tipo': type,
								'refer_id': refer_id
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
			 */
			 var tipo_old=$(getObject("referencia_tipo[]",0,obj.comp)).val()
			 
			 $("#save_referencia_"+rand).click(function(){
					var type=$(getObject("referencia_tipo[]",0,obj.comp)).val();
					var refer_id=$(getObject("refer_id",0,obj.comp)).val();
					
					var sndX={
							'estatus': $(getObject("ref_estado[]",0,obj.comp)).val(),
							'ref_submit':'1',
							'id': obj.client_id,
							'tipo': type,
							'refer_id': refer_id,
							'tipo_old':tipo_old,
							'referencia_telefono_one': $(getObject("referencia_telefono_one[]",0,obj.comp)).val(),
							'referencia_descripcion': $(getObject("referencia_descripcion[]",0,obj.comp)).val(),
							'referencia_nombre': $(getObject("referencia_nombre[]",0,obj.comp)).val(),
							'referencia_telefono_two': $(getObject("referencia_telefono_two[]",0,obj.comp)).val() 
						};
					
					$.post("./?mod_client/client_edit",sndX,function(data){	
						if (data.typeError=="104"){
							alert(data.mensaje);
							window.location.reload();
						}else{
							alert(data.mensaje + " error "+data.typeError);
						} 
					},"json");
			 });
			
		});
		
	},
	loadRef : function(){
		var container=this.container;
		var instance=this;
		var obj={	
					comp:this.formName+"list",
					client_id:this.client_id,
					disable_option:0,
					hide_remove:1,
					show_estatus:1
				}
		var laststatus=0;
		$("#"+container).html('');
		$.post("./?mod_component/comp_referencia_list",obj,function(data){
			$("#"+container).html(data);
			
			$(getObject("role_list",0,obj.comp)).dataTable({
				"bFilter": true,
				"bInfo": false,
				"bLengthChange": false,
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

			$(".referece_stl","#"+obj.comp).click(function(){
				instance.viewRef(container,0,$(this).attr("id"));
			});
		});
	}
	
});