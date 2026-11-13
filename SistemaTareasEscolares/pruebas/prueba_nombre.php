
<?php

require_once __DIR__ . "/../validaciones.php";
$pruebas = [
    ["nombre" => "Tarea de matemáticas", "esperado" => true],
    ["nombre" => "", "esperado" => false],
    ["nombre" => "   ", "esperado" => false],
    ["nombre" => "Proyecto de programación", "esperado" => true]
];

$aprobadas = 0;
$total = count($pruebas);

echo "<h1>Pruebas de validación del nombre</h1>";

foreach ($pruebas as $numero => $prueba) {

    $resultado = validarNombre($prueba["nombre"]);

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

echo "<hr>";
echo "<h2>Resultados: $aprobadas de $total pruebas aprobadas</h2>";

?>
