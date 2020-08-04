/*
	VALIDA SI UNA CONTRATO CONTIENE RESERVA Y ABONOS A CAJA
*/
var ContratoCaja = new Class({
	dialog_container : null, 
	data : {
			monto:0
		},
	initialize : function(dialog_container){
		this.main_class="ContratoCaja";
		this.dialog_container=dialog_container; 
	},
	getMontoMonto : function(producto){
		var instance=this;
		this.post("./?mod_contratos/listar",{
			"getMontoFromProduct":'1',
			"producto":producto
		},function(data){  
			instance.data=data;
			if (data.valid){
				var html='<div style="background-color:#F90;color:#FFF;width:800px;padding:5px;margin:5px;border-radius:2px;font-size:18px;">Esta propiedad posee un abono acumulado de $'+parseFloat(data.monto,10).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,").toString()+', el numero de reserva es:<a target="sd2" style="color:#FFF;text-decoration:none" href="?mod_caja/ingreso&mov_reserva&search='+data.no_reserva+'"> '+data.no_reserva+'</a></div>'
				
				$("#"+instance.dialog_container).html(html);
				$("#"+instance.dialog_container).show();
				
				var inf={
					'monto':data.monto,
					'transapciones':data.transapciones
				};
				
				instance.fire("updateMonto",inf);
			}else{
				$("#"+instance.dialog_container).hide();
				instance.fire("updateMonto",0);
			}
		},"json");
	}
	
});