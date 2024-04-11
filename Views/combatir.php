<?php
require_once '../Config/validar_sesion.php';
require_once '../Config/conexion.php';

try {
    // Conexión a la base de datos
    $dbClass = new Database();
    $db = $dbClass->conectar();

    // Verificar si se han enviado los datos necesarios
    if (isset($_POST['id_mapa'], $_POST['id_atacante'], $_POST['id_atacado'])) {
        // Obtener los datos necesarios
        $id_mapa = $_POST['id_mapa'];
        $id_atacante = $_POST['id_atacante'];
        $id_atacado = $_POST['id_atacado'];

        // Obtener información del jugador atacante
        $sql_info_atacante = "SELECT u.nombre, a.foto AS foto_agente, r.nombre AS nombre_rango, r.id_rango FROM usuarios u
                              INNER JOIN agentes a ON u.id_agente = a.id_agente
                              INNER JOIN rango r ON u.id_rango = r.id_rango
                              WHERE u.id_usuario = :id_atacante";
        $stmt_atacante = $db->prepare($sql_info_atacante);
        $stmt_atacante->bindParam(':id_atacante', $id_atacante, PDO::PARAM_INT);
        $stmt_atacante->execute();
        $info_atacante = $stmt_atacante->fetch(PDO::FETCH_ASSOC);

        // Obtener información del jugador atacado
        $sql_info_atacado = "SELECT u.nombre, a.foto AS foto_agente, r.nombre AS nombre_rango, r.id_rango FROM usuarios u
                             INNER JOIN agentes a ON u.id_agente = a.id_agente
                             INNER JOIN rango r ON u.id_rango = r.id_rango
                             WHERE u.id_usuario = :id_atacado";
        $stmt_atacado = $db->prepare($sql_info_atacado);
        $stmt_atacado->bindParam(':id_atacado', $id_atacado, PDO::PARAM_INT);
        $stmt_atacado->execute();
        $info_atacado = $stmt_atacado->fetch(PDO::FETCH_ASSOC);

        // Obtener las armas del mismo rango que el jugador atacante
        $sql_armas = "SELECT id_arma, nombre, daño FROM armas WHERE id_rango = :id_rango";
        $stmt_armas = $db->prepare($sql_armas);
        $stmt_armas->bindParam(':id_rango', $info_atacante['id_rango'], PDO::PARAM_INT);
        $stmt_armas->execute();
        $armas = $stmt_armas->fetchAll(PDO::FETCH_ASSOC);
    } else {
        // Si no se reciben los datos necesarios, mostrar un mensaje de error
        echo "Error: Datos insuficientes para iniciar el combate.";
    }
} catch (PDOException $e) {
    echo "Error al cargar la página: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Combate</title>
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
            margin-left: -10px;
        }
    </style>
</head>
<body>
    <div class='contenedor'>
        <!-- Información del jugador atacante -->
        <div class='jugador'>
            <h2><?php echo $info_atacante['nombre']; ?></h2>
            <img src='data:image/jpeg;base64,<?php echo base64_encode($info_atacante['foto_agente']); ?>' alt='<?php echo $info_atacante['nombre']; ?>' class='imagen-jugador-activo'>
            <p>Rango: <?php echo $info_atacante['nombre_rango']; ?></p>
        </div>
        <!-- Información del jugador atacado -->
        <div class='jugador'>
            <h2><?php echo $info_atacado['nombre']; ?></h2>
            <img src='data:image/jpeg;base64,<?php echo base64_encode($info_atacado['foto_agente']); ?>' alt='<?php echo $info_atacado['nombre']; ?>' class='imagen-jugador'>
            <p>Rango: <?php echo $info_atacado['nombre_rango']; ?></p>
        </div>
    </div>

    <!-- Selector de arma -->
    <form id='formDisparar' action='procesar_combatir.php' method='post'>
        <label for="id_arma">Selecciona un arma:</label>
        <select name="id_arma" id="id_arma">
            <?php foreach ($armas as $arma) : ?>
                <option value='<?php echo $arma['id_arma']; ?>'><?php echo $arma['nombre']; ?> - Daño: <?php echo $arma['daño']; ?></option>
            <?php endforeach; ?>
        </select>
        <!-- Agregar campos ocultos para enviar los datos necesarios -->
        <input type="hidden" name="id_atacante" value="<?php echo $id_atacante; ?>">
        <input type="hidden" name="id_atacado" value="<?php echo $id_atacado; ?>">
        <input type="hidden" name="id_mapa" value="<?php echo $id_mapa; ?>">
        <input type="submit" value="Disparar">
    </form>
</body>
</html>
