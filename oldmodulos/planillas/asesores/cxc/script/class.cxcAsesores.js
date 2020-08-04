/* CXC ASESOR*/
var cxcAsesores = new Class({
	dialog_container : null,  
	_rand : null,
	_polygon :null,
	initialize : function(dialog_container){
		this.main_class="cActas";
		this.dialog_container=dialog_container; 
	},	  

	doInitListado : function( ){ 
		var instance=this;   
		
		var table=$("#dtTable").DataTable({
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
 		
		$('#dtTable tbody').on('click', 'td.listado_cxc', function () {
			var tr = $(this).parents('tr');
			var id=$($(this).parents('tr')).attr("id");
			var row = table.row(tr);
			var td = tr.children('td');
		
			if ( row.child.isShown() ) {
				// This row is already open - close it
				row.child.hide();
				tr.removeClass('shown');
			}
			else {
				// Open this row
				instance.cargarDetalleCxC(row,id); 				
				tr.addClass('shown');
			}
		} );
		
		
		$("#do_aplicar_nota_cd").click(function(){
 			instance.doViewNotaCD();
		});
		
 		 	
	},
	doViewNotaCD  : function(){
		var instance=this; 
		instance.post("./?mod_planillas/delegate&listado_cxc",{doViewCDAsesor:true},function(data){   
			instance.doDialog("aplicar_nota_c_d",instance.dialog_container,data);
			
			var monto=0;
			var tipo_m="";
			var asesor="";
			var descripcion="";
			
			$("#t_movimiento").change(function(){
				tipo_m=$(this).val(); 
			});
			
			$("#notacd_monto").change(function(){
				if ($.isNumeric($(this).val())){
					monto=$(this).val();
				}else{
					$(this).val('');	
				}
			});
			
			$("#nota_descripcion").change(function(){
				descripcion=$(this).val(); 
			});	
			
			$("#_asesor").select2(); 
			$("#_asesor").on("change",function(e) { 
				asesor=e.val; 
			});	
					
			$("#aplicar_nota_cd").click(function(){
				instance.post("./?mod_planillas/delegate&listado_cxc",
				{
					"tipo_m":tipo_m,
					"monto":monto,
					"descripcion":descripcion,
					"id":asesor,
					"aplicar_nota_cd":true
				},function(data){   
					alert(data.mensaje);						
					if (!data.error){
						window.location.reload();
					} 		
				},"json");	
				
			});		
			
			
		});	
	},	
	cargarDetalleCxC  : function(row,id){
		var instance=this; 
		instance.post("./?mod_planillas/delegate&listado_cxc",{getDetallecxc:true,id:id},function(data){   
			row.child(data).show();
		});	
	}
	
	
});