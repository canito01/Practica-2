<?
/*
    Es como un despachador de rutas. Recibe las peticiones HTTP y llama a la funcion que las maneja
    Tiene un comportamiento por defecto (basado en nombre como handleGet, handlePost, etc.)
    Puede ser personalizado para casos especiales, por ejemplo validaciones POST
    Devuelve errores en formato JSON y con codigos HTTP estandarizados
*/

//Se encarga de gestionar las rutas REST(CRUD) Y de conectar cada metodo HTTP con la funcion correspondiente

function routeRequest($conn, $customHandlers = [], $prefix = 'handle')  //permite que la funcion funcione igual en todos los modulos, pero tambien puede ser personalozizada para un modulo en particular
{
    $method = $_SERVER['REQUEST_METHOD']; //php guarda los datos del servidor en la variable superglobal $_SERVER, y el metodo HTTP se guarda en la clave REQUEST_METHOD

    // Lista de handlers CRUD por defecto
    $defaultHandlers = [
        'GET'    => $prefix . 'Get',
        'POST'   => $prefix . 'Post',
        'PUT'    => $prefix . 'Put',
        'DELETE' => $prefix . 'Delete'
    ];

    // Sobrescribir handlers por defecto si hay personalizados, por ejemplo, la modificacion de la funcion post que  hicimos en studentsRoutes.php
    $handlers = array_merge($defaultHandlers, $customHandlers);

    if (!isset($handlers[$method])) 
    {
        http_response_code(405);
        echo json_encode(["error" => "Método $method no permitido"]);
        return;
    }
 
    $handler = $handlers[$method];  

    if (is_callable($handler)) 
    {
        $handler($conn);
    }
    else
    {
        http_response_code(500);
        echo json_encode(["error" => "Handler para $method no es válido"]);
    }
}
