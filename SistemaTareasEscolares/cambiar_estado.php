
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
    $sql = "UPDATE tareas
            SET estado = CASE
                WHEN estado = 'pendiente'
                    THEN 'completada'
                ELSE 'pendiente'
            END
            WHERE id = :id";

    $consulta = $conexion->prepare($sql);
    $consulta->execute([":id" => $id]);

    header("Location: index.php");
    exit;

} catch (PDOException $e) {
    error_log($e->getMessage());
    die("No se pudo actualizar el estado de la tarea.");
}
?>
