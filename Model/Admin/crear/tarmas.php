<?php 
// Incluir el encabezado de la página
include "../template/header.php";

// Incluir el archivo de conexión a la base de datos
require_once("../../../Config/conexion.php");
$DataBase = new Database;
$con = $DataBase->conectar();

// Verificar si se ha enviado el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST["nombre"];

    // Consulta SQL para insertar un nuevo tipo de arma en la tabla
    $sql = "INSERT INTO tipo_arma (NOMBRE) VALUES (:nombre)";
    $stmt = $con->prepare($sql);
    $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);

    // Ejecutar la consulta
    if ($stmt->execute()) {
        echo "<script>alert('Tipo de arma creado');</script>";
        echo "<script>window.location='../visualizar/tarmas.php';</script>";
        exit();
    } else {
        echo "<script>alert('Error al crear el tipo de arma');</script>";
    }
}
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h2 class="text-center">Crear Tipo de Arma</h2>
            <form method="post">
                <div class="form-group">
                    <label for="nombre">Nombre:</label>
                    <input type="text" class="form-control" name="nombre" required>
                </div>
                <div class="form-group text-center">
                    <input type="submit" class="btn btn-primary" value="Agregar">
                </div>
            </form>
        </div>
    </div>
</div>

<?php 
include "../template/footer.php";
?>
