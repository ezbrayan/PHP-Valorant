<?php
session_start();
require_once("Config/conexion.php");

// Crear una instancia de la clase Database para obtener la conexión PDO
$database = new Database();
$pdo = $database->conectar();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = $_POST["correo"];
    $contraseña = $_POST["contraseña"];

    if (empty($correo) || empty($contraseña)) {
        $_SESSION['error'] = 'Nombre de usuario y contraseña son obligatorios.';
        echo "<script>alert('Nombre de usuario y contraseña son obligatorios.'); window.location.href='login.php';</script>";
        exit();
    }

    $query = "SELECT * FROM usuarios WHERE correo = :correo";
    $stmt = $pdo->prepare($query);
    $stmt->execute(array(':correo' => $correo));

    if ($stmt->rowCount() == 1) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user['id_estado'] == 2) {
            $_SESSION['error'] = '';
            echo "<script>alert('Por el momento, su cuenta está desactivada. Por favor, espere a que su cuenta sea activada.'); window.location.href='login.php';</script>";
            exit();
        }

        if (password_verify($contraseña, $user['contraseña'])) {
            $_SESSION['jugador'] = $user;

            // Actualizar la última conexión en la base de datos
            $updateQuery = "UPDATE usuarios SET ultima_conexion = :ultima_conexion WHERE id_usuario = :user_id";
            $updateStmt = $pdo->prepare($updateQuery);
            $updateStmt->execute(array(':ultima_conexion' => date('Y-m-d H:i:s'), ':user_id' => $user['id_usuario']));

            // Redirigir el jugador a la página correspondiente a su tipo
            if ($user['id_rol'] == 1) {
                header("Location: Model/Admin/index.php");
                exit();
            } elseif ($user['id_rol'] == 2) {
                header("Location: Views/index.php");
                exit();
            }
        } else {
            $_SESSION['error'] = 'Contraseña incorrecta.';
            echo "<script>alert('Contraseña incorrecta.'); </script>";
            exit();
        }
    } else {
        $_SESSION['error'] = 'Usuario no encontrado.';
        echo "<script>alert('Usuario no encontrado.'); </script>";
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="Assets/css/registro.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <title>Iniciar Sesión</title>
</head>

<body>
<a href="index.php" class="volver-link"><i class="fas fa-sign-out-alt"></i></a>
    <div class="contenedor">
        <div>

        </div>
        <div class="formulario">
            <img src="Assets/img/riot.png" alt="Riot_logo"><br><br>
            <h2>Iniciar Sesión</h2><br><br>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <input type="text" id="correo" name="correo" placeholder="Correo electronico" required><br><br><br>
                <input type="contraseña" id="contraseña" name="contraseña" placeholder="Contraseña" required><br><br><br>
                <button type="submit" class="submit-btn">
                    <i class="fas fa-arrow-right"></i> <!-- Ícono de flecha hacia la derecha -->
                </button><br><br><br>
            </form>
            <div class="objetos">
                    <a href=""></a>
                    <a href="registro.php">Registrate</a>
                    <a href="recuperar_contraseña.php">¿Olvidaste la Contraseña?</a>
                    <a href=""></a>
                </div>
        </div>
        <div>

        </div>
    </div>
</body>

</html>