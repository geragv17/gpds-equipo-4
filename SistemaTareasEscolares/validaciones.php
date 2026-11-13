
<?php

function validarNombre($nombre) {
    return trim($nombre) !== "";
}

function validarFecha($fecha, $hoy = null) {

    $fechaValida = DateTime::createFromFormat(
        "!Y-m-d",
        $fecha
    );

    if (!$fechaValida ||
        $fechaValida->format("Y-m-d") !== $fecha) {
        return false;
    }

    if ($hoy === null) {
        $hoy = (new DateTime(
            "today",
            new DateTimeZone("America/Mexico_City")
        ))->format("Y-m-d");
    }

    return $fecha >= $hoy;
}
