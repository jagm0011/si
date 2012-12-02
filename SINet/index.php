<?
/*Utilizado como Front Controller
* $action variable que envia la vista para pedir una accion al controlador
* 
*/
require_once("controlador.php");
require_once("vista.php");
require_once("modelo.php");
//
$action='';
if(isset($_REQUEST['action'])) {
    $action=$_REQUEST['action'];	
}

switch($action) {
	//ejecutado al pulsar el boton "ejecutar" en la vista
    case "calculoVecino":{
        if(isset($_POST['kVecinos']) && isset($_POST['calculoSimilitud'])){
            $kVecinos=$_POST['kVecinos'];
            $calculoSimilitud=$_POST['calculoSimilitud'];
            $algPredicion=$_POST['algoritmoPrediccion'];
            $vista=new vista();
            $modelo=new modelo();
            $controlador=new controlador($vista,$modelo);
            $controlador->initPruebas($kVecinos,$calculoSimilitud,$algPredicion);

        }	
    break;
    }
    //se ejecuta al iniciar la app
    default:{
        $vista=new vista();
        $modelo=new modelo();
        $controlador=new controlador($vista,$modelo);
        $controlador->showVista();
    }	
}
?>