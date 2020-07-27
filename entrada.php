<?php

if( !isset($_POST['entrada']) && !isset($_POST['id_user']) ){
    die();
}

$id_user = $_POST['id_user']; // Id do usuário
$valor = $_POST['entrada']; // Valor da entrada
$a = date("Y-m-d H:i:s"); // Data atual

if($valor == '' && $id_user == ''){			
    $response = "Não pode estar vazio";
    echo json_encode($response);			
    die;
} 

$hostname = "mysql.laviegourmetemcasa.com.br";
$username = "laviegourm_add1";
$password = "ET2jiyl34ekvB";
$db = "laviegourmetem";

 // Create connection

 $conn = new mysqli($hostname, $username, $password, $db);

 // Check connection

 if ($conn->connect_error) {
    
    $response = "Houve um erro ao conectar com o banco de dados";
    echo json_encode($response);			
    die;
 }

 $sql = "INSERT INTO lgc_history (id_user, data_entrada, valor_entrada)
 VALUES ('" . $id_user . "', '". $a ."', '". $valor ."')";


if ($conn->query($sql) === TRUE) {

    $response = "sucesso";
    echo json_encode($response);			
    die;        

} else {
    $response = "Error: ". $conn->error;
    echo json_encode($response);			
    die;
}   