// Manejo de clientes
var PersonalData= Class({
	divItemName	:  null, //Div contenedor
	formName 	: null, //Nombre del formulario contenedor
	client_id 	: null, //ID del cliente
	initialize : function(divItemName,formName,client_id){
		this.divItemName=divItemName;
		this.formName=formName;
		this.client_id=client_id;
	 },
	loadCliente : function(){
		var instance=this;
 		divItemName=this.divItemName;
		formName=this.formName;
		client_id=this.client_id;
		this.post("./?mod_component/comp_detalle_cliente",{id:this.client_id,form:this.formName},function(data){
  
			$('#'+divItemName).html(data);
			
			$("#"+formName).validate();
			
			$("#update_personal").click(function(){
				if ($("#"+formName).valid()){
					instance.post("./?mod_client/client_edit",$("#"+formName).serializeArray(),function(data){
						if (data.typeError=="104"){
							alert(data.mensaje);
							window.location.reload();
						}else{
							alert(data.mensaje + " error "+data.typeError);
						} 
		
					},"json");
				}
			});
			
			$("#fecha_nacimiento").datepicker({
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
					
		});
	 },
	setClientID : function(client_id){
		this.client_id=client_id;
	},
	getPersonData : function(){
		var instance= this;
		divItemName=this.divItemName;
		formName=this.formName;
		client_id=this.client_id;
		this.post("./?mod_client/client_list",{id:this.client_id,getPersonData:1},function(data){
				instance.fire("personal_data_load",data); 	
		},"json");
	 }
 	

});