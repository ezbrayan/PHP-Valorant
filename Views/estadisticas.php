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

// Consulta SQL para obtener los datos de la tabla detalles_usuarios donde el id_usuario coincide con id_jugador_atacante
$sql_atacante = "SELECT du.id_detalle, du.daño_realizado, 
               usu.nombre AS jugador_atacante, usu.id_agente AS id_agente_atacante,
               jug.nombre AS jugador_atacado, jug.id_agente AS id_agente_atacado,
               du.fecha, arma.foto AS foto_arma, 
               mapa.nombre AS nombre_mapa,
               mapa.foto AS foto_mapa, -- Agregamos la columna foto_mapa
               a_agente.foto AS foto_agente_atacante,
               a_agredido.foto AS foto_agente_atacado
        FROM detalles_usuarios du
        LEFT JOIN usuarios usu ON du.id_jugador_atacante = usu.id_usuario
        LEFT JOIN usuarios jug ON du.id_jugador_atacado = jug.id_usuario
        LEFT JOIN armas arma ON du.id_arma = arma.id_arma
        LEFT JOIN mapa mapa ON du.id_mapa = mapa.id_mapa
        LEFT JOIN agentes a_agente ON usu.id_agente = a_agente.id_agente
        LEFT JOIN agentes a_agredido ON jug.id_agente = a_agredido.id_agente
        WHERE du.id_jugador_atacante = :id_jugador";

// Consulta SQL para obtener los datos de la tabla detalles_usuarios donde el id_usuario coincide con id_jugador_atacado
$sql_atacado = "SELECT du.id_detalle, du.daño_realizado, 
               usu.nombre AS jugador_atacante, usu.id_agente AS id_agente_atacante,
               jug.nombre AS jugador_atacado, jug.id_agente AS id_agente_atacado,
               du.fecha, arma.foto AS foto_arma, 
               mapa.nombre AS nombre_mapa,
               mapa.foto AS foto_mapa, -- Agregamos la columna foto_mapa
               a_agente.foto AS foto_agente_atacante,
               a_agredido.foto AS foto_agente_atacado
        FROM detalles_usuarios du
        LEFT JOIN usuarios usu ON du.id_jugador_atacante = usu.id_usuario
        LEFT JOIN usuarios jug ON du.id_jugador_atacado = jug.id_usuario
        LEFT JOIN armas arma ON du.id_arma = arma.id_arma
        LEFT JOIN mapa mapa ON du.id_mapa = mapa.id_mapa
        LEFT JOIN agentes a_agente ON usu.id_agente = a_agente.id_agente
        LEFT JOIN agentes a_agredido ON jug.id_agente = a_agredido.id_agente
        WHERE du.id_jugador_atacado = :id_jugador";

