<?php include "../template/header.php"; ?>

<?php
require_once("../../../Config/conexion.php");
$DataBase = new Database;
$con = $DataBase->conectar();

// Procesar el formulario cuando se envía
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $foto = file_get_contents($_FILES['foto']['tmp_name']);
    $balas = $_POST['balas'];
    $daño = $_POST['daño'];

    try {
        $stmt = $con->prepare("INSERT INTO armas (nombre, foto, balas, daño) VALUES (?, ?, ?, ?)");
        $stmt->bindParam(1, $nombre, PDO::PARAM_STR);
        $stmt->bindParam(2, $foto, PDO::PARAM_LOB);
        $stmt->bindParam(3, $balas, PDO::PARAM_INT);
        $stmt->bindParam(4, $daño, PDO::PARAM_INT);
        $stmt->execute();

        echo "<script>alert('Arma creada correctamente'); window.location='../visualizar/armas.php';</script>";
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h2 class="text-center">Crear Arma</h2>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="nombre">Nombre:</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" required>
                </div>
                <div class="form-group">
                    <label for="foto">Foto:</label>
                    <input type="file" class="form-control-file" id="foto" name="foto" required>
                </div>
                <div class="form-group">
                    <label for="balas">Balas:</label>
                    <input type="number" class="form-control" id="balas" name="balas" required>
                </div>
                <div class="form-group">
                    <label for="daño">Daño:</label>
                    <input type="number" class="form-control" id="daño" name="daño" required>
                </div>
                <button type="submit" class="btn btn-primary">Crear Arma</button>
            </form>
        </div>
    </div>
</div>

<?php include "../template/footer.php"; ?>