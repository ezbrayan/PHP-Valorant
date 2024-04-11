<?php include "../template/header.php"; ?>
<?php
require_once("../../../Config/conexion.php");
$DataBase = new Database;
$con = $DataBase->conectar();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST["nombre"];
    $foto = file_get_contents($_FILES['foto']['tmp_name']);

    $sql = "INSERT INTO rango (nombre, foto) VALUES (:nombre, :foto)";
    $stmt = $con->prepare($sql);

    $stmt->execute(array(':nombre' => $nombre, ':foto' => $foto));

    // Redireccionar a la página actual para actualizar la tabla

    echo "<script>alert('Rango creado'); window.location='../visualizar/rango.php';</script>";
    exit();
}
?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h2 class="text-center">Crear Rango</h2>
            <form method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="nombre">Nombre:</label>
                    <input type="text" class="form-control" name="nombre" required>
                </div>
                <div class="form-group">
                    <label for="foto">Foto:</label>
                    <input type="file" class="form-control-file" name="foto" accept="image/jpeg" required>
                </div>
                <div class="form-group text-center">
                    <input type="submit" class="btn btn-primary" value="Agregar">
                </div>
            </form>
        </div>
    </div>
</div>


<?php include "../template/footer.php"; ?>