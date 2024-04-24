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

// Consulta SQL para seleccionar todas las armas con sus detalles
$query = "SELECT id_arma, nombre, foto, balas, daño FROM armas";
$result = $db->query($query);

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Armas</title>
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
            max-width: 800px;
            margin: auto;
            padding: 20px;
        }

        .card {
            margin-bottom: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            height: 100%; /* Ajustar todas las tarjetas al mismo tamaño */
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card-img-top {
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
            height: auto; /* Ajustar altura automáticamente */
            max-width: 100%; /* Máximo ancho */
            object-fit: cover; /* Escalar y recortar la imagen */
        }

        .card-body {
            padding: 20px;
            height: 100%; /* Ajustar todas las tarjetas al mismo tamaño */
        }

        .card-title {
            margin-bottom: 10px;
            font-size: 1.5rem;
            color: #333;
        }

        .card-text {
            margin-bottom: 10px;
            color: #555;
        }

        .btn-primary {
            background-color: #4CAF50;
            border: none;
            color: white;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            transition-duration: 0.4s;
            cursor: pointer;
            border-radius: 8px;
        }

        .btn-primary:hover {
            background-color: #45a049;
            color: white;
        }
    </style>
</head>

<body>

    <div class="container">
        <h1 class="text-center mb-4">Armas Disponibles</h1>
        <div class="row">
            <?php while ($row = $result->fetch(PDO::FETCH_ASSOC)): ?>
            <div class="col-md-4">
                <div class="card">
                    <img src="data:image/jpeg;base64,<?php echo base64_encode($row['foto']); ?>" class="card-img-top" alt="<?php echo $row['nombre']; ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $row['nombre']; ?></h5>
                        <p class="btn btn-success">Balas: <?php echo $row['balas']; ?></p>
                        <p class="btn btn-danger">Daño: <?php echo $row['daño']; ?></p>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        <br>
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
