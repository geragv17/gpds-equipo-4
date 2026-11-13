
<?php
$servidor = "localhost";
$usuario = "root";
$contrasena = "";
$base_datos = "tareas_escolares";

try {
    $conexion = new PDO(
        "mysql:host=$servidor;dbname=$base_datos;charset=utf8mb4",
        $usuario,
        $contrasena
    );

    $conexion->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

} catch (PDOException $e) {
    error_log($e->getMessage());
    die("No se pudo conectar con la base de datos.");
}
?>
