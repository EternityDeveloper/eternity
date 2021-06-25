// JavaScript Document
var PersonReferidos = new Class({
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
		$.post("./?mod_component/comp_referidos",obj,function(data){
			$("#"+container).before(data);
			
			$("#"+instance.formName).find("#remove").click(function(){
				$("#"+instance.formName).remove();
			});	
			
			$("#"+instance.formName).validate({
				rules: {
					'nombre1': {
						required: true,
						minlength: 3	
					},
					'apellido1': {
						required: true,
						minlength: 3	
					}  				
				},
				messages : {
					'nombre1' : {
						required: "Este campo es obligatorio",
						minlength: "Debes de digitar un minimo de 5 caracteres" 
					},
					'apellido1' : {
						required: "Este campo es obligatorio",
						minlength: "Debes de digitar un minimo de 5 caracteres" 
					} 	 
				}
			});	
			
			/*AL PRECIONAR EL BOTON DE GUARDAR SAVE*/
			$("#"+instance.formName).find("#bt_add_referido").click(function(){
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
	
		instance.post("./?mod_component/comp_referidos",obj,function(data){
 
			var dialog=instance.createDialog(divContainer,"Ver detalles del referido",data,700);
			 
			$("#cerrar_ventana5").click(function(){
		    	$("#"+dialog).dialog("close");
			});
				
			$(getObject("ref_estado",0,obj.comp)).click(function(e){
				laststatus=$(this).val();
			 });
			/* 
			 $(getObject("ref_estado",0,obj.comp)).change(function(){
				 var c=confirm("Esta seguro que desea desabilitar este Referido?");
				 
					if (c){
						
						var type=$(getObject("referencia_tipo",0,obj.comp)).val();
						 
						var sndX={
								'estatus': $(this).val(),
								'referidos_submit':'1',
								'id': obj.client_id,
								'tipo': type 
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
			 var tipo_old=$(getObject("referencia_tipo",0,obj.comp)).val();
			 
			 $("#bt_save_ref_"+rand).click(function(){
					var type=$(getObject("referencia_tipo",0,obj.comp)).val();
					 
					var sndX={
							'estatus':$(getObject("ref_estado",0,obj.comp)).val(),
							'referidos_submit':'1',
							'id': obj.client_id,
							'tipo': type,
							'tipo_old':tipo_old,
							'nombre1':$(getObject("nombre1",0,obj.comp)).val(),
							'nombre2':$(getObject("nombre2",0,obj.comp)).val(),
							'apellido1':$(getObject("apellido1",0,obj.comp)).val(),
							'apellido2':$(getObject("apellido2",0,obj.comp)).val(),
							'telefono':$(getObject("telefono",0,obj.comp)).val(),
							'movil':$(getObject("movil",0,obj.comp)).val(),
							'descripcion':$(getObject("descripcion",0,obj.comp)).val()
						};
					
					instance.post("./?mod_client/client_edit",sndX,function(data){	
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
		$.post("./?mod_component/comp_referidos_list",obj,function(data){
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

			$(".referidos_stl","#"+obj.comp).click(function(){
				instance.viewRef(container,0,$(this).attr("id"));
			});
		});
	}
	
});