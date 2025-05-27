<?php
require_once("./models/studentsSubjects.php");

function handleGet($conn) 
{
    $result = getAllSubjectsStudents($conn);
    $data = [];
    while ($row = $result->fetch_assoc()) 
    {
        $data[] = $row;
    }
    echo json_encode($data);
}

function handlePost($conn) 
{
    $input = json_decode(file_get_contents("php://input"), true);

    $studentId = $input['student_id'];
    $subjectId = $input['subject_id'];
    $approved = $input['approved'];

    //verificar si ya existe esa relación
    $stmt = $conn->prepare("SELECT 1 FROM student_subject WHERE student_id = ? AND subject_id = ?");
    $stmt->bind_param("ii", $studentId, $subjectId); //asociamos los parametros a la consulta preparada
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) 
    {
        http_response_code(400);
        echo json_encode(["error" => "La relación entre el estudiante y la materia ya existe"]);
        return;
    }
    
    if (assignSubjectToStudent($conn, $input['student_id'], $input['subject_id'], $input['approved'])) 
    {
        echo json_encode(["message" => "Asignación realizada"]);
    } 
    else 
    {
        http_response_code(500);
        echo json_encode(["error" => "Error al asignar"]);
    }
}

function handlePut($conn) 
{
    $input = json_decode(file_get_contents("php://input"), true);

    if (!isset($input['id'], $input['student_id'], $input['subject_id'], $input['approved'])) 
    {
        http_response_code(400);
        echo json_encode(["error" => "Datos incompletos"]);
        return;
    }

    if (updateStudentSubject($conn, $input['id'], $input['student_id'], $input['subject_id'], $input['approved'])) 
    {
        echo json_encode(["message" => "Actualización correcta"]);
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
    if (removeStudentSubject($conn, $input['id'])) 
    {
        echo json_encode(["message" => "Relación eliminada"]);
    } 
    else 
    {
        http_response_code(500);
        echo json_encode(["error" => "No se pudo eliminar"]);
    }
}
?>
