<?php
require_once("./config/databaseConfig.php"); //datos de conexión a la base de datos
require_once("./routes/routesFactory.php"); //contiene la función routeRequest que maneja las rutas para get, post, put y delete
require_once("./controllers/studentsController.php"); //contiene funciones como handleget, handlepost,etc que hacen el trabajo real de manejar las solicitudes

// routeRequest($conn);


/**
 * Ejemplo de como se extiende un archivo de rutas 
 * para casos particulares
 * o validaciones:
 */
routeRequest($conn, [
    'POST' => function($conn) 
    {
        // Validación o lógica extendida
        $input = json_decode(file_get_contents("php://input"), true);
        if (empty($input['fullname'])) 
        {
            http_response_code(400);
            echo json_encode(["error" => "Falta el nombre"]);
            return;
        }
        handlePost($conn);
    }
]);

?>