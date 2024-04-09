<?php
session_start();
require_once("Config/conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $password = password_hash($_POST['contraseña'], PASSWORD_DEFAULT); // Corrección aquí
    $id_agente = $_POST['avatar']; 
    $puntos_rango = 0;
    $puntos_salud = 150;
    $id_rango = 1;
    $id_estado = 2;
    $id_rol = 1;
    $ultima_conexion = date('Y-m-d H:i:s');

    try {
        $db = new Database();
        $conn = $db->conectar();

        // Verificar si el usuario ya existe
        $stmt = $conn->prepare("SELECT COUNT(*) FROM usuarios WHERE id_usuario = ?");
        $stmt->execute([$id]);
        $id_count = $stmt->fetchColumn();

        $stmt = $conn->prepare("SELECT COUNT(*) FROM usuarios WHERE nombre = ?");
        $stmt->execute([$nombre]);
        $user_count = $stmt->fetchColumn();

        // Verificar si el correo electrónico ya existe
        $stmt = $conn->prepare("SELECT COUNT(*) FROM usuarios WHERE correo = ?");
        $stmt->execute([$correo]);
        $email_count = $stmt->fetchColumn();

        if ($id_count > 0) {
            echo "<script>alert('El id del usuario ya se encuentra registrado'); window.location='registro.php';</script>";
        } elseif ($user_count > 0) {
            echo "<script>alert('El nombre del usuario ya esta en uso'); window.location='registro.php';</script>";
        } elseif ($email_count > 0) {
            echo "<script>alert('El correo electronico ya se encuentra en uso'); window.location='registro.php';</script>";
        } else {
            // Insertar usuario si no existe ni el nombre de usuario ni el correo electrónico
            $stmt = $conn->prepare("INSERT INTO usuarios (id_usuario, nombre, correo, contraseña, puntos_salud, puntos_rango, id_agente, id_estado, id_rol, id_rango, ultima_conexion) 
                                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            $stmt->execute([$id, $nombre, $correo, $password, $puntos_salud, $puntos_rango, $id_agente, $id_estado, $id_rol, $id_rango, $ultima_conexion]);

            echo "<script>alert('Se ha registrado correctamente. Su cuenta será activada en un plazo de 10 a 15 minutos'); window.location='login.php';</script>";
            exit();
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Obtener datos de agentes para el select
try {
    $db = new Database();
    $conn = $db->conectar();
    $stmt = $conn->query("SELECT id_agente, nombre, foto FROM agentes");
    $agentes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Assets/css/registro.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <title>Registro de Usuario</title>

</head>

<body>
    <div class="contenedor">
        <div id="agenteSeleccionado">
            <img src="Assets/img/riot.png" alt="Riot_logo" style="height: 380px;">
        </div>
        <div class="formulario">
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <img src="Assets/img/riot.png" alt="Riot_logo">
                <h2>Registrarse</h2>
                <input type="number" id="id" name="id" placeholder="ID del usuario" required><br><br>
                <input type="text" id="nombre" name="nombre" placeholder="Nombre de Usuario - Nickname" required><br><br>
                <input type="correo" id="correo" name="correo" placeholder="Correo Electronico" required><br><br>
                <input type="password" id="contraseña" name="contraseña" placeholder="contraseña" required><br><br>

                <select id="avatar" name="avatar" required onchange="mostrarImagen()">
                    <option value="">Seleccione un agente</option>
                    <?php foreach ($agentes as $agente) : ?>
                        <option value="<?php echo $agente['id_agente']; ?>"><?php echo $agente['nombre']; ?></option> <!-- Cambio aquí -->
                    <?php endforeach; ?>
                </select><br><br>


                <button type="submit" class="submit-btn">
                    <i class="fas fa-arrow-right"></i> <!-- Ícono de flecha hacia la derecha -->
                </button><br><br>
                <div class="objetos">
                    <a href=""></a>
                    <a href="login.php">Inicia Sesión</a>
                    <a href="recuperar_contraseña.php">¿Olvidaste la Contraseña?</a>
                    <a href=""></a>
                </div>
            </form>
        </div>
    </div>
    <script>
        function mostrarImagen() {
            var avatarSelect = document.getElementById("avatar");
            var agenteSeleccionado = document.getElementById("agenteSeleccionado");
            var idAgente = avatarSelect.value; // Obtener el ID del agente seleccionado

            // Limpiar contenido anterior
            agenteSeleccionado.innerHTML = '';

            // Buscar el agente seleccionado en la lista de agentes
            <?php foreach ($agentes as $agente) : ?>
                if ('<?php echo $agente['id_agente']; ?>' === idAgente) { // Comprobar si el ID del agente coincide
                    // Crear imagen y establecer atributos
                    var img = document.createElement("img");
                    img.src = 'data:image/jpeg;base64,' + '<?php echo base64_encode($agente["foto"]); ?>'; // Campo foto como BLOB
                    img.alt = '<?php echo $agente["nombre"]; ?>';
                    img.width = 200; // Tamaño deseado de la imagen
                    img.height = 200;

                    // Agregar imagen al div
                    agenteSeleccionado.appendChild(img);
                }
            <?php endforeach; ?>
        }
    </script>

</body>

</html>