try {
    // Crear una instancia de la clase Database
    $dbClass = new Database();
    $db = $dbClass->conectar();

    // Preparar la consulta para los datos donde el id_usuario coincide con id_jugador_atacante
    $stmt_atacante = $db->prepare($sql_atacante);
    $stmt_atacante->bindParam(':id_jugador', $id_jugador, PDO::PARAM_INT);
    $stmt_atacante->execute();
    $estadisticas_atacante = $stmt_atacante->fetchAll(PDO::FETCH_ASSOC);

    // Preparar la consulta para los datos donde el id_usuario coincide con id_jugador_atacado
    $stmt_atacado = $db->prepare($sql_atacado);
    $stmt_atacado->bindParam(':id_jugador', $id_jugador, PDO::PARAM_INT);
    $stmt_atacado->execute();
    $estadisticas_atacado = $stmt_atacado->fetchAll(PDO::FETCH_ASSOC);

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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(45deg, #1a1a1a, #2a2a2a); /* Fondo estilo humo morado con negro */
            color: #fff;
            margin: 0;
            padding: 0;
        }
        h1 {
            text-align: center;
            font-size: 36px;
            padding-top: 30px;
            color: #bf94e4; /* Morado estilo Valorant */
        }
        table {
            width: 80%;
            margin: 0 auto; /* Centramos la tabla */
            border-collapse: collapse;
            border: 2px solid #bf94e4; /* Morado estilo Valorant */
            text-align: center; /* Centramos el contenido de la tabla */
            margin-bottom: 30px; /* Agregamos un margen inferior */
        }
        th, td {
            border: 1px solid #bf94e4; /* Morado estilo Valorant */
            padding: 10px;
            text-align: center; /* Centramos el contenido de las celdas */
        }
        th {
            background-color: #bf94e4; /* Morado estilo Valorant */
        }
        tr:nth-child(even) {
            background-color: #382e4d; /* Morado oscuro estilo Valorant */
        }
        tr:hover {
            background-color: #574b6d; /* Morado más claro estilo Valorant */
        }
        .img-small {
            width: 50%;
            height: auto;
            border-radius: 50%;
            margin-right: 10px;
        }
        .img-large {
            width: 70%;
            height: auto;
            border-radius: 10px;
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <h1>Estadísticas de mis ataques</h1>
    
    <!-- Tabla de ataques donde el usuario es el atacante -->
    <table>
        <thead>
            <tr>
             
                <th>Daño Realizado</th>
                <th>Jugador Atacante</th>
                <th>Jugador Atacado</th>
                <th>Fecha</th>
                <th>Arma</th>
                <th>Mapa</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($estadisticas_atacante as $estadistica): ?>
                <tr>
                  
                    <td><?= $estadistica['daño_realizado'] .'%' ?></td>
                    <td><img src="data:image/jpeg;base64,<?= base64_encode($estadistica['foto_agente_atacante']) ?>" alt="<?= $estadistica['jugador_atacante'] ?>" class="img-small"><?= $estadistica['jugador_atacante'] ?></td>
                    <td><img src="data:image/jpeg;base64,<?= base64_encode($estadistica['foto_agente_atacado']) ?>" alt="<?= $estadistica['jugador_atacado'] ?>" class="img-small"><?= $estadistica['jugador_atacado'] ?></td>
                    <td><?= $estadistica['fecha'] ?></td>
                    <td><img src="data:image/jpeg;base64,<?= base64_encode($estadistica['foto_arma']) ?>" class="img-large"></td>
                    <td><img src="data:image/jpeg;base64,<?= base64_encode($estadistica['foto_mapa']) ?>" class="img-large"></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <!-- Fin de la tabla de ataques donde el usuario es el atacante -->

    <h1>Estadísticas encontra de mi</h1>
    
    <!-- Tabla de ataques donde el usuario es el atacado -->
    <table>
        <thead>
            <tr>
               
                <th>Daño Realizado</th>
                <th>Jugador Atacante</th>
                <th>Jugador Atacado</th>
                <th>Fecha</th>
                <th>Arma</th>
                <th>Mapa</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($estadisticas_atacado as $estadistica): ?>
                <tr>
                    
                    <td><?= $estadistica['daño_realizado'].'%' ?></td>
                    <td><img src="data:image/jpeg;base64,<?= base64_encode($estadistica['foto_agente_atacante']) ?>" alt="<?= $estadistica['jugador_atacante'] ?>" class="img-small"><?= $estadistica['jugador_atacante'] ?></td>
                    <td><img src="data:image/jpeg;base64,<?= base64_encode($estadistica['foto_agente_atacado']) ?>" alt="<?= $estadistica['jugador_atacado'] ?>" class="img-small"><?= $estadistica['jugador_atacado'] ?></td>
                    <td><?= $estadistica['fecha'] ?></td>
                    <td><img src="data:image/jpeg;base64,<?= base64_encode($estadistica['foto_arma']) ?>" class="img-large"></td>
                    <td><img src="data:image/jpeg;base64,<?= base64_encode($estadistica['foto_mapa']) ?>" class="img-large"></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <!-- Fin de la tabla de ataques donde el usuario es el atacado -->

    <a href="index.php" class="btn btn-warning mb-3">Volver</a>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
