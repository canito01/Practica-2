<?php
require_once("./models/subjects.php");

function handleGet($conn) 
{
   $input = json_decode(file_get_contents("php://input"), true);

    if (isset($input['id'])) 
    {
        $subject = getSubjectById($conn, $input['id']);
        echo json_encode($subject);
    } 
    else 
    {
        $subjects = getAllSubjects($conn);
        echo json_encode($subjects);
    }
}

/*function handlePost($conn) 
{
    $input = json_decode(file_get_contents("php://input"), true);
    if (createSubject($conn, $input['name'])) 
    {
        echo json_encode(["message" => "Materia creada correctamente"]);
    } 
    else 
    {
        http_response_code(500);
        echo json_encode(["error" => "No se pudo crear"]);
    }
}*/
function handlePost($conn) 
{
    $input = json_decode(file_get_contents("php://input"), true);
    $result = createSubject($conn, $input['name']);
    
    /*
    stmt es una variable que contiene la consulta preparada
    $stmt = $conn->prepare("SELECT id FROM subjects WHERE LOWER(name) = LOWER(?)");  // Verificar si ya existe una materia con ese nombre 
    //la funcion lower convierte el nombre a minusculas para evitar problemas de mayusculas y minusculas
    $stmt->bind_param("s", $nombre); // ayuda a evitar inyecciones SQL ya que solo se permite un string como parametro
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) //encontro una coincidencia
    {
        http_response_code(400); 
        echo json_encode(["error" => "Ya existe una materia con ese nombre"]);
        return;
    }*/


    if ($result['inserted'] > 0) 
    {
        echo json_encode(["message" => "Materia creada correctamente"]);
    } 
    else 
    {
        http_response_code(500);
        echo json_encode(["error" => "No se pudo crear"]);
    }
}

function handlePut($conn) 
{
     $input = json_decode(file_get_contents("php://input"), true);

    $result = updateSubject($conn, $input['id'], $input['name']);
    if ($result['updated'] > 0) 
    {
        echo json_encode(["message" => "Materia actualizada correctamente"]);
    } 
    else 
    {
        http_response_code(500);
        echo json_encode(["error" => "No se pudo actualizar"]);
    }
}

function handleDelete($conn) 
{
    $input = json_decode(file_get_contents("php://input"), true);  
    $result = deleteSubject($conn, $input['id']);
    if ($result['deleted'] > 0) 
    {
        echo json_encode(["message" => "Materia eliminada correctamente"]);
    } 
    else 
    {
        http_response_code(500);
        echo json_encode(["error" => "No se pudo eliminar"]);
    }
}
?>