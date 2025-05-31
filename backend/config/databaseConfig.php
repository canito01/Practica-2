<?php

/*
    Se ejecuta automaticamente en todos los scripts del backend que necesiten conectarse a la base de datos.
    Define los datos de conexión
    Intenta conectarse usando new mysqli()
    Si la conexión falla, envía un mensaje de error y termina la ejecución del script.
*/


$host = "localhost";
$user = "students_user";
$password = "12345";
$database = "students_db";

$conn = new mysqli($host, $user, $password, $database); 
// Este objeto se puede usar para realizar consultas a la base de datos

if ($conn->connect_error) {
    http_response_code(500);
    die(json_encode(["error" => "Database connection failed"]));
}

//Si la conexion es exitosa ya podemos usar $conn para realizar consultas a la base de datos en otros archivos
?>