function ejecuta(){
	ocultaPantalla();
	
	$('#resultad').html('<h3> Su peticion se esta procesando...</h3>');
	
	var calculoSimilitud=document.getElementById('calculoSimilitud').value
	var kVecinos=document.getElementById('kVecinos').value
	var action='calculoVecino';
	
	$.post('index.php',{action:action,kVecinos:kVecinos,calculoSimilitud:calculoSimilitud  }, 
			function(data){			
			muestraPantalla();
			var aux=data.split('####')
			
			$('#resultad').html('- <b>Tiempo de ejecucion del algoritmo: '+aux[0]+'<br><br>- <b>Modelo intermedio</b>:<br>'+aux[1]);
	})
}
function ocultaPantalla(){
	document.body.style.cursor = 'wait';
	document.getElementById('capaBloqueoApp').style.display="block";	
}
function muestraPantalla(){
	document.body.style.cursor = 'default';
	document.getElementById('capaBloqueoApp').style.display="none";
}


