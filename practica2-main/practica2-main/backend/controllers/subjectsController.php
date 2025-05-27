<?php
require_once("./models/subjects.php");

function handleGet($conn) 
{
    if (isset($_GET['id'])) 
    {
        $result = getSubjectById($conn, $_GET['id']);
        echo json_encode($result->fetch_assoc());
    } 
    else 
    {
        $result = getAllSubjects($conn);
        $data = [];
        while ($row = $result->fetch_assoc()) 
        {
            $data[] = $row;
        }
        echo json_encode($data);
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
    $nombre = $input['name'];
    //stmt es una variable que contiene la consulta preparada
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
    }


    if (createSubject($conn, $nombre)) // si no existe, crearla
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
    if (updateSubject($conn, $input['id'], $input['name'])) 
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
    if (deleteSubject($conn, $input['id'])) 
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