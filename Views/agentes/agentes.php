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
            background-image: url('https://img.freepik.com/fotos-premium/patron-nubes-fisuras_230313-146.jpg'); /* Utilizando una textura para simular las nubes */
            animation: moveBackground 60s linear infinite;
        }

        @keyframes moveBackground {
            from {
                background-position: 0% 0%;
            }
            to {
                background-position: 100% 0%;
            }
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
            color: black;
        }
    </style>
</head>

<body>

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
        <a href="javascript:history.back()" class="btn btn-danger mb-3">Volver</a>
    </div>

    <!-- Agregar el script de Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>

<?php
// Cerrar la conexión a la base de datos
$db = null;
?>
