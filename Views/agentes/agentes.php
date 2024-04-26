<?php
session_start();

// Verificar si la sesión está activa
if (!isset($_SESSION['jugador'])) {
    header("Location: ../../../../valorant/login.php");
    exit;
}

// Incluir el archivo de conexión a la base de datos
require_once '../../Config/conexion.php';

// Crear una instancia de la clase Database
$dbClass = new Database();
$db = $dbClass->conectar();

// Consulta SQL para seleccionar todos los agentes
$query = "SELECT id_agente, nombre, tarjeta, foto FROM agentes";
$result = $db->query($query);

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agentes</title>
    <!-- Agregar enlaces a los estilos de Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
    margin: 0;
    padding: 0;
    overflow: hidden;
    font-family: 'Press Start 2P', cursive; /* Fuente estilo pixelada */
    /* Selecciona una fuente legible */
}

#video-background {
    position: fixed;
    right: 0;
    bottom: 0;
    min-width: 100%;
    min-height: 100%;
    width: auto;
    height: auto;
    z-index: -100;
}

#contenido {
    position: relative;
    z-index: 1;
    color: white;
    /* Color del texto sobre el video */
    padding: 20px;
    /* Añade un espacio alrededor del contenido */
}
#contenido-container {
            max-height: 100vh;
            /* Altura máxima del contenedor */
            overflow-y: scroll;
            /* Agregamos la barra de desplazamiento vertical */
            margin: 0 auto;
            /* Centramos el contenedor horizontalmente */
            padding: 20px;
            /* Añadimos un espacio alrededor del contenido */
        }
        .container {
            position: relative; /* Asegura que el contenido se mantenga dentro del contenedor */
        }

        .card {
            transition: filter 0.3s ease;
            filter: saturate(20%); /* Tarjetas oscuras por defecto */
        }

        .card:hover {
            filter: saturate(100%); /* Aumenta la saturación al pasar el ratón */
        }

        .card-img-top {
            transition: transform 0.5s ease;
        }

        .card:hover .card-img-top {
            transform: scale(1.05);
        }

        .card-title {
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
            max-width: 100%;
            color: purple; /* Cambia el color del nombre de los agentes a morado */
            font-size: 1.2rem; /* Reduce el tamaño del nombre */
        }

        .card-text {
            text-overflow: ellipsis;
            max-width: 100%;
            color: black; /* Asegura que el texto sea visible en el fondo */
        }

        h1 {
            color: #fff;
        }
        .btn-volver {
            position: absolute;
            top: 10px;
            right: 25px;
            background-color: red; /* Botón naranja */
            border: none;
        }

        .btn-volver:hover {
            background-color: #cc3a00; /* Naranja más oscuro al pasar el mouse */
        }
    </style>
</head>

<body>
<div id="contenido-container"> <!-- Agregamos un nuevo contenedor -->
    <video autoplay loop muted id="video-background">
        <source src="../../video/videoclove.mp4" type="video/mp4">
        Tu navegador no soporta videos HTML5.
    </video>
    <div class="container">
        <h1 class="text-center ">Agentes Disponibles</h1>
        <br>
        
        <div class="row">
            <?php while ($row = $result->fetch(PDO::FETCH_ASSOC)): ?>
            <div class="col-md-3 mb-4">
                <div class="card">
                    <!-- Aquí se convierte la tarjeta en base64 y se incrusta en la etiqueta de imagen -->
                    <img src="data:image/jpeg;base64,<?php echo base64_encode($row['tarjeta']); ?>" class="card-img-top"
                        alt="<?php echo $row['nombre']; ?>">
                    <div class="card-body text-center">
                        <h5 class="card-title"><?php echo $row['nombre']; ?></h5>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        <a href="javascript:history.back()" class="btn btn-danger btn-volver"">Volver</a>
    </div>

    <!-- Agregar el script de Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>

<?php
// Cerrar la conexión a la base de datos
$db = null;
?>
