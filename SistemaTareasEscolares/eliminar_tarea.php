

<?php
require_once "conexion.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit;
}

$id = filter_input(
    INPUT_POST,
    "id",
    FILTER_VALIDATE_INT
);

if (!$id || $id < 1) {
    header("Location: index.php");
    exit;
}

try {
    $sql = "DELETE FROM tareas WHERE id = :id";
    $consulta = $conexion->prepare($sql);
    $consulta->execute([":id" => $id]);

    header("Location: index.php?eliminado=1");
    exit;

} catch (PDOException $e) {
    error_log($e->getMessage());
    die("No se pudo eliminar la tarea.");
}
?>
