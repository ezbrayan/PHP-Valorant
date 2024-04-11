<?php
session_start();

// Verificar si la sesión está activa
if (!isset($_SESSION['jugador'])) {
    header("Location: ../../../../valorant/login.php");
    exit;
}

// Incluir el archivo de conexión a la base de datos
require_once '../Config/conexion.php';

// Crear una instancia de la clase Database
$dbClass = new Database();
$db = $dbClass->conectar();

// Obtener el ID del usuario en sesión
$id_usuario = $_SESSION['jugador']['id_usuario'];

// Consulta SQL para obtener los datos del jugador en sesión
$sql = "SELECT u.id_usuario, u.nombre AS nombre_usuario, u.puntos_salud, u.puntos_rango, 
               u.ultima_conexion, a.nombre AS nombre_agente, a.foto AS foto_agente,
               r.nombre AS nombre_rango, r.foto AS foto_rango
        FROM usuarios u
        LEFT JOIN agentes a ON u.id_agente = a.id_agente
        LEFT JOIN rango r ON u.id_rango = r.id_rango
        WHERE u.id_usuario = :id_usuario";

try {
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
    $stmt->execute();
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificar si se encontró el jugador en sesión
    if (!$usuario) {
        echo "El jugador en sesión no fue encontrado en la base de datos.";
        exit;
    }
} catch (PDOException $e) {
    echo 'Error al ejecutar la consulta: ' . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Usuario</title>
</head>
<body>
    <h1>Perfil de Usuario</h1>
    <div>
        <h2><?= $usuario['nombre_usuario'] ?></h2>
        <p>Puntos de vida: <?= $usuario['puntos_salud'] ?></p>
        <p>Puntos de rango: <?= $usuario['puntos_rango'] ?></p>
        <p>Última conexión: <?= $usuario['ultima_conexion'] ?></p>
        <p>Rango: <?= $usuario['nombre_rango'] ?></p>
        <img src="data:image/jpeg;base64,<?= base64_encode($usuario['foto_rango']) ?>" alt="Foto del rango"><br>
        <p>Agente: <?= $usuario['nombre_agente'] ?></p>
        <img src="data:image/jpeg;base64,<?= base64_encode($usuario['foto_agente']) ?>" alt="Foto del agente"><br>
        <!-- Botón "Jugar" que redirige a la página de mapas -->
        <form action="mapas.php" method="post">
            <input type="hidden" name="id_usuario" value="<?= $usuario['id_usuario'] ?>">
            <button type="submit">Jugar</button>
        </form>
        <hr>
    </div>
</body>
</html>
