<?
require_once("modelo.php");
require_once("vista.php");
require_once("KNN.php");


class controlador{
	private  $vista;
	private  $modelo;
	private $KNN;
	
	public function controlador($vista, $modelo){
		$this->vista=$vista;
		$this->modelo=$modelo;
		
	}	
	
	//funcion que ejecuta el algoritmo con los parametros dados.
	public function initPruebas($K,$calculoSimilitud, $algPredicion ){
		//cargamos y recogemos las valoraciones
		$this->modelo->cargaRatings();
		$ratings=$this->modelo->dameRatings();	
		//y un vector que es un indice con las posiciones a los item en el vector $ratings	
		$indiceItem=$this->modelo->dameIndiceRatingIdItem();
		//funcion para calcular el tiempo
		$time=time();
                //Configuracion de los parametros del KNN
		$PCC=0;
		$SC=0;
		if($calculoSimilitud=='PCC')
			$PCC=1;
		else
			$SC=1;
                $IA=0;
		$WS=0;
		if($algPredicion=='IA')
			$IA=1;
		else
			$WS=1;
		//creamos la clase KNN que es donde se van a realizar todos los calculos
		$this->KNN=new KNN($ratings,$indiceItem,$K, $PCC, $SC, $IA, $WS);
		//empezamos con el calculo del modelo intermedio     
		$desde=0;
		$hasta=10;
		$nombre='modelo_'.$K.'_'.$calculoSimilitud.'_'.$desde.'_'.$hasta;
		if($this->modelo->getVariable($nombre)!=null) {
			//
		}else{
                    $this->KNN->calculoVecinos($desde, $hasta);
                    $tablaModeloIntermedio=$this->KNN->dameModeloIntermedio();
                     //lo serializo el resultado para almacenarlo en la BD y tner el modelo guardado
                    $tablaModeloIntermedio=serialize($tablaModeloIntermedio);
                    $this->modelo->setVariable($nombre, $tablaModeloIntermedio);
		}
		$time2=time();
		
		
		echo ($time2-$time).'####';
		//printf de un vector
		//var_dump($this->KNN->dameSimilitudVecinos());
		
		
		
	}
	public function showVista(){
		$this->vista->show();
	}
}



?>