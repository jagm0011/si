<?
require_once("usuario.php");
require_once("pelicula.php");
class modelo{
	private $configDB;
	private $db;	
	private $usuario;
	private $pelicula;
	private $rating;
	//usados como indices para mejorar la eficiencia
	private $indiceRatingIdItem;
			
	public function modelo(){
		$this->configDB=array();
		$this->configDB['server']='localhost';
		$this->configDB['user']='root';
		$this->configDB['pass']='';
		$this->configDB['dataBase']='SI_DB';	
		$this->db=0;
		$this->usuario=new usuario();
		$this->pelicula=new pelicula();		
		$this->rating=array();
		
		$this->indiceRatingIdItem=array();
		$this->init();
		
	}	
	public  function cargaRatings(){
		$consulta="SELECT idUser, idItem, rating FROM  ratings ORDER BY idItem,idUser";	
		$response=mysql_query($consulta);
		$con=-1;;
		$this->indiceRatingIdItem[0]=0;
		while($row=mysql_fetch_row($response)){
			//rating[idItem][idUsu]
			$this->rating[$row[1]][$row[0]]=intval($row[2]);
			if($con==-1 || $this->indiceRatingIdItem[$con]!=$row[1]){
				$con++;
				$this->indiceRatingIdItem[$con]=$row[1];				
			}
		}
		//print_r($this->indiceRatingIdItem);
	}
	public function init(){
		$this->db = mysql_connect($this->configDB['server'], $this->configDB['user'], $this->configDB['pass']) or die("Database error");		
		mysql_select_db($this->configDB['dataBase'], $this->db);
		
	}
	
	
	public function  dameRatings(){
		return $this->rating;			
	}
	
	public function  dameIndiceRatingIdItem(){
		return $this->indiceRatingIdItem;			
	}
	public function compruebaVariableEnDB($nombre){
		$consulta="SELECT id FROM variables WHERE nombre='".$nombre."'";
		$response=mysql_query($consulta);
		if(mysql_num_rows($response)>0)
			return true;
		return false;	
		
	}
	public function setVariable($nombre, $valor){
		$consulta=	"INSERT INTO variables (nombre ,valor)
			VALUES ('".$nombre."',  '".$valor."')";
		if($this->compruebaVariableEnDB($nombre))
			$consulta=	"UPDATE  variables SET  valor =  '".urlencode($valor)."' WHERE  nombre ='".$nombre."'";
			
		echo $consulta;
		$response=mysql_query($consulta);
		
	}
	public function getVariable($nombre){
		$consulta="SELECT valor FROM variables WHERE nombre='".$nombre."'";
		$response=mysql_query($consulta);
		if(mysql_num_rows($response)>0)
			while($row=mysql_fetch_row($response)){
				return urldecode($row[0]);	
			}			
		return null;
	}
}

?>