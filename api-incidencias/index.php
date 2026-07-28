<?php

header("Content-Type: application/json; charset=UTF-8"); //Le dice al navegador "Lo que voy a enviar es JSON."

require_once __DIR__ . "/routes/incidencias.php"; // Carga el archivo de rutas. Cargar este archivo una sola vez. Si no existe, detener la ejecución.

?>