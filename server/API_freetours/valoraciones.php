<?php
include 'db.php';

// Obtener valoraciones por ruta o usuario
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['ruta_id'])) {
        $ruta_id = $_GET['ruta_id'];
        $result = $conn->query("SELECT * FROM valoraciones WHERE ruta_id = $ruta_id");
    } elseif (isset($_GET['user_id'])) {
        $user_id = $_GET['user_id'];
        $result = $conn->query("SELECT * FROM valoraciones WHERE user_id = $user_id");
    } else {
        $result = $conn->query("SELECT * FROM valoraciones");
    }

    $valoraciones = [];
    while ($row = $result->fetch_assoc()) {
        $valoraciones[] = $row;
    }
    echo json_encode($valoraciones);
}

// Crear una nueva valoración
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $user_id = $data['user_id'];
    $ruta_id = $data['ruta_id'];
    $estrellas = $data['estrellas'];
    $comentario = $data['comentario'];

    $query = "INSERT INTO valoraciones (user_id, ruta_id, estrellas, comentario) 
              VALUES ($user_id, $ruta_id, $estrellas, '$comentario')";
    if ($conn->query($query)) {
        echo json_encode(['status' => 'success', 'message' => 'Valoración creada']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al crear valoración']);
    }
}

// Actualizar una valoración existente
if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    $id = $_GET['id'];
    $data = json_decode(file_get_contents('php://input'), true);
    $estrellas = $data['estrellas'];
    $comentario = $data['comentario'];

    $query = "UPDATE valoraciones SET estrellas = $estrellas, comentario = '$comentario' WHERE id = $id";
    if ($conn->query($query)) {
        echo json_encode(['status' => 'success', 'message' => 'Valoración actualizada']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al actualizar valoración']);
    }
}

// Eliminar una valoración
if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    $id = $_GET['id'];
    $query = "DELETE FROM valoraciones WHERE id = $id";
    if ($conn->query($query)) {
        echo json_encode(['status' => 'success', 'message' => 'Valoración eliminada']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al eliminar valoración']);
    }
}
?>
