<?
class KNN {
				//estructura con las similitudes
    private $vecinosSimilitudes;
    private $predicciones;
    /* matriz con $matrizRating[i][j]
    * i= idItem
    * j= idUser
    */
    private $matrizRating;  
	 private $indiceRatingIdItem;
    //parametros configuracion. Por defecto PCC
    // k vecinos
    private $PCC;
    private $SC;
    private $k;
    private $IA;
    private $WS;
    private $usuarioActivo;
    private $curl;
    public function KNN($matrizRating,$indiceRatingIdItem, $k,$PCC, $SC, $IA, $WS){
        $this->matrizRating=$matrizRating;
        
			$this->indiceRatingIdItem=$indiceRatingIdItem;
        $this->vecinosSimilitudes=array();
        $this->predicciones=array();
        $this->SC=$SC;
        $this->PCC=$PCC;
        $this->IA=$IA;
        $this->WS=$WS;
        $this->k=$k;                	   	
    }
    
    //*funcion usada para comprobar que el item no esta ya introducido en el modelo intermedio
	function compruebaNoEstaYaIntroducido($idItem, $quienNoDebeEstar ){
	 	if(!isset($this->vecinosSimilitudes[$idItem])){			
	 		$this->vecinosSimilitudes[$idItem]=array();	 		
	 		return true;
	 	}
	 	$con=count($this->vecinosSimilitudes[$idItem])-1;
	 	while($con>0){
	 		if(intval($this->vecinosSimilitudes[$idItem][$con]['idItem'])==intval($quienNoDebeEstar))
	 			return false;
	 		$con--;
	 	}
	 	return true;
	 }
	 
	 //Calcula modelo intermedio
    public function calculoVecinos($desde, $hasta){        
        //Empezamos recorriendo el vector con las valoraciones
        $total=$hasta;//count($this->indiceRatingIdItem)-1;
			$con=$total;        
        $con2=$total;
        while($con>=$desde){
        	$item1=$this->matrizRating[$this->indiceRatingIdItem[$con]];
        	$idItem1=intval($this->indiceRatingIdItem[$con]);
        	while($con2>=$desde){
        		$item2=$this->matrizRating[$this->indiceRatingIdItem[$con2]];
        		$idItem2=intval($this->indiceRatingIdItem[$con2]);
        		//Si no son el mismo item seguimos
        		if($idItem2!=$idItem1){
        			//sino tienen ya la similitud introducida seguimos
        			if($this->compruebaNoEstaYaIntroducido($idItem1, $idItem2) && $this->compruebaNoEstaYaIntroducido($idItem2,$idItem1)){
        				//calcula la similitud        		
        				$similitud=$this->calculaSimilitud($item1, $item2);
        				//la almacenamos en el modelo intermedio           			     		
		        		$this->almacenaSimilitud($idItem1, $idItem2, $similitud);
		        	}
		        }
        		$con2--;	
        	}
        	$con2=$total;
        	$con--;	
        }       
    }
     /*
   * Funcin que almacena las similitudes en el modelo intermedio
   * $idItem1, $idItem2 y similitud entre ambos
   *	Al hacerlo de esta forma hacemos más eficiente el programa ya que con la misma pasada guardamos 2 valores
   */ 
    public function almacenaSimilitud($idItem1, $idItem2, $similitud){
   				
   	if(count($this->vecinosSimilitudes[$idItem1])>$this->k){	   		   	
		   $con=count($this->vecinosSimilitudes[$idItem1])-1;
		   $actual=$con;
			$con--;   		
		   while($con>=0){
		   	if($this->vecinosSimilitudes[$idItem1][$actual]['similitud']>$this->vecinosSimilitudes[$idItem1][$con]['similitud']){
		   		$actual=$con;
		   	}
		   	$con--;
		   }
		   $this->vecinosSimilitudes[$idItem1][$actual]['idItem']=$idItem2;
		   $this->vecinosSimilitudes[$idItem1][$actual]['similitud']=$similitud;
		 }else{
		  	$posActual=count($this->vecinosSimilitudes[$idItem1]);
			$this->vecinosSimilitudes[$idItem1][$posActual]['idItem']=$idItem2;
			$this->vecinosSimilitudes[$idItem1][$posActual]['similitud']=$similitud;   		
		 }		
		   	
		   	//Similitud en la fila del item2
		 if(count($this->vecinosSimilitudes[$idItem2])>$this->k){ 		   		
			$con=count($this->vecinosSimilitudes[$idItem2])-1;
			$actual=$con;
			$con--;   		
			while($con>=0){
				if($this->vecinosSimilitudes[$idItem2][$actual]['similitud']>$this->vecinosSimilitudes[$idItem2][$con]['similitud']){
		   		$actual=$con;
		   	}
		   	$con--;
		   }
			$this->vecinosSimilitudes[$idItem2][$actual]['idItem']=$idItem1;
			$this->vecinosSimilitudes[$idItem2][$actual]['similitud']=$similitud;
		 }else{
			$posActual=count($this->vecinosSimilitudes[$idItem2]);
			$this->vecinosSimilitudes[$idItem2][$posActual]['idItem']=$idItem1;
			$this->vecinosSimilitudes[$idItem2][$posActual]['similitud']=$similitud;
		 }		
   }
   
    //Calcula la similitud entre 2 peliculas.
    private function calculaSimilitud($v1, $v2){        
        //quiere decir que tienen valoraciones. Si alguno no tiene, la similitud es 0       
         if($this->PCC==1)
             return $this->PCC($v1,$v2);
         else
             return $this->SC($v1,$v2);        
       
    }
  
