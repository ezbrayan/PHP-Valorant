<?php
// Incluir el archivo de conexión a la base de datos
require_once '../Config/conexion.php';

// Crear una instancia de la clase Database
$dbClass = new Database();
$db = $dbClass->conectar();

// Obtener el ID del usuario que quiere unirse a un mapa
if (isset($_POST['id_usuario'])) {
    $id_usuario = $_POST['id_usuario'];

    // Consulta SQL para obtener el rango del usuario
    $sql_rango_usuario = "SELECT id_rango FROM usuarios WHERE id_usuario = :id_usuario";
    $stmt = $db->prepare($sql_rango_usuario);
    $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
    $stmt->execute();
    $rango_usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    // Consulta SQL para obtener los mapas con el mismo rango del usuario
    $sql_mapas = "SELECT id_mapa, nombre, foto, jugador1_id, jugador2_id, jugador3_id, jugador4_id, jugador5_id FROM mapa WHERE id_rango = :id_rango";
    $stmt = $db->prepare($sql_mapas);
    $stmt->bindParam(':id_rango', $rango_usuario['id_rango'], PDO::PARAM_INT);
    $stmt->execute();
    $mapas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Consulta SQL para obtener los mapas a los que el usuario ya está unido
    $sql_mapas_unidos = "
        SELECT id_mapa
        FROM (
            SELECT id_mapa, jugador1_id AS jugador_id FROM mapa
            UNION
            SELECT id_mapa, jugador2_id AS jugador_id FROM mapa
            UNION
            SELECT id_mapa, jugador3_id AS jugador_id FROM mapa
            UNION
            SELECT id_mapa, jugador4_id AS jugador_id FROM mapa
            UNION
            SELECT id_mapa, jugador5_id AS jugador_id FROM mapa
        ) AS mapas_unidos
        WHERE jugador_id = :id_usuario";
    $stmt = $db->prepare($sql_mapas_unidos);
    $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
    $stmt->execute();
    $mapas_unidos = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Verificar si se presionó el botón de unirse al mapa
    if (isset($_POST['id_mapa'])) {
        $id_mapa = $_POST['id_mapa'];

        // Verificar si el usuario ya está unido al mapa
        if (in_array($id_mapa, $mapas_unidos)) {
            echo "Ya estás unido a este mapa.";
        } else {
            // Consulta SQL para verificar si hay espacio disponible en el mapa
            $sql_verificar_espacio = "SELECT jugador1_id, jugador2_id, jugador3_id, jugador4_id, jugador5_id FROM mapa WHERE id_mapa = :id_mapa";
            $stmt = $db->prepare($sql_verificar_espacio);
            $stmt->bindParam(':id_mapa', $id_mapa, PDO::PARAM_INT);
            $stmt->execute();
            $mapa = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verificar si hay espacio en el mapa
            $campo_jugador = null;
            foreach ($mapa as $key => $value) {
                if (is_null($value)) {
                    $campo_jugador = $key;
                    break;
                }
            }

            if (is_null($campo_jugador)) {
                echo "Lo siento, la sala está llena. Por favor, prueba con otra.";
            } else {
                // Actualizar el campo correspondiente con el ID del usuario
                $sql_unirse_mapa = "UPDATE mapa SET $campo_jugador = :id_usuario WHERE id_mapa = :id_mapa";
                $stmt = $db->prepare($sql_unirse_mapa);
                $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
                $stmt->bindParam(':id_mapa', $id_mapa, PDO::PARAM_INT);
                $stmt->execute();

                echo "Te has unido al mapa exitosamente.";
            }
        }
    }
} else {
    echo "Error: No se recibió el ID del usuario.";
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mapas Valorant</title>
</head>
<body>
    <h1>Mapas Disponibles</h1>
    <?php foreach ($mapas as $mapa): ?>
        <div>
            <h2><?= $mapa['nombre'] ?></h2>
            <img src="data:image/jpeg;base64,<?= base64_encode($mapa['foto']) ?>" alt="Foto del mapa"><br>
            
            <!-- Mostrar el número de jugadores unidos -->
            <p>Número de jugadores unidos: <?= count(array_filter($mapa)) - 3 ?>/5</p>
            
            <!-- Mostrar el botón "Unirse" o el mensaje "Ya estás unido a este mapa" según corresponda -->
            <?php if (in_array($mapa['id_mapa'], $mapas_unidos)): ?>
                <form action="sala.php" method="post">
                    <input type="hidden" name="id_usuario" value="<?= $id_usuario ?>">
                    <input type="hidden" name="id_mapa" value="<?= $mapa['id_mapa'] ?>">
                    <button type="submit">Ingresar a la sala</button>
                </form>
                <form action="abandonar_mapa.php" method="post">
                    <input type="hidden" name="id_usuario" value="<?= $id_usuario ?>">
                    <input type="hidden" name="id_mapa" value="<?= $mapa['id_mapa'] ?>">
                    <button type="submit">Abandonar mapa</button>
                </form>
            <?php else: ?>
                <form action="" method="post">
                    <input type="hidden" name="id_usuario" value="<?= $id_usuario ?>">
                    <input type="hidden" name="id_mapa" value="<?= $mapa['id_mapa'] ?>">
                    <button type="submit">Unirse al mapa</button>
                </form>
            <?php endif; ?>
            <hr>
        </div>
    <?php endforeach; ?>
</body>
</html>
