
<?php

require_once __DIR__ . "/../validaciones.php";

$hoy = "2026-11-12";

$pruebas = [
    [
        "fecha" => "2026-11-15",
        "esperado" => true
    ],
    [
        "fecha" => "2026-11-12",
        "esperado" => true
    ],
    [
        "fecha" => "2026-11-10",
        "esperado" => false
    ],
    [
        "fecha" => "2026-02-30",
        "esperado" => false
    ],
    [
        "fecha" => "15/11/2026",
        "esperado" => false
    ],
    [
        "fecha" => "",
        "esperado" => false
    ]
];

$aprobadas = 0;

echo "<h1>Pruebas de validación de fechas</h1>";

foreach ($pruebas as $numero => $prueba) {

    $resultado = validarFecha(
        $prueba["fecha"],
        $hoy
    );

    if ($resultado === $prueba["esperado"]) {
        echo "<p style='color:green;'>
                Prueba " . ($numero + 1) . ": APROBADA
              </p>";
        $aprobadas++;
    } else {
        echo "<p style='color:red;'>
                Prueba " . ($numero + 1) . ": FALLIDA
              </p>";
    }
}

$total = count($pruebas);

echo "<hr>";
echo "<h2>Resultados: $aprobadas de $total pruebas aprobadas</h2>";
?>
