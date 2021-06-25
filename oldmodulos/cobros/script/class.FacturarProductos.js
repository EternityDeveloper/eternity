/* REGISTRAR DATOS DEL INHUMADO */
var FacturarProductos = new Class({
	dialog_container : null,  
	_rand : null, 
	initialize : function(dialog_container){
		this.main_class="DetalleInhumado";
		this.dialog_container=dialog_container; 
	}, 
	doView: function(id_nit,contrato){
		var instance=this;   
		instance.post("./?mod_cobros/delegate&inhumado&view_facturar_product",{ 
			},function(data){   
 			instance.doDialog("facturar_pro",instance.dialog_container,data); 
			instance.addListener("onCloseWindow",function(){
				//alert('fsd');	
			});  
		  	
		  	var producto=null;
			var costo =0;
			var detalle_
		  	$('#sv_inh_producto').select2();
			$('#sv_inh_producto').change(function(){
				var sp=$(this).val().split("__");
				var data= $.parseJSON($.base64.decode(sp[1]));	 
				$("#inh_precio").val(instance.number_format(data.costo));
				costo=data.costo;
				producto=sp[0];
			});
			$('#inh_precio').change(function(){ 
				costo=$(this).val();
			});		 
		 
			
			$("#agregar_producto_inhu").click(function(){
				if ($("#detalle_inhumado_form").valid()){ 					
					instance.doAddProduct(producto,costo)	
				}			
			}); 
			$.validator.messages.required = "Campo obligatorio.";					
							
		});			
	},
	doAddProduct : function(producto,costo){
		var instance=this;
		instance.post("./?mod_cobros/delegate&inhumado",{
			"doSaveFactura":1,
			"costo":costo,
			"producto":producto,
			"cantidad":$("#inh_cantidad").val()
		},function(data){  
			if (data.valid){ 
				instance.CloseDialog("facturar_pro");
				$("#detalle_factura_pro").html(data.html);
				instance.doRemoveProduct();
			}else{
				alert(data.mensaje)	
			}
		
		},"json");		
	},
	doRemoveProduct : function(){
		var instance =this;
		$(".prt_remove_inh").click(function(){
			var producto=$(this).attr("id");
			if (confirm("Esta seguro que desea elminar este item?")){
				instance.post("./?mod_cobros/delegate&inhumado",{
					"doRemoveItemFromCar":1,
					"producto":producto
				},function(data){  
					if (data.valid){ 
						$("#detalle_factura_pro").html(data.html);
						instance.doRemoveProduct(); 
					}else{
						alert(data.mensaje)	
					}
				},"json");										
			}					
		});		
	}
});