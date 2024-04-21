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
               a.tarjeta AS tarjeta_agente, r.nombre AS nombre_rango, r.foto AS foto_rango
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

    // Actualizar el ID de rango según los puntos de rango
    $puntos_rango = $usuario['puntos_rango'];
    if ($puntos_rango > 2250) {
        $id_rango = 9;
    } elseif ($puntos_rango > 2000) {
        $id_rango = 8;
    } elseif ($puntos_rango > 1750) {
        $id_rango = 7;
    } elseif ($puntos_rango > 1500) {
        $id_rango = 6;
    } elseif ($puntos_rango > 1250) {
        $id_rango = 5;
    } elseif ($puntos_rango > 1000) {
        $id_rango = 4;
    } elseif ($puntos_rango > 750) {
        $id_rango = 3;
    } elseif ($puntos_rango > 500) {
        $id_rango = 2;
    } else {
        $id_rango = 1;
    }

    // Actualizar el ID de rango en la base de datos
    $sql_update = "UPDATE usuarios SET id_rango = :id_rango WHERE id_usuario = :id_usuario";
    $stmt_update = $db->prepare($sql_update);
    $stmt_update->bindParam(':id_rango', $id_rango, PDO::PARAM_INT);
    $stmt_update->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
    $stmt_update->execute();
} catch (PDOException $e) {
    echo 'Error al ejecutar la consulta: ' . $e->getMessage();
    exit;
}

// Botón "Restaurar Salud"
$boton_restaurar_salud = '';
if ($usuario['puntos_salud'] <= 0) {
    $boton_restaurar_salud = '<form action="restaurar_salud.php" method="post">
                                    <input type="hidden" name="id_usuario" value="' . $usuario['id_usuario'] . '">
                                    <button type="submit">Restaurar Salud</button>
                                </form>';
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
        
        <!-- Botón "Restaurar Salud" (Mostrado si puntos_salud <= 0) -->
        <?= $boton_restaurar_salud ?>
        
        <!-- Botón "Estadísticas" -->
        <form action="estadisticas.php" method="post">
            <input type="hidden" name="id_usuario" value="<?= $usuario['id_usuario'] ?>">
            <button type="submit">Estadísticas</button>
        </form>
        <hr>
    </div>
</body>
</html>
