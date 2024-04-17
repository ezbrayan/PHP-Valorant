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
        $sql_info_sala = "SELECT mapa.*, usuarios.id_usuario, usuarios.nombre AS nombre_jugador, usuarios.puntos_salud, usuarios.id_rango, agentes.tarjeta AS tarjeta_agente, rango.nombre AS nombre_rango
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
            // Iterar sobre los jugadores para verificar el rango
            foreach ($jugadores as $jugador) {
                // Verificar si el jugador tiene un rango diferente al del mapa
                if ($jugador['id_rango'] !== $jugadores[0]['id_rango'] or $jugador['puntos_salud'] <= 0) {
                    // Iterar sobre los campos de jugador para encontrar el campo con el ID del jugador
                    foreach (['jugador1_id', 'jugador2_id', 'jugador3_id', 'jugador4_id', 'jugador5_id'] as $campo_jugador) {
                        // Si el ID del jugador está en el campo actual, actualizarlo a NULL
                        if ($jugador['id_usuario'] == $jugador[$campo_jugador]) {
                            $sql_actualizar_campo = "UPDATE mapa SET $campo_jugador = NULL WHERE id_mapa = :id_mapa";
                            $stmt_actualizar = $db->prepare($sql_actualizar_campo);
                            $stmt_actualizar->bindParam(':id_mapa', $id_mapa, PDO::PARAM_INT);
                            $stmt_actualizar->execute();
                            break; // Detener la iteración sobre los campos de jugador
                        }
                    }
                }
            }

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

            echo "<div class='contenedor'>";

            // Calculamos la cantidad total de jugadores
            $total_jugadores = count($jugadores);
            
            // Si no hay jugadores, mostramos solo al jugador activo
            if ($total_jugadores === 0) {
                echo "<div class='jugador'>";
                echo "<img src='data:image/jpeg;base64," . base64_encode($_SESSION['jugador']['tarjeta_agente']) . "' alt='{$_SESSION['jugador']['nombre_jugador']}' class='imagen-jugador-activo'>";
                echo "<p>Rango: {$_SESSION['jugador']['nombre_rango']}</p>";
                echo "</div>";
            } else {
                // Mostramos los jugadores disponibles
                foreach ($jugadores_ordenados as $jugador) {
                    echo "<div class='jugador'>";
                    echo "<h2>{$jugador['nombre_jugador']}</h2>";
                    if ($jugador['id_usuario'] === $_SESSION['jugador']['id_usuario']) {
                        echo "<img src='data:image/jpeg;base64," . base64_encode($jugador['tarjeta_agente']) . "' alt='{$jugador['nombre_jugador']}' class='imagen-jugador-activo'>";
                    } else {
                        echo "<img src='data:image/jpeg;base64," . base64_encode($jugador['tarjeta_agente']) . "' alt='{$jugador['nombre_jugador']}' class='imagen-jugador'>";
                    }
                    echo "<p>Rango: {$jugador['nombre_rango']}</p>";
                    echo "</div>";
                }
            
                // Mostramos los espacios vacíos al final si es necesario
                $espacios_vacios = 5 - $total_jugadores;
                for ($i = 0; $i < $espacios_vacios; $i++) {
                    echo "<div class='jugador'>";
                    echo "<div style='background-color: red; width: 100px; height: 150px;'></div>"; // Espacio vacío con fondo rojo
                    echo "</div>";
                }
            }
            
            echo "</div>";
            
            

            // Verificar la salud del jugador atacante
            $id_atacante = $_SESSION['jugador']['id_usuario'];
            $sql_salud_atacante = "SELECT puntos_salud FROM usuarios WHERE id_usuario = :id_atacante";
            $stmt_salud = $db->prepare($sql_salud_atacante);
            $stmt_salud->bindParam(':id_atacante', $id_atacante, PDO::PARAM_INT);
            $stmt_salud->execute();
            $salud_atacante = $stmt_salud->fetchColumn();

            // Imprimir mensaje si el jugador atacante tiene 0 de salud
            if ($salud_atacante <= 0) {
                echo "<div id='mensajeSalud'><script>document.getElementById('mensajeSalud').innerText = 'Cuentas con 0% de salud. Por favor, abandona el mapa y restaura tus puntos de salud.';</script></div>";
            } else {
                // Botón para combatir
                echo "<div id='btnCombatirDiv'><button id='btnCombatir'>Combatir</button></div>";

                // Formulario para seleccionar jugador a atacar (oculto inicialmente)
                echo "<form id='formCombatir' action='combatir.php' method='post' style='display:none;'>";
                echo "<select name='id_atacado'>";
                foreach ($jugadores as $jugador) {
                    if ($jugador['id_usuario'] !== $_SESSION['jugador']['id_usuario']) {
                        echo "<option value='{$jugador['id_usuario']}' data-puntos-salud='{$jugador['puntos_salud']}'>{$jugador['nombre_jugador']}</option>";
                    }
                }
                echo "</select>";
                echo "<input type='hidden' name='id_atacante' value='{$_SESSION['jugador']['id_usuario']}'>";
                echo "<input type='hidden' name='id_mapa' value='$id_mapa'>"; // Agregado el campo oculto para el id_mapa
                echo "<button type='submit'>Atacar</button>";
                echo "</form>";
            }
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

        // Verificar la salud del usuario atacado al enviar el formulario
        document.getElementById('formCombatir').addEventListener('submit', function(event) {
            var selectedOption = this.querySelector('select[name="id_atacado"] option:checked');
            var puntosSaludAtacado = parseInt(selectedOption.getAttribute('data-puntos-salud'));
            var puntosSaludAtacante = <?php echo $salud_atacante; ?>;
            if (puntosSaludAtacado <= 0) {
                event.preventDefault(); // Evitar enviar el formulario
                alert('El jugador seleccionado tiene 0% de salud. Por favor, selecciona otro jugador.');
            }
            if (puntosSaludAtacante <= 0) {
                event.preventDefault(); // Evitar enviar el formulario
                alert('Cuentas con 0% de salud. Por favor, abandona el mapa y restaura tus puntos de salud.');
            }
        });
        
    </script>
</body>

</html>