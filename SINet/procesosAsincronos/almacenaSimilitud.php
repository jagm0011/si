<?
require("../modelo.php");
$modelo=new modelo();
$idItem1=$_REQUEST['idItem1'];
$idItem2=$_REQUEST['idItem2'];
$similitud=$_REQUEST['similitud'];
$k=$_REQUEST['k'];

function compruebaNoEstaYaIntroducido(&$vecinosSimilitudes,$idItem, $quienNoDebeEstar ){
 	if(!isset($vecinosSimilitudes[$idItem])){			
 		$vecinosSimilitudes[$idItem]=array();	 		
 		return true;
 	}
 	$con=count($vecinosSimilitudes[$idItem])-1;
 	while($con>0){
 		if(intval($vecinosSimilitudes[$idItem][$con]['idItem'])==intval($quienNoDebeEstar))
 			return false;
 		$con--;
 	}
 	return true;
 }

//echo $modelo->getVariable('banderaControl');
//while(intval($modelo->getVariable('banderaControl'))!=1){}
$modelo->setVariable('banderaControl',0);
$vecinosSimilitudes=$modelo->getVariable('vecinosSimilitudes');
$vecinosSimilitudes=unserialize($vecinosSimilitudes);
//print_r($vecinosSimilitudes);
if(compruebaNoEstaYaIntroducido($vecinosSimilitudes,$idItem1, $idItem2) && compruebaNoEstaYaIntroducido($vecinosSimilitudes,$idItem2,$idItem1)){
	if(count($vecinosSimilitudes[$idItem1])>$k){
	   		   	
	   $con=count($vecinosSimilitudes[$idItem1])-1;
	   $actual=$con;
		$con--;   		
	   while($con>=0){
	   	if($vecinosSimilitudes[$idItem1][$actual]['similitud']>$vecinosSimilitudes[$idItem1][$con]['similitud']){
	   		$actual=$con;
	   	}
	   	$con--;
	   }
	   $vecinosSimilitudes[$idItem1][$actual]['idItem']=$idItem2;
	   $vecinosSimilitudes[$idItem1][$actual]['similitud']=$similitud;
	}else{
		$posActual=count($vecinosSimilitudes[$idItem1]);
		$vecinosSimilitudes[$idItem1][$posActual]['idItem']=$idItem2;
		$vecinosSimilitudes[$idItem1][$posActual]['similitud']=$similitud;   		
	}		
	   	
	   	//Similitud en la fila del item2
	if(count($vecinosSimilitudes[$idItem2])>$k){ 		   		
		$con=count($vecinosSimilitudes[$idItem2])-1;
		$actual=$con;
		$con--;   		
		while($con>=0){
			if($vecinosSimilitudes[$idItem2][$actual]['similitud']>$vecinosSimilitudes[$idItem2][$con]['similitud']){
	   		$actual=$con;
	   	}
	   	$con--;
	   }
		$vecinosSimilitudes[$idItem2][$actual]['idItem']=$idItem1;
		$vecinosSimilitudes[$idItem2][$actual]['similitud']=$similitud;
	}else{
		$posActual=count($vecinosSimilitudes[$idItem2]);
		$vecinosSimilitudes[$idItem2][$posActual]['idItem']=$idItem1;
		$vecinosSimilitudes[$idItem2][$posActual]['similitud']=$similitud;
	}	
	$vecinosSimilitudes=serialize($vecinosSimilitudes);
	$modelo->setVariable('vecinosSimilitudes',$vecinosSimilitudes);
	
}
$modelo->setVariable('banderaControl',1); 
?>