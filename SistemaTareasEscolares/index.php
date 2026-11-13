
<?php
require_once "conexion.php";
require_once "validaciones.php";
$filtro = $_GET["filtro"] ?? "todas";


$estadisticas = $conexion->query("
    SELECT
        COUNT(*) AS total,
        COALESCE(SUM(estado = 'pendiente'), 0) AS pendientes,
        COALESCE(SUM(estado = 'completada'), 0) AS completadas
    FROM tareas
")->fetch(PDO::FETCH_ASSOC);


$mensaje = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = trim($_POST["nombre"] ?? "");
    $descripcion = trim($_POST["descripcion"] ?? "");
    $fecha = $_POST["fecha_entrega"] ?? "";

    // Validar que el nombre no esté vacío
    if (!validarNombre($nombre)) {
        $error = "Debes escribir el nombre de la tarea.";
    } else {
        
        // Validar la fecha de entrega
        if (!validarFecha($fecha)) {
            $error = "Debes seleccionar una fecha válida que no sea anterior a hoy.";
        } else {
    try {
                $sql = "INSERT INTO tareas
                        (nombre, descripcion, fecha_entrega)
                        VALUES (:nombre, :descripcion, :fecha)";

                $consulta = $conexion->prepare($sql);

                $consulta->execute([
                    ":nombre" => $nombre,
                    ":descripcion" => $descripcion,
                    ":fecha" => $fecha
                ]);

                header("Location: index.php?guardado=1");
                exit;
            } catch (PDOException $e) {
                error_log($e->getMessage());
                $error = "No se pudo guardar la tarea.";
            }
        }
    }
}

