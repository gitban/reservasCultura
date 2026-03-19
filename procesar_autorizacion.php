<?php
// 1. Evitar que cualquier error de PHP se imprima y ensucie el JSON
ini_set('display_errors', 0);
error_reporting(0);

// 2. Iniciar un buffer para capturar cualquier salida accidental
ob_start();

require_once "defaultincludes.inc";
require_once "GestorAutorizacion.php";

// 3. Limpiar el buffer (borra espacios en blanco o BOMs de los archivos incluidos)
ob_clean();

$id = isset($_POST['id_reserva']) ? (int)$_POST['id_reserva'] : 0;
$nuevo = isset($_POST['nuevo_estado']) ? (int)$_POST['nuevo_estado'] : 0;

$gestor = new GestorAutorizacion();
$resultado = $gestor->cambiarEstado($id, $nuevo);

// 4. Asegurar la cabecera correcta
header('Content-Type: application/json; charset=utf-8');

// 5. Enviar solo el JSON
echo json_encode($resultado);

// Cortar la ejecución aquí para que no se filtre nada más
exit;