    /*****FUNCIONES CALCULO DE SIMILITUD******/
    //Coeficiente de correlacion de Pearson
    /*
     * r=1 correlacion perfect
     * 0<r<1 correlacion positiva
     * r=0 no existe relacion
     * 1<r<0 correlacion negativa
     * r=-1 correlacion negativa perfecta
     */
     
    public function PCC($v1, $v2){
    	
        $mediaValoracionItem1=$this->calculaMedia($v1);
        $mediaValoracionItem2=$this->calculaMedia($v2);
        
       //Calculo numerador
        $sumatoriaNumerador=0;
        $sumatoriaDenominador1=0;
	     $sumatoriaDenominador2=0;
        
        
        //$con=0;   
        foreach($v1 as $idUsu => $valor){        	
        	//compruebo que los dos usuarios con el mismo id han hecho una valoracion del item 
        	if(isset($v2[$idUsu])){        		
        		$sumatoriaNumerador+=($valor-$mediaValoracionItem1)*($v2[$idUsu]-$mediaValoracionItem2);
        		$sumatoriaDenominador1+=($v1[$idUsu]-$mediaValoracionItem1)*($v1[$idUsu]-$mediaValoracionItem1);
        		$sumatoriaDenominador2+=($v2[$idUsu]-$mediaValoracionItem2)*($v2[$idUsu]-$mediaValoracionItem2);        		
        	} 
			}
			$sumatoriaDenominador=sqrt($sumatoriaDenominador1*$sumatoriaDenominador2);
			if($sumatoriaNumerador==0){
				return 0.5;
			}
      
	     $resultado=$sumatoriaNumerador/$sumatoriaDenominador;
	      
        //hay que hacer esta cuenta para transformala a medida de similitud
        return ($resultado+1)/2;       
    }
    
    /*
    * Calcular la media de las valoraciones de un item
    */
    public function calculaMedia($v){
        $media=0;
        //$con=count($v));       
        foreach($v as $valor ){        
        	$media+=$valor;        	
        }        
        return $media/count($v);
    }
    
    //Similitud del coseno
    /*
     * r=0 minima similitus
     * r=1 maxima similitud
     */
    public function SC($v1, $v2){
        
        $sumatoriaNumerador=0;
        $sumatoriaDenominador=0;
        $sumatoriaDenominador1=0;
	     $sumatoriaDenominador2=0;
        
        //recorro solo un vector y compruebo que el usuario haya valorado tambien en el otro
        foreach($v1 as $idUsu => $valor){
        	//compruebo que los dos usuarios con el mismo id han hecho una valoracion del item 
        	if(isset($v2[$idUsu])){
        		$sumatoriaNumerador+=$v1[$idUsu]*$v2[$idUsu];
        		$sumatoriaDenominador1+=$v1[$idUsu]*$v1[$idUsu];
	        	$sumatoriaDenominador2+=$v2[$idUsu]*$v2[$idUsu];
        		//$indicesComunes[$con]=$idUsu;
        	} 
			}
			//si el numerador es 0 -> no hay valoraciones hechas por los mismos usuarios por lo que return 0;
			if($sumatoriaNumerador==0){
				return 0;	
			}	        
        $sumatoriaDenominador1=sqrt($sumatoriaDenominador1);
        $sumatoriaDenominador2=sqrt($sumatoriaDenominador2);
        return $sumatoriaNumerador/($sumatoriaDenominador1*$sumatoriaDenominador2);	     	     
    }
   
   
    public function dameSimilitudVecinos(){
    	return $this->vecinosSimilitudes;	
    }
    public function resetSimilitudVecinos(){
    	$this->vecinosSimilitudes=array();	
    }
    public function prediccion($usuario, $peliculas){
    	 if($this->IA==1)
           return $this->prediccionItemAverage($usuario, $peliculas);
      else
           return $this->prediccionWeigthedSum($usuario, $peliculas);        
       
    	
    }
    public function prediccionItemAverage($usuario, $peliculas){
    		
    		$peliculasValoradas=$usuario->damePeliculasValoradas();
    		$idUsuario=$usuario->dameId();
    		$con=count($peliculas)-1;
    		while($con>=0){
    			$idPelicula=$peliculas[$con]->dameId();
    			//Si la pelicula no esta valorada por el usuario pues se calcula su prediccion
    			if(!isset($peliculasValoradas[$idPelicula])){
    				$prediccion=itemAverage($idPelicula,$idUsuario);
    				$this->insertaPrediccion($idPelicula, $prediccion);
    			}
    			$con--;	
    		}
    }
   public function itemAverage($idPelicula,$idUsuario){
   	
   }
   public function prediccionWeigthedSum($usuario, $peliculas){
    		
    		$peliculasValoradas=$usuario->damePeliculasValoradas();
    		$con=count($peliculas)-1;
    		while($con>=0){
    			
    			$con--;	
    		}
    }
   public function weigthedSum(){
   	
   	
   }
	public function insertaPrediccion($idPelicula, $prediccion){
		$tamaPredicciones=count($this->predicciones);	
		if($tamaPredicciones<15){
			$this->predicciones[$tamaPredicciones]=array();
			$this->predicciones[$tamaPredicciones]['idPelicula']=$idPelicula;
			$this->predicciones[$tamaPredicciones]['prediccion']=$prediccion;
		}else{
			$actual=$this->predicciones[$tamaPredicciones]['prediccion'];
			$pos=$tamaPredicciones;
			$tamaPredicciones--;
			while($tamaPredicciones>=0){
				if($this->predicciones[$tamaPredicciones]['prediccion']<$actual){
					$actual=$this->predicciones[$tamaPredicciones]['prediccion'];
					$pos=$tamaPredicciones;
				}
				$tamaPredicciones--;	
			}	
			$this->predicciones[$pos]['idPelicula']=$idPelicula;
			$this->predicciones[$pos]['prediccion']=$prediccion;
		}
	}
}
?>