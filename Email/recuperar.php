<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require_once '../Config/conexion.php';

    $documento = $_POST["documento"];
    $db = new Database();
    $pdo = $db->conectar();

    // Verificar si el documento existe en la base de datos
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id_usuario = ?");
    $stmt->execute([$documento]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        // Generar una nueva contraseña aleatoria
        $nueva_contraseña = bin2hex(random_bytes(8)); // Genera una cadena hexadecimal aleatoria de 16 caracteres

        // Encriptar la nueva contraseña
        $contraseña_encriptada = password_hash($nueva_contraseña, PASSWORD_DEFAULT);

        // Actualizar la contraseña en la base de datos
        $sql = "UPDATE usuarios SET contraseña = :contraseña WHERE id_usuario = :documento";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':contraseña', $contraseña_encriptada);
        $stmt->bindParam(':documento', $documento);
        $stmt->execute();

        // Enviar la nueva contraseña por correo electrónico
        $mensaje = "Su nueva contraseña es: $nueva_contraseña";
        $asunto = "Recuperación de Contraseña";
        $headers = "From: tecnelectrics@gmail.com\r\n";
        mail($usuario['correo'], $asunto, $mensaje, $headers);
        echo "<script>alert('Se ha enviado una nueva contraseña al correo electrónico asociado al documento proporcionado.'); window.location.href='../login.php?accion=registro';</script>";
    } else {
        echo "<script>alert('No se encontró ninguna cuenta asociada a este documento.'); window.location.href='../Email/recuperar.php?accion=registro';</script>";
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
    <link rel="apple-touch-icon" sizes="60x60" href="../Assets/favicon/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="../Assets/favicon/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="../Assets/favicon/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="../Assets/favicon/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="../Assets/favicon/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="../Assets/favicon/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="../Assets/favicon/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="../Assets/favicon/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192" href="../Assets/favicon/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../Assets/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="../Assets/favicon/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../Assets/favicon/favicon-16x16.png">
    <link rel="manifest" href="../Assets/favicon/manifest.json">

    <title>Valorant - Rqecuperar Contraseña</title>
    <link rel="stylesheet" href="../Assets/css/login.css">
</head>

<body>
    <!-- Inicio opciones -->
    <div id="opciones">
        <a href="../index.php" class="volver-link">
            <i class="fas fa-sign-out-alt"></i>
        </a>
        <button id="pauseButton" class="audio">
            <i id="audioIcon" class="fas fa-volume-up" onclick="pausarAudio()"></i>
        </button>
        <audio id="miAudio" autoplay loop>
            <source src="../Assets/audio/carga.mp3" type="audio/mpeg">
        </audio>

    </div>

    <!-- Inicio Login -->
    <div class="login">
        <div class="header">
            <div class="center">
                <img src="../Assets/img/logo.png" alt="Logo Valorant">
                <div class="clear"></div>
            </div>
            <!-- Inicio Form recuperar -->
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                <div class="center">
                    <div class="form-login">
                        <h2>Recupera Tu Contraseña</h2>
                        <input type="text" id="documento" name="documento" placeholder="ID de tu Usuario" required>
                        <a class="icon-face" href="https://www.facebook.com/?locale=es_LA"><i
                                class="fab fa-facebook"></i></a>
                        <a class="icon-google" href="https://www.google.com/intl/es-419/gmail/about/"><i
                                class="fab fa-google"></i></a>
                        <a class="icon-apple" href="https://www.apple.com/co/"><i class="fab fa-apple"></i></a><br>
                        <span><input type="checkbox" id="continuar" name="continuar"><label
                                for="continuar">Recordar</label></span>
                                <div class="button">
                            <button class="button">
                                <a href="" value="Recuperar Contraseña"><i class="fas fa-arrow-alt-circle-right"></i></a>
                            </button>
                        </div>
                    </div>
                    <div class="clear"></div>
                </div>
            </form>
        </div>
        <!-- Inicio Footer -->
        <footer>
            <div class="settings">
                <a href="../login.php">
                    <p>Iniciar Sesion</p>
                </a>
                <a href="../registro.php">
                    <p>Crea una Cuenta?</p>
                </a>
            </div>
        </footer>
    </div>
    <!-- Bnner -->
    <div class="banner" name="Banner"></div>
</body>
<!-- audios -->
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