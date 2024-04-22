<?php


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
    <!-- Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/nav.css">
    <style>
        body {
            margin: 0;
            padding: 0;
        }

        #sidebar {
            position: fixed;
            top: 0;
            right: -250px;
            width: 250px;
            height: 100%;
            background: gray;
            transition: all 0.3s;
            margin-top: 60px;
        }

        #sidebar.active {
            right: 0;
            /* Cambia a la derecha */
        }

        #sidebar ul.components {
            padding: 20px 0;
        }

        #sidebar ul li {
            padding: 10px;
            font-size: 1.2em;
            color: white;
        }

        #sidebar ul li:hover {
            background: #555;
        }

        /* Estilo para la imagen pequeña del agente en el menú lateral */
        #sidebar .agent-image {
            width: 50px;
            height: 50px;

            margin-bottom: 10px;
            border-radius: 0;
            /* Cambia la forma de la imagen a cuadrada */
        }

        /* Estilo para la imagen del agente en la parte superior derecha */
        #agent-icon {
            padding: 5px;
            position: fixed;
            top: 70px;
            right: 0;
            width: 70px;
            height: 100%;
            background-color: #555;
            cursor: pointer;
            /* Asegura que esté sobre el contenido */
        }

        #agent-icon img {
            width: 100%;
            height: auto;
        }
    </style>

</head>

<body>
    <div id="agent-icon">
        <img src="data:image/jpeg;base64,<?= base64_encode($usuario['foto_agente']) ?>" alt="Foto del agente"
            class="user" id="agente">
    </div>
    <!-- Menú lateral -->
    <nav id="sidebar">
        <div class="sidebar-header">
            <img src="data:image/jpeg;base64,<?= base64_encode($usuario['foto_agente']) ?>" alt="Foto del agente"
                class="img-fluid rounded-circle agent-image">
        </div>
        <ul class="list-unstyled components">
            <li>
                Nombre: <?= $usuario['nombre_usuario'] ?>
            </li>
            <li>
                Puntos de Rango: <?= $usuario['puntos_rango'] ?>
                <img src="data:image/jpeg;base64,<?= base64_encode($usuario['foto_rango']) ?>" alt="Foto del agente"
                    class="img-fluid rounded-circle agent-image">
            </li>
            <li>
                Rango: <?= $usuario['nombre_rango'] ?>
            </li>
        </ul>
    </nav>

    <!-- Bootstrap JS y jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function () {
            // Función para desplegar/ocultar el menú lateral al hacer clic en la imagen del agente o en cualquier lado del contenedor #agent-icon
            $('#agent-icon, #sidebar').on('click', function (e) {
                $('#sidebar').toggleClass('active');
                // Ocultar la imagen del agente en la parte superior derecha cuando se despliega el menú
                $('#agente,#agent-icon').toggle();
                e.stopPropagation(); // Evita que el clic en el menú propague al cuerpo y cierre el menú
            });

            // Función para cerrar el menú lateral al hacer clic en cualquier zona fuera del menú
            $(document).on('click', function (e) {
                if (!$(e.target).closest('#sidebar').length && !$(e.target).is('#agente')) {
                    $('#sidebar').removeClass('active');
                    // Mostrar la imagen del agente en la parte superior derecha cuando se cierra el menú
                    $('#agente,#agent-icon').show();
                }
            });
        });
    </script>
</body>

</html>
