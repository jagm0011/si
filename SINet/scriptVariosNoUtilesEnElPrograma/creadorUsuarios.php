<?
$configDB=array();
//parametros de la conexion a la DB
$configDB['server']='localhost';
$configDB['user']='root';
$configDB['pass']='';
$configDB['dataBase']='SI_DB';
//conexion y eleccion de la DB
$db = mysql_connect($configDB['server'], $configDB['user'], $configDB['pass']) or die("Database error");		
mysql_select_db($configDB['dataBase'], $db);
//consulta sql
$consulta="SELECT DISTINCT(idUser) FROM ratings";
//ejecucion de consulta
$response=	mysql_query($consulta);
$idUsuarios=array();
$con=0;
//el resultado lo vuelco a un array. Mientras que el fetch siga dando resultados se introducen id de usuarios en el 
//vector
while($row=mysql_fetch_row($response)){
		$idUsuarios[$con]=$row[0];
		$con++;
}

//preparo la consulta insert de usuarios
$consulta="INSERT INTO usuario (id ,email ,pass)
VALUES ";
$con=0;
while($con< count($idUsuarios)){
		$consulta.="(".$idUsuarios[$con].", 'email".$con."@email.com', '".$con."'),";
		$con++;
}
//le quito el ultimo caracter porque se quedaria con una coma de mas
$consulta= substr($consulta, 0, -1);
$response=	mysql_query($consulta);
echo "texto impreso por pantalla. Fin";
?>