<?php
session_start();

// Verificar si la sesión está activa
if (!isset($_SESSION['jugador'])) {
    header("Location: ../../../../valorant/login.php");
    exit;
}

// Obtener el ID del usuario en sesión
$id_usuario = $_SESSION['jugador']['id_usuario'];

// Verificar si se recibió el ID del jugador por POST
$id_jugador = isset($_POST['id_usuario']) ? $_POST['id_usuario'] : $id_usuario;

// Incluir el archivo de conexión a la base de datos
require_once '../Config/conexion.php';

// Consulta SQL para obtener los datos de la tabla detalles_usuarios
$sql = "SELECT du.id_detalle, du.daño_realizado, usu.nombre AS jugador_atacante, 
               jug.nombre AS jugador_atacado, du.fecha, arma.nombre AS nombre_arma, 
               mapa.nombre AS nombre_mapa
        FROM detalles_usuarios du
        LEFT JOIN usuarios usu ON du.id_jugador_atacante = usu.id_usuario
        LEFT JOIN usuarios jug ON du.id_jugador_atacado = jug.id_usuario
        LEFT JOIN armas arma ON du.id_arma = arma.id_arma
        LEFT JOIN mapa mapa ON du.id_mapa = mapa.id_mapa
        WHERE du.id_jugador_atacante = :id_jugador";

try {
    // Crear una instancia de la clase Database
    $dbClass = new Database();
    $db = $dbClass->conectar();

    // Preparar la consulta
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':id_jugador', $id_jugador, PDO::PARAM_INT);

    // Ejecutar la consulta
    $stmt->execute();

    // Obtener los resultados de la consulta
    $estadisticas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo 'Error al obtener las estadísticas: ' . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estadísticas</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h1>Estadísticas</h1>
    <table>
        <thead>
            <tr>
                <th>ID Detalle</th>
                <th>Daño Realizado</th>
                <th>Jugador Atacante</th>
                <th>Jugador Atacado</th>
                <th>Fecha</th>
                <th>Arma</th>
                <th>Mapa</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($estadisticas as $estadistica): ?>
                <tr>
                    <td><?= $estadistica['id_detalle'] ?></td>
                    <td><?= $estadistica['daño_realizado'] ?></td>
                    <td><?= $estadistica['jugador_atacante'] ?></td>
                    <td><?= $estadistica['jugador_atacado'] ?></td>
                    <td><?= $estadistica['fecha'] ?></td>
                    <td><?= $estadistica['nombre_arma'] ?></td>
                    <td><?= $estadistica['nombre_mapa'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