if (isset($_GET["guardado"])) {
    $mensaje = "¡Tarea registrada correctamente!";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">
    <title>Gestión de Tareas Escolares</title>

<link rel="stylesheet" href="css/estilos.css">

</head>
<body>


<!-- MENU LATERAL -->
<aside class="sidebar">

    <div class="sidebar-logo">
        <h2>EduTasks</h2>
        <p>Gestión Escolar</p>
    </div>

    <nav class="sidebar-menu">

        <a href="#inicio" class="menu-activo">
            <span>▦</span> Panel principal
        </a>

        <a href="#tareas">
            <span>☷</span> Mis tareas
        </a>

        <a href="#nueva-tarea">
            <span>＋</span> Nueva tarea
        </a>

    </nav>

    <div class="sidebar-footer">
        <p>Sistema de Gestión Escolar</p>
    </div>

</aside>
<!-- FIN DEL MENU LATERAL -->

<!-- CONTENIDO PRINCIPAL -->
<main class="contenido-principal" id="inicio">

    <header class="encabezado">
        <div>
            <h1>Panel de control</h1>
            <p>Organiza y administra tus tareas escolares</p>
        </div>

            </header>



  

<div class="estadisticas">

    <div class="tarjeta">
        <h3>Total de tareas</h3>
        <h2><?php echo (int)$estadisticas["total"]; ?></h2>
    </div>

    <div class="tarjeta">
        <h3>Pendientes</h3>
        <h2><?php echo (int)$estadisticas["pendientes"]; ?></h2>
    </div>

    <div class="tarjeta">
        <h3>Completadas</h3>
        <h2><?php echo (int)$estadisticas["completadas"]; ?></h2>
    </div>

</div>


    <h3 id="nueva-tarea">Registrar nueva tarea</h3>

    
<?php if ($mensaje !== ""): ?>
    <div id="mensaje-exito" class="alerta-exito">
        <span class="alerta-icono">✓</span>
        <div>
            <strong>¡Operación exitosa!</strong>
            <p><?php echo htmlspecialchars($mensaje); ?></p>
        </div>
    </div>
<?php endif; ?>


    <?php if ($error !== ""): ?>
        <p style="color: red;">
            <?php echo htmlspecialchars($error); ?>
        </p>
    <?php endif; ?>

    
<form method="POST" action="index.php" class="formulario-tarea">

    <div class="formulario-encabezado">
        <h4>Información de la tarea</h4>
        <p>Completa los datos para registrar una nueva actividad.</p>
    </div>

    <div class="campo">
        <label for="nombre">Nombre de la tarea</label>
        <input
            type="text"
            id="nombre"
            name="nombre"
            maxlength="255"
            placeholder="Ej. Proyecto de programación"
            required
        >
    </div>

    <div class="campo">
        <label for="descripcion">Descripción</label>
        <textarea
            id="descripcion"
            name="descripcion"
            placeholder="Escribe una breve descripción de la actividad..."
        ></textarea>
    </div>

    <div class="campo">
        <label for="fecha_entrega">Fecha de entrega</label>
        <input
            type="date"
            id="fecha_entrega"
            name="fecha_entrega"
            min="<?php echo (new DateTime('today', new DateTimeZone('America/Mexico_City')))->format('Y-m-d'); ?>"
            required
        >
    </div>

    <div class="formulario-acciones">
        <button type="reset" class="btn-limpiar">
            Limpiar
        </button>

        <button type="submit" class="btn-guardar">
            + Guardar tarea
        </button>
    </div>

</form>

<hr>

<h3 id="tareas">Lista de tareas escolares</h3>


<div class="buscador-tareas">
    <span>🔍</span>
    <input
        type="text"
        id="buscarTarea"
        placeholder="Buscar tarea por nombre..."
        autocomplete="off"
    >
</div>


<div class="filtros-tareas">

    <a href="index.php?filtro=todas"
       class="<?php echo $filtro === 'todas' ? 'filtro-activo' : ''; ?>">
        Todas
    </a>

    <a href="index.php?filtro=pendiente"
       class="<?php echo $filtro === 'pendiente' ? 'filtro-activo' : ''; ?>">
        Pendientes
    </a>

    <a href="index.php?filtro=completada"
       class="<?php echo $filtro === 'completada' ? 'filtro-activo' : ''; ?>">
        Completadas
    </a>

</div>


<?php


if (!in_array($filtro, ["todas", "pendiente", "completada"], true)) {
    $filtro = "todas";
}

if ($filtro === "todas") {
    $sql = "SELECT * FROM tareas ORDER BY fecha_entrega ASC";
    $consulta = $conexion->query($sql);
} else {
    $sql = "SELECT * FROM tareas
            WHERE estado = :estado
            ORDER BY fecha_entrega ASC";

    $consulta = $conexion->prepare($sql);
    $consulta->execute([":estado" => $filtro]);
}

$tareas = $consulta->fetchAll(PDO::FETCH_ASSOC);

?>

<?php if (count($tareas) > 0): ?>

<table border="1" cellpadding="10" cellspacing="0">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Descripción</th>
            <th>Fecha de entrega</th>
            <th>Estado</th>
<th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($tareas as $tarea): ?>
            <tr>
                <td>
                    <?php echo (int)$tarea["id"]; ?>
                </td>

                <td>
                    <?php echo htmlspecialchars(
                        $tarea["nombre"]
                    ); ?>
                </td>

                <td>
                    <?php echo htmlspecialchars(
                        $tarea["descripcion"] ?? ""
                    ); ?>
                </td>

                <td>
                    <?php echo htmlspecialchars(
                        $tarea["fecha_entrega"]
                    ); ?>
                </td>

                
<td>
    <?php
    $estado = $tarea["estado"];
    $hoy = (new DateTime(
        "today",
        new DateTimeZone("America/Mexico_City")
    ))->format("Y-m-d");

    if ($estado === "completada") {
        echo '<span class="estado completada">Completada</span>';
    } elseif ($tarea["fecha_entrega"] < $hoy) {
        echo '<span class="estado vencida">Vencida</span>';
    } else {
        echo '<span class="estado pendiente">Pendiente</span>';
    }
    ?>
</td>

<td>
    <form method="POST" action="cambiar_estado.php">
        <input
            type="hidden"
            name="id"
            value="<?php echo (int)$tarea['id']; ?>"
        >

        <?php if ($tarea["estado"] === "pendiente"): ?>

            <button type="submit" class="btn-completar">
    Marcar como completada
</button>
        <?php else: ?>

            <button type="submit" class="btn-pendiente">
    Marcar como pendiente
</button>
        <?php endif; ?>
    </form>

<form method="POST"
      action="eliminar_tarea.php"
      onsubmit="return confirm('¿Estás seguro de que deseas eliminar esta tarea?');">

    <input
        type="hidden"
        name="id"
        value="<?php echo (int)$tarea['id']; ?>"
    >

    <button type="submit" class="btn-eliminar">
    Eliminar
</button>
</form>

</td>

            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php else: ?>

    <p>No hay tareas registradas todavía.</p>

<?php endif; ?>

</main>


<script>
document.addEventListener("DOMContentLoaded", function () {
    const alerta = document.querySelector(".alerta-exito");

    if (alerta) {
        // Quitar el parámetro guardado de la URL
        const url = new URL(window.location.href);
        url.searchParams.delete("guardado");
        window.history.replaceState({}, "", url);

        // Ocultar la notificación después de 4 segundos
        setTimeout(function () {
            alerta.style.display = "none";
        }, 4000);
    }
});
</script>


<script>
document.addEventListener("DOMContentLoaded", function () {

    const buscador = document.getElementById("buscarTarea");
    const tabla = document.querySelector(".contenido-principal table");

    if (buscador && tabla) {

        const filas = tabla.querySelectorAll("tbody tr");

        buscador.addEventListener("input", function () {

            const texto = buscador.value.toLowerCase().trim();

            filas.forEach(function (fila) {

                const columnaNombre = fila.cells[1];

                if (columnaNombre) {

                    const nombre = columnaNombre.textContent.toLowerCase();

                    if (nombre.includes(texto)) {
                        fila.style.display = "";
                    } else {
                        fila.style.display = "none";
                    }

                }

            });

        });

    }

});
</script>



</body>
</html>


