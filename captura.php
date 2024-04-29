<?php

include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
   header('location:home.php');
   exit();
};

$json = file_get_contents('php://input');
$datos = json_decode($json, true);

print_r($datos);

if(is_array($datos)){
    $id_transaccion = $datos['detalles'];
}
 
?>