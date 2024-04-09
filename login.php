<?php
session_start();
require_once ("Config/conexion.php");

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
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!--favicon-->
    <link rel="apple-touch-icon" sizes="60x60" href="Assets/favicon/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="Assets/favicon/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="Assets/favicon/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="Assets/favicon/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="Assets/favicon/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="Assets/favicon/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="Assets/favicon/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="Assets/favicon/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192" href="Assets/favicon/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="Assets/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="Assets/favicon/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="Assets/favicon/favicon-16x16.png">
    <link rel="manifest" href="Assets/favicon/manifest.json">

    <title>Valorant - Iniciar Sesión</title>
    <link rel="stylesheet" href="Assets/css/login.css">
</head>

<body>

    <div id="opciones">
        <button id="pauseButton" class="audio">
            <i id="audioIcon" class="fas fa-volume-up" onclick="pausarAudio()"></i>
        </button>
        <audio id="miAudio" autoplay loop>
            <source src="Assets/audio/carga.mp3" type="audio/mpeg">
        </audio>

        <a href="index.php" class="volver-link">
            <i class="fas fa-sign-out-alt"></i>
        </a>
    </div>
    <div class="contenedor">
        <div class="mitad-izquierda">
            <div class="botones">
                <a href="login.php" class="inicio-btn">Inicio Sesión</a>
                <a href="registro.php" class="registro-btn">Registrarme</a>
            </div>
        </div>
        <div class="mitad-derecha">
            <div class="formulario">
                <img src="Assets/img/Riots.webp" alt="Riot_logo"><br><br>
                <h2>Iniciar Sesión</h2><br><br>
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <input type="text" id="correo" name="correo" placeholder="Correo electrónico" required><br><br>
                    <input type="password" id="contraseña" name="contraseña" placeholder="Contraseña" required><br><br>
                    <button type="submit" class="submit-btn">
                        <i class="fas fa-arrow-right"></i>
                    </button><br><br>
                </form>
                <div class="objetos">
                    <a href=""></a>
                    <a href="recuperar_contraseña.php">¿Olvidaste la Contraseña?</a>
                    <a href=""></a>
                </div>
            </div>
        </div>
    </div>
</body>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        var miAudio = document.getElementById("miAudio");
        var audioIcon = document.getElementById("audioIcon");

        setTimeout(function () {
            miAudio.play();
            miAudio.volume = miAudio.volume * 0.5; // Establecer el volumen 
        }, 500); // Retraso de 0.5 segundos (500 milisegundos)

        audioIcon.addEventListener("click", function () {
            if (miAudio.paused) {
                miAudio.play();
                audioIcon.classList.remove("fa-volume-off");
                audioIcon.classList.add("fa-volume-up");
            } else {
                miAudio.pause();
                audioIcon.classList.remove("fa-volume-up");
                audioIcon.classList.add("fa-volume-off");
            }
        });
    });

</script>

</html>