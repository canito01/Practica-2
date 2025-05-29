<?php
//PREGUNTAR POR EL .HTACCESS
// darle acceso a ciertas rutas del servidor, para
/**
 * DEBUG MODE
 */
ini_set('display_errors', 0);// indica a php que muestre los errores directamente por pantalla
//error_reporting(E_ALL);  //muestra todos los tipos de errores 

header("Access-Control-Allow-Origin: *"); /** permite que cualquier frontend, desde cualquier origen *,
  *pueda acceder al backend. es peligroso en temas de seguridad esto o no tiene que ver?  
  *a que se refiere con front end? al lenguaje? */
  /* es necesario cuando el frontend y backend no estan en el mismo dominio o puerto */

header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS"); //indica al navegador los metodos http aceptados por el bcend
header("Access-Control-Allow-Headers: Content-Type"); /** permite las solicitudes con ciertos encabezados
*ese encabezado solo permite el nombre del encabezado, no sus valores
*el tipo de contenido se chequea en el backend*/

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { /* el metodo options atiende preflights requests del navegador
  It is an HTTP request of the OPTIONS method, sent before the request itself, 
  in order to determine if it is safe to send it.
 */
    http_response_code(200);
    exit();
}

// require_once("./routes/studentsRoutes.php"); 
/** incluye el archivo que define las RUTAS o la lógica que responderá a la peticion actual
  * usa require once para incluirlo una sola vez, lo que evita errores por multiples inclusiones
 */
/* este archivo debería analizar la URL, metodo y decidir qué controlador invocar. controlador?
 */

 /** tiene que estar preparado para modulos futuros
 *analizando la URL y decidiendo qué archivo de ruta invocar usando alguna convencion
 *un switch?
  */



  function sendCodeMessage($code, $message = "")
{
    http_response_code($code);
    echo json_encode(["message" => $message]);
    exit();
}

// Respuesta correcta para solicitudes OPTIONS (preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS')
{
    sendCodeMessage(200); // 200 OK
}

// Obtener el módulo desde la query string
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
if (!preg_match('/^\w+$/', $module))
{
    sendCodeMessage(400, "Nombre de módulo inválido");
}

// Buscar el archivo de ruta correspondiente
$routeFile = __DIR__ . "/routes/{$module}Routes.php";

if (file_exists($routeFile))
{
    require_once($routeFile);
}
else
{
    sendCodeMessage(404, "Ruta para el módulo '{$module}' no encontrada");
}

?>