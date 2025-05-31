<?php
//archivo que maneja, a partir de comando sql, la tabla students, agregar, eliminar, consultar y modificar

function getAllStudents($conn) {
    $sql = "SELECT * FROM students";
    return $conn->query($sql)->fetch_all(MYSQLI_ASSOC); //fetch all devuelve un array asociativo con todos los resultados
} //por que antes no se usaba el fetch_all y se usaba el fetch_assoc? porque antes se usaba un while para recorrer los resultados, ahora se usa fetch_all para obtener todos los resultados de una sola vez y devolverlos como un array asociativo

function getStudentById($conn, $id) {
    $stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    //fetch_assoc() devuelve un array asociativo ya listo para convertir en JSON de una fila:
    return $result->fetch_assoc(); //ideal cuando se busca por id, ya que se espera un solo resultado
}  

function createStudent($conn, $fullname, $email, $age) {
    $sql = "INSERT INTO students (fullname, email, age) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $fullname, $email, $age);
    $stmt->execute();

    //Se retorna un arreglo con la cantidad e filas insertadas 
    //y id insertado para validar en el controlador:
    return 
    [
        'inserted' => $stmt->affected_rows,        
        'id' => $conn->insert_id
    ];
}

function updateStudent($conn, $id, $fullname, $email, $age) {
    $sql = "UPDATE students SET fullname = ?, email = ?, age = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssii", $fullname, $email, $age, $id);
    $stmt->execute();
    return  ['updated' => $stmt->affected_rows];
    /*
        Devuelve cuantas filas se actualizaron, si es 0, no se actualizo nada
        el estudiante no existe o los datos son iguales a los que ya estaban
    */
}

function deleteStudent($conn, $id) {
    $sql = "DELETE FROM students WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();

    //Se retorna fila afectadas para validar en controlador
    return ['deleted' => $stmt->affected_rows];
}
?>