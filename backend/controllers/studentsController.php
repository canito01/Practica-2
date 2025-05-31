<?php

/*
    Los controladores definen funciones que se ejecutan 
    cuando el usuario, en este caso, hace una operacion
    sobre los estudiantes: ver, crear, modificar o eliminar.

    Se llama desde routesFactory.php, que es el despachador de rutas.
    Usa funciones del modelo students.php para interactuar con la base de datos.
*/
require_once("./models/students.php");

function handleGet($conn) {
{
    $input = json_decode(file_get_contents("php://input"), true);
    
    if (isset($input['id']))   //En get no suele usarse el cuerpo del envio, sino que se usa la URL, pero aca se nos permite para poder filtrar por id
    {
        $student = getStudentById($conn, $input['id']);
        echo json_encode($student);
    } 
    else
    {
        $students = getAllStudents($conn);
        echo json_encode($students);
    }
}

function handlePost($conn) 
{
    $input = json_decode(file_get_contents("php://input"), true);

    $result = createStudent($conn, $input['fullname'], $input['email'], $input['age']);
    if ($result['inserted'] > 0) //inserted es el numero de filas insertadas, si es mayor a 0, se inserto correctamente
    {
        echo json_encode(["message" => "Estudiante agregado correctamente"]);
    } 
    else 
    {
        http_response_code(500);
        echo json_encode(["error" => "No se pudo agregar"]);
    }
}

function handlePut($conn) 
{
    $input = json_decode(file_get_contents("php://input"), true);

    $result = updateStudent($conn, $input['id'], $input['fullname'], $input['email'], $input['age']);
    if ($result['updated'] > 0) 
    {
        echo json_encode(["message" => "Actualizado correctamente"]);
    } 
    else 
    {
        http_response_code(500);
        echo json_encode(["error" => "No se pudo actualizar"]);
    }
}

function handleDelete($conn) {
    $input = json_decode(file_get_contents("php://input"), true);

   /*
    $studentId = $input['student_id'];
    $stmt = $conn->prepare("SELECT 1 FROM student_subject WHERE student_id = ?");
    $stmt->bind_param("i", $studentId); 
    $stmt->execute();
    $result = $stmt->get_result();

    if( $result->num_rows > 0) 
    {
        http_response_code(400);
        echo json_encode(["error" => "No se puede eliminar el estudiante porque está asignado a por lo menos una materia"]);
        return;
    }
    // Si no hay materias asignadas, proceder a eliminar el estudiante   */

    $result = deleteStudent($conn, $input['id']);

     if ($result['deleted'] > 0) 
    {
        echo json_encode(["message" => "Eliminado correctamente"]);
    } 
    else 
    {
        http_response_code(500);
        echo json_encode(["error" => "No se pudo eliminar"]);
    }
}
?>