// JavaScript Document
var popupStatus = 0;  

function loadPopup(){  
//loads popup only if it is disabled  
if(popupStatus==0){  
$("#backgroundPopup").css({  
"opacity": "0.7"  
});  
$("#backgroundPopup").fadeIn("slow");  
$("#ResortInforVid").fadeIn("slow");  
popupStatus = 1;  
}  
}

//disabling popup with jQuery magic!  
function disableResortPopup(){  
//disables popup only if it is enabled  
if(popupStatus==1){  
$("#backgroundPopup").fadeOut("slow");  
$("#ResortContainer").fadeOut("slow");  
popupStatus = 0;  
}  
}  

function centerPopup(){  
//request data for centering  
var windowWidth = document.documentElement.clientWidth;  
var windowHeight = document.documentElement.clientHeight;  
var popupHeight = $("#popupDesicion").height();  
var popupWidth = $("#popupDesicion").width();  
//centering  
$("#popupDesicion").css({  
"position": "absolute",  
"top": windowHeight/2-popupHeight/2,  
"left": windowWidth/2-popupWidth/2  
});  
//only need force for IE6  
  
$("#backgroundPopup").css({  
"height": windowHeight  
});  
  
}

$(document).ready(function(){  
//following code will be here  


//LOADING RESORT INFORMATION POPUP 
	$("#ResortInf").click(function(){  	
								   
			var windowWidth = document.documentElement.clientWidth;  
			var windowHeight = document.documentElement.clientHeight;  
			var popupHeight = $("#ResortContainer").height();  
			var popupWidth = $("#ResortContainer").width();  
			//centering  
			$("#ResortContainer").css({  
			"position": "absolute",  
			"top": windowHeight/2-popupHeight/2,  
			"left": windowWidth/2-popupWidth/2  
			});  
			//only need force for IE6  
			  
			$("#backgroundPopup").css({  
			"height": windowHeight  
			});  					   
								   
			if(popupStatus==0){  
				$("#backgroundPopup").css({  
				"opacity": "0.7"});  
				$("#backgroundPopup").fadeIn("slow");  
				$("#ResortContainer").fadeIn("slow");  
				popupStatus = 1;  
		}  
 	  
	});
	
//CLOSE RESORT INFORMATION POPUP
	$("#CloseResortPop").click(function(){  											
	if(popupStatus==1){  
		$("#backgroundPopup").fadeOut("slow");  
		$("#ResortContainer").fadeOut("slow");  
		popupStatus = 0;  
	}    
	});
		//Click out event!  
	
//OPEN PAYMENT INFORMATION POPUP
	
	$("#paymentInf").click(function(){  
			
			var windowWidth = document.documentElement.clientWidth;  
			var windowHeight = document.documentElement.clientHeight;  
			var popupHeight = $("#PaymentInfDIV").height();  
			var popupWidth = $("#PaymentInfDIV").width();  
			//centering  
			$("#PaymentInfDIV").css({  
			"position": "absolute",  
			"top": windowHeight/2-popupHeight/2,  
			"left": windowWidth/2-popupWidth/2  
			});  
			//only need force for IE6  
			  
			$("#backgroundPopup").css({  
			"height": windowHeight  
			});  					   
					
									
									
			if(popupStatus==0){  
				$("#backgroundPopup").css({  
				"opacity": "0.7"});  
				$("#backgroundPopup").fadeIn("slow");  
				$("#PaymentInfDIV").fadeIn("slow");  
				popupStatus = 1;  
		}    
	});
	
//CLOSE PAYMENT INFORMATION POPUP
	$("#ClosePaymentPop").click(function(){  											
	if(popupStatus==1){  
		$("#backgroundPopup").fadeOut("slow");  
		$("#PaymentInfDIV").fadeOut("slow");  
		popupStatus = 0;  
	}    
	});



	//$("#backgroundPopup").click(function(){disableResortPopup();});  
	
	//Press Escape event!  
	$(document).keypress(function(e){  
	if(e.keyCode==27 && popupStatus==1){  
	disableResortPopup();  
	}  
	});  


});  

