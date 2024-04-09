<?php
session_start();

if (!isset($_SESSION['jugador'])) {
    header("Location: ../../../../valorant/registro.php");
    exit;
}

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: ../../../../valorant/registro.php");
    exit;
}

?>