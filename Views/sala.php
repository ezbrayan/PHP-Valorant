<?php
require_once '../Config/validar_sesion.php';
require_once '../Config/conexion.php';

try {
    // Conexión a la base de datos
    $dbClass = new Database();
    $db = $dbClass->conectar();

    // Obtener el ID del mapa
    if (isset($_POST['id_mapa'])) {
        $id_mapa = $_POST['id_mapa'];

        // Consulta SQL para obtener información de los jugadores en la sala
        $sql_info_sala = "SELECT mapa.*, usuarios.id_usuario, usuarios.nombre AS nombre_jugador, agentes.foto AS foto_agente, rango.nombre AS nombre_rango
                          FROM mapa
                          LEFT JOIN usuarios ON mapa.jugador1_id = usuarios.id_usuario OR
                                               mapa.jugador2_id = usuarios.id_usuario OR
                                               mapa.jugador3_id = usuarios.id_usuario OR
                                               mapa.jugador4_id = usuarios.id_usuario OR
                                               mapa.jugador5_id = usuarios.id_usuario
                          LEFT JOIN agentes ON usuarios.id_agente = agentes.id_agente
                          LEFT JOIN rango ON usuarios.id_rango = rango.id_rango
                          WHERE id_mapa = :id_mapa";
        $stmt = $db->prepare($sql_info_sala);
        $stmt->bindParam(':id_mapa', $id_mapa, PDO::PARAM_INT);
        $stmt->execute();
        $jugadores = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Verificar si se encontraron jugadores en la sala
        if ($jugadores) {
            // Obtener el índice del jugador activo
            $indice_activo = array_search($_SESSION['jugador']['id_usuario'], array_column($jugadores, 'id_usuario'));
            // Obtener la cantidad total de jugadores
            $total_jugadores = count($jugadores);

            // Calcular el índice medio de los jugadores
            $indice_medio = floor($total_jugadores / 2);

            // Calcular el índice del jugador activo en el medio
            $indice_en_medio = $indice_activo - $indice_medio;

            // Si el índice en medio es negativo, ajustarlo al principio de la lista
            if ($indice_en_medio < 0) {
                $jugadores_en_medio = array_slice($jugadores, 0, $total_jugadores + $indice_en_medio);
                $jugadores_al_final = array_slice($jugadores, $total_jugadores + $indice_en_medio);
                $jugadores_ordenados = array_merge($jugadores_al_final, $jugadores_en_medio);
            } else {
                // Obtener los jugadores al principio y al final
                $jugadores_al_principio = array_slice($jugadores, 0, $indice_en_medio);
                $jugadores_en_medio = array_slice($jugadores, $indice_en_medio, $total_jugadores - $indice_en_medio);
                $jugadores_ordenados = array_merge($jugadores_en_medio, $jugadores_al_principio);
            }

            // Mostrar información de los jugadores
            echo "<div class='contenedor'>";
            foreach ($jugadores_ordenados as $jugador) {
                echo "<div class='jugador'>";
                echo "<h2>{$jugador['nombre_jugador']}</h2>";
                if ($jugador['id_usuario'] === $_SESSION['jugador']['id_usuario']) {
                    echo "<img src='data:image/jpeg;base64," . base64_encode($jugador['foto_agente']) . "' alt='{$jugador['nombre_jugador']}' class='imagen-jugador-activo'>";
                } else {
                    echo "<img src='data:image/jpeg;base64," . base64_encode($jugador['foto_agente']) . "' alt='{$jugador['nombre_jugador']}' class='imagen-jugador'>";
                }
                echo "<p>Rango: {$jugador['nombre_rango']}</p>";
                echo "</div>";
            }
            echo "</div>";

            // Botón para combatir
            echo "<button id='btnCombatir'>Combatir</button>";

            // Formulario para seleccionar jugador a atacar (oculto inicialmente)
            echo "<form id='formCombatir' action='combatir.php' method='post' style='display:none;'>";
            echo "<select name='id_atacado'>";
            foreach ($jugadores as $jugador) {
                if ($jugador['id_usuario'] !== $_SESSION['jugador']['id_usuario']) {
                    echo "<option value='{$jugador['id_usuario']}'>{$jugador['nombre_jugador']}</option>";
                }
            }
            echo "</select>";
            echo "<input type='hidden' name='id_atacante' value='{$_SESSION['jugador']['id_usuario']}'>";
            echo "<input type='hidden' name='id_mapa' value='$id_mapa'>"; // Agregado el campo oculto para el id_mapa
            echo "<button type='submit'>Atacar</button>";
            echo "</form>";
        } else {
            echo "No hay jugadores en la sala.";
        }
    } else {
        echo "Error: No se recibió el ID del mapa.";
    }
} catch (PDOException $e) {
    echo "Error al cargar la sala: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jugadores en la sala</title>
    <style>
        .contenedor {
            display: flex;
            justify-content: space-evenly;
            align-items: center;
        }
        .jugador {
            width: 200px;
            margin: 10px;
            text-align: center;
        }
        .imagen-jugador {
            width: 100%;
            height: auto;
        }
        .imagen-jugador-activo {
            width: 110%;
            height: auto;
            margin-left: -10p;
        }
    </style>
</head>
<body>
    <!-- Aquí puedes incluir tu estructura HTML para mostrar la información de la sala -->

    <script>
        document.getElementById('btnCombatir').addEventListener('click', function() {
            // Mostrar el formulario de combate
            document.getElementById('formCombatir').style.display = 'block';
        });
    </script>
</body>
</html>
