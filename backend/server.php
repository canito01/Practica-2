<?php


/* 
    1) recibe la solicitud del navegador o frontend
    2) valida si el modulo es correcto
    3) redirige la paeticion al archivo php adecuado segun el modulo
    4) maneja solicitudes OPTIONS (preflight) para CORS
    5) si algo falla, responde con un mensaje de error en formato JSON
*/ 

/**FOR DEBUG: */  // Para habilitar la visualización de errores en el navegador en el entorno de desarrollo
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

//Estas 3 lineas son necesarias cuando el frontend y backend no estan en el mismo dominio o puerto


header("Access-Control-Allow-Origin: *"); // permite que cualquier frontend, desde cualquier origen 
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS"); //indica al navegador los metodos http aceptados por el bcend
header("Access-Control-Allow-Headers: Content-Type"); /** permite las solicitudes con ciertos encabezados
*ese encabezado solo permite el nombre del encabezado, no sus valores
*el tipo de contenido se chequea en el backend*/


  function sendCodeMessage($code, $message = "") // Esta función envía una respuesta JSON con un código de estado HTTP y un mensaje, cuando se recibe una solicitud no válida
{
    http_response_code($code);
    echo json_encode(["message" => $message]);
    exit();
}

// Respuesta correcta para solicitudes OPTIONS (preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') //solicitud de prueba para verificar si el navegador puede hacer solicitudes al backend
{
    sendCodeMessage(200); // 200 OK
}

// Obtener el módulo desde la URL
$uri = parse_url($_SERVER['REQUEST_URI']);   //la funcion parse_url() analiza una URL y devuelve sus componentes
$query = $uri['query'] ?? ''; //query toma el valor de la clave 'query' del array devuelto por parse_url
// Si no hay query, se asigna una cadena vacía
parse_str($query, $query_array); //parse_str() convierte una cadena de consulta en un array asociativo
$module = $query_array['module'] ?? null; // Si no hay módulo, se asigna null

// Validación de existencia del módulo
if (!$module)
{
    sendCodeMessage(400, "Módulo no especificado");
}

// Validación de caracteres seguros: solo letras, números y guiones bajos
if (!preg_match('/^\w+$/', $module)) //protege contra inyecciones de código
{
    sendCodeMessage(400, "Nombre de módulo inválido");
}

// Buscar el archivo de ruta correspondiente
$routeFile = __DIR__ . "/routes/{$module}Routes.php";  
// __DIR__ es una constante que contiene la ruta del directorio actual del script
// Se construye la ruta al archivo de rutas del módulo especificado
// Si el archivo de rutas existe, se incluye; de lo contrario, se envía un mensaje de error 404

if (file_exists($routeFile))
{
    require_once($routeFile);
}
else
{
    sendCodeMessage(404, "Ruta para el módulo '{$module}' no encontrada");
}

?>