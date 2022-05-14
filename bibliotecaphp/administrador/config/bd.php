<?php
$host="127.0.0.1";
$bd="sitio";
$usuario="nino";
$contrasenia="nino";

try{
    $conexion= new PDO("mysql:host=$host;dbname=$bd",$usuario,$contrasenia);
   // if($conexion){echo "Conectado... a sistema";}
}catch(Exeption $ex){
    echo $ex->getMessage();
}
?>